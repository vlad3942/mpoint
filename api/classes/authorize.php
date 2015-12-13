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
	 * @param TxnInfo $oTxn_Info
	 * @param callable $oPSP
	 */
	public function __construct(RDB $oDB, TranslateText $oTxt, TxnInfo $oTxn_Info, Callback $oPSP)
	{
		parent::__construct($oDB, $oTxt, $oTxn_Info->getClientConfig() );

		$this->_obj_TxnInfo = $oTxn_Info;
		$this->_obj_PSP = $oPSP;
	}

	/**
	 * Performs a redemption with a voucher issuing PSP and a supplied voucher ID.
	 *
	 * @param $iVoucherID
	 * @param int $iAmount (optional) amount to be captured
	 * @return int
	 * @throws Exception
	 * @throws TxnInfoException
	 */
	public function redeemVoucher($iVoucherID, $iAmount=-1)
	{
		// Add control state and immediately commit database transaction
		$this->newMessage($this->_obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_VOUCHER_STATE, "");
		$this->getDBConn()->query("COMMIT");

		// If amount if not set by caller, assume full transaction amount
		if ($iAmount <= 0) { $iAmount = $this->_obj_TxnInfo->getAmount(); }

		// If PSP supports the Redeem operation, perform the redemption
		try
		{
			if ( ($this->_obj_PSP instanceof Redeemable) === true)
			{
				$code = $this->_obj_PSP->redeem($iVoucherID, $iAmount);

				if ( (is_int($code) && $code > 0) || strlen($code) > 0)
				{
					$iStateID = Constants::iPAYMENT_ACCEPTED_STATE;

					// Add pspid, extenalid to transaction info
					$misc = array('psp-id'=>$this->_obj_PSP->getPSPID(), 'extid'=>$code);
					$this->_obj_TxnInfo = TxnInfo::produceInfo($this->_obj_TxnInfo->getID(), $this->_obj_TxnInfo, $misc);
					$code = 100;
				}
				else { $iStateID = Constants::iPAYMENT_REJECTED_STATE; }
			}
			else { throw new BadMethodCallException("Redeem not supported by PSP: ". get_class($this->_obj_PSP) ); }
		}
		catch (Exception $e)
		{
			$code = $e->getCode();
			$iStateID = Constants::iPAYMENT_REJECTED_STATE;
			$this->delMessage($this->_obj_TxnInfo->getID(), Constants::iPAYMENT_WITH_VOUCHER_STATE);
			$this->newMessage($this->_obj_TxnInfo->getID(), Constants::iPAYMENT_REJECTED_STATE, "Status code: ". $e->getCode(). "\n". $e->getMessage() );
		}

		if ( ($this->_obj_PSP instanceof CPMPSP) === true)
		{
			$this->_obj_PSP->initCallback($this->_obj_PSP->getPSPConfig(), $this->_obj_TxnInfo, $iStateID, "Status: ". $code);
		}
		else { trigger_error("Callback for voucher payment is only supported for inheritors of CPMPSP so far", E_USER_WARNING); }

		return $code;
	}

}
