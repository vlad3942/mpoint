<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:mPointSettlement.php
 */

abstract class mPointSettlement
{
    protected $_iPspId = NULL;

    protected $_iClientId = NULL;

    private $_objClientConfig = NULL;

    private $_objPspConfig = NULL;

    protected $_objConnectionInfo = NULL;

    protected $_iSettlementId = NULL;

    protected $_sFileReferenceNumber = NULL;

    protected $_iRecordNumber = NULL;

    protected $_iFileSequenceNumber = NULL;

    protected $_sFileCreatedDate = NULL;

    protected $_sRecordType = NULL;

    private $_sTransactionXML = NULL;

    protected $_iAccountId = NULL;

    protected $_iTotalTransactionAmount = 0;

    protected $_arrayTransactionIds = [];

    protected $_objTXT;

    public function __construct($_OBJ_TXT, $clientId, $pspId, $connectionInfo)
    {
        $this->_iClientId = $clientId;
        $this->_iPspId = $pspId;
        $this->_objConnectionInfo = $connectionInfo;
        $this->_objTXT = $_OBJ_TXT;
    }

    private function _getAccountIds($_OBJ_DB)
    {
        $sql = "SELECT account.id
                FROM client" . sSCHEMA_POSTFIX . ".account_tbl account
                  INNER JOIN client" . sSCHEMA_POSTFIX . ".merchantsubaccount_tbl submerchant ON submerchant.accountid = account.id
                WHERE account.clientid = $this->_iClientId
                      AND submerchant.pspid = $this->_iPspId
                      AND account.enabled
                      AND submerchant.enabled";

        $aRS = $_OBJ_DB->getAllNames($sql);
        $aAccounts = [];
        if (is_array($aRS) === true && count($aRS) > 0) {
            foreach ($aRS as $rs) {
                array_push($aAccounts,(int)$rs["ID"]);
            }
        }
        return $aAccounts;
    }

    private function _getClientConfiguration($_OBJ_DB)
    {
        $this->_objClientConfig = ClientConfig::produceConfig($_OBJ_DB, $this->_iClientId);
    }

    private function _getPSPConfiguration($_OBJ_DB)
    {
        $accountIds = $this->_getAccountIds($_OBJ_DB);
        $this->_iAccountId =$accountIds[0];
        $this->_objPspConfig = PSPConfig::produceConfig($_OBJ_DB, $this->_iClientId, $this->_iAccountId, $this->_iPspId);
    }

    private function _getTransactions($_OBJ_DB, $aStateIds){

        $stateIds = implode(",", $aStateIds);
        $sql = "SELECT record_number, status
                FROM log" . sSCHEMA_POSTFIX . ".settlement_tbl
                WHERE client_id = $this->_iClientId 
                AND psp_id = '$this->_iPspId'                
                AND record_type = '$this->_sRecordType'                
                ORDER BY id DESC LIMIT 1 ";

        $res = $_OBJ_DB->getName($sql);
        $recordNumber = 0;
        if (is_array($res) === true && count($res) > 0) {
            $recordNumber = (int)$res["RECORD_NUMBER"];
            if( strtolower( $res["STATUS"]) == Constants::sSETTLEMENT_REQUEST_ACTIVE  ||  strtolower( $res["STATUS"]) ==  Constants::sSETTLEMENT_REQUEST_WAITING )
                return;
        }

        $this->_iRecordNumber = $recordNumber + 1 ;
        $this->_arrayTransactionIds=[];
        $this->_iTotalTransactionAmount = 0;
        $sql = "SELECT txn.id
                FROM log" . sSCHEMA_POSTFIX . ".transaction_tbl AS txn, log" . sSCHEMA_POSTFIX . ".message_tbl AS msg
                WHERE txn.clientid = $this->_iClientId
                AND txn.pspid =  $this->_iPspId
                AND txn.cardid NOTNULL
                AND msg.stateid IN ( $stateIds) 
                AND msg.txnid = txn.id ";

        $aRS = $_OBJ_DB->getAllNames($sql);
        if (is_array($aRS) === true && count($aRS) > 0) {

            $this->_sTransactionXML = "<transactions>";

            foreach ($aRS as $rs) {
                $transactionId = (int)$rs["ID"];
                array_push($this->_arrayTransactionIds,$transactionId);
                $obj_TxnInfo = TxnInfo::produceInfo($transactionId, $_OBJ_DB);
                $obj_TxnInfo->produceOrderConfig($_OBJ_DB);
                $this->_sTransactionXML .= $obj_TxnInfo->toXML();
                $this->_iTotalTransactionAmount += $obj_TxnInfo->getAmount();
            }

            $this->_sTransactionXML .= "</transactions>";
        }
    }

