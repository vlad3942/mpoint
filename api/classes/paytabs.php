<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
* Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol.
*
* @author Amar
* @copyright Cellpoint Mobile
* @link http://www.cellpointmobile.com
* @package Callback
* @subpackage PayTabs
* @version 1.00
*/

/* ==================== PayTabs Exception Classes Start ==================== */
/**
 * Super class for all PayTabs Exceptions
*/
class PayTabsException extends CallbackException { }
/* ==================== PayTabs Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: PayTabs
 *
 */
class PayTabs extends CPMPSP
{
	public function capture($iAmount=-1) { throw new PayTabsException("Method: capture is not supported by PayTabs"); }
	public function refund($iAmount=-1) { throw new PayTabsException("Method: refund is not supported by PayTabs"); }
	public function void($iAmount=-1) { throw new PayTabsException("Method: void is not supported by PayTabs"); }
	public function cancel() { throw new PayTabsException("Method: cancel is not supported by PayTabs"); }
	public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL) { throw new PayTabsException("Method: getPaymentData is not supported by PayTabs"); }
	public function authorize(PSPConfig $obj_PSPConfig, $ticket) { throw new PayTabsException("Method: authTicket is not supported by PayTabs"); }
	public function getPSPID() { return Constants::iPAY_TABS_PSP; }
}
