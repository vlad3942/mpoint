<?php
/**
 * @author Chaitenya Yadav
 * @copyright Cellpoint Digital
 * @link http://www.cellpointdigital.com
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
    public function initialize(PSPConfig $obj_PSP,$euaid=-1, $sc=false, $card_type_id=-1, $card_token='', $obj_BillingAddress = NULL, ClientInfo $obj_ClientInfo = NULL, $authToken = NULL, $aWalletCardSchemes = array()) { /* no operation */ }
    public function authorize(PSPConfig $obj_PSPConfig, $ticket,$clientInfo=null) { throw new ApplePayException("Method: authorize is not supported by Travel Fund"); }
    public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL) { throw new TravelFundException("Method: getPaymentData is not supported by Travel Fund"); }
    public function void($iAmount=-1) { throw new TravelFundException("Method: void is not supported by Travel Fund"); }
    public function cancel($iStatus = null) { throw new TravelFundException("Method: cancel is not supported by Travel Fund"); }
    public function capture($iAmount = -1)
    {
        $this->completeCapture($iAmount, 0, array('Dummy capture') );
        return 1000;
    }
}