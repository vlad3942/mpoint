<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed by using mPoint's own Callback protocol.
 *
 * @author Nitin Gaikwad
 * @copyright Cellpoint Digital
 * @link http://www.cellpointdigital.com
 * @package Callback
 * @subpackage VeriTrans4G
 * @version 1.00
 */

/* ==================== VeriTrans4G Exception Classes Start ==================== */
/**
 * Super class for all VeriTrans4G Exceptions
 */
class VeriTrans4GException extends CallbackException { }

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: VeriTrans4G
 *
 */
Class VeriTrans4G extends CPMPSP
{
    public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL) { throw new CCPPException("Method: getPaymentData is not supported by VeriTrans4G"); }
    public function getPSPID() { return Constants::iVeriTrans4G_PSP; }
}