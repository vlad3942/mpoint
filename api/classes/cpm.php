<?php
/**
 * The Billing package provides features for charging the customer through alternatives to Credit Card such as Premium SMS and WAP Billing.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Billing
 * @subpackage CellpointMobile
 * @version 1.0
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
	 */
	public function sendBillingSMS(GoMobileConnInfo &$oCI)
	{
		// Production Mode
		if ($this->getTxnInfo()->getMode() == 0) { $iAmount = $this->getTxnInfo()->getAmount(); }
		else { $iAmount = 0; }

		// Construct body for the Premium SMS
		$sBody = str_replace("{PRICE", General::formatAmount($this->getTxnInfo()->getClientConfig()->getCountryConfig(), $this->getTxnInfo()->getAmount() ), $this->getText()->_("Billing SMS") );
		
		// Create Premium MT-SMS
		$obj_MsgInfo = GoMobileMessage::produceMessage(2, $this->getTxnInfo()->getClientConfig()->getCountryConfig()->getID(), $this->getTxnInfo()->getOperator(), $this->getTxnInfo()->getClientConfig()->getCountryConfig()->getChannel(), $this->getTxnInfo()->getClientConfig()->getKeywordConfig()->getKeyword(), $iAmount, $this->getTxnInfo()->getAddress(), $sBody);
		
		$this->sendMT($oCI, $obj_MsgInfo, $this->getTxnInfo());
	}
	
	/**
	 * Initialises Callback to the Client.
	 *
	 * @param 	HTTPConnInfo $oCI 	Connection Info required to communicate with the Callback component for Cellpoint Mobile
	 * @param 	SMS $oMI 			Reference to the Message Object for holding the message data which was sent to GoMobile to bill the customer
	 */
	public function initCallback(HTTPConnInfo &$oCI, SMS &$oMI)
	{
		$b = "mpointid=". $this->getTxnInfo()->getID() ."&gomobileid=". $oMI->getGoMobileID() ."&language=". $this->getTxnInfo()->getLanguage() ."&status=". $oMI->getReturnCodes();

		$obj_HTTP = new HTTPClient(new Template(), $oCI);
		$obj_HTTP->connect();
		$obj_HTTP->send($this->constHeaders(), $b);
		$obj_HTTP->disConnect();
	}
}
?>