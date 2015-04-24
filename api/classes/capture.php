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
	 * Model class used to communicate with PSP
	 *
	 * @var Callback
	 */
	private $_obj_PSP;

	/**
	 * Default Constructor.
	 *
	 * @param    RDB $oDB Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param    TranslateText $oTxt Text Translation Object for translating any text into a specific language
	 * @param    TxnInfo $oTI Data object with the Transaction Information
	 * @param	 Callback $oPSP
	 */
	public function __construct(RDB $oDB, TranslateText $oTxt, TxnInfo $oTI, Callback $oPSP)
	{
		parent::__construct($oDB, $oTxt, $oTI->getClientConfig() );

		$this->_obj_TxnInfo = $oTI;
		$this->_obj_PSP = $oPSP;
	}

	/**
	 * Returns the Data object with the Transaction Information.
	 *
	 * @return TxnInfo
	 */
	public function getTxnInfo() { return $this->_obj_TxnInfo; }

	/**
	 * Returns the Model for the PSP to which the capture operation should be executed
	 *
	 * @return Callback
	 */
	public function getPSP() { return $this->_obj_PSP; }

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
	 * @param int $iAmount (optional) amount to be captured
	 * @return int
	 * @throws CaptureException
	 * @see        DIBS::capture();
	 * @link    http://tech.dibs.dk/toolbox/dibs-error-codes/
	 *
	 * NETAXEPT:
	 *		0. Capture succeeded
	 *		-1. Capture failed
	 *
	 * @link    http://www.betalingsterminal.no/Netthandel-forside/Teknisk-veiledning/Response-codes/
	 *
	 */
	public function capture($iAmount = -1)
	{
		// Serialize capture operations by using the Database as a mutex
		$this->getDBConn()->query("START TRANSACTION");
		$this->getMessageData($this->_obj_TxnInfo->getID(), Constants::iPAYMENT_ACCEPTED_STATE, true);

		// Payment not Captured
		if ($this->_isPaymentCaptured() === false)
		{
			// If amount if not set by caller, assume capture of full transaction amount
			if ($iAmount <= 0) { $iAmount = $this->getTxnInfo()->getAmount(); }

			// If PSP supports the Capture operation, perform the capture
			if ( ($this->_obj_PSP instanceof Captureable) === true) { $code = $this->_obj_PSP->capture($iAmount); }
			else { throw new BadMethodCallException("Capture not supported by PSP: ". get_class($this->_obj_PSP) ); }
		}
		else { $code = 1001; }
		// Release mutex
		$this->getDBConn()->query("COMMIT");
		
		return $code; 
	}

	private function _isPaymentCaptured()
	{
		return count($this->getMessageData($this->_obj_TxnInfo->getID(), Constants::iPAYMENT_CAPTURED_STATE) ) > 0;
	}
}
?>