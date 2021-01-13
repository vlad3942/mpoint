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
     * @var ClientRouteConfigurations
     */
    private $_obj_ClientRouteConfigurations;

    /**
     * Default Constructor
     *
     * @param 	ClientRouteConfig $aObj_ClientRouteConfigurations 	Hold Configuration for the client route configuration
     */
    public function __construct($aObj_ClientRouteConfigurations)
    {
        $this->_obj_ClientRouteConfigurations = $aObj_ClientRouteConfigurations;
    }

    /**
     * Returns the XML payload of client route feature configuration
     * @return 	String
     */
    private function getRouteFeatureAsXML(array $features): String
    {
        $xml = '<route_features>';
        foreach ($features as $feature)
        {
            if($feature instanceof RouteFeature) {
                $xml .= $feature->toXML();
            }
        }
        $xml .= '</route_features>';

        return $xml;
    }

    /***
     * Prepare XML string
     * @return string
     */
    public function toXML(): String
    {
        $xml = '<route_configurations>';
        foreach ($this->_obj_ClientRouteConfigurations as $valClientRouteConfigurations)
        {
            $xml .= '<route_configuration>';
            $xml .= '<id>'. $valClientRouteConfigurations['ROUTECONFIGID'] .'</id>';
            $xml .= '<provider_id>'. $valClientRouteConfigurations['PROVIDERID'] .'</provider_id>';
            $xml .= '<country_id>'. $valClientRouteConfigurations['COUNTRYID'] .'</country_id>';
            $xml .= '<currency_id>'. $valClientRouteConfigurations['CURRENCYID'] .'</currency_id>';
            $xml .= '<mid>'. $valClientRouteConfigurations['MID'] .'</mid>';
            $xml .= '<route_name>'. $valClientRouteConfigurations['ROUTENAME'] .'</route_name>';
            $xml .= '<username>'. $valClientRouteConfigurations['USERNAME'] .'</username>';
            $xml .= '<password>'. $valClientRouteConfigurations['PASSWORD'] .'</password>';
            $xml .= '<capture_type>'. $valClientRouteConfigurations['CAPTURETYPE'] .'</capture_type>';

            if(is_array($valClientRouteConfigurations['ROUTE_FEATURES'])
                && count($valClientRouteConfigurations['ROUTE_FEATURES']) > 0)
            {
                $xml .= $this->getRouteFeatureAsXML($valClientRouteConfigurations['ROUTE_FEATURES']);
            }
            $xml .= '<enabled>'. General::bool2xml($valClientRouteConfigurations['ROUTECONFIGENABLED']) .'</enabled>';
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
                FROM client'.sSCHEMA_POSTFIX. '.routeconfig_tbl RC
                LEFT JOIN client' .sSCHEMA_POSTFIX. '.route_tbl R ON RC.routeid = R.id
                WHERE R.clientid = '. $clientId;

        $res = $oDB->query($sql);
        $aObj_RouteConfigurations = array();

        while ($RS = $oDB->fetchName($res)) {

            $aObj_RouteConfigurations[$RS["ROUTECONFIGID"]] = $RS;
            //Get route feature
            $RouteFeature_SQL = "SELECT CRF.id as featureid, SRF.featurename
					 FROM Client" . sSCHEMA_POSTFIX . ".RouteFeature_Tbl CRF
					 LEFT JOIN System" . sSCHEMA_POSTFIX . ".RouteFeature_Tbl SRF ON CRF.featureid = SRF.id AND SRF.enabled = '1'
					 WHERE routeconfigid = " . intval($RS["ROUTECONFIGID"]);

            $aRouteFeature = $oDB->getAllNames($RouteFeature_SQL);

            if(is_array($aRouteFeature) && count($aRouteFeature)) {
                $Obj_aRouteFeatures = array();
                foreach ($aRouteFeature as $valRouteFeature) {
                    $Obj_aRouteFeatures[$valRouteFeature["FEATUREID"]] = new RouteFeature ($valRouteFeature["FEATUREID"], $valRouteFeature["FEATURENAME"]);
                }
                $aObj_RouteConfigurations[$RS["ROUTECONFIGID"]]['ROUTE_FEATURES'] = $Obj_aRouteFeatures;

                # Reset variable;
                $aRouteFeature = '';
                $Obj_aRouteFeatures = '';
            }
        }
        return new ClientRouteConfigurations($aObj_RouteConfigurations);
    }
}
?>