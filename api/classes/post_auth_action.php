<?php
/**
 * The PostAuth package would provide method to do required action for updating transaction statistics, 
 * gateway performnace monitrong etc after the Authorization done irrespective of succes or failure
 *
 * @author Deblina Das
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Postauth
 * @version 2.02
 */

/* ==================== PostAuthException Classes Start ==================== */
/**
 * Exception class for all Post auth exceptions
 */
class PostAuthException extends mPointException {
}
/* ==================== PostAuthException Classes End ==================== */

/**
 * Model Class containing all the Business Logic for handing action required post authorization
 */
class PostAuthAction {
	/**
	 * Data object with the Transaction Information
	 *
	 * @var TxnInfo
	 */
	private $_obj_TxnInfo;
	
	/**
	 * Data array with Connection Information for the specific PSP
	 *
	 * @var array
	 */
	protected $aCONN_INFO;
	
	/**
	 * Data object with PSP configuration Information
	 *
	 * @var PSPConfig
	 */
	private $_obj_PSPConfig;
	
	/**
	 * Default Constructor.
	 *
	 * @param RDB $oDB
	 *        	Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param TranslateText $oTxt
	 *        	Text Translation Object for translating any text into a specific language
	 * @param TxnInfo $oTI
	 *        	Data object with the Transaction Information
	 * @param PSPConfig $oPSPConfig
	 *        	Configuration object with the PSP Information
	 */
	public function __construct(RDB $oDB, TranslateText $oTxt, TxnInfo $oTI, array $aConnInfo, PSPConfig $oPSPConfig = null) {
		
		$this->_obj_TxnInfo = $oTI;
		$this->aCONN_INFO = $aConnInfo;
		
		$pspID = ( integer ) $oTI->getPSPID () > 0 ? $oTI->getPSPID () : $this->getPSPID ();
		if ($oPSPConfig == null) {
			$oPSPConfig = PSPConfig::produceConfig ( $oDB, $oTI->getClientConfig ()->getID (), $oTI->getClientConfig ()->getAccountConfig ()->getID (), $pspID );
		}
		$this->_obj_PSPConfig = $oPSPConfig;
	}
	
	/**
	 * Returns the Data object with the Transaction Information.
	 *
	 * @return TxnInfo
	 */
	public function getTxnInfo() {
		return $this->_obj_TxnInfo;
	}
	
	/**
	 * Returns the Configuration object with the PSP Information.
	 *
	 * @return PSPConfig
	 */
	public function getPSPConfig() {
		return $this->_obj_PSPConfig;
	}
	
	/**
	 * Inserts or updates transaction count for a PSP and merchant 
	 * 
	 * @param 	TxnInfo txnInfo
	 * @param 	PSPConfig obj_PSPConfig
	 * 
	 * @throws 	E_USER_ERROR, E_USER_NOTICE
	 */
	public function updateTxnVolume($txnInfo, $obj_PSPConfig, $oDB) {
		
		$clientId = $txnInfo->getClientConfig ()->getAccountConfig ()->getClientID ();
		$pspId = $obj_PSPConfig->getID ();
		
		$sql = "SELECT id
				FROM Client" . sSCHEMA_POSTFIX . ".gatewaystat_tbl
				WHERE clientid = " . intval ( $clientId ) . " AND gatewayid = " . intval ( $pspId ) . " AND statetypeid=1 AND enabled = '1'";
		
		// echo $sql ."\n";
		$RS = $oDB->getName ( $sql );
		
		if (is_array ( $RS ) === true && intval ( $RS ["ID"] ) > 0) {
			// Record exists, and counter to be increased
			$sql = "UPDATE client" . sSCHEMA_POSTFIX . ".gatewaystat_tbl gt SET statvalue = statvalue + 1 WHERE id = " . intval ( $RS ["ID"] );
			$res = $oDB->query ( $sql );
			
			if (is_resource ( $res ) === true) {
				trigger_error ( "Updated count for transaction: " . $txnInfo->getID (), E_USER_NOTICE );
			} else {
				trigger_error ( "Failed to update count for transaction: " . $txnInfo->getID (),  E_USER_ERROR  );
			}
		} else {
			// No record exists and create a new one
			$sql = "INSERT INTO client" . sSCHEMA_POSTFIX . ".gatewaystat_tbl( gatewayid, clientid, statetypeid, statvalue)" . " VALUES (" . $pspId . ", " . $clientId . ",1,1 )";
			// echo $sql ;
			$res = $oDB->query ( $sql );
			if (is_resource ( $res ) === true) {
				trigger_error ( "Inserted count for transaction: " . $txnInfo->getID (), E_USER_NOTICE );
			} else {
				trigger_error ( "Failed to insert count for transaction: " . $txnInfo->getID (), E_USER_ERROR );
			}
		}
		
		
	}
}
?>