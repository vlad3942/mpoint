<?php
/**
 * The Capture Package provide methods for capturing a previously authorized amount through mPoint's API.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package API
 * @subpackage Refund
 * @version 1.00
 */

/* ==================== Capture Exception Classes Start ==================== */
/**
 * Exception class for all Capture exceptions
 */
class RefundException extends mPointException { }
/* ==================== Capture Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for handling the Callback request from the Payment Service Provider (PSP).
 * The class contains methods that completes the transaction log with information received from the PSP, notifies the Client
 * and sends out an SMS Receipt to the Customer.
 *
 */
class Refund extends General
{
	/**
	 * Data object with the Transaction Information
	 *
	 * @var TxnInfo
	 */
	private $_obj_TxnInfo;
	/**
	 * Model object for the PSP to which the capture operation should be executed
	 *
	 * @var PSP
	 */
	private $_obj_PSP;
	/**
	 * The PSP's unique ID for the transaction which should be Refunded
	 *
	 * @var string
	 */
	private $_sPSPID;
	/**
	 * Information on how to connect to the Payment Service Provider
	 *
	 * @var HTTPConnInfo
	 */
	private $_obj_ConnInfo;

	/**
	 * Default Constructor.
	 *
	 * @param	RDB $oDB				Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param	TranslateText $oTxt 	Text Translation Object for translating any text into a specific language
	 * @param 	TxnInfo $oTI 			Data object with the Transaction Information
	 * @param 	Object $oPSP 			Model for the PSP to which the capture operation should be executed
	 * @param 	String $pspid 			Unique ID for the Payment Service Provider (PSP) mPoint should use to capture the transaction amount
	 * @param	HTTPConnInfo $oCI		Information on how to connect to PSP defaults to null
	 */
	public function __construct(RDB &$oDB, TranslateText &$oTxt, TxnInfo &$oTI, &$oPSP, $pspid, HTTPConnInfo &$oCI=null)
	{
		parent::__construct($oDB, $oTxt, $oTI->getClientConfig() );

		$this->_obj_TxnInfo = $oTI;
		$this->_obj_PSP = $oPSP;
		$this->_sPSPID = $pspid;
		$this->_obj_ConnInfo = $oCI;		
	}

	/**
	 * Returns the Data object with the Transaction Information.
	 *
	 * @return TxnInfo
	 */
	public function &getTxnInfo() { return $this->_obj_TxnInfo; }
	/**
	 * Returns the Model for the PSP to which the capture operation should be executed
	 *
	 * @return object
	 */
	public function &getPSP() { return $this->_obj_PSP; }
	/**
	 * Returns the PSP's unique ID for the transaction which should be captured
	 *
	 * @return string
	 */
	public function &getPSPID() { return $this->_sPSPID; }
	/**
	 * Returns the connection information specifying how to communicate with the Payment Service Provider
	 *
	 * @return HTTPConnInfo
	 */
	public function &getConnInfo() { return $this->_obj_ConnInfo; }
	
	/**
	 * Produces a new instance of a Refund operation object
	 *
	 * @param 	RDB $oDB 				Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param 	TranslateText $oTxt 	Text Translation Object for translating any text into a specific language
	 * @param 	TxnInfo $oTI 			Data object with the Transaction Information
	 * @param	HTTPConnInfo $oCI		Information on how to connect to PSP defaults to null
	 * @return 	Refund
	 */
	public static function produce(RDB &$oDB, TranslateText &$oTxt, TxnInfo &$oTI, HTTPConnInfo &$oCI=null)
	{
		$sql = "SELECT pspid, extid
				FROM Log".sSCHEMA_POSTFIX.".Transaction_Tbl
				WHERE id = ". $oTI->getID() ." AND enabled = '1'";
//		echo $sql ."\n";
		$RS = $oDB->getName($sql);
		
		switch ($RS["PSPID"])
		{
		case (Constants::iDIBS_PSP):	// DIBS
			// Authorise payment with PSP based on Ticket
			$obj_PSP = new DIBS($oDB, $oTxt, $oTI);
			break;
		case (Constants::iNETAXEPT_PSP):	// NetAxept
			// Authorise payment with PSP based on Ticket
			$obj_PSP = new NetAxept($oDB, $oTxt, $oTI);
			break;
		default:	// Unkown Payment Service Provider
			throw new RefundException("Unkown Payment Service Provider", 1001);
			break;
		}		
		return new Refund($oDB, $oTxt, $oTI, $obj_PSP, $RS["EXTID"], &$oCI);
	}
	
