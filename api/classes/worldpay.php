<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol.
 * The WorldPay subpackage is a specific implementation capable of imitating WorldPay's own protocol.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage WorldPay
 * @version 1.00
 */

/**
 * Model Class containing all the Business Logic for handling interaction with WorldPay
 *
 */
class WorldPayException extends CallbackException { }
/* ==================== SecureTrading Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: CCAvenue
 *
 */
class WorldPay extends CPMPSP
{
	public function getPSPID() { return Constants::iWORLDPAY_PSP; }
	
}
