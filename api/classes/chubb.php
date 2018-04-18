<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
* Callbacks can be performed by using mPoint's own Callback protocol.
*
* @author Rohit M
* @copyright Cellpoint Mobile
* @link http://www.cellpointmobile.com
* @package Callback
* @subpackage CHUBB
* @version 1.00
*/

/* ==================== PublicBank Exception Classes Start ==================== */
/**
 * Super class for all PublicBank Exceptions
*/
class CHUBBException extends CallbackException { }

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: CHUBB
 *
 */
Class CHUBB extends CPMPSP
{
	public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL) { throw new CHUBBException("Method: getPaymentData is not supported by CHUBB"); }
	public function getPSPID() { return Constants::iCHUBB_PSP;}
}