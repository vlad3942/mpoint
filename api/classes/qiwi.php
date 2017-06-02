<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol. 
 *
 * @author Qaeed Ramiwala
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage Qiwi
 * @version 1.00
 */

/* ==================== Qiwi Exception Classes Start ==================== */
/**
 * Super class for all Qiwi Exceptions
 */
class QiwiException extends CallbackException { }
/* ==================== Qiwi Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: Qiwi
 *
 */
class Qiwi extends CPMPSP
{
	public function capture($iAmount=-1) { throw new QiwiException("Method: capture is not supported by Qiwi"); }
	public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL) { throw new QiwiException("Method: getPaymentData is not supported by Qiwi"); }
	public function authorize(PSPConfig $obj_PSPConfig, $ticket) { throw new QiwiException("Method: authTicket is not supported by Qiwi"); }
	public function getPSPID() { return Constants::iQIWI_PSP; }
}
