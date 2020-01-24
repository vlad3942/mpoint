<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed by using mPoint's own Callback protocol.
 *
 * @author Nitin Gaikwad
 * @copyright Cellpoint Digital
 * @link http://www.cellpointdigital.com
 * @package Callback
 * @subpackage DragonPay
 * @version 1.00
 */

/* ==================== DragonPay Exception Classes Start ==================== */
/**
 * Super class for all DragonPay Exceptions
 */
class DragonPayException extends CallbackException { }

/**
 * Model Class containing all the Business Logic for the Payment Aggregator: DragonPay
 *
 */
//TODO: We need to check  CPM Aggregator code.. For now we are using the CPMPSP
Class DragonPay extends CPMPSP
{
    public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL) { throw new CCPPException("Method: getPaymentData is not supported by DragonPay"); }
    public function getPSPID() { return Constants::iDragonPay_AGGREGATOR; }
}