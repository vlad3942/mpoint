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
	 * @var Callback
	 */
	private $_obj_PSP;

	/**
	 * Default Constructor.
	 *
	 * @param	RDB $oDB				Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param	TranslateText $oTxt 	Text Translation Object for translating any text into a specific language
	 * @param 	TxnInfo $oTI 			Data object with the Transaction Information
	 * @param 	Callback $oPSP 			Model for the PSP to which the capture operation should be executed
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
	 * @return object
	 */
	public function getPSP() { return $this->_obj_PSP; }


	/**
	 * Performs a refund operation with the PSP that authorized and captured the transaction.
	 * The method will log one the following status codes:
	 *
	 * DIBS:
	 *    0. Refund succeeded
	 *    1. No response from acquirer.
	 *    2. Timeout
	 *    3. Credit card expired.
	 *    4. Rejected by acquirer.
	 *    5. Authorisation older than 7 days.
	 *    6. Transaction status on the DIBS server does not allow capture.
	 *    7. Amount too high.
	 *    8. Error in the parameters sent to the DIBS server. An additional parameter called "message" is returned, with a value that may help identifying the error.
	 *    9. Order number (orderid) does not correspond to the authorisation order number.
	 * 10. Re-authorisation of the transaction was rejected.
	 * 11. Not able to communicate with the acquier.
	 * 12. Confirm request error
	 * 14. Refund is called for a transaction which is pending for batch - i.e. refund was already called
	 * 15. Refund was blocked by DIBS.
	 *
	 * @see        DIBS::refund();
	 * @link    http://tech.dibs.dk/toolbox/dibs-error-codes/
	 *
	 * NETAXEPT:
	 *     0. Refund succeeded
	 *    -1. Refund failed
	 *
	 * @link    http://www.betalingsterminal.no/Netthandel-forside/Teknisk-veiledning/Response-codes/
	 *
	 * @param int $iAmount Amount to refund
	 * @return int Refund status code
	 * @throws SQLQueryException
	 * @throws TxnInfoException
	 * @triggers E_USER_WARNING
	 */
	public function refund($iAmount = -1)
	{
		$status = null;
		if ($iAmount <= 0) { $iAmount = $this->_obj_TxnInfo->getAmount(); }
		
		$obj_TxnInfo = TxnInfo::produceInfo($this->_obj_TxnInfo->getID(), $this->getDBConn());
		
		$iAccountValidation = $obj_TxnInfo->hasEitherState($this->getDBConn(),Constants::iPAYMENT_ACCOUNT_VALIDATED);
		
		if($iAccountValidation == 1){
 			$status = Constants::iPAYMENT_ACCOUNT_VALIDATED_CANCELLED ;
		}
		// If PSP supports the Refund operation, perform the refund
		if ( ($this->_obj_PSP instanceof Refundable) === true) { $code = $this->_obj_PSP->refund($iAmount,$status); }
		else {throw new BadMethodCallException("Refund not supported by PSP: ". get_class($this->_obj_PSP) ); }

		if ($code === 1000)
		{
			$sql = "UPDATE Log".sSCHEMA_POSTFIX.".Transaction_Tbl
					SET refund = refund + ". intval($iAmount) ."
					WHERE id = ". $this->_obj_TxnInfo->getID();
//			echo $sql ."\n";
			$res = $this->getDBConn()->query($sql);
			if (is_resource($res) === false) { trigger_error("Failed to update refunded amount for transaction: ". $this->getTxnInfo()->getID(), E_USER_WARNING); }

			$aArgs = array("amount" => $iAmount);
			$this->_obj_TxnInfo = TxnInfo::produceInfo($this->_obj_TxnInfo->getID(),$this->getDBConn(), $this->_obj_TxnInfo, $aArgs);
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

	public function updateRefundedAmount($iAmount)
	{
		$sql = "UPDATE Log".sSCHEMA_POSTFIX.".Transaction_Tbl
				SET	refund = ". intval($iAmount) ."
				WHERE id = ". intval($this->_obj_TxnInfo->getID() );
//		echo $sql ."\n";
		$res = $this->getDBConn()->query($sql);

		// Refund amount updated successfully
		return is_resource($res) === true && $this->getDBConn()->countAffectedRows($res) == 1;
	}
}
?>