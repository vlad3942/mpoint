<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol. 
 * The CCAvenue's subpackage is a specific implementation capable of imitating CCAveue's own protocol.
 *
 * @author Deblina Das
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage CCAvenue
 * @version 1.00
 */

/* ==================== SecureTrading Exception Classes Start ==================== */
/**
 * Super class for all Secure Trading Exceptions
 */
class CCAvenueException extends CallbackException { }
/* ==================== SecureTrading Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: CCAvenue
 *
 */
class CCAvenue extends CPMPSP
{
	public function getPSPID() { return Constants::iCCAVENUE_PSP; }
	
}
