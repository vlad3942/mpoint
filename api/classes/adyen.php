<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol.
 * The MobilePay subpackage is a specific implementation capable of imitating MobilePay's own protocol.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage Adyen
 * @version 1.00
 */

/* ==================== Adyen Exception Classes Start ==================== */
/**
 * Super class for all mPoint Exceptions
 */
class AdyenException extends CallbackException { }
/* ==================== Adyen Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: Adyen
 *
 */
class Adyen extends CPMPSP
{
	public function status() { throw new AdyenException("Transact status call is not supported by Adyen"); }

	public function getPSPID() { return Constants::iADYEN_PSP; }
}
