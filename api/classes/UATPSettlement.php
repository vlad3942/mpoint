<?php
/**
 * Created by IntelliJ IDEA.
 * User: Rohit M
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:UATPSettlement.php
 */

class UATPSettlement extends mPointSettlement
{
    public function __construct($_OBJ_TXT, $clientId, $pspId, $connecctionInfo)
    {
        parent::__construct($_OBJ_TXT, $clientId, Constants::iUATP_CARD_ACCOUNT, $connecctionInfo["uatp"]);
    }

    public function capture($_OBJ_DB)
    {
        return;
    }

    public function refund($_OBJ_DB)
    {
        return;
    }

    protected function _createSettlementRecord($_OBJ_DB)
    {
        $this->_iRecordNumber = 0;
        $this->_sRecordType = "CAPTURE";
        $objCC = ClientConfig::produceConfig($_OBJ_DB, $this->_iClientId);

        $sql = "INSERT INTO log" . sSCHEMA_POSTFIX . ".settlement_tbl
                    (record_number, file_reference_number, file_sequence_number, client_id, record_type, psp_id, status)
                    SELECT $1, $2, $3, $4, $5, $6, $7
                    WHERE NOT EXISTS (
                    SELECT 1 FROM log" . sSCHEMA_POSTFIX . ".settlement_tbl 
                    WHERE file_reference_number= $8 AND file_sequence_number = $9 )
                    RETURNING id, created";
        $resource = $_OBJ_DB->prepare($sql);

        if (is_resource($resource) === true) {
            $aParam = array(
                $this->_iRecordNumber,
                $objCC->getAdditionalProperties(Constants::iInternalProperty, 'UATP_SETTLEMENT_FILE_NAME'),
                intval(date("Ymd") ),
                $this->_iClientId,
                $this->_sRecordType,
                $this->_iPspId,
                Constants::sSETTLEMENT_REQUEST_WAITING,
                $objCC->getAdditionalProperties(Constants::iInternalProperty, 'UATP_SETTLEMENT_FILE_NAME'),
                intval(date("Ymd") )
                );

            $result = $_OBJ_DB->execute($resource, $aParam);

            if ($result === false) {
                throw new Exception("Unable to create settlement record", E_USER_ERROR);
            } else {
                $RS = $_OBJ_DB->fetchName($result);
                $this->_iSettlementId = $RS["ID"];
                $this->_sFileCreatedDate = $RS["CREATED"];
                $this->_sFileReferenceNumber = $objCC->getAdditionalProperties(Constants::iInternalProperty, 'UATP_SETTLEMENT_FILE_NAME');
                $this->_iFileSequenceNumber = intval(date("Ymd") );
                $this->_iRecordNumber = $this->_iRecordNumber;
            }
        }

    }


    public function sendRequest($_OBJ_DB)
    {
       return;
    }

    public function _parseConfirmationReport($_OBJ_DB, $response)
    {
        try
        {
            $xmlResponse =  simpledom_load_string($response);
            $sStatus = Constants::sSETTLEMENT_REQUEST_WAITING;
            if(count($xmlResponse->status->Status) > 0)
            {
                if(trim($xmlResponse->status->Status) == "ERROR")
                {
                    $sStatus = Constants::sSETTLEMENT_REQUEST_WAITING;
                }
                elseif (trim($xmlResponse->status->Status) == "OK")
                {
                    $sStatus = trim($xmlResponse->status->Status);
                }

                $sFileName = $this->_objClientConfig->getAdditionalProperties(Constants::iInternalProperty, 'UATP_SETTLEMENT_FILE_NAME');
                $sql ="UPDATE log" . sSCHEMA_POSTFIX . ".settlement_tbl 
                                SET status = $1 
                                WHERE file_reference_number = $2 AND file_sequence_number = $3;";

                $resource = $_OBJ_DB->prepare($sql);
                if (is_resource($resource) === true) {
                    $aParam = array(
                        (string)$sStatus,
                        $sFileName,
                        intval(date("Ymd") )
                    );

                    $result = $_OBJ_DB->execute($resource, $aParam);
                }
            }
            else
            {

            }

        }
        catch (Exception $e)
        {
            throw new Exception("Failed to Capture the settlement file status ", E_USER_ERROR);
        }
    }

    public function createBulkSettlementEntry($_OBJ_DB)
    {
        $this->_createSettlementRecord($_OBJ_DB);
    }

    public static function getInprogressSettlements($_OBJ_DB)
    {
        $sql = "SELECT psp_id, client_id
                FROM log" . sSCHEMA_POSTFIX . ".settlement_tbl
                WHERE LOWER(status) IN ('".Constants::sSETTLEMENT_REQUEST_WAITING."','".Constants::sSETTLEMENT_REQUEST_ERROR."') 
                AND file_sequence_number = ".intval(date("Ymd") )."
                GROUP BY psp_id, client_id";

        $aRS = $_OBJ_DB->getAllNames($sql);

        $settlementRecords = [];
        $index = 0;
        if (is_array($aRS) === true && count($aRS) > 0)
        {
            foreach ($aRS as $res)
            {
                $settlementRecords[$index]["client"] = (int)$res["CLIENT_ID"];
                $settlementRecords[$index]["psp"] = (int)$res["PSP_ID"];
                $index++;
            }

        }
        return $settlementRecords;
    }

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

            $requestBody = '<?xml version="1.0" encoding="UTF-8"?><root><bulk-settlement client-id="'.$this->_objClientConfig->getID().'">';
            $requestBody .= $this->_objClientConfig->toXML(Constants::iPrivateProperty);
            $requestBody .= $this->_objPspConfig->toXML(Constants::iPrivateProperty);
            $requestBody .= '</bulk-settlement></root>';

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