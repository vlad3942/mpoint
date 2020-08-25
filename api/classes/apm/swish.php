<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed by using mPoint's own Callback protocol.
 *
 * @author Nitin Gaikwad
 * @copyright Cellpoint Digital
 * @link http://www.cellpointdigital.com
 * @package Callback
 * @subpackage SWISH
 * @version 1.00
 */

/* ==================== SWISH Exception Classes Start ==================== */
/**
 * Super class for all SWISH Exceptions
 */
class SwishException extends CallbackException { }

/**
 * Model Class containing all the Business Logic for the Payment APM: SWISH
 *
 */
//TODO: We need to check  CPM APM code.. For now we are using the CPMPSP
Class SWISH extends CPMPSP
{
    public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL) { throw new CCPPException("Method: getPaymentData is not supported by SWISH"); }
    public function getPSPID() { return Constants::iSWISH_APM; }
}