    public function capture($_OBJ_DB)
    {
        $this->_sRecordType = "CAPTURE";
        $this->_getTransactions($_OBJ_DB, array(Constants::iPAYMENT_CAPTURE_INITIATED_STATE));
    }

    public function cancel($_OBJ_DB)
    {
        $this->_sRecordType = "CANCEL";
        $this->_getTransactions($_OBJ_DB, array(Constants::iPAYMENT_CANCEL_INITIATED_STATE));
    }

    public function refund($_OBJ_DB)
    {
        $this->_sRecordType = "REFUND";
        $this->_getTransactions($_OBJ_DB, array(Constants::iPAYMENT_REFUND_INITIATED_STATE));
    }

    abstract protected function _createSettlementRecord($_OBJ_DB);

    protected function _toSettlementInfoXML()
    {

        $xml = "<settlement-info>";
        $xml .= "<record-number>$this->_iRecordNumber</record-number>";
        $xml .= "<file-reference-number>$this->_sFileReferenceNumber</file-reference-number>";
        $xml .= "<file-sequence-number>$this->_iFileSequenceNumber</file-sequence-number>";
        $xml .= "<file-creation-date>" . date("Ymd", strtotime($this->_sFileCreatedDate)) . "</file-creation-date>";
        $xml .= "<file-creation-time>" . date("His", strtotime($this->_sFileCreatedDate)) . "</file-creation-time>";
        $xml .= "<record-type>$this->_sRecordType</record-type>";
        $xml .= "<total-amount>$this->_iTotalTransactionAmount</total-amount>";
        $xml .= "</settlement-info>";

        return $xml;
    }

    protected function _send($_OBJ_DB)
    {
        try {

            $this->_insertSettlementRecords($_OBJ_DB);

            if ($this->_objClientConfig == NULL) {
                $this->_getClientConfiguration($_OBJ_DB);
            }

            if ($this->_objPspConfig == NULL) {
                $this->_getPSPConfiguration($_OBJ_DB);
            }
            $requestBody = '<?xml version="1.0" encoding="UTF-8"?><root><settlement>';
            $requestBody .= $this->_objClientConfig->toXML();
            $requestBody .= $this->_toSettlementInfoXML();
            $requestBody .= $this->_objPspConfig->toXML();
            $requestBody .= $this->_sTransactionXML;
            $requestBody .= '</settlement></root>';

            $obj_ConnInfo = $this->_constConnInfo($this->_objConnectionInfo["paths"]["settlement"]);

            $obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
            $obj_HTTP->connect();
            $code = $obj_HTTP->send($this->_constHTTPHeaders($this->_objClientConfig->getUsername(), $this->_objClientConfig->getPassword()), $requestBody);
            $obj_HTTP->disConnect();
            if ($code != 200) {
                throw new mPointException("Settlement Request Error code: " . $code . " and body: " . $obj_HTTP->getReplyBody(), $code);
            }
            else
            {
                $replyBody = simpledom_load_string($obj_HTTP->getReplyBody());
                if($replyBody->settlement->file["upload-status"] == "true")
                {
                    $this->_updateSettlementState($_OBJ_DB, Constants::sSETTLEMENT_REQUEST_WAITING );
                    $this->_updateDescription($_OBJ_DB, $replyBody);
                }
                else
                {
                    $this->_updateSettlementState($_OBJ_DB, Constants::sSETTLEMENT_REQUEST_FAIL);
                }
            }
        } catch (Exception $e) {
            $this->_updateSettlementState($_OBJ_DB, Constants::sSETTLEMENT_REQUEST_FAIL);
            trigger_error("Settlement record no: " . $this->_iSettlementId . " failed with code: " . $e->getCode() . " and message: " . $e->getMessage(), E_USER_ERROR);
            return $e->getCode();
        }
    }

    protected function _constConnInfo($path)
    {
        $aCI = $this->_objConnectionInfo;
        $aURLInfo = parse_url($this->_objClientConfig->getMESBURL() );

        return new HTTPConnInfo($aCI["protocol"], $aURLInfo["host"], $aCI["port"], $aCI["timeout"], $path, $aCI["method"], $aCI["contenttype"], $this->_objClientConfig->getUsername(), $this->_objClientConfig->getPassword() );
    }

    /**
     * Construct standard mPoint HTTP Headers for notifying the Client via HTTP.
     *
     * @return string
     */
    protected function _constHTTPHeaders($authUser=null, $authPass=null)
    {
        /* ----- Construct HTTP Header Start ----- */
        $h = "{METHOD} {PATH} HTTP/1.0" .HTTPClient::CRLF;
        $h .= "host: {HOST}" .HTTPClient::CRLF;
        $h .= "referer: {REFERER}" .HTTPClient::CRLF;
        $h .= "content-length: {CONTENTLENGTH}" .HTTPClient::CRLF;
        $h .= "content-type: {CONTENTTYPE}; charset=UTF-8" .HTTPClient::CRLF;
        $h .= "user-agent: mPoint" .HTTPClient::CRLF;
        if (isset($authUser) === true && isset($authPass) === true)
        {
            $h .= "Authorization: Basic ". base64_encode($authUser. ":". $authPass);
        }
        /* ----- Construct HTTP Header End ----- */

        return $h;
    }

