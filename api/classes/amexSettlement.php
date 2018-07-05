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
    public function __construct($clientId, $pspId, $connecctionInfo)
    {
        parent::__construct($clientId, Constants::iAMEX_ACQUIRER, $connecctionInfo["amex"]);
    }

    protected function createSettlementRecord($_OBJ_DB)
    {
        $recordNumber = $this->_recordNumber;
        $referenceNumber = $this->_recordType . "_" . $recordNumber;

        $sql = "INSERT INTO log" . sSCHEMA_POSTFIX . ".settlement_tbl
                    (record_number, file_reference_number, file_sequence_number, client_id, record_type, psp_id)
                    values ($1, $2, $3, $4, $5, $6) RETURNING id, created";

        $resource = $_OBJ_DB->prepare($sql);

        if (is_resource($resource) === true) {
            $aParam = array(
                $recordNumber,
                $referenceNumber,
                $recordNumber,
                $this->_clientId,
                $this->_recordType,
                $this->_pspId
            );

            $result = $_OBJ_DB->execute($resource, $aParam);

            if ($result === false) {
                throw new Exception("Unable to create settlement record", E_USER_ERROR);
            } else {
                $RS = $_OBJ_DB->fetchName($result);
                $this->_settlementId = $RS["ID"];
                $this->_fileCreatedDate = $RS["CREATED"];
                $this->_fileReferenceNumber = $referenceNumber;
                $this->_fileSequenceNumber = $recordNumber;
                $this->_recordNumber = $recordNumber;
            }
        }
    }


    public function sendRequest($_OBJ_DB)
    {
        if($this->_totalTransactionAmount > 0  )
        {
            $this->createSettlementRecord($_OBJ_DB);
            $this->send($_OBJ_DB);
        }
    }
}