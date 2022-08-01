<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Badave
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:ChaseSettlement.php
 */

class ChaseSettlement extends mPointSettlement
{
    public function __construct($_OBJ_TXT, $clientId, $pspId, $connecctionInfo)
    {
        parent::__construct($_OBJ_TXT, $clientId, Constants::iCHASE_ACQUIRER, $connecctionInfo["chase"]);
    }

    protected function _createSettlementRecord($_OBJ_DB)
    {

        $sql = "SELECT file_reference_number, file_sequence_number
                FROM log" . sSCHEMA_POSTFIX . ".settlement_tbl
                WHERE client_id = $this->_iClientId
                AND psp_id = '$this->_iPspId'
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

        $aParam = array(
            $this->_iRecordNumber,
            $referenceNumber,
            $this->_iRecordNumber,
            $this->_iClientId,
            $this->_sRecordType,
            $this->_iPspId
        );
    
        $resource = $_OBJ_DB->executeQuery($sql, $aParam);

        if ($resource === false) {
            throw new Exception("Unable to create a record", E_USER_ERROR);
        }

        $result = $_OBJ_DB->fetchName($resource);

        if(!isset($result["ID"])) {
            throw new Exception("Unable to create a record", E_USER_ERROR);
        }

        $this->_iSettlementId = $result["ID"];
        $this->_sFileCreatedDate = $result["CREATED"];
        $this->_sFileReferenceNumber = $referenceNumber;
        $this->_iFileSequenceNumber = $this->_iRecordNumber;
        $this->_iRecordNumber = $this->_iRecordNumber;

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
            for ($index = 0, $indexMax = count($xmlResponse->{'settlement-report'}->file); $index < $indexMax; $index++)
            {
                $xmlFile = $xmlResponse->{'settlement-report'}->file[$index];
                $files[$index] = [];
                $files[$index]["reference-number"]=trim($xmlFile["reference-number"]);
                $files[$index]["status"]= strtolower($xmlFile["status"]);
                $files[$index]["created-date"]=$xmlFile["created-date"];
                $files[$index]["receipt-date"]=$xmlFile["receipt-date"];
                $files[$index]["tracking-number"]=$xmlFile["tracking-number"];

                $records = [];

                for ($secondIndex=0, $secondIndexMax = count($xmlFile->record); $secondIndex < $secondIndexMax; $secondIndex++)
                {
                    $xmlRecord = $xmlFile->record[$secondIndex];
                    $recordId = (string)$xmlRecord["id"];
                    $ticketNumbers = [];
                    if(array_key_exists($recordId, $records ) === false)
                    {
                        $records[$recordId]["error"] = "";
                        $records[$recordId]["warning"] = "";
                        $records[$recordId]["success"] = "";
                    }

                    if(isset($xmlRecord->error))
                    {
                        $records[$recordId]["error"]= (string)$xmlRecord->error["id"].":".(string)$xmlRecord->error;
                    }
                    if(isset($xmlRecord->warning))
                    {
                        $records[$recordId]["warning"]= (string)$xmlRecord->warning["id"].":".(string)$xmlRecord->warning;
                    }
                    if(isset($xmlRecord->success))
                    {
                        $records[$recordId]["success"]= (string)$xmlRecord->success["id"].":".(string)$xmlRecord->success;
                    }
                    $ticketNumberIndexMax = count($xmlRecord->{'booking-ref'});
                    $totalSettlementAmount = 0;
                    if($ticketNumberIndexMax > 1)
                    {
                        for ($ticketNumberIndex = 0; $ticketNumberIndex < $ticketNumberIndexMax; $ticketNumberIndex++)
                        {
                            $ticketNumbers[(string)$xmlRecord->{'booking-ref'}[$ticketNumberIndex]['id']]['status'] = (string)$xmlRecord->{'booking-ref'}[$ticketNumberIndex]['status'];
                            $ticketNumbers[(string)$xmlRecord->{'booking-ref'}[$ticketNumberIndex]['id']]['amount'] = (int)$xmlRecord->{'booking-ref'}[$ticketNumberIndex]['amount'];
                            $totalSettlementAmount +=  (int)$xmlRecord->{'booking-ref'}[$ticketNumberIndex]['amount'];
                        }
                    }
                    else
                    {
                        $ticketNumbers[(string)$xmlRecord->{'booking-ref'}['id']]['status'] = (string)$xmlRecord->{'booking-ref'}['status'];
                        $ticketNumbers[(string)$xmlRecord->{'booking-ref'}['id']]['amount'] = (int)$xmlRecord->{'booking-ref'}['amount'];
                        $totalSettlementAmount =  (int)$xmlRecord->{'booking-ref'}['amount'];

                    }
                    $records[$recordId]['ticketnumbers'] = $ticketNumbers;
                    $records[$recordId]['totalsettlementamount'] = $totalSettlementAmount;
                }

                if(count($records)>0)
                {

                    $files[$index]["records"] = $records;
                }
                else
                {
                    $files[$index]["desc"] = $xmlFile;
                }
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

                $bErrorEncountered  = false;
                if (is_array($res) === true && count($res) > 0)
                {
                    $fileId = $res["ID"];
                    $recordType= $res["RECORD_TYPE"];

                    if (isset($file["records"]))
                    {
                        $sql = "SELECT *
                          FROM log" . sSCHEMA_POSTFIX . ".settlement_record_tbl
                          WHERE settlementid = $fileId";

                        $aRS = $_OBJ_DB->getAllNames($sql);

                        if (is_array($aRS) === true && count($aRS) > 0)
                        {
                            foreach ($aRS as $rs) {
                                try
                                {

                                $pId = $rs["ID"];
                                $txnId = $rs["TRANSACTIONID"];
                                $description = $rs["DESCRIPTION"];
                                $isSuccess = true;
                                $finalDescription = "";
                                $isDescriptionUpdated = false;

                                $recordId = $description;

                                $response = "";
                                if (strlen($file["records"][$recordId]["warning"]) > 0)
                                {
                                    $isDescriptionUpdated = true;
                                    $response .= $file["records"][$recordId]["warning"] . ":";
                                }

                                if (strlen($file["records"][$recordId]["success"]) > 0)
                                {
                                    $isDescriptionUpdated = true;
                                    $response .= $file["records"][$recordId]["success"] . ":";

                                }


                                $finalDescription .= $response . "," . $rs["DESCRIPTION"];
                                $obj_TxnInfo = TxnInfo::produceInfo($txnId, $_OBJ_DB);
                                $txnPassbookObj = TxnPassbook::Get($_OBJ_DB, $txnId, $this->_iClientId);
                                $amount = $file['records'][$recordId]['totalsettlementamount'];
                                if ($txnPassbookObj instanceof TxnPassbook) {
                                    $passbookState = 0;
                                    $passbookStatus = '';
                                    if ($recordType == "CAPTURE") {
                                        $passbookState = Constants::iPAYMENT_CAPTURED_STATE;
                                        //$amount = $obj_TxnInfo->getFinalSettlementAmount($_OBJ_DB,array(Constants::iPAYMENT_CAPTURE_INITIATED_STATE));
                                    } else {
                                        $passbookState = Constants::iPAYMENT_REFUNDED_STATE;
                                        //$amount=$obj_TxnInfo->getFinalSettlementAmount($_OBJ_DB,array(Constants::iPAYMENT_REFUND_INITIATED_STATE));
                                    }
                                    if ($isSuccess === TRUE) {
                                        $passbookStatus = Constants::sPassbookStatusDone;
                                    } else {
                                        $passbookStatus = Constants::sPassbookStatusError;
                                    }
                                    /*In case of Capture Success completeCapture will take care of updateing passbook entry to done*/
                                    if ($passbookState !== 0 && !($isSuccess === TRUE && $recordType == "CAPTURE")) {
                                        $txnPassbookObj->updateInProgressOperations($amount, $passbookState, $passbookStatus);
                                    }
                                }

                                if ($isSuccess === true)
                                {
                                    $obj_PSP = new Chase($_OBJ_DB, $this->_objTXT, $obj_TxnInfo, $this->_objConnectionInfo);
                                    $isTicketLevelSettlement = $this->_objPspConfig->getAdditionalProperties(Constants::iInternalProperty,'IS_TICKET_LEVEL_SETTLEMENT');
                                    $args = [];
                                    $stateId = 0;
                                    if ($recordType == "CAPTURE") {
                                        $obj_PSP->completeCapture($amount, $obj_TxnInfo->getFee());
                                        $args = array("transact" => $obj_TxnInfo->getExternalID(),
                                            "amount" => $amount,
                                            "fee" => $obj_TxnInfo->getFee()
                                        );

                                        $stateId=Constants::iPAYMENT_CAPTURED_STATE;

                                    }
                                    else
                                    {
                                        $obj_PSP->newMessage($txnId, Constants::iPAYMENT_REFUNDED_STATE, null);
                                        $args = array("transact" => $obj_TxnInfo->getExternalID(),
                                            "amount" => $amount
                                            );
                                        $stateId = Constants::iPAYMENT_REFUNDED_STATE;
                                        $obj_TxnInfo->updateRefundedAmount($_OBJ_DB, $amount);
                                    }
                                    if($isTicketLevelSettlement === 'true')
                                    {
                                        $aTicketNumbers = [];
                                        foreach ($file['records'][$recordId]['ticketnumbers'] as $ticketnumber => $value)
                                        {
                                            array_push($aTicketNumbers, $ticketnumber . ':' .$value['amount']);
                                        }
                                        //$aTicketNumbers = array_keys($file['records'][$recordId]['ticketnumbers']);
                                        if(count($aTicketNumbers) > 0) {
                                            $args['additionaldata'] = 'tickernumbers=' . implode(',', $aTicketNumbers);
                                        }
                                    }

                                    $obj_PSP->notifyClient($stateId, $args,$this->_objClientConfig->getSurePayConfig($_OBJ_DB));

                                if ($isDescriptionUpdated === true) {

                                    $sql = "UPDATE log.settlement_record_tbl
                                    SET description = $1
                                    WHERE id = $2;";

                                    $aParam = array(
                                        $finalDescription,
                                        $pId
                                    );

                                    $resource = $_OBJ_DB->executeQuery($sql, $aParam);

                                    if ($resource === false) {
                                        throw new Exception("Unable to create a record", E_USER_ERROR);
                                    }

                                }

                                }
                            }
                           catch (Exception $e)
                           {
                               $bErrorEncountered = true;
                               trigger_error("Failed to updated record for SettlementId:".$fileId." With Error Code: ". $e->getCode(). " and message: ". $e->getMessage(), E_USER_ERROR);

                           }
                        }
                        }
                    }
                    else
                    {
                        $sql ="UPDATE log" . sSCHEMA_POSTFIX . ".settlement_tbl
                          SET description = CONCAT(description,',','".$file["desc"]."')  WHERE id = $1;";

                        $aParam = array(
                            $fileId
                        );
                        $resource = $_OBJ_DB->executeQuery($sql, $aParam);

                        if ($resource === false) {
                            throw new Exception("Unable to create a record", E_USER_ERROR);
                        }

                    }

                    $sql ="UPDATE log" . sSCHEMA_POSTFIX . ".settlement_tbl
                            SET record_tracking_number = $1, status = $2
                            WHERE id = $3;";

                    $status = $file["status"];
                    if($bErrorEncountered === true)
                    {
                        $status = $file["status"].' with error';
                    }
                    $aParam = array(
                        $file["tracking-number"],
                        $status,
                        $fileId
                    );

                    $resource = $_OBJ_DB->executeQuery($sql, $aParam);

                    if ($resource === false) {
                        throw new Exception("Unable to create a record", E_USER_ERROR);
                    }
        
                }
            }
        }
        catch (Exception $e)
        {
           throw new Exception("Failed to updated Chase Confirmation report With Error Code: ". $e->getCode(). " and message: ". $e->getMessage(), E_USER_ERROR);
        }
    }
}