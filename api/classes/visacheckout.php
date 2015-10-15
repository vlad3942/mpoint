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
 * Super class for all mPoint Exceptions
 */
class VisaCheckoutException extends CallbackException { }
/* ==================== VISA Checkout Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: VISA Checkout
 *
 */
class VisaCheckout extends CPMPSP
{
	public function status() { throw new AdyenException("Transact status call is not supported by VISA Checkout"); }
}
