<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
* Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol.
*
* @author Urmila
* @copyright Cellpoint Mobile
* @link http://www.cellpointmobile.com
* @package Callback
* @subpackage Global Payments
* @version 1.00
*/

/* ==================== Global Payments xception Classes Start ==================== */
/**
 * Super class for all Global Payments Exceptions
*/
class GlobalPaymentsException extends CallbackException { }
/* ==================== Global Payments Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: PAYU
 *
 */
class GlobalPayments extends CPMPSP
{
	public function getPSPID() { return Constants::iGlobal_Payments_PSP; }
}
