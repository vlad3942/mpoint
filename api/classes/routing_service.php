<?php

class RoutingService extends General
{
    /**
     * Data object with the Client Configuration
     *
     * @var TxnInfo
     */
    private $_obj_ClientConfig;

    /**
     * Data object with the ClientInfo Configuration
     *
     * @var ClientInfo
     */
    private $_obj_ClientInfo;

    /**
     * Data array with Connection Information for the specific PSP
     *
     * @var array
     */
    protected $aCONN_INFO;

    /**
     * Data object with the Payment Methods Information
     *
     * @var TxnInfo
     */
    private $_obj_TxnInfo;

    /**
     * Default Constructor
     *
     * @param	ClientConfig $clientConfig 		Reference to the Data object with the client information
     * @param	ClientInfo $obj_ClientInfo 	    Reference to the Data object with the clientInfo configuration
     * @param 	HTTPConnInfo $obj_ConnInfo 	    Reference to the HTTP connection information
     * @param   SimpleDOMElement $obj_InitInfo  Initialize payment request transaction information
     */
    public function __construct(ClientConfig $clientConfig, ClientInfo $obj_ClientInfo, HTTPConnInfo &$obj_ConnInfo, SimpleDOMElement $obj_TxnInfo)
    {
        $this->_obj_ClientConfig = $clientConfig;
        $this->_obj_ClientInfo = $obj_ClientInfo;
        $this->aCONN_INFO = $obj_ConnInfo;
        $this->_obj_TxnInfo = $obj_TxnInfo;
    }

    /**
     * Produces a list of eligible payment methods.
     *
     * @return 	SimpleDOMElement $obj_XML   List of payment methods/cards
     */
    public function getPaymentMethods()
    {
        $b = '<?xml version="1.0" encoding="UTF-8"?>';
        $b .= '<payment_method_search_criteria>';
        $b .= '<transaction>';
        $b .= '<amount>';
        if(empty($this->_obj_TxnInfo->transaction->amount)===false)
        {
            $b .= '<value>'.$this->_obj_TxnInfo->transaction->amount.'</value>';
        }
        $b .= '<country_id>'.$this->_obj_TxnInfo->transaction->amount["country-id"].'</country_id>';
        if(empty($this->_obj_TxnInfo->transaction->amount["currency-id"])===false)
        {
            $b .= '<currency_id>'.$this->_obj_TxnInfo->transaction->amount["currency-id"].'</currency_id>';
        }
        $b .= '</amount>';
        if(empty($this->_obj_TxnInfo->transaction["type-id"])===false)
        {
            $b .= '<type_id>'.$this->_obj_TxnInfo->transaction["type-id"].'</type_id>';
        }
        if(empty($this->_obj_TxnInfo->transaction["order-no"])===false)
        {
            $b .= '<order_no>'.$this->_obj_TxnInfo->transaction["order-no"].'</order_no>';
        }
        $b .= '</transaction>';
        $b .= '<client_info>';
        $b .=  $this->_obj_ClientInfo->toAttributeLessXML();
        $b .= '<client_id>'.$this->_obj_TxnInfo["client-id"].'</client_id>';
        $b .= '</client_info>';
        $b .= '</payment_method_search_criteria>';
        $obj_XML = null;

        try
        {
            $path = $this->aCONN_INFO["paths"]["get-payment-methods"];
            $aURLInfo = parse_url($this->_obj_ClientConfig->getMESBURL() );
            $obj_ConnInfo =  new HTTPConnInfo ($this->aCONN_INFO["protocol"], $aURLInfo["host"], $this->aCONN_INFO["port"], $this->aCONN_INFO["timeout"], $path, $this->aCONN_INFO["method"], $this->aCONN_INFO["contenttype"], $this->_obj_ClientConfig->getUsername(), $this->_obj_ClientConfig->getPassword() );
            $obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
            $obj_HTTP->connect();
            $code = $obj_HTTP->send($this->constHTTPHeaders(), $b);
            $obj_HTTP->disConnect();
            if ($code == 200)
            {
                $obj_XML = simplexml_load_string($obj_HTTP->getReplyBody() );
            }
            else { throw new mPointException("Could not fetch payment card list responded with HTTP status code: ". $code. " and body: ". $obj_HTTP->getReplyBody(), $code ); }
        }
        catch (mPointException $e)
        {
            trigger_error("construct XML failed with code: ". $e->getCode(). " and message: ". $e->getMessage(), E_USER_ERROR);
        }

        return $obj_XML;
    }

}