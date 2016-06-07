<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol. 
 *
 * @author Rohit Malhotra
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage AMEX Express Checkout
 * @version 1.00
 */

/* ==================== AMEX Express Checkout Exception Classes Start ==================== */
/**
 * Super class for all AMEX Express Checkout Exceptions
 */
class AMEXExpressCheckoutException extends CallbackException { }
/* ==================== AMEX Express Checkout Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: AMEX Express Checkout
 *
 */
class AMEXExpressCheckout extends CPMPSP
{
	
	
	public function capture($iAmount=-1) { throw new AMEXExpressCheckoutException("Method: capture is not supported by AMEX Express Checkout"); }
	public function refund($iAmount=-1) { throw new AMEXExpressCheckoutException("Method: refund is not supported by AMEX Express Checkout"); }
	public function void($iAmount=-1) { throw new AMEXExpressCheckoutException("Method: void is not supported by AMEX Express Checkout"); }
	public function cancel() { throw new AMEXExpressCheckoutException("Method: cancel is not supported by AMEX Express Checkout"); }
	public function authTicket(PSPConfig $obj_PSPConfig, $ticket) { throw new AMEXExpressCheckoutException("Method: authTicket is not supported by AMEX Express Checkout"); }
	public function status() { throw new AMEXExpressCheckoutException("Method: status is not supported by AMEX Express Checkout"); }
	public function getPSPID() { return Constants::iAMEX_EXPRESS_CHECKOUT_PSP; }
}
