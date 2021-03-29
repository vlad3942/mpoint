<?php
/**
 * Created by IntelliJ IDEA.
 * User: Vikas Gupta
 * Copyright: Cellpoint Digital
 * Link: http://www.cellpointdigital.com
 * Project: mPoint
 * File Name:ClientRouteConfigurations.php
 */

class ClientRouteConfigurations
{
    /**
     * Configuration for the client route configuration
     * @var RouteConfig
     */
    private $_obj_RouteConfig;

    /**
     * Configuration for the route Feature
     * @var RouteFeature
     */
    private $_obj_RouteFeatures;

    /**
     * Configuration for the route country
     * @var ClientRouteCountry
     */
    private $_obj_RouteCountry;

    /**
     * Configuration for the route currency
     * @var ClientRouteCurrency
     */
    private $_obj_RouteCurrecny;

    /**
     * Hold Additional Property Configuration
     * @var AdditionalProperties
     */
    private $_obj_AdditionalProperty;


    /**
     * Default Constructor
     *
     * @param array $aObj_RouteConfigs Hold Configuration for the client route configuration
     * @param array $aObj_RouteFeatures Hold Configuration for the client route Features
     * @param array $aObj_RouteCountry Hold Configuration for the client route country
     * @param array $aObj_RouteCurrecny Hold Configuration for the client route currency
     * @param array $aObj_AdditionalProperties Hold Additional Property Configuration
     */
    public function __construct(array $aObj_RouteConfigs, array $aObj_RouteFeatures, array $aObj_RouteCountry, array $aObj_RouteCurrecny, array $aObj_AdditionalProperties)
    {
        $this->_obj_RouteConfig = $aObj_RouteConfigs;
        $this->_obj_RouteFeatures = $aObj_RouteFeatures;
        $this->_obj_RouteCountry = $aObj_RouteCountry;
        $this->_obj_RouteCurrecny = $aObj_RouteCurrecny;
        $this->_obj_AdditionalProperty = $aObj_AdditionalProperties;
    }

    /**
     * Returns the XML of route feature
     * @param Int routeConfigID Hold Route config ID
     * @return    String
     */
    private function getRouteFeatureAsXML(int $routeConfigID): string
    {
        $xml = '';
        if (empty($this->_obj_RouteFeatures[$routeConfigID]) === false) {
            $xml = '<route_features>';
            foreach ($this->_obj_RouteFeatures[$routeConfigID] as $feature) {
                if ($feature instanceof RouteFeature) {
                    $xml .= $feature->toXML();
                }
            }
            $xml .= '</route_features>';
        }
        return $xml;
    }

    /**
     * Returns the XML of route country
     * @param Int routeConfigID Hold Route config ID
     * @return    String
     */
    private function getRouteCountryAsXML(int $routeConfigID): string
    {
        $xml = '';
        if (empty($this->_obj_RouteCountry[$routeConfigID]) === false) {
            $xml = '<country_ids>';
            foreach ($this->_obj_RouteCountry[$routeConfigID] as $country) {
                if ($country instanceof ClientRouteCountry) {
                    $xml .= $country->toXML();
                }
            }
            $xml .= '</country_ids>';
        }
        return $xml;
    }

    /**
     * Returns the XML of route currency
     * @param Int routeConfigID Hold Route config ID
     * @return    String
     */
    private function getRouteCurrencyAsXML(int $routeConfigID): string
    {
        $xml = '';
        if (empty($this->_obj_RouteCurrecny[$routeConfigID]) === false) {
            $xml = '<currency_ids>';
            foreach ($this->_obj_RouteCurrecny[$routeConfigID] as $currency) {
                if ($currency instanceof ClientRouteCurrency) {
                    $xml .= $currency->toXML();
                }
            }
            $xml .= '</currency_ids>';
        }
        return $xml;
    }

    private function getRouteAdditionalPropertyAsXML(int $routeConfigID) : string
    {
        $xml = '';
        if (empty($this->_obj_AdditionalProperty[$routeConfigID]) === false) {
            $xml = '<additional_data>';
            foreach ($this->_obj_AdditionalProperty[$routeConfigID] as $additionalProperty) {
                if ($additionalProperty instanceof AdditionalProperties) {
                    $xml .= $additionalProperty->toXML();
                }
            }
            $xml .= '</additional_data>';
        }
        return $xml;
    }

