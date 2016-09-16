<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol. 
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage AndroidPay
 * @version 1.00
 */

/* ==================== Android Pay Exception Classes Start ==================== */
/**
 * Super class for all Android Pay Exceptions
 */
class AndroidPayException extends CallbackException { }
/* ==================== Android Pay Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: Android Pay
 *
 */
class AndroidPay extends CPMPSP
{
	public function capture($iAmount=-1) { throw new AndroidPayException("Method: capture is not supported by Android Pay"); }
	public function refund($iAmount=-1) { throw new AndroidPayException("Method: refund is not supported by Android Pay"); }
	public function void($iAmount=-1) { throw new AndroidPayException("Method: void is not supported by Android Pay"); }
	public function cancel() { throw new AndroidPayException("Method: cancel is not supported by Android Pay"); }
	public function authorize(PSPConfig $obj_PSPConfig, $ticket) { throw new AndroidPayException("Method: authTicket is not supported by Android Pay"); }
	public function status() { throw new AndroidPayException("Method: status is not supported by Android Pay"); }

	public function getPSPID() { return Constants::iANDROID_PAY_PSP; }
}
