<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
* Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol.
*
* @author NTIN GAIWKAD
* @copyright Cellpoint Digital
* @link http://www.cellpointdigital.com
* @package Callback
* @subpackage safetypay
* @version 1.00
*/

/* ==================== SAFETYPAY Exception Classes Start ==================== */
/**
 * Super class for all SAFETYPAY Exceptions
*/
class safetypayException extends CallbackException { }
/* ==================== SAFETYPAY Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: SAFETYPAY
 *
 */
class safetypay extends CPMPSP
{
    public function capture($iAmount=-1) { throw new SAFETYPAYException("Method: capture is not supported for SAFETYPAY."); }
	/* SAFETYPAY reversal requires txn status query and txn can be reversed only if not yet settled. */
    public function cancel($amount = -1) { throw new SAFETYPAYException("Method: cancel is not supported by SAFETYPAY"); }
    public function refund($iAmount=-1, $iStatus = NULL) { throw new SAFETYPAYException("Method: refund is not supported by SAFETYPAY"); }
    
    public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL) { throw new SAFETYPAYException("Method: getPaymentData is not supported by SAFETYPAY"); }
    public function authorize(PSPConfig $obj_PSPConfig, $ticket,$clientInfo=null) { throw new SAFETYPAYException("Method: authorize is not supported by SAFETYPAY"); }
    public function getPSPID() { return Constants::iSAFETYPAY_AGGREGATOR; }
    public function getPaymentMethods(PSPConfig $obj_PSPConfig)
    {
        $activePaymentMethods =  parent::getPaymentMethods($obj_PSPConfig);
        $aStatisticalData = $this->getStatisticalData('issuing_bank_%');
        $sortable = array();
        if(is_object($activePaymentMethods->{'active-payment-menthods'}->{'payment-method'}) && count($activePaymentMethods->{'active-payment-menthods'}->{'payment-method'}) > 1){
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

function compare_usage($a, $b)
{
    if((int)$a->usage === (int)$b->usage)
        return strnatcmp($a->displayName, $b->displayName);
    return ((float) $a->usage < (float) $b->usage);
}