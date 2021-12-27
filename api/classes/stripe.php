<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
* Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol.
*
* @author Abhinav Shaha
* @copyright Cellpoint Mobile
* @link http://www.cellpointmobile.com
* @package Callback
* @subpackage Stripe
* @version 1.00
*/

/* ==================== Stripe exception Classes Start ==================== */
/**
 * Super class for all Stripe Exceptions
*/
class StripeException extends CallbackException { }
/* ==================== Stripe Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: Stripe
 *
 */
class Stripe_PSP extends CPMPSP
{
    public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL) { throw new StripeException("Method: getPaymentData is not supported by Stripe."); }
    public function getPSPID() { return Constants::iSTRIPE_PSP; }
}