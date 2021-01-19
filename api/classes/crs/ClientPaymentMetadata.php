<?php
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
     * @var ClientRouteConfig
     */
    private array $_obj_ClientRouteConfig;

    /**
     * Configuration for the client payment route country currency
     * @var ClientCountryCurrencyConfig
     */
    private array $_obj_ClientCountryCurrencyConfig;

    /**
     * Configuration for the client supported payment methods
     * @var ClientPaymentMethodConfig
     */
    private array $_obj_ClientPaymentMethodConfig;

    /**
     * Configuration for the client route feature
     * @var RouteFeature
     */
    private array $_obj_ClientRouteFeatureConfig;

    /**
     * Object that holds the transaction type configurations
     *
     * @var TransactionTypeConfig
     */
    private array $_obj_TransactionTypeConfig;

    /**
     * Object that holds the card state configurations
     *
     * @var CardState
     */
    private array $_obj_CardStateConfig;

    private array $_obj_AccountsConfigurations;

    /**
     * Default Constructor
     *
     * @param 	ClientRouteConfig $aObj_ClientRouteConfig 						 Hold Configuration for the client payment route
     * @param 	ClientCountryCurrencyConfig $aObj_ClientCountryCurrencyConfig 	 Hold Configuration for the client payment route country currency
     * @param 	ClientPaymentMethodConfig $aObj_ClientPaymentMethodConfig 		 Hold Configuration for the client supported payment methods
     * @param 	RouteFeature $aObj_ClientRouteFeatureConfig 					 Hold Configuration for the client route feature
     */
	public function __construct(array $aObj_ClientRouteConfig, array $aObj_ClientCountryCurrencyConfig, array $aObj_ClientPaymentMethodConfig, array $aObj_ClientRouteFeatureConfig, array $aObj_AccountsConfigurations, array $obj_TransactionTypeConfig, array $aObj_CardStateConfig)
	{
        $this->_obj_ClientRouteConfig = $aObj_ClientRouteConfig;
        $this->_obj_ClientCountryCurrencyConfig = $aObj_ClientCountryCurrencyConfig;
        $this->_obj_ClientPaymentMethodConfig = $aObj_ClientPaymentMethodConfig;
        $this->_obj_ClientRouteFeatureConfig = $aObj_ClientRouteFeatureConfig;
        $this->_obj_TransactionTypeConfig = $obj_TransactionTypeConfig;
        $this->_obj_CardStateConfig = $aObj_CardStateConfig;
        $this->_obj_AccountsConfigurations = $aObj_AccountsConfigurations;
	}

    /**
     * Returns the XML payload of array of Configurations for the Accounts the Transaction will be associated with
     *
     * @return 	String
     */
    private function getAccountsConfigurationsAsXML() : string
    {
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
        $xml = '<payment_countries>';
        foreach ($this->_obj_ClientCountryCurrencyConfig as $obj_ClientCountryCurrencyConfig)
        {
            if (($obj_ClientCountryCurrencyConfig instanceof ClientCountryCurrencyConfig) === true)
            {
                $xml .= $obj_ClientCountryCurrencyConfig->toCountryAsXML();
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
        $xml = '<payment_currencies>';
        foreach ($this->_obj_ClientCountryCurrencyConfig as $obj_ClientCountryCurrencyConfig)
        {
            if (($obj_ClientCountryCurrencyConfig instanceof ClientCountryCurrencyConfig) === true)
            {
                $xml .= $obj_ClientCountryCurrencyConfig->toCurrencyAsXML();
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
    public static function produceConfig(RDB $oDB, int $clientId) : object
    {
        $aObj_ClientRouteConfig = array();
        $aObj_ClientCountryCurrencyConfig = array();
        $aObj_ClientPaymentMethodConfig = array();
        $aObj_ClientRouteFeatureConfig = array();
        $aObj_AccountsConfigurations = array();

        if(empty($clientId) === false)
        {
            $aObj_ClientRouteConfig = ClientRouteConfig::produceConfig($oDB, $clientId);
            $aObj_ClientCountryCurrencyConfig = ClientCountryCurrencyConfig::produceConfig($oDB, $clientId);
            $aObj_ClientPaymentMethodConfig = ClientPaymentMethodConfig::producePaymentMethodConfig($oDB, $clientId);
            $aObj_ClientRouteFeatureConfig = RouteFeature::produceConfig($oDB, $clientId);
            $aObj_AccountsConfigurations = AccountConfig::produceConfigurations($oDB, $clientId);
        }
        $obj_TransactionTypeConfig = TransactionTypeConfig::produceConfig($oDB);
        $aObj_CardStateConfig = CardState::produceConfig($oDB);

        return new ClientPaymentMetadata($aObj_ClientRouteConfig, $aObj_ClientCountryCurrencyConfig, $aObj_ClientPaymentMethodConfig, $aObj_ClientRouteFeatureConfig, $aObj_AccountsConfigurations, $obj_TransactionTypeConfig, $aObj_CardStateConfig);
    }
	

}
?>