<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed by using mPoint's own Callback protocol.
 *
 * @author Chaitenya Yadav
 * @copyright Cellpoint Digital
 * @link http://www.cellpointdigital.com
 * @package Callback
 * @subpackage Travel Fund
 * @version 1.00
 */

/* ==================== TravelFund Exception Classes Start ==================== */
/**
 * Super class for all TravelFund Exceptions
 */
class TravelFundException extends CallbackException { }

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: TravelFund
 *
 */
Class TravelFund extends CPMPSP
{
    public function getPSPID() { return Constants::iTRAVELFUND_VOUCHER; }
    public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL) { throw new TravelFundException("Method: getPaymentData is not supported by Travel Fund"); }
    public function void($iAmount=-1) { throw new TravelFundException("Method: void is not supported by Travel Fund"); }
    public function cancel($iStatus = null) { throw new TravelFundException("Method: cancel is not supported by Travel Fund"); }
    public function capture($iAmount = -1)
    {
        $this->completeCapture($iAmount, 0, array('Dummy capture') );
        return 1000;
    }
}