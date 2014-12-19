<?php
/**
 * The Capture Package provide methods for capturing a previously authorized amount through mPoint's API.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package API
 * @subpackage Capture
 * @version 1.10
 */

/* ==================== Capture Exception Classes Start ==================== */
/**
 * Exception class for all Capture exceptions
 */
class CaptureException extends mPointException { }
/* ==================== Capture Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for handling the Callback request from the Payment Service Provider (PSP).
 * The class contains methods that completes the transaction log with information received from the PSP, notifies the Client
 * and sends out an SMS Receipt to the Customer.
 *
 */
class Capture extends General
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
	 * The PSP's unique ID for the transaction which should be captured
	 *
	 * @var string
	 */
	private $_sPSPID;

	/**
	 * Default Constructor.
	 *
	 * @param	RDB $oDB				Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param	TranslateText $oTxt 	Text Translation Object for translating any text into a specific language
	 * @param 	TxnInfo $oTI 			Data object with the Transaction Information
	 * @param 	Object $oPSP 			Model for the PSP to which the capture operation should be executed
	 * @param 	String $pspid 			Unique ID for the Payment Service Provider (PSP) mPoint should use to capture the transaction amount
	 */
	public function __construct(RDB &$oDB, TranslateText &$oTxt, TxnInfo &$oTI, &$oPSP, $pspid)
	{
		parent::__construct($oDB, $oTxt, $oTI->getClientConfig() );

		$this->_obj_TxnInfo = $oTI;
		$this->_obj_PSP = $oPSP;
		$this->_sPSPID = $pspid;
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
	 * Produces a new instance of a Capture operation object
	 *
	 * @param 	RDB $oDB 				Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param 	TranslateText $oTxt 	Text Translation Object for translating any text into a specific language
	 * @param 	TxnInfo $oTI 			Data object with the Transaction Information
	 * @return 	Capture
	 */
	public static function produce(RDB &$oDB, TranslateText &$oTxt, TxnInfo &$oTI)
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
			case (Constants::iNETAXEPT_PSP):	// Netaxept
				// Authorise payment with PSP based on Ticket
				$obj_PSP = new NetAxept($oDB, $oTxt, $oTI);
				break;
		case (Constants::iWANNAFIND_PSP):// WannaFind
			// Authorise payment with PSP based on Ticket
			$obj_PSP = new WannaFind($oDB, $oTxt, $oTI);
			break;
		default:	// Unkown Payment Service Provider
			throw new CaptureException("Unkown Payment Service Provider", 1001);
			break;
		}
		
		return new Capture($oDB, $oTxt, $oTI, $obj_PSP, $RS["EXTID"]);
	}

	/**
	 * Performs a capture operation with the PSP that authorized the transaction.
	 *
	 * @param    HTTPConnInfo $oCI Information on how to connect to PSP. Defaults to NULL
	 * @param    integer $merchant The merchant ID to identify us to PSP. Defaults to -1
	 *
	 *    DIBS
	 * The method will log one the following status codes for DIBS:
	 *    0. Capture succeeded
	 *    1. No response from acquirer.
	 *    2. Error in the parameters sent to the DIBS server. An additional parameter called "message" is returned, with a value that may help identifying the error.
	 *    3. Credit card expired.
	 *    4. Rejected by acquirer.
	 *    5. Authorisation older than 7 days.
	 *    6. Transaction status on the DIBS server does not allow capture.
	 *    7. Amount too high.
	 *    8. Amount is zero.
	 *    9. Order number (orderid) does not correspond to the authorisation order number.
	 * 10. Re-authorisation of the transaction was rejected.
	 * 11. Not able to communicate with the acquier.
	 * 12. Confirm request error
	 * 14. Capture is called for a transaction which is pending for batch - i.e. capture was already called
	 * 15. Capture was blocked by DIBS.
	 *
	 * @param int $amount (optional) amount to be captured
	 * @return int
	 * @throws CaptureException
	 * @see        DIBS::capture();
	 * @link    http://tech.dibs.dk/toolbox/dibs-error-codes/
	 *
	 * NETAXEPT:
	 *     OK. Capture succeeded
	 *     String. Refund failed
	 *
	 * @link    http://www.betalingsterminal.no/Netthandel-forside/Teknisk-veiledning/Response-codes/
	 *
	 */
	public function capture(HTTPConnInfo &$oCI=NULL, $merchant=-1, $amount = null)
	{
		// Serialize capture operations by using the Database as a mutex
		$this->getDBConn()->query("START TRANSACTION");// START TRANSACTION does not work with Oracle db
		$this->getMessageData($this->_obj_TxnInfo->getID(), Constants::iPAYMENT_ACCEPTED_STATE, true);
		
		// Payment not Captured
		if (count($this->getMessageData($this->_obj_TxnInfo->getID(), Constants::iPAYMENT_CAPTURED_STATE) ) == 0)
		{				
			switch ($this->getTxnInfo()->getPSPID() )
			{
			case (Constants::iDIBS_PSP):	// DIBS
			case (Constants::iWANNAFIND_PSP):// WannaFind
				$code = $this->_obj_PSP->capture($this->_sPSPID);
				break;
			case (Constants::iNETAXEPT_PSP):	// Netaxept						
				$code = $this->_obj_PSP->capture($oCI, $merchant, $this->_sPSPID, $this->getTxnInfo(), $amount);
				break;
			default:	// Unkown Payment Service Provider
				throw new CaptureException("Unkown Payment Service Provider", 1001);
				break;
			}
			if ($code === 0 || $code === "OK") { $code = 1000; }
		}
		else { $code = 1001; }
		// Release mutex
		$this->getDBConn()->query("COMMIT");
		
		return $code; 
	}
}
?>