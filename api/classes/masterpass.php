<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol. 
 *
 * @author Rohit Malhotra
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage Master Pass
 * @version 1.00
 */

/* ==================== Master Pass Exception Classes Start ==================== */
/**
 * Super class for all Master Pass Exceptions
 */
class MasterPassException extends CallbackException { }
/* ==================== Master Pass Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: Master Pass
 *
 */
class MasterPass extends CPMPSP
{
	
	
	public function capture($iAmount=-1) { throw new MasterPassException("Method: capture is not supported by Master Pass"); }
	public function refund($iAmount=-1) { throw new MasterPassException("Method: refund is not supported by Master Pass"); }
	public function void($iAmount=-1) { throw new MasterPassException("Method: void is not supported by Master Pass"); }
	public function cancel() { throw new MasterPassException("Method: cancel is not supported by Master Pass"); }
	public function authTicket(PSPConfig $obj_PSPConfig, $ticket) { throw new MasterPassException("Method: authTicket is not supported by Master Pass"); }
	public function status() { throw new MasterPassException("Method: status is not supported by Master Pass"); }
	public function getPSPID() { return Constants::iMASTER_PASS_PSP; }
}
