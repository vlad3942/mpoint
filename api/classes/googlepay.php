<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol. 
 *
 * @author Urmila Sridharan
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage GooglePay
 * @version 1.00
 */

/* ==================== Google Pay Exception Classes Start ==================== */
/**
 * Super class for all Google Pay Exceptions
 */
class GooglePayException extends CallbackException { }
/* ==================== Google Pay Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: Google Pay
 *
 */
class GooglePay extends CPMPSP
{
	public function capture($iAmount=-1) { throw new GooglePayException("Method: capture is not supported by Google Pay"); }
	public function refund($iAmount=-1) { throw new GooglePayException("Method: refund is not supported by Google Pay"); }
	public function void($iAmount=-1) { throw new GooglePayException("Method: void is not supported by Google Pay"); }
	public function cancel() { throw new GooglePayException("Method: cancel is not supported by Google Pay"); }
	public function authorize(PSPConfig $obj_PSPConfig, $ticket) { throw new GooglePayException("Method: authTicket is not supported by Google Pay"); }
	public function status() { throw new GooglePayException("Method: status is not supported by Google Pay"); }

	public function getPSPID() { return Constants::iGOOGLE_PAY_PSP; }

	public function getPaymentData($objPSPConfig, $obj_Elem, $mode = null)
	{
        $paymentData = "";
		$obj_XML = simpledom_load_string(parent::getPaymentData($objPSPConfig, $obj_Elem, $mode));
        if (count($obj_XML->{'payment-data'}) == 1) {
            if ($mode == Constants::sPAYMENT_DATA_SUMMARY) {
                unset($obj_XML->{'payment-data'}->card->{'card-number'});
                unset($obj_XML->{'payment-data'}->card->{'expiry'});
            }

            $paymentData = $obj_XML->asXML();
        }
        return $paymentData;
	}
}


