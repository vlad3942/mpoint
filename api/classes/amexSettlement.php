<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:AmexSettlement.php
 */

class AmexSettlement extends mPointSettlement
{
    public function __construct($_OBJ_TXT, $clientId, $pspId, $connecctionInfo)
    {
        parent::__construct($_OBJ_TXT, $clientId, Constants::iAMEX_ACQUIRER, $connecctionInfo["amex"]);
    }

    protected function _createSettlementRecord($_OBJ_DB)
    {

        $sql = "SELECT file_reference_number, file_sequence_number
                FROM log" . sSCHEMA_POSTFIX . ".settlement_tbl
                WHERE client_id = $this->_iClientId 
                AND psp_id = '$this->_iPspId'                
                AND status <> '" . Constants::sSETTLEMENT_REQUEST_FAIL . "'                
                ORDER BY id DESC LIMIT 1 ";

        $res = $_OBJ_DB->getName($sql);
        $referenceNumber  = 0;
        $this->_iRecordNumber = 0;
        if (is_array($res) === true && count($res) > 0) {
            $referenceNumber  = (int)$res["FILE_REFERENCE_NUMBER"];
            $this->_iRecordNumber  = (int)$res["FILE_SEQUENCE_NUMBER"];
        }
        $referenceNumber = $referenceNumber+1;
        $this->_iRecordNumber = $this->_iRecordNumber + 1;

        $sql = "INSERT INTO log" . sSCHEMA_POSTFIX . ".settlement_tbl
                    (record_number, file_reference_number, file_sequence_number, client_id, record_type, psp_id)
                    values ($1, $2, $3, $4, $5, $6) RETURNING id, created";

        $resource = $_OBJ_DB->prepare($sql);

        if (is_resource($resource) === true) {
            $aParam = array(
                $this->_iRecordNumber,
                $referenceNumber,
                $this->_iRecordNumber,
                $this->_iClientId,
                $this->_sRecordType,
                $this->_iPspId
            );

            $result = $_OBJ_DB->execute($resource, $aParam);

            if ($result === false) {
                throw new Exception("Unable to create settlement record", E_USER_ERROR);
            } else {
                $RS = $_OBJ_DB->fetchName($result);
                $this->_iSettlementId = $RS["ID"];
                $this->_sFileCreatedDate = $RS["CREATED"];
                $this->_sFileReferenceNumber = $referenceNumber;
                $this->_iFileSequenceNumber = $this->_iRecordNumber;
                $this->_iRecordNumber = $this->_iRecordNumber;
            }
        }
    }


    public function sendRequest($_OBJ_DB)
    {
        if($this->_iTotalTransactionAmount > 0  )
        {
            $this->_createSettlementRecord($_OBJ_DB);
            $this->_send($_OBJ_DB);
        }
    }

