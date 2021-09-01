<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
* Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol.
*
* @author Urmila
* @copyright Cellpoint Mobile
* @link http://www.cellpointmobile.com
* @package Callback
* @subpackage PAYU
* @version 1.00
*/

/* ==================== PAYU exception Classes Start ==================== */
/**
 * Super class for all PAYU Exceptions
*/
class PayUException extends CallbackException { }
/* ==================== PAYU Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: PAYU
 *
 */
class PayU extends CPMPSP
{
	public function capture($iAmount=-1) { throw new PayUException("Method: capture is not supported by PayU"); }
    public function void($iAmount=-1) { throw new PayUException("Method: void is not supported by PayU"); }
    public function cancel($amount = -1) { throw new PayUException("Method: cancel is not supported by PayU"); }
	public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL) { throw new PayUException("Method: getPaymentData is not supported by PayU."); }
    public function getPSPID() { return Constants::iPAYU_PSP; }
    public function getPaymentMethods(PSPConfig $obj_PSPConfig)
    {
        $activePaymentMethods =  parent::getPaymentMethods($obj_PSPConfig);
        $aStatisticalData = $this->getStatisticalData('issuing_bank_%');
        $sortable = array();
        if(is_object($activePaymentMethods->{'active-payment-menthods'}->{'payment-method'}) && count($activePaymentMethods->{'active-payment-menthods'}->{'payment-method'}) >= 1){
            foreach ($activePaymentMethods->{'active-payment-menthods'}->{'payment-method'} as $node) {
                $issuingBank = strtolower($node->issuingBank);
                $usageCount = (int)$aStatisticalData['issuing_bank_' . $issuingBank];
                $node->addChild('usage', $usageCount);
                $sortable[] = $node;
            }
        }
        usort($sortable,   'compare_usage');
        $newSortedList = "<root><active-payment-menthods>";
        foreach ($sortable as $node)
        {
            unset($node->usage);
            $newSortedList .= $node->asXML();
        }
        $newSortedList .= "</active-payment-menthods></root>";
        $sxml = simplexml_load_string($newSortedList);
        return $sxml;

    }
}
