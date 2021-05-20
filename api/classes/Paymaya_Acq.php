<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol.
 *
 * @author Amar
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage Paymaya-acq
 * @version 1.00
 */

/* ==================== Paymaya-acq Exception Classes Start ==================== */
/**
 * Super class for all Paymaya-acq Exceptions
 */
class Paymaya_AcqException extends CallbackException { }
/* ==================== Paymaya-acq Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: Paymaya-acq
 *
 */
class Paymaya_Acq extends CPMPSP
{
	public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL) { throw new Paymaya_AcqException("Method: getPaymentData is not supported by Paymaya-acq"); }
	public function getPSPID() { return Constants::iPAYMAYA_ACQ; }
}