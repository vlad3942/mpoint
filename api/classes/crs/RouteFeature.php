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
    private int $_iFeatureId;

    /**
     * Holds name of the route feature
     * @var string
     */
    private string $_sFeatureName;

    /**
     * Default Constructor
     *
     * @param 	integer $featureid 	Unique ID for the route feature
     * @param 	string $featurename	Holds name of the route feature
     */
	public function __construct(int $featureid, string $featurename)
	{
        $this->_iFeatureId = $featureid;
        $this->_sFeatureName = $featurename;
	}

    /**
     * Unique ID for the client supported route feature
     * @return integer
     */
	public function getFeatureId() : int
    {
        return $this->_iFeatureId;
    }

    /**
     * Name of the client supported route feature
     * @return integer
     */
	public function getFeatureName() : string
    {
        return $this->_sFeatureName;
    }

    public function toXML() : string
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
    public static function produceConfig(RDB &$oDB, $clientId) : array
    {
        $aObj_Configurations = array();

        $sql = "SELECT CRF.featureid, SRF.featurename
				FROM Client".sSCHEMA_POSTFIX.".Routefeature_Tbl CRF
				INNER JOIN System".sSCHEMA_POSTFIX.".Routefeature_Tbl SRF ON CRF.featureid = SRF.id AND SRF.enabled = '1'
				WHERE CRF.clientid = ". $clientId ." 
				AND CRF.enabled = '1'
				ORDER BY CRF.featureid";
        try {
            $res = $oDB->query($sql);
            while ($RS = $oDB->fetchName($res)) {
                $aObj_Configurations[] = new RouteFeature ($RS["FEATUREID"], $RS["FEATURENAME"]);
            }
        }catch (SQLQueryException $e){
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
        return $aObj_Configurations;
    }

}
?>