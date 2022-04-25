<?php

use api\classes\core\Product;

/**
 * Created by IntelliJ IDEA.
 * User: Anna Lagad
 * Copyright: Cellpoint Digital
 * Link: http://www.cellpointdigital.com
 * Project: mPoint
 * File Name:ClientPaymentMetadata.php
 */

class ClientPaymentMetadata
{
    /**
     * Configuration for the client payment route
     * @Array ClientRouteConfig
     */
    private array $_obj_ClientRouteConfig;

    /**
     * Configuration for the client payment route country
     * @Array ClientCountryCurrencyConfig
     */
    private array $_obj_ClientCountryConfig;

    /**
     * Configuration for the client payment route currency
     * @Array ClientCountryCurrencyConfig
     */
    private array $_obj_ClientCurrencyConfig;

    /**
     * Configuration for the client supported payment methods
     * @Array ClientPaymentMethodConfig
     */
    private array $_obj_ClientPaymentMethodConfig;

    /**
     * Configuration for the client route feature
     * @Array RouteFeature
     */
    private array $_obj_ClientRouteFeatureConfig;

    /**
     * Object that holds the transaction type configurations
     *
     * @Array TransactionTypeConfig
     */
    private array $_obj_TransactionTypeConfig;

    /**
     * Object that holds the card state configurations
     *
     * @Array CardState
     */
    private array $_obj_CardStateConfig;

    /**
     * Object that holds the Account configurations
     *
     * @Array AccountConfig
     */
    private array $_obj_AccountsConfigurations;

    /**
     * Object that holds the Foreign Exchange Service Type Configurations
     *
     * @Array FxServiceType
     */
    private array $_obj_FxServiceTypeConfig;


    /**
     * @var Product[]
     */
    private array $products;

    /**
     * Default Constructor
     *
     * @param 	?Array ClientRouteConfig $aObj_ClientRouteConfig  						 Hold Configuration for the client payment route
     * @param 	?Array ClientCountryCurrencyConfig $aObj_ClientCountryConfig 	 Hold Configuration for the client payment route country
     * @param 	?Array ClientPaymentMethodConfig $aObj_ClientPaymentMethodConfig 		 Hold Configuration for the client supported payment methods
     * @param 	?Array RouteFeature $aObj_ClientRouteFeatureConfig 					 Hold Configuration for the client route feature
     * @param   ?Array FxServiceType $aObj_FxServiceTypeConfig                          Hold an array of object of Foreign Exchange Service Type Configurations
     * @param   ?Array ClientCountryCurrencyConfig $aObj_ClientCurrencyConfig            Hold Configuration for the client payment route currency
     */
	public function __construct(array $aObj_ClientRouteConfig, array $aObj_ClientCountryConfig, array $aObj_ClientPaymentMethodConfig, array $aObj_ClientRouteFeatureConfig, array $aObj_AccountsConfigurations, array $obj_TransactionTypeConfig, array $aObj_CardStateConfig, array $aObj_FxServiceTypeConfig, array $aObj_ClientCurrencyConfig, array $products)
	{
        $this->_obj_ClientRouteConfig = $aObj_ClientRouteConfig;
        $this->_obj_ClientCountryConfig = $aObj_ClientCountryConfig;
        $this->_obj_ClientCurrencyConfig = $aObj_ClientCurrencyConfig;
        $this->_obj_ClientPaymentMethodConfig = $aObj_ClientPaymentMethodConfig;
        $this->_obj_ClientRouteFeatureConfig = $aObj_ClientRouteFeatureConfig;
        $this->_obj_TransactionTypeConfig = $obj_TransactionTypeConfig;
        $this->_obj_CardStateConfig = $aObj_CardStateConfig;
        $this->_obj_AccountsConfigurations = $aObj_AccountsConfigurations;
        $this->_obj_FxServiceTypeConfig = $aObj_FxServiceTypeConfig;
        $this->products = $products;
	}

    /**
     * Returns the XML payload of Foreign Exchange Service Type Configurations
     *
     * @return 	String
     */
	private function getFxServiceTypeConfigAsXML() : string
    {
        // If not found object Return blank
        if(count($this->_obj_FxServiceTypeConfig) === 0) return '';

        $xml = '<fx_service_types>';
        foreach ($this->_obj_FxServiceTypeConfig as $obj_FxServiceType)
        {
            if ( ($obj_FxServiceType instanceof FxServiceType) === true)
            {
                $xml .= $obj_FxServiceType->toXML();
            }
        }
        $xml .= '</fx_service_types>';

        return $xml;
    }

