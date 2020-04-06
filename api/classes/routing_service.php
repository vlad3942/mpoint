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
     * Hold Unique client ID
     *
     * @var integer
     */
    private $_iClientId;

    /**
     * Hold Unique country ID
     *
     * @var integer
     */
    private $_iCountryId;

    /**
     * Hold currency ID for respective country
     *
     * @var integer
     */
    private $_iCurrencyId;

    /**
     * Hold transaction amount
     *
     * @var integer
     */
    private $_iAmount;

    /**
     * Unique ID for the Transaction
     *
     * @var integer
     */
    private $_iTransactionId;

    /**
     * Product Type of transaction
     *
     * @var integer
     */
    private $_iProductType;

    /**
     * Unique Card Type Id used for the payment
     *
     * @var integer
     */
    private $_iCardTypeId;

    /**
     * Default Constructor
     *
     * @param	ClientConfig $clientConfig 		Reference to the Data object with the client information
     * @param	ClientInfo $obj_ClientInfo 	    Reference to the Data object with the clientInfo configuration
     * @param 	HTTPConnInfo $obj_ConnInfo 	    Reference to the HTTP connection information
     * @param   SimpleDOMElement $obj_InitInfo  Initialize payment request transaction information
     */
    public function __construct(ClientConfig $clientConfig, ClientInfo $obj_ClientInfo, HTTPConnInfo &$obj_ConnInfo, $clientId, $countryId, $currencyId = NULL, $amount = NULL, $transactionId = NULL, $cardTypeId = NULL, $productType = NULL)
    {
        $this->_obj_ClientConfig = $clientConfig;
        $this->_obj_ClientInfo = $obj_ClientInfo;
        $this->aCONN_INFO = $obj_ConnInfo;
        $this->_iClientId = $clientId;
        $this->_iCountryId = $countryId;
        $this->_iCurrencyId = $currencyId;
        $this->_iAmount = $amount;
        $this->_iTransactionId = $transactionId;
        $this->_iProductType = $productType;
        $this->_iCardTypeId = $cardTypeId;
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
        if(empty($this->_iAmount)===false)
        {
            $b .= '<value>'.$this->_iAmount.'</value>';
        }
        $b .= '<country_id>'.$this->_iCountryId.'</country_id>';
        if(empty($this->_iCurrencyId)===false)
        {
            $b .= '<currency_id>'.$this->_iCurrencyId.'</currency_id>';
        }
        $b .= '</amount>';
        $b .= '</transaction>';
        $b .= '<client_info>';
        $b .=  $this->_obj_ClientInfo->toAttributeLessXML();
        $b .= '<client_id>'.$this->_iClientId.'</client_id>';
        $b .= '</client_info>';
        $b .= '</payment_method_search_criteria>';
        $obj_XML = '';
        try
        {
            $path = $this->aCONN_INFO["paths"]["get-payment-methods"];
            $aURLInfo = parse_url($this->_obj_ClientConfig->getMESBURL() );
            $obj_ConnInfo =  new HTTPConnInfo ($this->aCONN_INFO["protocol"], $aURLInfo["host"], $this->aCONN_INFO["port"], $this->aCONN_INFO["timeout"], $path, $this->aCONN_INFO["method"], $this->aCONN_INFO["contenttype"], $this->_obj_ClientConfig->getUsername(), $this->_obj_ClientConfig->getPassword() );
            $obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
            $obj_HTTP->connect();
            $code = $obj_HTTP->send($this->constHTTPHeaders(), $b);
            $obj_HTTP->disConnect();
            $obj_XML = simplexml_load_string($obj_HTTP->getReplyBody() );
            return RoutingServiceResponse::produceGetPaymentMethodResponse($obj_XML);
        }
        catch (Exception $e)
        {
            trigger_error("construct XML failed with code: ". $e->getCode(). " and message: ". $e->getMessage(), E_USER_ERROR);
            return $obj_XML;
        }
    }

    /**
     * Produces a list of the psp/accquirer which are best suited for the current payment transaction
     *
     * @return 	SimpleDOMElement $obj_XML  List of the psp/accquirer
     */
    public function getRoute()
    {
        $b = '<?xml version="1.0" encoding="UTF-8"?>';
        $b .= '<payment_route_search_criteria>';
        $b .= '<transaction>';
        $b .= '<id>'.$this->_iTransactionId.'</id>';
        $b .= '<product_type>'.$this->_iProductType.'</product_type>';
        $b .= '<card>';
        $b .= '<type_id></type_id>';
        $b .= '<amount>';
        if(empty($this->_iAmount)===false)
        {
            $b .= '<value>'.$this->_iAmount.'</value>';
        }
        $b .= '<country_id>'.$this->_iCountryId.'</country_id>';
        if(empty($this->_iCurrencyId)===false)
        {
            $b .= '<currency_id>'.$this->_iCurrencyId.'</currency_id>';
        }
        $b .= '</amount>';
        $b .= '</card>';
        $b .= '</transaction>';
        $b .= '<client_info>';
        $b .=  $this->_obj_ClientInfo->toAttributeLessXML();
        $b .= '<client_id>'.$this->_iClientId.'</client_id>';
        $b .= '</client_info>';
        $b .= '</payment_route_search_criteria>';
        $obj_XML = '';
        try
        {
            $path = $this->aCONN_INFO["paths"]["get-routes"];
            $aURLInfo = parse_url($this->_obj_ClientConfig->getMESBURL() );
            $obj_ConnInfo =  new HTTPConnInfo ($this->aCONN_INFO["protocol"], $aURLInfo["host"], $this->aCONN_INFO["port"], $this->aCONN_INFO["timeout"], $path, $this->aCONN_INFO["method"], $this->aCONN_INFO["contenttype"], $this->_obj_ClientConfig->getUsername(), $this->_obj_ClientConfig->getPassword() );
            $obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
            $obj_HTTP->connect();
            $code = $obj_HTTP->send($this->constHTTPHeaders(), $b);
            $obj_HTTP->disConnect();
            $obj_XML = simplexml_load_string($obj_HTTP->getReplyBody());
            return RoutingServiceResponse::produceGetRouteResponse($obj_XML);
        }
        catch (Exception $e)
        {
            trigger_error("construct XML failed with code: ". $e->getCode(). " and message: ". $e->getMessage(), E_USER_ERROR);
            return $obj_XML;
        }
    }

}