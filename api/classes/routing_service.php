<?php

class RoutingService extends General
{
    /**
     * Data object with the Transaction Information
     *
     * @var TxnInfo
     */
    private $_obj_TxnInfo;

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
     * Unique Card Type Id used for the payment
     *
     * @var integer
     */
    private $_iCardTypeId;

    /**
     * Unique Card issuer identification-number
     *
     * @var integer
     */
    private $_iIssuerIdentificationNumber;

    /**
     * Unique Card name used for the payment
     *
     * @var string
     */
    private $_sCardName = '';

    /**
     * Data object with the failed payment methods Configuration
     *
     * @var FailedPaymentMethodConfig
     */
    private $_obj_FailedPaymentMethods;

    /**
     * Hold unique id of wallet being chosen for payment
     *
     * @var FailedPaymentMethodConfig
     */
    private $_iWalletId;

    /**
     * Default Constructor
     *
     * @param	ClientConfig $clientConfig 		Reference to the Data object with the client information
     * @param	ClientInfo $obj_ClientInfo 	    Reference to the Data object with the clientInfo configuration
     * @param 	HTTPConnInfo $obj_ConnInfo 	    Reference to the HTTP connection information
     * @param   SimpleDOMElement $obj_InitInfo  Initialize payment request transaction information
     */
    public function __construct(TxnInfo $obj_TxnInfo, ClientInfo $obj_ClientInfo, &$obj_ConnInfo, $clientId, $countryId, $currencyId = NULL, $amount = NULL, $cardTypeId = NULL, $issuerIdentificationNumber = NULL, $cardName = NULL, $obj_FailedPaymentMethod = NULL, ?int $walletId = NULL)
    {
        $this->_obj_TxnInfo = $obj_TxnInfo;
        $this->_obj_ClientInfo = $obj_ClientInfo;
        $this->aCONN_INFO = $obj_ConnInfo;
        $this->_iClientId = $clientId;
        $this->_iCountryId = $countryId;
        $this->_iCurrencyId = $currencyId;
        $this->_iAmount = $amount;
        $this->_iCardTypeId = $cardTypeId;
        $this->_iIssuerIdentificationNumber = $issuerIdentificationNumber;
        $this->_sCardName = $cardName;
        $this->_obj_FailedPaymentMethods = $obj_FailedPaymentMethod;
        $this->_iWalletId = $walletId;
    }

