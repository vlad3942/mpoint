<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol. 
 *
 * @author Qaeed Ramiwala
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage MobilePay Online
 * @version 1.00
 */

/* ==================== MobilePay Online Exception Classes Start ==================== */
/**
 * Super class for all MobilePay Online Exceptions
 */
class MobilePayOnlineException extends CallbackException { }
/* ==================== MobilePay Online Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: MobilePay Online
 *
 */
class MobilePayOnline extends CPMPSP
{
	public function capture($iAmount=-1) { throw new MobilePayOnlineException("Method: capture is not supported by MobilePay Online"); }
	public function refund($iAmount=-1) { throw new MobilePayOnlineException("Method: refund is not supported by MobilePay Online"); }
	public function void($iAmount=-1) { throw new MobilePayOnlineException("Method: void is not supported by MobilePay Online"); }
	public function cancel() { throw new MobilePayOnlineException("Method: cancel is not supported by MobilePay Online"); }
	public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL) { throw new MobilePayOnlineException("Method: getPaymentData is not supported by MobilePay Online"); }
	public function getPSPID() { return Constants::iMOBILEPAY_ONLINE_PSP; }
}
