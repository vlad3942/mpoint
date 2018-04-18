<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
* Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol.
*
* @author Urmila
* @copyright Cellpoint Mobile
* @link http://www.cellpointmobile.com
* @package Callback
* @subpackage Citcon
* @version 1.00
*/

/* ==================== Citcon Exception Classes Start ==================== */
/**
 * Super class for all Citcon Exceptions
*/
class CitconException extends CallbackException { }
/* ==================== Citcon Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: Citcon
 *
 */
class Citcon extends CPMPSP
{
	public function capture($iAmount=-1) { throw new CitconException("Method: capture is not supported by Citcon"); }
	public function cancel() { throw new CitconException("Method: cancel is not supported by Citcon"); }
	public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL) { throw new CitconException("Method: getPaymentData is not supported by Citcon"); }
	public function authorize(PSPConfig $obj_PSPConfig, $ticket) { throw new CitconException("Method: authTicket is not supported by Citcon"); }
	public function getPSPID() { return Constants::iCITCON_PSP; }
}
