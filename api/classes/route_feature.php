<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anna Lagad
 * Copyright: Cellpoint Digital
 * Link: http://www.cellpointdigital.com
 * Project: mPoint
 * File Name:route_feature.php
 */

class RouteFeature
{
    /**
     * Hold an unique ID for the route feature
     * @var integer
     */
    private $_iFeatureId;

    /**
     * Holds name of the route feature
     * @var string
     */
    private $_sFeatureName;

    /**
     * Default Constructor
     *
     * @param 	integer $featureid 	Unique ID for the route feature
     * @param 	string $featurename	Holds name of the route feature
     */
	public function __construct($featureid, $featurename)
	{
        $this->_iFeatureId = $featureid;
        $this->_sFeatureName = $featurename;
	}

    /**
     * Unique ID for the client supported route feature
     * @return integer
     */
	public function getFeatureId(){ return $this->_iFeatureId; }

    /**
     * Name of the client supported route feature
     * @return integer
     */
	public function getFeatureName() { return $this->_sFeatureName; }

    public function toXML()
    {
      $xml  = '<route_feature>';
      $xml .= '<id>'. $this->getFeatureId() .'</id>';
      $xml .= '<name>'. $this->getFeatureName() .'</name>';
      $xml .= '</route_feature>';
      return $xml;
    }

    /**
     * Produces a new instance of a Client Route Configuration Object.
     *
     * @param 	RDB $oDB 		    Reference to the Database Object that holds the active connection to the mPoint Database
     * @param 	integer $clientId 	Unique ID for the Client performing the request
     * @return 	RouteFeature
     */
    public static function produceConfig(RDB &$oDB, $clientId)
    {
        $sql = "SELECT CRF.featureid, SRF.featurename
				FROM Client".sSCHEMA_POSTFIX.".Routefeature_Tbl CRF
				INNER JOIN System".sSCHEMA_POSTFIX.".Routefeature_Tbl SRF ON CRF.featureid = SRF.id AND SRF.enabled = '1'
				WHERE CRF.clientid = ". $clientId ." 
				AND CRF.enabled = '1'
				ORDER BY CRF.featureid";

        $res = $oDB->query($sql);
        $aObj_Configurations = array();
        while ($RS = $oDB->fetchName($res) )
        {
            $aObj_Configurations[] = new RouteFeature ($RS["FEATUREID"], $RS["FEATURENAME"]);
        }
        return $aObj_Configurations;
    }


    /**
     * Produces a new instance of a Route Configuration Object from Route config ID.
     *
     * @param 	RDB $oDB 		    Reference to the Database Object that holds the active connection to the mPoint Database
     * @param 	integer $clientId 	Unique ID for the Client performing the request
     * @return 	RouteFeature Object Array
     */
    public static function produceConfigByRouteConfigID(RDB $oDB, int $routeConfigID): array
    {
        $aObj_Configurations = array();

        $RouteFeature_SQL = "SELECT CRF.id as featureid, SRF.featurename
                         FROM Client" . sSCHEMA_POSTFIX . ".RouteFeature_Tbl CRF
                         INNER JOIN System" . sSCHEMA_POSTFIX . ".RouteFeature_Tbl SRF ON CRF.featureid = SRF.id AND SRF.enabled = '1'
                         WHERE CRF.routeconfigid = " . $routeConfigID .
				        "ORDER BY CRF.featureid";

        try {
            $res = $oDB->query($RouteFeature_SQL);

            while ($RS = $oDB->fetchName($res)) {
                $aObj_Configurations[] = new RouteFeature ($RS["FEATUREID"], $RS["FEATURENAME"]);
            }
        } catch (SQLQueryException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
        return $aObj_Configurations;
    }

}
?>