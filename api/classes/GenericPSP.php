<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package: api\.classes
 * File Name:GenericPSP.php
 */

namespace api\classes;

use ClientInfo;
use Constants;
use PSPConfig;
use RDB;
use SimpleXMLElement;
use TranslateText;
use TxnInfo;

class GenericPSP extends \CPMPSP
{
    private int $PSP_ID = -1;
    public function __construct(RDB $oDB, \api\classes\core\TranslateText $oTxt, TxnInfo $oTI, array $aConnInfo, PSPConfig $obj_PSPConfig = null, ClientInfo $oClientInfo = null, int $pspId = -1)
    {
        parent::__construct($oDB, $oTxt, $oTI, $aConnInfo, $obj_PSPConfig, $oClientInfo);
        $this->PSP_ID = $pspId;
    }

    /**
     * @throws \mPointException
     */
    public function capture($iAmount = -1)
    {
        if(isset($this->aCONN_INFO["paths"]["capture"]) === false)
        {
            throw new \mPointException('Method Capture is not supported by Provider id '. $this->PSP_ID .', Name '. $this->getPSPConfig()->getName());
        }
        return parent::capture($iAmount); 
    }

    /**
     * @throws \mPointException
     */
    public function refund($iAmount = -1, $iStatus = null)
    {
        if(isset($this->aCONN_INFO["paths"]["refund"]) === false)
        {
            throw new \mPointException('Method Refund is not supported by Provider id '. $this->PSP_ID .', Name '. $this->getPSPConfig()->getName());
        }
        return parent::refund($iAmount, $iStatus); 
    }

    /**
     * @throws \mPointException
     */
    public function void($iAmount = -1)
    {
        if(isset($this->aCONN_INFO["paths"]["void"]) === false)
        {
            throw new \mPointException('Method Void is not supported by Provider id '. $this->PSP_ID .', Name '. $this->getPSPConfig()->getName());
        }
        return parent::void($iAmount); 
    }

    /**
     * @throws \mPointException
     * @throws \TxnInfoException
     */
    public function status()
    {
        if(isset($this->aCONN_INFO["paths"]["status"]) === false)
        {
            throw new \mPointException('Method Status is not supported by Provider id '. $this->PSP_ID .', Name '. $this->getPSPConfig()->getName());
        }
        return parent::status(); 
    }

    /**
     * @throws \PaymentProcessorInitializeException
     * @throws \mPointException
     */
    public function initialize(PSPConfig $obj_PSPConfig, $euaid = -1, $sc = false, $card_type_id = -1, $card_token = '', $obj_BillingAddress = NULL, ClientInfo $obj_ClientInfo = NULL, $authToken = NULL, $cardName = '', $aWalletCardSchemes = array())
    {
        if(isset($this->aCONN_INFO["paths"]["initialize"]) === false)
        {
            throw new \mPointException('Method Initialize is not supported by Provider id '. $this->PSP_ID .', Name '. $this->getPSPConfig()->getName());
        }
        return parent::initialize($obj_PSPConfig, $euaid, $sc, $card_type_id, $card_token, $obj_BillingAddress, $obj_ClientInfo, $authToken, $cardName, $aWalletCardSchemes); 
    }

    /**
     * @throws \mPointException
     */
    public function authorize(PSPConfig $obj_PSPConfig, $obj_Card, $clientInfo = null)
    {
        if(isset($this->aCONN_INFO["paths"]["auth"]) === false)
        {
            throw new \mPointException('Method Authorize is not supported by Provider id '. $this->PSP_ID .', Name '. $this->getPSPConfig()->getName());
        }
        return parent::authorize($obj_PSPConfig, $obj_Card, $clientInfo); 
    }

    /**
     * @throws \mPointException
     */
    public function tokenize(array $aConnInfo, PSPConfig $obj_PSPConfig, $obj_Card)
    {
        if(isset($this->aCONN_INFO["paths"]["tokenize"]) === false)
        {
            throw new \mPointException('Method Tokenize is not supported by Provider id '. $this->PSP_ID .', Name '. $this->getPSPConfig()->getName());
        }
        return parent::tokenize($aConnInfo, $obj_PSPConfig, $obj_Card); 
    }

    /**
     * @throws \mPointException
     */
    public function redeem(string $iVoucherID, float $iAmount = -1)
    {
        if(isset($this->aCONN_INFO["paths"]["redeem"]) === false)
        {
            throw new \mPointException('Method Redeem is not supported by Provider id '. $this->PSP_ID .', Name '. $this->getPSPConfig()->getName());
        }
        return parent::redeem($iVoucherID, $iAmount); 
    }

