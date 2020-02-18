<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol. 
 *
 * @author Gaurav Pawar
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage MADA PPGS
 * @version 1.00
 */

/* ==================== Mada Mpgs Exception Classes Start ==================== */
/**
 * Super class for all Mada Mpgs Exceptions
 */
class MadaMpgsException extends CallbackException { }
/* ==================== Mada Mpgs Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: Mada Mpgs
 *
 */
class MadaMpgs extends CPMPSP
{
	public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL) { throw new DataCashException("Method: getPaymentData is not supported by Mada Mpgs"); }

	public function getPSPID() { return Constants::iMADA_MPGS_PSP; }
}