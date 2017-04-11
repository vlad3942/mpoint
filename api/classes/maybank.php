<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed by using mPoint's own Callback protocol. 
 *
 * @author Qaeed Ramiwala
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage MayBank
 * @version 1.00
 */

/* ==================== MayBank Exception Classes Start ==================== */
/**
 * Super class for all MayBank Exceptions
 */
class MayBankException extends CallbackException { }

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: MayBank
 *
 */
Class MayBank extends CPMPSP
{
	public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL) { throw new CCPPException("Method: getPaymentData is not supported by MayBank"); }
	public function getPSPID() { return Constants::iMAYBANK_PSP; }
}