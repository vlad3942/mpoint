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
    private $_pspId = NULL;

    protected $_clientId = NULL;

    private $_clientConfig = NULL;

    private $_pspConfig = NULL;

    private $_connectionInfo = NULL;

    protected $_settlementId = NULL;

    protected $_fileReferenceNumber = NULL;

    protected $_recordNumber = NULL;

    protected $_fileSequenceNumber = NULL;

    protected $_fileCreatedDate = NULL;

    protected $_recordType = NULL;

    private $_transactionXML = NULL;

    protected $_accountId = NULL;

    protected $_totalTransactionAmount = 0;

    protected $_transactionIds = [];

    public function __construct($clientId, $pspId, $connectionInfo)
    {
        $this->_clientId = $clientId;
        $this->_pspId = $pspId;
        $this->_connectionInfo = $connectionInfo;
    }

    private function getAccountIds($_OBJ_DB)
    {
        $sql = "SELECT account.id
                FROM client" . sSCHEMA_POSTFIX . ".account_tbl account
                  INNER JOIN client" . sSCHEMA_POSTFIX . ".merchantsubaccount_tbl submerchant ON submerchant.accountid = account.id
                WHERE account.clientid = $this->_clientId
                      AND submerchant.pspid = $this->_pspId
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

    private function getClientConfiguration($_OBJ_DB)
    {
        $this->_clientConfig = ClientConfig::produceConfig($_OBJ_DB, $this->_clientId);
    }

    private function getPSPConfiguration($_OBJ_DB)
    {
        $accountIds = $this->getAccountIds($_OBJ_DB);
        $this->_accountId =$accountIds[0];
        $this->_pspConfig = PSPConfig::produceConfig($_OBJ_DB, $this->_clientId, $this->_accountId, $this->_pspId);
    }

    private function getTransactions($_OBJ_DB, $stateIds){

        $sql = "SELECT record_number, status
                FROM log" . sSCHEMA_POSTFIX . ".settlement_tbl
                WHERE client_id = $this->_clientId AND record_type = '$this->_recordType'                
                ORDER BY id DESC LIMIT 1 ";

        $res = $_OBJ_DB->getName($sql);
        $recordNumber = 0;
        if (is_array($res) === true && count($res) > 0) {
            $recordNumber = (int)$res["RECORD_NUMBER"];
            if( strtolower( $res["STATUS"]) == "active")
                return;
        }

        $this->_recordNumber = $recordNumber + 1 ;

        $sql = "SELECT txn.id
                FROM log" . sSCHEMA_POSTFIX . ".transaction_tbl AS txn, log" . sSCHEMA_POSTFIX . ".message_tbl AS msg
                WHERE txn.clientid = $this->_clientId
                AND txn.pspid =  $this->_pspId
                AND txn.cardid NOTNULL
                AND msg.stateid IN ( $stateIds) 
                AND msg.txnid = txn.id ";

        $aRS = $_OBJ_DB->getAllNames($sql);
        if (is_array($aRS) === true && count($aRS) > 0) {

            $this->_transactionXML = "<transactions>";

            foreach ($aRS as $rs) {
                $transactionId = (int)$rs["ID"];
                array_push($this->_transactionIds,$transactionId);
                $obj_TxnInfo = TxnInfo::produceInfo($transactionId, $_OBJ_DB);
                $obj_TxnInfo->produceOrderConfig($_OBJ_DB);
                $this->_transactionXML .= $obj_TxnInfo->toXML();
                $this->_totalTransactionAmount += $obj_TxnInfo->getAmount();
            }

            $this->_transactionXML .= "</transactions>";
        }
    }

    public function capture($_OBJ_DB)
    {
        $this->_recordType = "CAPTURE";
        $this->getTransactions($_OBJ_DB, "2101");
    }

    public function cancel($_OBJ_DB)
    {
        $this->_recordType = "CANCEL";
        $this->getTransactions($_OBJ_DB, "2102,2001,2101");
    }

    public function refund($_OBJ_DB)
    {
        $this->_recordType = "REFUND";
        $this->getTransactions($_OBJ_DB, "2103");
    }

    abstract protected function createSettlementRecord($_OBJ_DB);

    protected function toSettlementInfoXML()
    {

        $xml = "<settlement-info>";
        $xml .= "<record-number>$this->_recordNumber</record-number>";
        $xml .= "<file-reference-number>$this->_fileReferenceNumber</file-reference-number>";
        $xml .= "<file-sequence-number>$this->_fileSequenceNumber</file-sequence-number>";
        $xml .= "<file-creation-date>" . date("Ymd", strtotime($this->_fileCreatedDate)) . "</file-creation-date>";
        $xml .= "<file-creation-time>" . date("His", strtotime($this->_fileCreatedDate)) . "</file-creation-time>";
        $xml .= "<record-type>$this->_recordType</record-type>";
        $xml .= "<total-amount>$this->_totalTransactionAmount</total-amount>";
        $xml .= "</settlement-info>";

        return $xml;
    }

    protected function send($_OBJ_DB)
    {
        try {

            $this->updateSettlementRecords($_OBJ_DB);

            if ($this->_clientConfig == NULL) {
                $this->getClientConfiguration($_OBJ_DB);
            }

            if ($this->_pspConfig == NULL) {
                $this->getPSPConfiguration($_OBJ_DB);
            }

            $requestBody = $this->_clientConfig->toXML();
            $requestBody .= $this->toSettlementInfoXML();
            $requestBody .= $this->_pspConfig->toXML();
            $requestBody .= $this->_transactionXML;
            
            $obj_ConnInfo = $this->_constConnInfo($this->_connectionInfo["paths"]["settlement"]);

            $obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
            $obj_HTTP->connect();
            $code = $obj_HTTP->send($this->constHTTPHeaders($this->_clientConfig->getUsername(), $this->_clientConfig->getPassword()), $requestBody);
            $obj_HTTP->disConnect();
            if ($code != 200) {
                throw new mPointException("Settlement Request Error code: " . $code . " and body: " . $obj_HTTP->getReplyBody(), $code);
            }
        } catch (Exception $e) {
            $this->updateSettlementState($_OBJ_DB, "fail");
            trigger_error("Settlement record no: " . $this->_settlementId . " failed with code: " . $e->getCode() . " and message: " . $e->getMessage(), E_USER_ERROR);
            return $e->getCode();
        }
    }

    protected function _constConnInfo($path)
    {
        $aCI = $this->_connectionInfo;
        $aURLInfo = parse_url($this->_clientConfig->getMESBURL() );

        return new HTTPConnInfo($aCI["protocol"], $aURLInfo["host"], $aCI["port"], $aCI["timeout"], $path, $aCI["method"], $aCI["contenttype"], $this->_clientConfig->getUsername(), $this->_clientConfig->getPassword() );
    }

    /**
     * Construct standard mPoint HTTP Headers for notifying the Client via HTTP.
     *
     * @return string
     */
    protected function constHTTPHeaders($authUser=null,$authPass=null)
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

    protected function updateSettlementState($_OBJ_DB, $status){
        $sql = "UPDATE log" . sSCHEMA_POSTFIX . ".settlement_tbl
                SET status = '$status' 
                WHERE id = $this->_settlementId";
        $_OBJ_DB->query($sql);
    }

    protected function updateSettlementRecords($_OBJ_DB){
        $sql = "INSERT INTO log" . sSCHEMA_POSTFIX . ".settlement_record_tbl 
                   (settlementid, transactionid) 
                   VALUES ($this->_settlementId, ". $this->_transactionIds[0] .")";

        for ($index = 1 ; $index < count($this->_transactionIds); $index++)
        {
            $sql .= " , ( $this->_settlementId, ".$this->_transactionIds[$index].")";
        }
        $_OBJ_DB->query($sql);
    }
}