	/**
	 * Performs a capture operation with the PSP that authorized the transaction.
	 * The method will log one the following status codes:
	 * 
	 * DIBS:
	 * 	0. Refund succeeded
	 * 	1. No response from acquirer.
	 * 	2. Timeout
	 * 	3. Credit card expired.
	 * 	4. Rejected by acquirer.
	 * 	5. Authorisation older than 7 days.
	 * 	6. Transaction status on the DIBS server does not allow capture.
	 * 	7. Amount too high.
	 * 	8. Error in the parameters sent to the DIBS server. An additional parameter called "message" is returned, with a value that may help identifying the error. 
	 * 	9. Order number (orderid) does not correspond to the authorisation order number.
	 * 10. Re-authorisation of the transaction was rejected.
	 * 11. Not able to communicate with the acquier.
	 * 12. Confirm request error
	 * 14. Refund is called for a transaction which is pending for batch - i.e. refund was already called
	 * 15. Refund was blocked by DIBS.
	 * 
	 * @see		DIBS::refund();
	 * @link	http://tech.dibs.dk/toolbox/dibs-error-codes/
	 * 
	 * NETAXEPT:
	 * 	 0. Refund succeeded
	 * 	-1. Refund failed
	 * 
	 * @link	http://www.betalingsterminal.no/Netthandel-forside/Teknisk-veiledning/Response-codes/
	 * 
	 * @return	integer
	 * @throws	E_USER_WARNING
	 */
	public function refund($amt=0)
	{
		if ($amt == 0) { $amt = $this->_obj_TxnInfo->getAmount(); }		
		
		switch (strtoupper(get_class($this->_obj_PSP) ) )
		{
		case ("DIBS"):	// DIBS
			$code = $this->_obj_PSP->refund($this->_sPSPID, $amt);
			break;
		case ("NETAXEPT"):	// NetAxept		
			$obj_PSPConfig = PSPConfig::produceConfig($this->getDBConn(), $this->_obj_TxnInfo->getClientConfig()->getID(), $this->_obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), Constants::iNETAXEPT_PSP);
 
			$sql = "SELECT Txn.enabled
					FROM Log".sSCHEMA_POSTFIX.".Transaction_Tbl Txn
					INNER JOIN Log".sSCHEMA_POSTFIX.".Message_Tbl Msg ON Txn.id = Msg.txnid AND Msg.enabled = '1'
					WHERE Txn.id = ". intval($this->_obj_TxnInfo->getID() ) ." AND Txn.clientid = ". intval($this->_obj_TxnInfo->getClientConfig()->getID() ) ."
						AND Msg.stateid = ". Constants::iPAYMENT_CAPTURED_STATE;
//			echo $sql ."\n";
			$RS = $this->getDBConn()->getName($sql);
			
			$sType = "ANNUL";
			if ($RS["ENABLED"] === true) { $sType = "CREDIT"; }
			
			$code = $this->_obj_PSP->refund($this->_sPSPID, $amt, &$this->_obj_ConnInfo,  $obj_PSPConfig->getMerchantAccount(), $sType);
			break;
		default:	// Unkown Payment Service Provider
			throw new RefundException("Unkown Payment Service Provider", 1001);
			break;
		}
		if ($code === 0)
		{
			$sql = "UPDATE Log".sSCHEMA_POSTFIX.".Transaction_Tbl
					SET refund = refund + ". intval($amt) ."
					WHERE id = ". $this->_obj_TxnInfo->getID();
//			echo $sql ."\n";
			$res = $this->getDBConn()->query($sql);
			$aArgs = array("amount" => $amt);
			$this->_obj_TxnInfo = TxnInfo::produceInfo($this->_obj_TxnInfo, $aArgs);
		}
		return $code;
	}
	
	public function getClientsForUser($id)
	{
		$sql = "SELECT clientid
				FROM Admin".sSCHEMA_POSTFIX.".Access_Tbl
				WHERE userid = ". intval($id);
//		echo $sql ."\n";
		$res = $this->getDBConn()->query($sql);
		
		$aClientIDs = array();
		while ($RS = $this->getDBConn()->fetchName($res) )
		{
			$aClientIDs[] = $RS["CLIENTID"];
		}
		
		return $aClientIDs; 
	}
}
?>