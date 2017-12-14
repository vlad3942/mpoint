<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol. 
 *
 * @author Deblina Das
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage Trustly
 * @version 1.00
 */

/* ==================== Trustly Exception Classes Start ==================== */
/**
 * Super class for all Trustly Exceptions
 */
class TrustlyException extends CallbackException { }
/* ==================== Trustly Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: Trustly
 *
 */
class Trustly extends CPMPSP
{
	public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL) { throw new TrustlyException("Method: getPaymentData is not supported by Trustly"); }
	public function getPSPID() { return Constants::iTRUSTLY_PSP ; }
}
