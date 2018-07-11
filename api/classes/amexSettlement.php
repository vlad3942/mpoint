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

    protected function _createSettlementRecord($_OBJ_DB)
    {
        $recordNumber = $this->_iRecordNumber;
        $referenceNumber = $this->_sRecordType . "_" . $recordNumber;

        $sql = "INSERT INTO log" . sSCHEMA_POSTFIX . ".settlement_tbl
                    (record_number, file_reference_number, file_sequence_number, client_id, record_type, psp_id)
                    values ($1, $2, $3, $4, $5, $6) RETURNING id, created";

        $resource = $_OBJ_DB->prepare($sql);

        if (is_resource($resource) === true) {
            $aParam = array(
                $recordNumber,
                $referenceNumber,
                $recordNumber,
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
                $this->_iFileSequenceNumber = $recordNumber;
                $this->_iRecordNumber = $recordNumber;
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
}