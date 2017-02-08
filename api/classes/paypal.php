<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed by using mPoint's own Callback protocol. 
 *
 * @author Gaorav
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage PayPal
 * @version 1.00
 */

/* ==================== PayPal Exception Classes Start ==================== */
/**
 * Super class for all PayPal Exceptions
 */
class PayPalException extends CallbackException { }

Class PayPal extends CPMPSP
{
	public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL) { throw new PayPalException("Method: getPaymentData is not supported by PayPal"); }
	
	public function getPSPID() { return Constants::iPAYPAL_PSP; }

}

?>