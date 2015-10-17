<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol. 
 *
 * @author Rohit Malhotra
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage Visa Checkout
 * @version 1.00
 */

/* ==================== VISA Checkout Exception Classes Start ==================== */
/**
 * Super class for all VISA Checkout Exceptions
 */
class VISACheckoutException extends CallbackException { }
/* ==================== VISA Checkout Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: VISA Checkout
 *
 */
class VISACheckout extends CPMPSP
{
	
	
	public function capture($iAmount=-1) { throw new VISACheckoutException("Method: capture is not supported by VISA Checkout"); }
	public function refund($iAmount=-1) { throw new VISACheckoutException("Method: refund is not supported by VISA Checkout"); }
	public function void($iAmount=-1) { throw new VISACheckoutException("Method: void is not supported by VISA Checkout"); }
	public function cancel() { throw new VISACheckoutException("Method: cancel is not supported by VISA Checkout"); }
	public function authTicket(PSPConfig $obj_PSPConfig, $ticket) { throw new VISACheckoutException("Method: authTicket is not supported by VISA Checkout"); }
	public function status() { throw new VISACheckoutException("Method: status is not supported by VISA Checkout"); }
}
