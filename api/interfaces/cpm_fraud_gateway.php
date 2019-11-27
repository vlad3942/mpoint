<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package:
 * File Name:cpm_acquirer.php
 */

abstract class CPMFRAUDGATEWAY extends CPMPSP
{
    public function __construct(RDB $oDB, TranslateText $oTxt, TxnInfo $oTI, array $aConnInfo, PSPConfig $obj_PSPConfig=null)
    {
        parent::__construct($oDB, $oTxt, $oTI, $aConnInfo, $obj_PSPConfig);
    }

    public function fraudCheck(array $aConnInfo, PSPConfig $obj_PSPConfig, $obj_Card)
    {
        $aMerchantAccountDetails = $this->genMerchantAccountDetails();
        $b  = '<?xml version="1.0" encoding="UTF-8"?>';
        $b .= '<root>';
        $b .= '<tokenize client-id="'. $this->getClientConfig()->getID(). '" account="'. $this->getClientConfig()->getAccountConfig()->getID(). '" store-card="'. parent::bool2xml($sc) .'">';
        $b .= $obj_PSPConfig->toXML(Constants::iPrivateProperty, $aMerchantAccountDetails);
        $b .= $this->_constTxnXML();
        $b .= $this->_constNewCardAuthorizationRequest($obj_Card);
        $b .= '</tokenize>';
        $b .= '</root>';
        $obj_XML = null;
        try
        {
            $obj_ConnInfo = $this->_constConnInfo($this->aCONN_INFO["paths"]["fraud-check"]);
            $returnCode = 999;
            $obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
            $obj_HTTP->connect();
            $code = 200;//$obj_HTTP->send($this->constHTTPHeaders(), $b);
            $obj_HTTP->disConnect();
            if ($code == 200)
            {
                $obj_XML = simplexml_load_string($obj_HTTP->getReplyBody() );
                if($obj_XML->status['code'] == '100')
                {
                    $this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_FRAUD_CHECK_COMPLETE_STATE, $obj_HTTP->getReplyBody());
                    $returnCode = 100;
                }
                else
                {
                    $this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_FRAUD_CHECK_FAILURE_STATE, $obj_HTTP->getReplyBody());
                    $returnCode = 999;
                }
            }
            else { throw new mPointException("Fraud check failed with Processor: ". $obj_PSPConfig->getName() ." responded with HTTP status code: ". $code. " and body: ". $obj_HTTP->getReplyBody(), $code ); }

        }
        catch (mPointException $e)
        {
            trigger_error("Unable to connect to fraud check service for txnID : ". $this->getTxnInfo()->getID(). " failed with code: ". $e->getCode(). " and message: ". $e->getMessage(), E_USER_ERROR);
            //re-throw the exception to the calling controller.
            throw $e;
        }
        return $returnCode;
    }
}