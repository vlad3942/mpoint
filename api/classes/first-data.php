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

/* ==================== First-Data Exception Classes Start ==================== */
/**
 * Super class for all FirstData Exceptions
 */
class FirstDataException extends CallbackException { }
/* ==================== FirstData Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: FirstData
 *
 */
class FirstData extends CPMACQUIRER
{
	public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL) { throw new FirstDataException("Method: getPaymentData is not supported by FirstData"); }

	public function getPSPID() { return Constants::iFirstData_PSP; }
}
