<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
* Callbacks can be performed by using mPoint's own Callback protocol.
*
* @author Qaeed Ramiwala
* @copyright Cellpoint Mobile
* @link http://www.cellpointmobile.com
* @package Callback
* @subpackage PublicBank
* @version 1.00
*/

/* ==================== PublicBank Exception Classes Start ==================== */
/**
 * Super class for all PublicBank Exceptions
*/
class PublicBankException extends CallbackException { }

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: PublicBank
 *
 */
Class PublicBank extends CPMPSP
{
	public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL) { throw new PublicBankException("Method: getPaymentData is not supported by PublicBank"); }
	public function getPSPID() { return Constants::iPUBLIC_BANK_PSP;}
}