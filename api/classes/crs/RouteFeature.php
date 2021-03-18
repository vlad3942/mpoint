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
     * Function used process route responsew for end user
     *
     * @param array $response an array containing route feature configuration status
     * @return string XML playload structure of route feature configuration status
     */
    public function processResponse(array $response):string
    {
        $xml = '<route_features_response>';
        if($response['status'] === TRUE){
            $xml .= '<id>'.$this->_iFeatureId.'</id>';
            $xml .= '<status>Success</status>';
            $xml .= '<message>Route Feature Configuration Successfully Done.</message>';
        }else{
            $xml .= '<id>-1</id>';
            $xml .= '<status>Fail</status>';
            if($response['is_duplicate'] === TRUE){
                $xml .= '<message>Route Feature Configuration Already Exist</message>';
            }else{
                $xml .= '<message>Unable to Configure Route Feature. </message>';
            }
        }
        $xml .= '</route_features_response>';
        return $xml;
    }

    /**
     * Function used to add route feature if route feature already not present
     *
     * @param RDB $_OBJ_DB        Reference to the Database Object that holds the active connection to the mPoint Database
     * @param int $clientId       Unique ID for the Client performing the request
     * @param int $routeConfigId  Holds unique id of the route configuration
     * @return array              an array response of update route feature
     */
    public function AddFeature(RDB $_OBJ_DB, int $clientId, int $routeConfigId) : array
    {
        $response = array();
        $isRouteFeaturealreadyExist = $this->isRouteFeatureAlreadyExist($_OBJ_DB, $clientId, $routeConfigId);
        if($isRouteFeaturealreadyExist === false){
            $response['status'] = $this->AddRouteFeature($_OBJ_DB, $clientId, $routeConfigId);
        }else{
            trigger_error('Configuration Already Exist for route: '.$this->_iRouteConfigId , E_USER_NOTICE);
            $response['status'] = FALSE;
            $response['is_duplicate'] = $isRouteFeaturealreadyExist;
        }
        return $response;
    }

    /**
     * Function used process route response for end user
     *
     * @param RDB $_OBJ_DB        Reference to the Database Object that holds the active connection to the mPoint Database
     * @param int $clientId       Unique ID for the Client performing the request
     * @param int $routeConfigId  Holds unique id of the route configuration
     * @return bool              Status of add route feature query
     */
    private function AddRouteFeature(RDB $_OBJ_DB, int $clientId, int $routeConfigId) : bool
    {
        try {
            $sql = "INSERT INTO Client" . sSCHEMA_POSTFIX . ".RouteFeature_Tbl
                (clientid, routeconfigid, featureid)
                values ($1, $2, $3)";

            $resource = $_OBJ_DB->prepare($sql);
            if (is_resource($resource) === true) {
                $aParam = array( $clientId, $routeConfigId, $this->_iFeatureId );
                $result = $_OBJ_DB->execute($resource, $aParam);
                if ($result === false) {
                    throw new Exception("Unable to update route feature", E_USER_ERROR);
                    return FALSE;
                }
                return TRUE;
            } else {
                trigger_error("Unable to build query for update route feature", E_USER_WARNING);
                return FALSE;
            }
        }catch (SQLQueryException $e){
            trigger_error($e->getMessage(), E_USER_ERROR);
            return FALSE;
        }
    }

    /**
     *  Function used check whether route feature is already exist or not
     *
     * @param RDB $_OBJ_DB        Reference to the Database Object that holds the active connection to the mPoint Database
     * @param int $clientId       Unique ID for the Client performing the request
     * @param int $routeConfigId  Holds unique id of the route configuration
     * @return bool               Whether the route feature already exist or not
     */
    private function isRouteFeatureAlreadyExist(RDB $_OBJ_DB, int $clientId, int $routeConfigId) : bool
    {
        $sql = "SELECT id
                    FROM Client" . sSCHEMA_POSTFIX . ".RouteFeature_Tbl
                    WHERE clientid = $clientId
                    AND routeconfigid = $routeConfigId
                    AND featureid =".$this->_iFeatureId;
        try {
            $res = $_OBJ_DB->getName($sql);
            if (is_array($res) === true && count($res) > 0) {
                return true;
            }
        }catch (SQLQueryException $e){
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
        return false;
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