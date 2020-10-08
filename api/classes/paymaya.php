<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol. 
 *
 * @author Amar kumar
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage Paymaya
 * @version 1.00
 */

/* ==================== Paymaya Exception Classes Start ==================== */
/**
 * Super class for all Paymaya Exceptions
 */
class PaymayaException extends CallbackException { }
/* ==================== Paymaya Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: Paymaya
 *
 */
class Paymaya extends CPMPSP
{
    public function refund($iAmount=-1) { throw new PaymayaException("Method: refund is not supported by Paymaya"); }
    public function void($iAmount=-1) { throw new PaymayaException("Method: void is not supported by Paymaya"); }
    public function cancel() { throw new PaymayaException("Method: cancel is not supported by Paymaya"); }
    public function capture($iAmount=-1) { throw new PaymayaException("Method: capture is not supported by Paymaya"); }
    public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL) { throw new PaymayaException("Method: getPaymentData is not supported by Paymaya"); }
	public function getPSPID() { return Constants::iPAYMAYA_WALLET; }
}
