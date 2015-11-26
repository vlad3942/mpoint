<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol.
 * The DSB subpackage is a specific implementation capable of imitating DSB's own protocol.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage MobilePay
 * @version 1.00
 */

/**
 * Model Class containing all the Business Logic for handling Callback requests from DSB.
 *
 */
class DSB extends CPMPSP
{
	public function initialize(PSPConfig $obj_PSP) { /* no operation */ }

	public function auth($ticket, $apiKey, $cardID, $storecard)  { /* no operation */ }

	public function getPSPID() { return Constants::iDSB_PSP; }

	public function capture($iAmount = -1)
	{
		$this->completeCapture($iAmount, 0, array('Dummy capture') );
		return 1000;
	}

}
