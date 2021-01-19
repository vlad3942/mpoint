<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anna Lagad
 * Copyright: Cellpoint Digital
 * Link: http://www.cellpointdigital.com
 * Project: mPoint
 * File Name:client_payment_metadata.php
 */

class ClientPaymentMetadata
{
    /**
     * Configuration for the client payment route
     * @var ClientRouteConfig
     */
    private $_obj_ClientRouteConfig;

    /**
     * Configuration for the client payment route country currency
     * @var ClientCountryCurrencyConfig
     */
    private $_obj_ClientCountryCurrencyConfig;

    /**
     * Configuration for the client supported payment methods
     * @var ClientPaymentMethodConfig
     */
    private $_obj_ClientPaymentMethodConfig;

    /**
     * Configuration for the client route feature
     * @var RouteFeature
     */
    private $_obj_ClientRouteFeatureConfig;

    /**
     * Default Constructor
     *
     * @param 	ClientRouteConfig $aObj_ClientRouteConfig 						 Hold Configuration for the client payment route
     * @param 	ClientCountryCurrencyConfig $aObj_ClientCountryCurrencyConfig 	 Hold Configuration for the client payment route country currency
     * @param 	ClientPaymentMethodConfig $aObj_ClientPaymentMethodConfig 		 Hold Configuration for the client supported payment methods
     * @param 	RouteFeature $aObj_ClientRouteFeatureConfig 					 Hold Configuration for the client route feature
     */
	public function __construct($aObj_ClientRouteConfig, $aObj_ClientCountryCurrencyConfig, $aObj_ClientPaymentMethodConfig, $aObj_ClientRouteFeatureConfig)
	{
        $this->_obj_ClientRouteConfig = $aObj_ClientRouteConfig;
        $this->_obj_ClientCountryCurrencyConfig = $aObj_ClientCountryCurrencyConfig;
        $this->_obj_ClientPaymentMethodConfig = $aObj_ClientPaymentMethodConfig;
        $this->_obj_ClientRouteFeatureConfig = $aObj_ClientRouteFeatureConfig;
	}

    /**
     * Returns the XML payload of client payment service provider configuration
     * @return 	String
     */
	private function getPaymentProviderAsXML()
    {
        $xml = '<payment_providers>';
        foreach ($this->_obj_ClientRouteConfig as $obj_RC)
        {
            if ( ($obj_RC instanceof ClientRouteConfig) === true)
            {
                $xml .= $obj_RC->toXML();
            }
        }
        $xml .= '</payment_providers>';

        return $xml;
    }

    /**
     * Returns the XML payload of client route country configuration
     * @return 	String
     */
    private function getPaymentCountryAsXML()
    {
        $xml = '<payment_countries>';
        if ( ($this->_obj_ClientCountryCurrencyConfig instanceof ClientCountryCurrencyConfig) === true)
        {
            $xml .= $this->_obj_ClientCountryCurrencyConfig->toCountryAsXML();
        }
        $xml .= '</payment_countries>';

        return $xml;
    }

    /**
     * Returns the XML payload of client route currency configuration
     * @return 	String
     */
    private function getPaymentCurrencyAsXML()
    {
        $xml = '<payment_currencies>';
        if ( ($this->_obj_ClientCountryCurrencyConfig instanceof ClientCountryCurrencyConfig) === true)
        {
            $xml .= $this->_obj_ClientCountryCurrencyConfig->toCurrencyAsXML();
        }
        $xml .= '</payment_currencies>';

        return $xml;
    }

    /**
     * Returns the XML payload of client supported payment methods
     * @return 	String
     */
    public function getPaymentMethodsAsXML(RDB &$oDB = NULL)
    {
        $xml = '<payment_methods>';
        foreach ($this->_obj_ClientPaymentMethodConfig as $obj_PM)
        {
            if ( ($obj_PM instanceof ClientPaymentMethodConfig) === true)
            {
                $xml .= $obj_PM->toPaymnetMethodAsXML();
            }
        }
        $xml .= '</payment_methods>';

        return $xml;
    }

    /**
     * Returns the XML payload of client route feature configuration
     * @return 	String
     */
    private function getRouteFeatureAsXML()
    {
        $xml = '<route_features>';
        foreach ($this->_obj_ClientRouteFeatureConfig as $obj_RF)
        {
            if ( ($obj_RF instanceof RouteFeature) === true)
            {
                $xml .= $obj_RF->toXML();
            }
        }
        $xml .= '</route_features>';

        return $xml;
    }

	public function toXML()
    {
        $xml = '<payment_metadata>';
        $xml .= $this->getPaymentMethodsAsXML();
        $xml .= $this->getPaymentProviderAsXML();
        $xml .= $this->getPaymentCurrencyAsXML();
        $xml .= $this->getPaymentCountryAsXML();
        $xml .= $this->getRouteFeatureAsXML();
        $xml .= '</payment_metadata>';
        return $xml;
    }

    /**
     * Produces a new instance of a Client Payment Metadata
     *
     * @param 	RDB $oDB 		    Reference to the Database Object that holds the active connection to the mPoint Database
     * @param 	integer $clientId 	Unique ID for the Client performing the request
     * @return 	ClientPaymentMetadata
     */
    public static function produceConfig(RDB $oDB, $clientId)
    {
        if(empty($clientId) === false)
        {
            $aObj_ClientRouteConfig = ClientRouteConfig::produceConfig($oDB, $clientId);
            $aObj_ClientCountryCurrencyConfig = ClientCountryCurrencyConfig::produceConfig($oDB, $clientId);
            $aObj_ClientPaymentMethodConfig = ClientPaymentMethodConfig::producePaymentMethodConfig($oDB, $clientId);
            $aObj_ClientRouteFeatureConfig = RouteFeature::produceConfig($oDB, $clientId);
        }
        return new ClientPaymentMetadata($aObj_ClientRouteConfig, $aObj_ClientCountryCurrencyConfig, $aObj_ClientPaymentMethodConfig, $aObj_ClientRouteFeatureConfig);
    }
	

}
?>