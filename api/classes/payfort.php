<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed by using mPoint's own Callback protocol. 
 *
 * @author Qaeed Ramiwala
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage PayFort
 * @version 1.00
 */

/* ==================== PayFort Exception Classes Start ==================== */
/**
 * Super class for all PayFort Exceptions
 */
class PayFortException extends CallbackException { }

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: PayFort
 *
 */
Class PayFort extends CPMPSP
{
	public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL) { throw new PayFortException("Method: getPaymentData is not supported by PayFort"); }
	public function getPSPID() { return Constants::iPAYFORT_PSP; }
}