    /**
     * Returns the XML payload of array of Configurations for the Accounts the Transaction will be associated with
     *
     * @return 	String
     */
    private function getAccountsConfigurationsAsXML() : string
    {
        // If not found object Return blank
        if(count($this->_obj_AccountsConfigurations) === 0) return '';

        $xml = '<account_configurations>';
        foreach ($this->_obj_AccountsConfigurations as $obj_AccountConfig)
        {
            if ( ($obj_AccountConfig instanceof AccountConfig) == true)
            {
                $xml .= $obj_AccountConfig->toAttributeLessXML();
            }
        }
        $xml .= '</account_configurations>';

        return $xml;
    }

    /**
     * Returns the XML payload of array of Configurations for the Card State
     *
     * @return 	String
     */
	private function getCardStateAsXML() : string
    {
        // If not found object Return blank
        if(count($this->_obj_CardStateConfig) === 0) return '';

        $xml = '<card_states>';
        foreach ($this->_obj_CardStateConfig as $obj_CardState)
        {
            if ( ($obj_CardState instanceof CardState) === true)
            {
                $xml .= $obj_CardState->toXML();
            }
        }
        $xml .= '</card_states>';
        return $xml;
    }

    /**
     * Returns the XML payload of array of Configurations for the Transaction Type
     *
     * @return 	String
     */
    private function getTransactionTypeAsXML() : string
    {
        // If not found object Return blank
        if(count($this->_obj_TransactionTypeConfig) === 0) return '';

        $xml = '<transaction_types>';
        foreach ($this->_obj_TransactionTypeConfig as $obj_TransactionType)
        {
            if ( ($obj_TransactionType instanceof TransactionTypeConfig) === true)
            {
                $xml .= $obj_TransactionType->toAttributelessXML();
            }
        }
        $xml .= '</transaction_types>';
        return $xml;
    }

