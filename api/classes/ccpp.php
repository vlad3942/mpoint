<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed by using mPoint's own Callback protocol. 
 *
 * @author Qaeed Ramiwala
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage 2C2P
 * @version 1.00
 */

/* ==================== 2C2P Exception Classes Start ==================== */
/**
 * Super class for all 2C2P Exceptions
 */
class CCPPException extends CallbackException { }

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: 2C2P
 *
 */
Class CCPP extends CPMPSP
{
	public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL) { throw new CCPPException("Method: getPaymentData is not supported by 2C2P"); }
	public function getPSPID() { return Constants::i2C2P_PSP; }
}