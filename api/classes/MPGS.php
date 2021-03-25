<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol. 
 *
 * @author Karishan Kumar
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage MPGS
 * @version 1.00
 */

/* ==================== MPGS Exception Classes Start ==================== */
/**
 * Super class for all MPGS Exceptions
 */
class MPGSException extends CallbackException { }
/* ==================== MPGS Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: MPGS
 *
 */
class MPGS extends CPMPSP
{
	public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL) { throw new MPGSException("Method: getPaymentData is not supported by MPGS"); }

	public function getPSPID() { return Constants::iMPGS_PSP; }
}
