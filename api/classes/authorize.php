<?php
/**
 * Model Class containing all the Business Logic for handling the Callback request from the Payment Service Provider (PSP).
 * The class contains methods that completes the transaction log with information received from the PSP, notifies the Client
 * and sends out an SMS Receipt to the Customer.
 *
 */
class Authorize extends General
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
	 * @param    ClientConfig $oClientConfig
	 */
	public function __construct(RDB $oDB, TranslateText $oTxt, TxnInfo $oTxn_Info, Callback $oPSP)
	{
		parent::__construct($oDB, $oTxt, $oTxn_Info->getClientConfig() );

		$this->_obj_TxnInfo = $oTxn_Info;
		$this->_obj_PSP = $oPSP;
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
	public function redeemVoucher($iVoucherID, $iAmount=-1)
	{
		// Serialize redeem operation by using the Database as a mutex
		$this->getDBConn()->query("START TRANSACTION");
		$this->newMessage($this->_obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_VOUCHER_STATE, "");

		// Add pspid to transaction
		$this->_updatePSPID($this->_obj_PSP->getPSPID(), $this->_obj_TxnInfo->getID() );

		// If amount if not set by caller, assume full transaction amount
		if ($iAmount <= 0) { $iAmount = $this->_obj_TxnInfo->getAmount(); }

		try
		{
			// If PSP supports the Redeem operation, perform the redemption
			if ( ($this->_obj_PSP instanceof Redeemable) === true) { $code = $this->_obj_PSP->redeem($iAmount); }
			else { throw new BadMethodCallException("Redeem not supported by PSP: ". get_class($this->_obj_PSP) ); }

			// Release mutex
			$this->getDBConn()->query("COMMIT");

			// Perform fake callback to callback controller
			//TODO
			//$this->_obj_PSP->initCallback(Constants::iPAYMENT_WITH_VOUCHER_STATE, array("transact"=>))

			return $code = 200 ? 2000 : $code;
		}
		catch (Exception $e)
		{
			// Release mutex
			$this->getDBConn()->query("ROLLBACK");
			throw $e;
		}
	}

	private function _updatePSPID($iPSPID, $iTxnID)
	{
		$sql = "UPDATE Log" . sSCHEMA_POSTFIX . ".Transaction_Tbl
				SET pspid = " . $iPSPID . "
				WHERE id = " . $iTxnID;
//		echo $sql ."\n";
		$res = $this->getDBConn()->query($sql);

		if (is_resource($res) === false || $this->getDBConn()->countAffectedRows($res) < 1) { trigger_error("Failed to set pspid: ". $iPSPID ." on transaction: ". $iTxnID); }
	}

}
