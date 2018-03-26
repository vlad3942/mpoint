<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:Mpi.php
 */

class Mpi
{
    /**
     * Function to Create a MPI object
     *
     * @param 	Object    $_OBJ_TXT   	        Object of TranslateText
     * @param 	Object    $obj_TxnInfo   	    Object of Transaction info
     * @param   Object    $obj_Card             Object of Card
     * @param   Array     $aHTTP_CONN_INFO      Array of connection info
     * @param   Integer   $clientId             Client id
     * @param   Integer   $countryId            Country id
     * @param   Integer   $cardId               Card id
     *
     * @return  Object                      Object of PSPConfig class
     */
    public function GetMpi(RDB $objDb, $_OBJ_TXT, $obj_TxnInfo, $obj_Card, $aHTTP_CONN_INFO, $clientId, $countryId, $cardId){
        $obj = null;

        $pspConfig = $this->createPSPConfig($objDb, $clientId, $countryId, $cardId);
        switch ($pspConfig->getId()) {
            case Constants::iNETS_MPI:
                $obj = new NetsMpi($objDb,$_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO["netsmpi"],$pspConfig, $obj_Card);
                break;
            case Constants::iMODIRUM_MPI:
                $obj = new ModirumMPI($objDb, $_OBJ_TXT,  $obj_TxnInfo, $aHTTP_CONN_INFO["modirummpi"], $pspConfig, $obj_Card);
        }
        return $obj;
    }


    /**
     * Function to PSPConfig object from countryid, clientid and cardid
     *
     * @param   Integer   $clientId             Client id
     * @param   Integer   $countryId            Country id
     * @param   Integer   $cardId               Card id
     *
     * @return  Object                      Object of PSPConfig class
     */
    private function createPSPConfig(RDB  $objDb, $clientId, $countryId, $cardId){
        $sql = "SELECT DISTINCT PSP.id, PSP.name,
					MA.name AS ma, MA.username, MA.passwd AS password, MSA.name AS msa, CA.countryid
				FROM System".sSCHEMA_POSTFIX.".PSP_Tbl PSP
				INNER JOIN Client".sSCHEMA_POSTFIX.".MerchantAccount_Tbl MA ON PSP.id = MA.pspid AND MA.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".Client_Tbl CL ON MA.clientid = CL.id AND CL.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".Account_Tbl Acc ON CL.id = Acc.clientid AND Acc.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".MerchantSubAccount_Tbl MSA ON Acc.id = MSA.accountid AND PSP.id = MSA.pspid AND MSA.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".CardAccess_Tbl CA ON PSP.id = CA.pspid AND CL.id = CA.clientid AND CA.enabled = '1'  
				WHERE CL.id = ". intval($clientId) ." AND CA.cardid = ". intval($cardId) ."
					AND (CA.countryid = ". intval($countryId) ." OR CA.countryid IS NULL)
                    AND psp.system_type = 6
				ORDER BY CA.countryid ASC";
//		echo $sql ."\n";
        $RS = $objDb->getName($sql);

        if (is_array($RS) === true && count($RS) > 1) {	return new PSPConfig($RS["ID"], $RS["NAME"], "6", "", "", "","", array(),array()); }
        else { return null; }
    }
}