<?php
/**
 * The Billing package provides features for charging the customer through alternatives to Credit Card such as Premium SMS and WAP Billing.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Billing
 * @subpackage CellpointMobile
 * @version 1.01
 */

/**
 * Model Class containing all the Business Logic for handling Billing through GoMobile.
 *
 */
class CellpointMobile extends Callback
{
	/**
	 * Constructs and sends the Billing SMS through GoMobile.
	 *
	 * @param 	GoMobileConnInfo $oCI 	Connection Info required to communicate with GoMobile
	 * @return 	SMS
	 */
	public function sendBillingSMS(GoMobileConnInfo &$oCI)
	{
		// Production Mode
		if ($this->getTxnInfo()->getMode() == 0) { $iAmount = $this->getTxnInfo()->getAmount(); }
		else { $iAmount = 0; }

		// Construct body for the Premium SMS
		$sBody = str_replace("{PRICE}", General::formatAmount($this->getTxnInfo()->getClientConfig()->getCountryConfig(), $this->getTxnInfo()->getAmount() ), $this->getText()->_("Billing SMS") );

		// Create Premium MT-SMS
		$obj_MsgInfo = GoMobileMessage::produceMessage(Constants::iMT_SMS_TYPE, $this->getTxnInfo()->getClientConfig()->getCountryConfig()->getID(), $this->getTxnInfo()->getOperator(), $this->getTxnInfo()->getClientConfig()->getCountryConfig()->getChannel(), $this->getTxnInfo()->getClientConfig()->getKeywordConfig()->getKeyword(), $iAmount, $this->getTxnInfo()->getMobile(), utf8_decode($sBody) );
		$obj_MsgInfo->setDescription("mPoint - PSMS Charge");
		$this->sendMT($oCI, $obj_MsgInfo, $this->getTxnInfo() );

		return $obj_MsgInfo;
	}

	/**
	 * Initialises Callback to the Client.
	 *
	 * @param 	HTTPConnInfo $oCI 	Connection Info required to communicate with the Callback component for Cellpoint Mobile
	 * @param	integer $cardid		Unique ID of the "card" that was used to pay for the transaction:
	 * 									10 - Premium SMS
	 * 									11 - Prepaid Account
	 * @param	integer $status		Status Code for the transaction:
	 * 									 200 - Success for Premium SMS
	 * 									2000 - Success for Prepaid Account
	 * @param	integer $gmid		GoMobile's unique ID for the transaction if payment was made via Premium SMS (defaults to 0)
	 */
	public function initCallback(HTTPConnInfo &$oCI, $cardid, $status, $gmid=0)
	{
		$b = "mpointid=". $this->getTxnInfo()->getID();
		$b .= "&cardid=". $cardid;
		$b .= "&gomobileid=". $gmid;
		$b .= "&language=". $this->getTxnInfo()->getLanguage();
		$b .= "&status=". $status;

		$obj_HTTP = new HTTPClient(new Template(), $oCI);
		$obj_HTTP->connect();
		$obj_HTTP->send($this->constHTTPHeaders(), $b);
		$obj_HTTP->disConnect();
	}
}
?>