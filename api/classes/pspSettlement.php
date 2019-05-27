<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:pspSettlement.php
 */

class PSPSettlement extends mPointSettlement
{

    public function sendRequest($_OBJ_DB)
    {
        if ($this->_iTotalTransactionAmount > 0) {
            $this->_createSettlementRecord($_OBJ_DB);
            $this->_send($_OBJ_DB);
        }
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
        $referenceNumber = 0;
        $this->_iRecordNumber = 0;
        if (is_array($res) === true && count($res) > 0) {
            $referenceNumber = (int)$res["FILE_REFERENCE_NUMBER"];
            $this->_iRecordNumber = (int)$res["FILE_SEQUENCE_NUMBER"];
        }
        ++$referenceNumber;
        ++$this->_iRecordNumber;

        $sql = 'INSERT INTO log' . sSCHEMA_POSTFIX . '.settlement_tbl
                    (record_number, file_reference_number, file_sequence_number, client_id, record_type, psp_id, status)
                    values ($1, $2, $3, $4, $5, $6, $7) RETURNING id, created';

        $resource = $_OBJ_DB->prepare($sql);

        if (is_resource($resource) === true) {
            $aParam = array(
                $this->_iRecordNumber,
                $referenceNumber,
                $this->_iRecordNumber,
                $this->_iClientId,
                $this->_sRecordType,
                $this->_iPspId,
                Constants::sSETTLEMENT_REQUEST_WAITING
            );

            $result = $_OBJ_DB->execute($resource, $aParam);

            if ($result === false) {
                throw new Exception('Unable to create settlement record', E_USER_ERROR);
            } else {
                $RS = $_OBJ_DB->fetchName($result);
                $this->_iSettlementId = $RS['ID'];
                $this->_sFileCreatedDate = $RS['CREATED'];
                $this->_sFileReferenceNumber = $referenceNumber;
                $this->_iFileSequenceNumber = $this->_iRecordNumber;
                $this->_iRecordNumber = $this->_iRecordNumber;
            }
        }
    }

    public function _send($_OBJ_DB)
    {
        $this->_insertSettlementRecords($_OBJ_DB);
    }

    public function _parseConfirmationReport($_OBJ_DB, $response = null)
    {
        try {
            $sql = "SELECT id, record_type
                        FROM log" . sSCHEMA_POSTFIX . ".settlement_tbl
                        WHERE client_id = $this->_iClientId 
                        AND psp_id = '$this->_iPspId'                             
                        AND status= '" . Constants::sSETTLEMENT_REQUEST_WAITING . "' 
                        ORDER BY id DESC LIMIT 1 ";

            $res = $_OBJ_DB->getName($sql);

            if (is_array($res) === true && count($res) > 0) {
                $fileId = $res["ID"];
                $recordType = $res["RECORD_TYPE"];

                $totalTxnCount = -1;
                $totalSuccessfulTxnCount = 0;


                $sql = "SELECT *
                          FROM log" . sSCHEMA_POSTFIX . ".settlement_record_tbl
                          WHERE settlementid = $fileId";

                $aRS = $_OBJ_DB->getAllNames($sql);
                $totalTxnCount = count($aRS);
                if (is_array($aRS) === true && $totalTxnCount > 0) {
                    foreach ($aRS as $rs) {
                        $txnId = $rs['TRANSACTIONID'];

                        $obj_TxnInfo = TxnInfo::produceInfo($txnId, $_OBJ_DB);
                        $obj_PSP = Callback::producePSP($_OBJ_DB, $this->_objTXT, $obj_TxnInfo, $this->_objConnectionInfo);
                        if ($recordType === "CAPTURE") {
                            $messageData = $obj_TxnInfo->getMessageData($_OBJ_DB, [Constants::iPAYMENT_CAPTURE_INITIATED_STATE]);
                            $captureAmount = (int)$messageData[0]['data'];
                            if($captureAmount === 0)
                                $captureAmount=null;
                            $iStatusCode = $obj_PSP->capture($captureAmount);
                            if ($iStatusCode === 1000 && $obj_TxnInfo->hasEitherState($_OBJ_DB, Constants::iPAYMENT_CAPTURED_STATE) === false) {
                                $totalSuccessfulTxnCount++;
                                $args = array("transact" => $obj_TxnInfo->getExternalID(),
                                    "amount" => $obj_TxnInfo->getAmount(),
                                    "fee" => $obj_TxnInfo->getFee());
                                if ($obj_TxnInfo->getCallbackURL() !== '') {
                                    $obj_PSP->notifyClient(Constants::iPAYMENT_CAPTURED_STATE, $args);
                                }
                            } elseif ($iStatusCode === 1100) {
                                $totalSuccessfulTxnCount++;
                            }
                        }
                    }
                }

                $fileStatus = Constants::sSETTLEMENT_REQUEST_FAIL;
                if ($totalTxnCount === $totalSuccessfulTxnCount) {
                    $fileStatus = 'accepted';
                }

                $sql = 'UPDATE log' . sSCHEMA_POSTFIX . '.settlement_tbl 
                            SET  status = $1 
                            WHERE id = $2;';

                $resource = $_OBJ_DB->prepare($sql);

                if (is_resource($resource) === true) {
                    $aParam = array(
                        $fileStatus,
                        $fileId
                    );

                    $result = $_OBJ_DB->execute($resource, $aParam);
                }
            }
        } catch (Exception $e) {
            throw new Exception('Failed to updated Confirmation report', E_USER_ERROR);
        }
    }

    public function getConfirmationReport($_OBJ_DB)
    {
        $this->_parseConfirmationReport($_OBJ_DB, null);
    }
}