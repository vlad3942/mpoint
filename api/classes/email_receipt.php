<?php
/**
 * The Receipt sub-package contains Business Logic for constructing either an SMS or an E-Mail Receipt.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Payment
 * @subpackage Receipt
 * @version 1.0
 */

/**
 * Business Logic for constructing the necesarry data fields for an E-Mail receipt:
 * 	- SMTP Headers
 * 	- Subject
 * 	- Body
 *
 */
class EMailReceipt extends EndUserAccount
{
	/**
	 * Data object with the Transaction InformaStion
	 *
	 * @var TxnInfo
	 */
	private $_obj_TxnInfo;

	/**
	 * Default Constructor
	 *
	 * @param	RDB $oDB			Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param	TranslateText $oDB 	Reference to the Text Translation Object for translating any text into a specific language
	 * @param	TxnInfo $oTI 		Reference to the Data object with the Transaction Information
	 */
	public function __construct(RDB &$oDB, TranslateText &$oTxt, TxnInfo &$oTI)
	{
		parent::__construct($oDB, $oTxt, $oTI->getClientConfig() );

		$this->_obj_TxnInfo = $oTI;
	}

	/**
	 * Constructs the Subject for the E-Mail Receipt.
	 * The method will replace the following text tags:
	 * 	- {ORDERID}, will be replaced with the Order ID provided by the Client
	 * 	- {MPOINTID}, will be replaced with mPoint's unique ID for the Transaction
	 *
	 * @return 	string
	 */
	public function constSubject()
	{
		$sSubject = $this->getText()->_("E-Mail Receipt - Subject");
		$sSubject = str_replace("{ORDERID}", $this->_obj_TxnInfo->getOrderID(), $sSubject);
		$sSubject = str_replace("{MPOINTID}", $this->_obj_TxnInfo->getID(), $sSubject);

		return $sSubject;
	}

	/**
	 * Constructs the Body for the E-Mail Receipt.
	 * The method will replace the following text tags:
	 * 	- {MOBILE}, will be replaced with the customer's MSISDN as provided by the Client
	 * 	- {ORDERID}, will be replaced with the Order ID provided by the Client
	 * 	- {MPOINTID}, will be replaced with mPoint's unique ID for the Transaction
	 * 	- {PRICE}, will be replaced with a formatted version of the total the customer was charged for the Transaction
	 *
	 * @return 	string
	 */
	public function constBody()
	{
		$sBody = $this->getText()->_("E-Mail Receipt - Body");
		$sBody = str_replace("{MOBILE}", $this->_obj_TxnInfo->getMobile(), $sBody);
		$sBody = str_replace("{ORDERID}", $this->_obj_TxnInfo->getOrderID(), $sBody);
		$sBody = str_replace("{MPOINTID}", $this->_obj_TxnInfo->getID(), $sBody);
		$sBody = str_replace("{PRICE}", General::formatAmount($this->_obj_TxnInfo->getClientConfig()->getCountryConfig(), $this->_obj_TxnInfo->getAmount() ), $sBody);
		$sBody = str_replace("{CLIENT}", $this->_obj_TxnInfo->getClientConfig()->getName(), $sBody);

		return $sBody;
	}

	/**
	 * Sends an E-Mail Receipt with Payment Information to the Customer.
	 *
	 * @param	string $email	Customer's E-Mail address
	 * @return	boolean			True if E-Mail is successfully sent to the Customer, otherwise false
	 */
	public function sendReceipt($email)
	{
		$bStatus = mail($email, $this->constSubject(), $this->constBody(), $this->constSMTPHeaders() );

		if ($bStatus === true)
		{
			$this->newMessage($this->_obj_TxnInfo->getID(), Constants::iEMAIL_ACCEPTED_STATE, $this->constSubject() );
		}
		else { $this->newMessage($this->_obj_TxnInfo->getID(), Constants::iEMAIL_REJECTED_STATE, $this->constSubject() ); }

		return $bStatus;
	}
}
?>