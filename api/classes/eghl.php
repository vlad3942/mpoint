<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol.
 *
 * @author Urmila
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage eGHL
 * @version 1.00
 */

/* ==================== eGHL Exception Classes Start ==================== */
/**
 * Super class for all eGHL Exceptions
 */
class EGHLException extends CallbackException { }
/* ==================== eGHL Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: eGHL
 *
 */
class EGHL extends CPMPSP
{
    public function capture($iAmount=-1) { throw new EGHLException("Method: capture is not supported for eGHL-FPX."); }
    /* eGHL reversal requires txn status query and txn can be reversed only if not yet settled. */
    public function cancel() { throw new EGHLException("Method: cancel is not supported by eGHL-FPX"); }
    public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL) { throw new EGHLException("Method: getPaymentData is not supported by eGHL-FPX"); }
    public function authorize(PSPConfig $obj_PSPConfig, $ticket) { throw new EGHLException("Method: authTicket is not supported by eGHL-FPX"); }
    public function getPSPID() { return Constants::iEGHL_PSP; }
    public function refund($iAmount=-1) { throw new EGHLException("Method: refund is not supported by eGHL-FPX"); }
}