    /**
     * Returns the XML payload of client payment service provider configuration
     * @return 	String
     */
	private function getPaymentProviderAsXML() : string
    {
        // If not found object Return blank
        if(count($this->_obj_ClientRouteConfig) === 0) return '';

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
    private function getPaymentCountryAsXML() : string
    {
        // If not found object Return blank
        if(count($this->_obj_ClientCountryConfig) === 0) return '';

        $xml = '<payment_countries>';
        foreach ($this->_obj_ClientCountryConfig as $obj_ClientCountryConfig)
        {
            if (($obj_ClientCountryConfig instanceof ClientCountryCurrencyConfig) === true)
            {
                $xml .= $obj_ClientCountryConfig->toCountryAsXML();
            }
        }
        $xml .= '</payment_countries>';

        return $xml;
    }

    /**
     * Returns the XML payload of client route currency configuration
     * @return 	String
     */
    private function getPaymentCurrencyAsXML() : string
    {
        // If not found object Return blank
        if(count($this->_obj_ClientCurrencyConfig) === 0) return '';

        $xml = '<payment_currencies>';
        foreach ($this->_obj_ClientCurrencyConfig as $obj_ClientCurrencyConfig)
        {
            if (($obj_ClientCurrencyConfig instanceof ClientCountryCurrencyConfig) === true)
            {
                $xml .= $obj_ClientCurrencyConfig->toCurrencyAsXML();
            }
        }
        $xml .= '</payment_currencies>';

        return $xml;
    }

    /**
     * Returns the XML payload of client supported payment methods
     * @return 	String
     */
    public function getPaymentMethodsAsXML(RDB &$oDB = NULL) : string
    {
        // If not found object Return blank
        if(count($this->_obj_ClientPaymentMethodConfig) === 0) return '';

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
    private function getRouteFeatureAsXML() : string
    {
        // If not found object Return blank
        if(count($this->_obj_ClientRouteFeatureConfig) === 0) return '';

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

    /**
     * Returns the xml string of product list
     * @return 	String
     */
    private function getProductsAsXML() : string
    {
        // If not found object Return blank
        if(count($this->products) === 0) return '';

        $xml = '<products>';
        foreach ($this->products as $product)
        {
            if ( ($product instanceof Product) === true)
            {
                $xml .= $product->toXML();
            }
        }
        $xml .= '</products>';

        return $xml;
    }

	public function toXML() : string
    {
        $xml = '<payment_metadata>';
        $xml .= $this->getPaymentMethodsAsXML();
        $xml .= $this->getPaymentProviderAsXML();
        $xml .= $this->getPaymentCurrencyAsXML();
        $xml .= $this->getPaymentCountryAsXML();
        $xml .= $this->getRouteFeatureAsXML();
        $xml .= $this->getTransactionTypeAsXML();
        $xml .= $this->getCardStateAsXML();
        $xml .= $this->getAccountsConfigurationsAsXML();
        $xml .= $this->getFxServiceTypeConfigAsXML();
        $xml .= $this->getProductsAsXML();
        $xml .= '</payment_metadata>';
        return $xml;
    }

    /***
     * Function is used to set Restrict data as per request param
     * @param array|null $restrictData  Contains array based on desired dataset.
     *
     * @return array
     */
    private static function processRestrictDataParam(?array $restrictData): array{

        // Remove Client ID
        unset($restrictData['client_id']);

        $restrictRequiredData = [];
        $restrictRequiredData['method']         = !($restrictData['method'] === 'false');
        $restrictRequiredData['provider']       = !($restrictData['provider'] === 'false');
        $restrictRequiredData['feature']        = !($restrictData['feature'] === 'false');
        $restrictRequiredData['currency']       = !($restrictData['currency'] === 'false');
        $restrictRequiredData['country']        = !($restrictData['country'] === 'false');
        $restrictRequiredData['card_state']     = !($restrictData['card_state'] === 'false');
        $restrictRequiredData['fx_service']     = !($restrictData['fx_service'] === 'false');
        $restrictRequiredData['account_config']     = !($restrictData['account_config'] === 'false');
        $restrictRequiredData['transaction_type']   = !($restrictData['transaction_type'] === 'false');
        $restrictRequiredData['products']   = !($restrictData['products'] === 'false');

        // If no GET PARAM found,
        if(count($restrictData) === 0) { return $restrictRequiredData; }

        $includeArr = array();
        $excludeArr = array();
        foreach ($restrictData as $keyParam => $valParam) {
            switch ($valParam){
                case 'true':
                    $includeArr[$keyParam] = true;
                    break;
                case 'false':
                    $excludeArr[$keyParam] = false;
                    break;
            }
        }

        if(count($includeArr) > 0) {
            $restrictRequiredData = $includeArr;
        }
        else if(count($excludeArr) > 0) {
            $restrictRequiredData = array_merge_recursive($restrictRequiredData, $excludeArr);
        }

        return $restrictRequiredData;
    }

    /**
     * Produces a new instance of a Client Payment Metadata
     *
     * @param 	RDB $oDB 		    Reference to the Database Object that holds the active connection to the mPoint Database
     * @param 	integer $clientId 	Unique ID for the Client performing the request
     * @param   ?array $requestParam Hold Request parAm, based on that, filter data
     * @return 	ClientPaymentMetadata
     */
    public static function produceConfig(RDB $oDB, int $clientId, ?array $requestParam = array()) : object
    {
        // Clean RQ Restrict Param
        $restrictData = self::processRestrictDataParam($requestParam);

        $aObj_ClientRouteConfig = array();
        $aObj_ClientCurrencyConfig = array();
        $aObj_ClientCountryConfig = array();
        $aObj_ClientPaymentMethodConfig = array();
        $aObj_ClientRouteFeatureConfig = array();
        $aObj_AccountsConfigurations = array();
        $obj_TransactionTypeConfig = array();
        $aObj_CardStateConfig = array();
        $aObj_FxServiceTypeConfig = array();
        $products = array();

        if(empty($clientId) === false)
        {
            if($restrictData['provider'] === true) {
                $aObj_ClientRouteConfig = ClientRouteConfig::produceConfig($oDB, $clientId);
            }

            if($restrictData['country'] === true) {
                $aObj_ClientCountryConfig = ClientCountryCurrencyConfig::produceConfig($oDB, $clientId);
            }

            if($restrictData['currency'] === true) {
                $aObj_ClientCurrencyConfig = ClientCountryCurrencyConfig::produceConfig($oDB, $clientId);
            }

            if($restrictData['method'] === true) {
                $aObj_ClientPaymentMethodConfig = ClientPaymentMethodConfig::producePaymentMethodConfig($oDB,
                    $clientId);
            }

            if($restrictData['account_config'] === true) {
                $aObj_AccountsConfigurations = AccountConfig::produceConfigurations($oDB, $clientId);
            }
        }

        if($restrictData['feature'] === true) {
            $aObj_ClientRouteFeatureConfig = RouteFeature::produceConfig($oDB);
        }

        if($restrictData['transaction_type'] === true) {
            $obj_TransactionTypeConfig = TransactionTypeConfig::produceConfig();
        }

        if($restrictData['card_state'] === true) {
            $aObj_CardStateConfig = CardState::produceConfig($oDB);
        }

        if($restrictData['fx_service'] === true) {
            $aObj_FxServiceTypeConfig = FxServiceType::produceConfig($oDB);
        }

        if($restrictData['products'] === true) {
            $products = Product::produceProducts($oDB, $clientId);
        }

        return new ClientPaymentMetadata($aObj_ClientRouteConfig, $aObj_ClientCountryConfig, $aObj_ClientPaymentMethodConfig, $aObj_ClientRouteFeatureConfig, $aObj_AccountsConfigurations, $obj_TransactionTypeConfig, $aObj_CardStateConfig, $aObj_FxServiceTypeConfig, $aObj_ClientCurrencyConfig, $products);
    }
}
?>