    /***
     * Prepare XML string
     * @return string
     */
    public function toXML(): string
    {
        $xml = '<route_configurations>';
        foreach ($this->_obj_RouteConfig as $valRouteConfig) {
            $xml .= '<route_configuration>';
            $xml .= '<id>' . $valRouteConfig['ROUTECONFIGID'] . '</id>';
            $xml .= '<route_id>' . $valRouteConfig['ROUTEID'] . '</route_id>';
            $xml .= '<provider_id>' . $valRouteConfig['PROVIDERID'] . '</provider_id>';
            $xml .= '<mid>' . $valRouteConfig['MID'] . '</mid>';
            $xml .= '<route_name>' . $valRouteConfig['ROUTENAME'] . '</route_name>';
            $xml .= '<username>' . $valRouteConfig['USERNAME'] . '</username>';
            $xml .= '<password>' . $valRouteConfig['PASSWORD'] . '</password>';
            $xml .= '<capture_type>' . $valRouteConfig['CAPTURETYPE'] . '</capture_type>';
            $xml .= '<enabled>' . General::bool2xml($valRouteConfig['ROUTECONFIGENABLED']) . '</enabled>';

            if ($this->_obj_RouteFeatures[(int)$valRouteConfig['ROUTECONFIGID']]) {
                $xml .= $this->getRouteFeatureAsXML((int)$valRouteConfig['ROUTECONFIGID']);
            }
            if ($this->_obj_RouteCountry[(int)$valRouteConfig['ROUTECONFIGID']]) {
                $xml .= $this->getRouteCountryAsXML((int)$valRouteConfig['ROUTECONFIGID']);
            }
            if ($this->_obj_RouteCurrecny[(int)$valRouteConfig['ROUTECONFIGID']]) {
                $xml .= $this->getRouteCurrencyAsXML((int)$valRouteConfig['ROUTECONFIGID']);
            }
            if ($this->_obj_AdditionalProperty[(int)$valRouteConfig['ROUTECONFIGID']]) {
                $xml .= $this->getRouteAdditionalPropertyAsXML((int)$valRouteConfig['ROUTECONFIGID']);
            }
            $xml .= '</route_configuration>';
        }
        $xml .= '</route_configurations>';
        return $xml;
    }

    /**
     * Produces a new instance of a Client Route Configuration Object.
     *
     * @param RDB $oDB Reference to the Database Object that holds the active connection to the mPoint Database
     * @param integer $clientId Unique ID for the Client performing the request
     * @return    ClientRouteConfigurations
     */
    public static function produceConfig(RDB $oDB, int $clientId, ?int $routeConfigId = NULL): ClientRouteConfigurations
    {
        $sql = "SELECT R.id as routeid, R.providerid, RC.id AS routeconfigid, RC.name AS routename, RC.username, 
                RC.password, RC.mid, RC.capturetype, RC.enabled AS routeconfigenabled
                FROM client" . sSCHEMA_POSTFIX . ".route_tbl R
                INNER JOIN client" . sSCHEMA_POSTFIX . ".routeconfig_tbl RC ON RC.routeid = R.id
                WHERE R.clientid = " . $clientId;
        if(empty($routeConfigId) === false){
            $sql .= " AND RC.id = ".$routeConfigId;
        }

        try {
            $res = $oDB->query($sql);

            $aObj_RouteConfigurations = array();
            $aObj_RouteFeatures = array();
            $aObj_RouteCountry = array();
            $aObj_RouteCurrecny = array();
            $aObj_AdditionalProperties = array();

            while ($RS = $oDB->fetchName($res)) {
                $aObj_RouteConfigurations[$RS["ROUTECONFIGID"]] = $RS;
                # Get Route Feature
                $routeConfigID = (int)$RS["ROUTECONFIGID"];
                $aObj_RouteFeatures[$routeConfigID] = RouteFeature::produceConfigByRouteConfigID($oDB, $routeConfigID);
                $aObj_RouteCountry[$routeConfigID] = ClientRouteCountry::produceConfig($oDB, $routeConfigID);
                $aObj_RouteCurrecny[$routeConfigID] = ClientRouteCurrency::produceConfig($oDB, $routeConfigID);
                $aObj_AdditionalProperties[$routeConfigID] = AdditionalProperties::produceConfig($oDB, $routeConfigID, 'merchant', );
            }
        } catch (SQLQueryException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }

        return new ClientRouteConfigurations($aObj_RouteConfigurations, $aObj_RouteFeatures, $aObj_RouteCountry, $aObj_RouteCurrecny, $aObj_AdditionalProperties);
    }
}

?>