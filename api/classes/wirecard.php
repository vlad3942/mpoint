<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol. 
 *
 * @author Abhishek Sawant
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage Wire Card
 * @version 1.00
 */

/* ==================== Data Cash Exception Classes Start ==================== */
/**
 * Super class for all Data Cash Exceptions
 */
class WireCardException extends CallbackException { }
/* ==================== Data Cash Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: Data Cash
 *
 */
class WireCard extends CPMPSP
{
	public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL) { throw new DataCashException("Method: getPaymentData is not supported by Wire Card"); }
}