    /**
     * Produces a list of eligible payment methods.
     *
     * @return 	SimpleDOMElement $obj_XML   List of payment methods/cards
     */
    public function getPaymentMethods()
    {
        $body = '<?xml version="1.0" encoding="UTF-8"?>';
        $body .= '<payment_method_search_criteria>';
        $body .= '<event_id>'.$this->_obj_TxnInfo->getID().'</event_id>';
        $body .= '<account_id>'.$this->_obj_TxnInfo->getClientConfig()->getAccountConfig()->getID().'</account_id>';
        $body .= '<transaction>';
        $body .= '<type_id>'.$this->_obj_TxnInfo->getTypeID().'</type_id>';
        $body .= '<product_type>'.$this->_obj_TxnInfo->getProductType().'</product_type>';
        $body .= '<amount>';
        if(empty($this->_iAmount)===false)
        {
            $body .= '<value>'.$this->_iAmount.'</value>';
        }
        $body .= '<country_id>'.$this->_iCountryId.'</country_id>';
        if(empty($this->_iCurrencyId)===false)
        {
            $body .= '<currency_id>'.$this->_iCurrencyId.'</currency_id>';
        }else{
            $body .= '<currency_id>'.$this->_obj_TxnInfo->getCurrencyConfig()->getID().'</currency_id>';
        }
        $body .= '<decimal>'.$this->_obj_TxnInfo->getCurrencyConfig()->getDecimals().'</decimal>';
        $body .= '</amount>';
        if(is_array($this->_obj_FailedPaymentMethods) && count($this->_obj_FailedPaymentMethods) > 0 )
        {
            $body .= '<retry_attempts>';
            foreach ($this->_obj_FailedPaymentMethods as $obj_FailedPaymentMethod)
            {
                if (($obj_FailedPaymentMethod instanceof FailedPaymentMethodConfig) === TRUE)
                {
                    $body .= $obj_FailedPaymentMethod->toAttributeLessXML();
                }
            }
            $body .= '</retry_attempts>';
        }
        $body .= $this->toAttributeLessOrderDataXML();
        $body .= '</transaction>';
        $body .= '<client_info>';
        $body .=  $this->_obj_ClientInfo->toAttributeLessXML();
        $body .= '<client_id>'.$this->_iClientId.'</client_id>';
        $body .= '</client_info>';
        $body .= '</payment_method_search_criteria>';
        $obj_XML = '';
        try
        {
            $path = $this->aCONN_INFO["paths"]["get-payment-methods"];
            $aURLInfo = parse_url($this->_obj_TxnInfo->getClientConfig()->getMESBURL() );
            $obj_ConnInfo =  new HTTPConnInfo ($this->aCONN_INFO["protocol"], $aURLInfo["host"], $this->aCONN_INFO["port"], $this->aCONN_INFO["timeout"], $path, $this->aCONN_INFO["method"], $this->aCONN_INFO["contenttype"], $this->_obj_TxnInfo->getClientConfig()->getUsername(), $this->_obj_TxnInfo->getClientConfig()->getPassword() );
            $obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
            $obj_HTTP->connect();
            $code = $obj_HTTP->send($this->constHTTPHeaders(), $body);
            $obj_HTTP->disConnect();
            $obj_XML = simplexml_load_string($obj_HTTP->getReplyBody() );
            return RoutingServiceResponse::produceGetPaymentMethodResponse($obj_XML);
        }
        catch (Exception $e)
        {
            trigger_error("construct XML failed with code: ". $e->getCode(). " and message: ". $e->getMessage(), E_USER_ERROR);
        }
        return null;
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
        $b .= '<account_id>'.$this->_obj_TxnInfo->getClientConfig()->getAccountConfig()->getID().'</account_id>';
        $b .= '<transaction>';
        $b .= '<id>'.$this->_obj_TxnInfo->getID().'</id>';
        $b .= '<product_type>'.$this->_obj_TxnInfo->getProductType().'</product_type>';
        $b .= '<amount>';
        if(empty($this->_iAmount)===false)
        {
            $b .= '<value>'.$this->_iAmount.'</value>';
        }
        $b .= '<country_id>'.$this->_iCountryId.'</country_id>';
        if(empty($this->_iCurrencyId)===false)
        {
            $b .= '<currency_id>'.$this->_iCurrencyId.'</currency_id>';
        }else{
            $b .= '<currency_id>'.$this->_obj_TxnInfo->getCurrencyConfig()->getID().'</currency_id>';
        }
        $b .= '<decimal>'.$this->_obj_TxnInfo->getCurrencyConfig()->getDecimals().'</decimal>';
        $b .= '</amount>';
        $b .= '<card>';
        $b .= '<id>'.$this->_iCardTypeId.'</id>';
        $b .= '<type_id>VISA</type_id>';
        $b .= '<amount>';
        if(empty($this->_iAmount)===false)
        {
            $b .= '<value>'.$this->_iAmount.'</value>';
        }
        $b .= '<country_id>'.$this->_iCountryId.'</country_id>';
        if(empty($this->_iCurrencyId)===false)
        {
            $b .= '<currency_id>'.$this->_iCurrencyId.'</currency_id>';
        }else{
            $b .= '<currency_id>'.$this->_obj_TxnInfo->getCurrencyConfig()->getID().'</currency_id>';
        }
        $b .= '</amount>';
        $b .= '<issuer_identification_number>'.$this->_iIssuerIdentificationNumber.'</issuer_identification_number>';
        $b .= '</card>';
        if(empty($this->_iWalletId)===false)
        {
            $b .= '<wallet_id>'.$this->_iWalletId.'</wallet_id>';
        }
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
            $aURLInfo = parse_url($this->_obj_TxnInfo->getClientConfig()->getMESBURL() );
            $obj_ConnInfo =  new HTTPConnInfo ($this->aCONN_INFO["protocol"], $aURLInfo["host"], $this->aCONN_INFO["port"], $this->aCONN_INFO["timeout"], $path, $this->aCONN_INFO["method"], $this->aCONN_INFO["contenttype"], $this->_obj_TxnInfo->getClientConfig()->getUsername(), $this->_obj_TxnInfo->getClientConfig()->getPassword() );
            $obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
            $obj_HTTP->connect();
            $code = $obj_HTTP->send($this->constHTTPHeaders(), $b);
            $obj_HTTP->disConnect();
            $obj_XML = simplexml_load_string($obj_HTTP->getReplyBody());
            if($obj_XML instanceof SimpleXMLElement){
                return RoutingServiceResponse::produceGetRouteResponse($obj_XML);
            }
        }
        catch (Exception $e)
        {
            trigger_error("construct XML failed with code: ". $e->getCode(). " and message: ". $e->getMessage(), E_USER_ERROR);
        }
        return $obj_XML;
    }

    /**
     * Store all alternate payment routes to authorize transaction if psp1 fails during authorize
     * and return primary route to authorize transaction
     *
     * @return (integer) $firstPSP	Primary route to authorize transaction
     */
    public function getAndStoreRoute(PaymentRoute $objTxnRoute)
    {
        $obj_RoutingServiceResponse = $this->getRoute();
        $aRoutes = [];
        if($obj_RoutingServiceResponse instanceof RoutingServiceResponse)
        {
            $aObj_Route = $obj_RoutingServiceResponse->getRoutes();
            $aRoutes = $aObj_Route->routes->route;
        }
        $firstPSP = -1;
        if (count ( $aRoutes ) > 0) {
            $aAlternateRoutes = array();
            foreach ($aRoutes as $oRoute) {
                if(empty($oRoute->preference) === false){
                    if ($oRoute->preference == 1) {
                        $firstPSP = $oRoute->id;
                    }
                    $aAlternateRoutes[] = array(
                        'id' => $oRoute->id,
                        'preference' => $oRoute->preference
                    );
                }else{
                    $firstPSP = $oRoute->id;
                }
            }
            // Store alternate routes to authorize transaction if psp1 fails during authorize
            $objTxnRoute->setAlternateRoute($aAlternateRoutes);
        }
        return (int)$firstPSP;
    }

    private function toAttributeLessOrderDataXML()
    {
        $objOrderConfig = $this->_obj_TxnInfo->getOrderConfigs();
        $xml = '';
        if( empty($objOrderConfig) === false )
        {
            $xml .= '<orders>';
            $xml .= '<line_item>';
            $xml .= '<product>';
            foreach ($objOrderConfig as $obj_OrderInfo)
            {
                if( ($obj_OrderInfo instanceof OrderInfo) === true )
                {
                    $xml .= '<name>'. $obj_OrderInfo->getProductName() .'</name>';
                    $xml .= '<sku>'. $obj_OrderInfo->getProductSKU() .'</sku>';
                    $xml .= '<description>'. $obj_OrderInfo->getProductDesc() .'</description>';

                    if(count($obj_OrderInfo->getFlightConfigs()) > 0 )
                    {
                        $xml .= '<airline_data>';
                        $xml .= '<flight_details>';
                        foreach ($obj_OrderInfo->getFlightConfigs() as $flight_Obj)
                        {
                            if (($flight_Obj instanceof FlightInfo) === TRUE)
                            {
                                $xml .= '<flight_detail>';
                                $xml .= '<tag>'.$flight_Obj->getATag().'</tag>';
                                $xml .= '<trip_count>'.$flight_Obj->getATripCount().'</trip_count>';
                                $xml .= '<service_level>'.$flight_Obj->getAServiceLevel().'</service_level>';
                                $xml .= '<service_class>' . $flight_Obj->getServiceClass () . '</service_class>';
                                $xml .= '<departure_date>' . date("Y-m-d\Th:i:s\Z", strtotime($flight_Obj->getDepartureDate ())) . '</departure_date>';
                                $xml .= '<arrival_date>' . date("Y-m-d\Th:i:s\Z", strtotime($flight_Obj->getArrivalDate ())) . '</arrival_date>';
                                $xml .= '<departure_country>' . $flight_Obj->getDepartureCountry () . '</departure_country>';
                                $xml .= '<arrival_country>' . $flight_Obj->getArrivalCountry () . '</arrival_country>';
                                $xml .= '<time_zone>' . $flight_Obj->getTimeZone () . '</time_zone>';
                                $xml .= '</flight_detail>';
                            }
                        }
                        $xml .= '</flight_details>';
                        $xml .= '</airline_data>';
                    }
                }
            }
            $xml .= '</product>';
            $xml .= '</line_item>';
            $xml .= '<amount>';
            $xml .= '<country_id>'. $obj_OrderInfo->getCountryID() .'</country_id>';
            $xml .= '<value>'. $obj_OrderInfo->getAmount(). '</value>';
            $xml .= '</amount>';
            $xml .= '</orders>';
        }
        return $xml;
    }

}