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
 * @subpackage MobilePay
 * @version 1.00
 */

/**
 * Model Class containing all the Business Logic for handling Callback requests from MobilePay.
 *
 */
class MobilePay extends CPMPSP
{
	public function initialize(PSPConfig &$obj_PSP)
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml .= '<root>';
		$xml .= '<url method="app" />';
		$xml .= '</root>';
		$obj_XML = simplexml_load_string($xml);
		
		return $obj_XML;
	}

	public function notifyClient($iStateId, $iAmount) { parent::notifyClient($iStateId, Constants::iMOBILEPAY_PSP, $iAmount); }

	public function auth($ticket, $apiKey, $cardID, $storecard)  { /* no operation */ }

	public function initCallback(HTTPConnInfo &$oCI, $cardid, $txnid, $cardno, $expiry) { /* no operation */ }

}
