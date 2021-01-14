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
     * Default Constructor
     *
     * @param 	array $aObj_RouteConfigs 	Hold Configuration for the client route configuration
     * @param 	array $aObj_RouteFeatures 	Hold Configuration for the client route Features
     */
    public function __construct(array $aObj_RouteConfigs, array $aObj_RouteFeatures)
    {
        $this->_obj_RouteConfig = $aObj_RouteConfigs;
        $this->_obj_RouteFeatures = $aObj_RouteFeatures;
    }

    /**
     * Returns the XML of route feature
     * @param  Int routeConfigID Hold Route config ID
     * @return 	String
     */
    private function getRouteFeatureAsXML(int $routeConfigID): String
    {
        $xml = '';
        if(empty($this->_obj_RouteFeatures[$routeConfigID]) === false) {
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

    /***
     * Prepare XML string
     * @return string
     */
    public function toXML(): String
    {
        $xml = '<route_configurations>';
        foreach ($this->_obj_RouteConfig as $valRouteConfig)
        {
            $xml .= '<route_configuration>';
            $xml .= '<id>'. $valRouteConfig['ROUTECONFIGID'] .'</id>';
            $xml .= '<provider_id>'. $valRouteConfig['PROVIDERID'] .'</provider_id>';
            $xml .= '<country_id>'. $valRouteConfig['COUNTRYID'] .'</country_id>';
            $xml .= '<currency_id>'. $valRouteConfig['CURRENCYID'] .'</currency_id>';
            $xml .= '<mid>'. $valRouteConfig['MID'] .'</mid>';
            $xml .= '<route_name>'. $valRouteConfig['ROUTENAME'] .'</route_name>';
            $xml .= '<username>'. $valRouteConfig['USERNAME'] .'</username>';
            $xml .= '<password>'. $valRouteConfig['PASSWORD'] .'</password>';
            $xml .= '<capture_type>'. $valRouteConfig['CAPTURETYPE'] .'</capture_type>';
            $xml .= '<enabled>'. General::bool2xml($valRouteConfig['ROUTECONFIGENABLED']) .'</enabled>';

            if($this->_obj_RouteFeatures[(int)$valRouteConfig['ROUTECONFIGID']]) {
                $xml .= $this->getRouteFeatureAsXML((int)$valRouteConfig['ROUTECONFIGID']);
            }
            $xml .= '</route_configuration>';
        }
        $xml .= '</route_configurations>';
        return $xml;
    }

    /**
     * Produces a new instance of a Client Route Configuration Object.
     *
     * @param 	RDB $oDB 		    Reference to the Database Object that holds the active connection to the mPoint Database
     * @param 	integer $clientId 	Unique ID for the Client performing the request
     * @return 	ClientRouteConfigurations
     */
    public static function produceConfig(RDB $oDB, int $clientId): ClientRouteConfigurations
    {
        $sql = 'SELECT R.id as routeid, R.providerid, RC.id AS routeconfigid, RC.name AS routename, RC.username, 
                RC.password, RC.countryid, RC.currencyid, RC.mid, RC.capturetype, RC.enabled AS routeconfigenabled
                FROM client'.sSCHEMA_POSTFIX. '.route_tbl R
                INNER JOIN client' .sSCHEMA_POSTFIX. '.routeconfig_tbl RC ON RC.routeid = R.id
                WHERE R.clientid = '. $clientId;

        try {
            $res = $oDB->query($sql);

            $aObj_RouteConfigurations = array();
            $aObj_RouteFeatures = array();

            while ($RS = $oDB->fetchName($res)) {
                $aObj_RouteConfigurations[$RS["ROUTECONFIGID"]] = $RS;
                # Get Route Feature
                $routeConfigID = (int) $RS["ROUTECONFIGID"];
                $aObj_RouteFeatures[$routeConfigID] = RouteFeature::produceConfigByRouteConfigID($oDB, $routeConfigID);
            }
        }
        catch (SQLQueryException $e){
            trigger_error($e->getMessage(), E_USER_ERROR);
        }

        return new ClientRouteConfigurations($aObj_RouteConfigurations, $aObj_RouteFeatures);
    }
}
?>