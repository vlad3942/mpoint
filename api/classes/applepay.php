<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol. 
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage ApplePay
 * @version 1.00
 */

/* ==================== Apple Pay Exception Classes Start ==================== */
/**
 * Super class for all Apple Pay Exceptions
 */
class ApplePayException extends CallbackException { }
/* ==================== Apple Pay Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: Apple Pay
 *
 */
class ApplePay extends CPMPSP
{
	public function capture($iAmount=-1) { throw new ApplePayException("Method: capture is not supported by Apple Pay"); }
	public function refund($iAmount=-1, $iStatus = NULL) { throw new ApplePayException("Method: refund is not supported by Apple Pay"); }
	public function void($iAmount=-1) { throw new ApplePayException("Method: void is not supported by Apple Pay"); }
	public function cancel() { throw new ApplePayException("Method: cancel is not supported by Apple Pay"); }
	public function authorize(PSPConfig $obj_PSPConfig, $ticket) { throw new ApplePayException("Method: authorize is not supported by Apple Pay"); }
	public function status() { throw new ApplePayException("Method: status is not supported by Apple Pay"); }

	public function getPSPID() { return Constants::iAPPLE_PAY_PSP; }

	public function getPaymentData($objPSPConfig, $obj_Elem, $mode = null)
	{
		$obj_XML = simpledom_load_string(parent::getPaymentData($objPSPConfig, $obj_Elem, $mode));
		if($mode == Constants::sPAYMENT_DATA_SUMMARY){
			unset($obj_XML->{'payment-data'}->card->{'card-number'});
			unset($obj_XML->{'payment-data'}->card->{'expiry'});
		}
		$paymentData = $obj_XML->asXML();

		return $paymentData;
	}
}
