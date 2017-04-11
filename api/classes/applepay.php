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
	public function refund($iAmount=-1) { throw new ApplePayException("Method: refund is not supported by Apple Pay"); }
	public function void($iAmount=-1) { throw new ApplePayException("Method: void is not supported by Apple Pay"); }
	public function cancel() { throw new ApplePayException("Method: cancel is not supported by Apple Pay"); }
	public function authorize(PSPConfig $obj_PSPConfig, $ticket) { throw new ApplePayException("Method: authTicket is not supported by Apple Pay"); }
	public function status() { throw new ApplePayException("Method: status is not supported by Apple Pay"); }

	public function getPSPID() { return Constants::iAPPLE_PAY_PSP; }
}