    abstract public function sendRequest($_OBJ_DB);

    protected function _updateSettlementState($_OBJ_DB, $status){
        $sql = "UPDATE log" . sSCHEMA_POSTFIX . ".settlement_tbl
                SET status = '$status' 
                WHERE id = $this->_iSettlementId";
        $_OBJ_DB->query($sql);
    }

    protected function _insertSettlementRecords($_OBJ_DB){
        $sql = "INSERT INTO log" . sSCHEMA_POSTFIX . ".settlement_record_tbl 
                   (settlementid, transactionid) 
                   VALUES ($this->_iSettlementId, ". $this->_arrayTransactionIds[0] .")";

        for ($index = 1 ; $index < count($this->_arrayTransactionIds); $index++)
        {
            $sql .= " , ( $this->_iSettlementId, ".$this->_arrayTransactionIds[$index].")";
        }
        $_OBJ_DB->query($sql);
    }

    protected function _updateDescription($_OBJ_DB, $replyBody){

        $sql = "UPDATE log" . sSCHEMA_POSTFIX . ".settlement_tbl
            SET description = '".$replyBody->settlement->file['description']."' 
            WHERE id = $this->_iSettlementId";


        $_OBJ_DB->query($sql);

        for ($i=0; $i<count($replyBody->settlement->transactions->transaction); $i++)
        {
           $sql = "UPDATE log" . sSCHEMA_POSTFIX . ".settlement_record_tbl
                SET description = '".$replyBody->settlement->transactions->transaction[$i]["description"]."' 
                WHERE settlementid = $this->_iSettlementId
                AND transactionid=" . $replyBody->settlement->transactions->transaction[$i]["id"];

            $_OBJ_DB->query($sql);
        }
    }

    public static function getInprogressSettlements($_OBJ_DB)
    {
        $sql = "SELECT psp_id, client_id
                FROM log" . sSCHEMA_POSTFIX . ".settlement_tbl
                WHERE status = '".Constants::sSETTLEMENT_REQUEST_WAITING."' 
                GROUP BY psp_id, client_id";

        $res = $_OBJ_DB->getName($sql);
        $settlementRecords = [];
        $index = 0;
        if (is_array($res) === true && count($res) > 0) {
            $settlementRecords[$index]["client"] = (int)$res["CLIENT_ID"];
            $settlementRecords[$index]["psp"] = (int)$res["PSP_ID"];
        }
        return $settlementRecords;
    }

    abstract protected function _parseConfirmationReport($_OBJ_DB, $response);

    public function getConfirmationReport($_OBJ_DB)
    {
        try
        {
            if ($this->_objClientConfig == NULL) {
                $this->_getClientConfiguration($_OBJ_DB);
            }

            if ($this->_objPspConfig == NULL) {
                $this->_getPSPConfiguration($_OBJ_DB);
            }
            $requestBody = '<?xml version="1.0" encoding="UTF-8"?><root><process-settlement>';
            $requestBody .= $this->_objClientConfig->toXML();
            $requestBody .= $this->_objPspConfig->toXML();
            $requestBody .= '</process-settlement></root>';

            $obj_ConnInfo = $this->_constConnInfo($this->_objConnectionInfo["paths"]["process-settlement"]);

            $obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
            $obj_HTTP->connect();
            $code = $obj_HTTP->send($this->_constHTTPHeaders($this->_objClientConfig->getUsername(), $this->_objClientConfig->getPassword()), $requestBody);
            $obj_HTTP->disConnect();
            if ($code != 200) {
                throw new mPointException("Settlement Confirmation Process Request Error code: " . $code . " and body: " . $obj_HTTP->getReplyBody(), $code);
            }
            else
            {
                $replyBody = $obj_HTTP->getReplyBody();
                if(trim($replyBody ) === "")
                {
                    throw new mPointException("Settlement Confirmation Process Request Error code: " . $code . " and body: " . $obj_HTTP->getReplyBody(), $code);
                }
                else
                {
                    $this->_parseConfirmationReport($_OBJ_DB, $replyBody);
                }
            }
        } 
		catch (Exception $e) 
		{
            trigger_error("Settlement Confirmation Process failed with code: " . $e->getCode() . " and message: " . $e->getMessage(), E_USER_ERROR);
            return $e->getCode();
        }
    }
}