    public function _parseConfirmationReport($_OBJ_DB, $response)
    {
        try
        {
            $xmlResponse =  simpledom_load_string($response);
            $files = [];
            for ($index = 0; $index < count($xmlResponse->{'settlement-report'}->file); $index++)
            {
                $xmlFile = $xmlResponse->{'settlement-report'}->file[$index];
                $files[$index] = [];
                $files[$index]["reference-number"]=trim($xmlFile["reference-number"]);
                $files[$index]["status"]= strtolower($xmlFile["status"]);
                $files[$index]["created-date"]=$xmlFile["created-date"];
                $files[$index]["receipt-date"]=$xmlFile["receipt-date"];
                $files[$index]["tracking-number"]=$xmlFile["tracking-number"];

                $records = [];

                for ($secondIndex=0; $secondIndex < count($xmlFile->record) ; $secondIndex++)
                {
                    $xmlRecord = $xmlFile->record[$secondIndex];
                    $recordId = (string)$xmlRecord["id"];
                    if(array_key_exists($recordId, $records ) === false)
                    {
                        $records[$recordId]["error"] = [];
                        $records[$recordId]["warning"] = [];
                    }
                    if(isset($xmlRecord->error))
                    {
                        array_push($records[$recordId]["error"], (string)$xmlRecord->error["id"]);
                    }
                    if(isset($xmlRecord->warning))
                    {
                        array_push($records[$recordId]["warning"], (string)$xmlRecord->warning["id"]);
                    }
                }
                $files[$index]["records"] = $records ;
            }

            foreach ($files as $file)
            {
                $sql = "SELECT id, record_type
                        FROM log" . sSCHEMA_POSTFIX . ".settlement_tbl
                        WHERE client_id = $this->_iClientId 
                        AND psp_id = '$this->_iPspId'                
                        AND file_reference_number = '".$file["reference-number"]."' 
                        AND status= '" . Constants::sSETTLEMENT_REQUEST_WAITING . "' 
                        ORDER BY id DESC LIMIT 1 ";

                $res = $_OBJ_DB->getName($sql);

                if (is_array($res) === true && count($res) > 0)
                {
                    $fileId = $res["ID"];
                    $recordType= $res["RECORD_TYPE"];

                    $sql ="UPDATE log" . sSCHEMA_POSTFIX . ".settlement_tbl 
                            SET record_tracking_number = $1, status = $2 
                            WHERE id = $3;";


                    $resource = $_OBJ_DB->prepare($sql);

                    if (is_resource($resource) === true) {
                        $aParam = array(
                            $file["tracking-number"],
                            $file["status"],
                            $fileId
                        );

                        $result = $_OBJ_DB->execute($resource, $aParam);

                        if ($result === false) {
                            throw new Exception("Unable to create settlement record", E_USER_ERROR);
                        }
                        else
                        {
                            $sql = "SELECT *
                                  FROM log" . sSCHEMA_POSTFIX . ".settlement_record_tbl
                                  WHERE settlementid = $fileId";

                            $aRS = $_OBJ_DB->getAllNames($sql);
                            if (is_array($aRS) === true && count($aRS) > 0)
                            {
                                foreach ($aRS as $rs)
                                {
                                    $pId = $rs["ID"];
                                    $txnId = $rs["TRANSACTIONID"];
                                    $txnDescription = $rs["DESCRIPTION"];
                                    $txnDescription = explode(',', $txnDescription );
                                    $isSuccess = true;
                                    $finalDescription = "";
                                    $isDescriptionUpdated = false;
                                    foreach ($txnDescription as $description)
                                    {
                                        $finalDescription .= ",";
                                        if(trim($description) === "")
                                        {
                                            continue;
                                        }
                                        $description =explode('-', $description );
                                        $recordId = $description[1];
                                        $response = "";
                                        if(count($file["records"][$recordId]["warning"] ) > 0)
                                        {
                                            $isDescriptionUpdated = true;
                                            foreach ($file["records"][$recordId]["warning"] as $warning)
                                            {
                                                $response .= $warning . ":";
                                            }
                                        }

                                        if(count($file["records"][$recordId]["error"] ) > 0)
                                        {
                                            $isDescriptionUpdated = true;
                                            foreach ($file["records"][$recordId]["error"] as $error)
                                            {
                                                $response .= $error . ":";
                                                $isSuccess = false;
                                            }
                                        }
                                        foreach ($description as $desc)
                                        {
                                            $finalDescription .= $desc ."-";
                                        }
                                        $finalDescription .= $response;
                                    }

                                    if($isSuccess === true)
                                    {
                                        $obj_TxnInfo = TxnInfo::produceInfo($txnId, $_OBJ_DB);
                                        $obj_PSP = new Amex($_OBJ_DB, $this->_objTXT, $obj_TxnInfo, $this->_objConnectionInfo);
                                        $args = [];
                                        if($recordType == "CAPTURE")
                                        {
                                            $obj_PSP->completeCapture($obj_TxnInfo->getAmount(), $obj_TxnInfo->getFee());
                                            $args = array("transact" => $obj_TxnInfo->getExternalID(),
                                                    "amount" => $obj_TxnInfo->getAmount(),
                                                    "fee" => $obj_TxnInfo->getFee() );
                                            if (strlen($obj_TxnInfo->getCallbackURL() ) > 0)
                                            {
                                                $obj_PSP->notifyClient(Constants::iPAYMENT_CAPTURED_STATE, $args);
                                            }
                                        }
                                        else
                                        {
                                            $obj_PSP->newMessage($txnId, Constants::iPAYMENT_REFUNDED_STATE, null );
                                            $args = array("transact" => $obj_TxnInfo->getExternalID(),
                                                    "amount" => $obj_TxnInfo->getAmount());
                                            if (strlen($obj_TxnInfo->getCallbackURL() ) > 0)
                                            {
                                                $obj_PSP->notifyClient(Constants::iPAYMENT_REFUNDED_STATE, $args);
                                            }
                                        }

                                    }

                                    if($isDescriptionUpdated === true)
                                    {
                                        $sql = "UPDATE log.settlement_record_tbl 
                                            SET description = $1 
                                            WHERE id = $2;";

                                        $resource = $_OBJ_DB->prepare($sql);

                                        if (is_resource($resource) === true)
                                        {
                                            $aParam = array(
                                                $finalDescription,
                                                $pId
                                            );

                                            $result = $_OBJ_DB->execute($resource, $aParam);

                                            if ($result === false)
                                            {
                                                throw new Exception("Unable to update settlement record", E_USER_ERROR);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        catch (Exception $e)
        {
            throw new Exception("Failed to updated AMEX Confirmation report", E_USER_ERROR);
        }
    }
}