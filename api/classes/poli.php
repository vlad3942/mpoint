<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol. 
 *
 * @author Qaeed Ramiwala
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage POLi
 * @version 1.00
 */

/* ==================== POLi Exception Classes Start ==================== */
/**
 * Super class for all POLi Exceptions
 */
class PoliException extends CallbackException { }
/* ==================== POLi Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: POLi
 *
 */
class Poli extends CPMPSP
{
	public function capture($iAmount=-1) { throw new PoliException("Method: capture is not supported by POLi"); }
	public function refund($iAmount=-1) { throw new PoliException("Method: refund is not supported by POLi"); }
	public function void($iAmount=-1) { throw new PoliException("Method: void is not supported by POLi"); }
	public function cancel() { throw new PoliException("Method: cancel is not supported by POLi"); }
	public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL) { throw new PoliException("Method: getPaymentData is not supported by POLi"); }
	public function authorize(PSPConfig $obj_PSPConfig, $ticket) { throw new PoliException("Method: authTicket is not supported by POLi"); }
	public function getPSPID() { return Constants::iPOLI_PSP; }
}
