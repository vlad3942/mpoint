<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol. 
 *
 * @author Deblina Das
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage Klarna
 * @version 2.00
 */

/* ==================== Klarna Exception Classes Start ==================== */
/**
 * Super class for all Qiwi Exceptions
 */
class KlarnaException extends CallbackException { }
/* ==================== Klarna Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: Klarna
 *
 */
class Klarna extends CPMPSP
{
	public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL) { throw new KlarnaException("Method: getPaymentData is not supported by Klarna"); }
	public function authorize(PSPConfig $obj_PSPConfig, $ticket) { throw new KlarnaException("Method: authTicket is not supported by Klarna"); }
	public function getPSPID() { return Constants::iKLARNA_PSP; }
}