    /**
     * @throws \mPointException
     */
    public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode = Constants::sPAYMENT_DATA_FULL)
    {
        if(isset($this->aCONN_INFO["paths"]["get-payment-data"]) === false)
        {
            throw new \mPointException('Method Get-payment-data is not supported by Provider id '. $this->PSP_ID .', Name '. $this->getPSPConfig()->getName());
        }

        $paymentData = "";
        $obj_XML = simpledom_load_string(parent::getPaymentData($obj_PSPConfig, $obj_Card, $mode));
        if (count($obj_XML->{'payment-data'}) == 1) {
            if ($mode == Constants::sPAYMENT_DATA_SUMMARY) {
                unset($obj_XML->{'payment-data'}->card->{'card-number'});
                unset($obj_XML->{'payment-data'}->card->{'expiry'});
            }

            $paymentData = $obj_XML->asXML();
        }
        return $paymentData;
    }

    /**
     * @throws \mPointException
     */
    public function getPaymentMethods(PSPConfig $obj_PSPConfig)
    {
        if(isset($this->aCONN_INFO["paths"]["get-payment-methods"]) === false)
        {
            throw new \mPointException('Method Get-payment-methods is not supported by Provider id '. $this->PSP_ID .', Name '. $this->getPSPConfig()->getName());
        }

        $activePaymentMethods =  parent::getPaymentMethods($obj_PSPConfig);
        $aStatisticalData = $this->getStatisticalData('issuing_bank_%');
        $sortable = array();
        $paymentMethods = '';
        if($activePaymentMethods->{'active-payment-methods'}->{'payment-method'}){
            $paymentMethods = $activePaymentMethods->{'active-payment-methods'}->{'payment-method'};
                }
        else{
            $paymentMethods = $activePaymentMethods->{'active-payment-menthods'}->{'payment-method'};
        }
        if(is_object($paymentMethods) && count($paymentMethods) >= 1){
            foreach ($paymentMethods as $node) {
                $issuingBank = strtolower($node->issuingBank);
                $usageCount = (int)$aStatisticalData['issuing_bank_' . $issuingBank];
                $node->addChild('usage', $usageCount);
                $sortable[] = $node;
            }
        }
        usort($sortable,   'compare_usage');
        $newSortedList = "<root><active-payment-methods>";
        foreach ($sortable as $node)
        {
            unset($node->usage);
            $newSortedList .= $node->asXML();
        }
        $newSortedList .= "</active-payment-methods></root>";
        $sxml = simplexml_load_string($newSortedList);
        return $sxml;
    }

    /**
     * @throws \mPointException
     */
    public function authenticate($xml, $obj_Card, $obj_ClientInfo = null)
    {
        if(isset($this->aCONN_INFO["paths"]["authenticate"]) === false)
        {
            throw new \mPointException('Method Authenticate is not supported by Provider id '. $this->PSP_ID .', Name '. $this->getPSPConfig()->getName());
        }
        return parent::authenticate($xml, $obj_Card, $obj_ClientInfo); 
    }

    /**
     * @throws \mPointException
     */
    public function generate_receipt(): bool
    {
        if(isset($this->aCONN_INFO["paths"]["generate-receipt"]) === false)
        {
            throw new \mPointException('Method Generate-receipt is not supported by Provider id '. $this->PSP_ID .', Name '. $this->getPSPConfig()->getName());
        }
        return parent::generate_receipt(); 
    }

    public function callback(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, SimpleXMLElement $obj_Status, $purchaseDate = null)
    {
        if(isset($this->aCONN_INFO["paths"]["callback"]) === false)
        {
            throw new \mPointException('Method Callback is not supported by Provider id '. $this->PSP_ID .', Name '. $this->getPSPConfig()->getName());
        }
        return parent::callback($obj_PSPConfig, $obj_Card, $obj_Status, $purchaseDate); // TODO: Change the autogenerated stub
    }

    public function postStatus($obj_Elem)
    {
        if(isset($this->aCONN_INFO["paths"]["post-status"]) === false)
        {
            throw new \mPointException('Method postStatus is not supported by Provider id '. $this->PSP_ID .', Name '. $this->getPSPConfig()->getName());
        }
        return parent::postStatus($obj_Elem); // TODO: Change the autogenerated stub
    }


    public function getPSPID()
    {
        return $this->PSP_ID;
    }

    function compare_usage($a, $b)
    {
        if((int)$a->usage === (int)$b->usage)
            return strnatcmp($a->displayName, $b->displayName);
        return ((float) $a->usage < (float) $b->usage);
    }
}