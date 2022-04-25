<?php
/**
 *
 * @author Anna Lagad
 * @copyright Cellpoint Digital
 * @link http://www.cellpointdigital.com
 * @version 1.01
 */

/**
 * Data class for holding Client Route Country Configurations
 *
 */
class ClientRouteCountry
{
    /**
     * Handles the active database connection
     * @var RDB
     */
    private $_objDB;

    /**
     * Hold unique ID of the client route country
     *
     * @var integer
     */
    private ?int $_iID;

    /**
     * Hold unique ID of route config
     *
     * @var integer
     */
    private ?int $_iCountryId;


    /**
     * Default Constructor
     * @param   RDB $oDB 		    Reference to the Database Object that holds the active connection to the mPoint Database
     * @param 	integer $id 	    Hold unique ID of the country list
     * @param 	integer $countryid	Hold unique ID of the country
     */
    public function __construct(RDB $oDB, ?int $id = null, ?int $countryid = null)
    {
        $this->objDB = $oDB;
        $this->_iID = $id;
        $this->_iCountryId = $countryid;
    }

    /**
     * Returns route country id
     * @return    integer
     */
    public function getCountryID(): int
    {
        return $this->_iCountryId;
    }

    /**
     * Returns the XML payload of Configurations for client route country.
     *
     * @return    String
     */
    public function toXML(): string
    {
        $xml = '<country_id>' . $this->getCountryID() . '</country_id>';
        return $xml;
    }

    /**
     * Creates a list of all route client country configuration instances that are available in the database
     *
     * @param RDB $oDB  Reference to the Database Object that holds the active connection to the mPoint Database
     * @param int $routeConfigId   Hold unique id of the route configuration
     * @return ClientRouteCountry  $aObj_Configurations    List of client country configuration
     */
    public static function produceConfig(RDB $oDB, int $routeConfigId): array
    {
        $aObj_Configurations = array();
        $sql = "SELECT id, countryid
                FROM Client". sSCHEMA_POSTFIX .".RouteCountry_Tbl 
                WHERE routeconfigid = $routeConfigId 
                AND enabled = '1' 
                ORDER BY id ASC";
        try {
            $res = $oDB->query($sql);
            while ($RS = $oDB->fetchName($res)) {
                $aObj_Configurations[] = new ClientRouteCountry ($oDB, (int) $RS["ID"], (int) $RS["COUNTRYID"]);
            }
        } catch (SQLQueryException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
        return $aObj_Configurations;
    }


    /**
     * Function used to add country which support for the given route
     *
     * @param int $routeConfigId   Hold unique id of the route configuration
     * @param array $aCountryId    Hold an array of supported route country
     * @return bool                success / failure as a response
     * @throws Exception
     */
    public function addRouteCountry(int $routeConfigId, array $aCountryId): bool
    {
        if(empty($routeConfigId) === false) {
            foreach ($aCountryId as $countryId){
                $sql = "INSERT INTO Client" . sSCHEMA_POSTFIX . ".RouteCountry_Tbl
                    (routeconfigid, countryid)
                    values ($1, $2)";

                $aParam = array( $routeConfigId, $countryId );
                $result = $this->objDB->executeQuery($sql, $aParam);
                if ($result === false) {
                    trigger_error("Unable to build query for add route country", E_USER_WARNING);
                    throw new Exception("Unable to add route country", E_USER_ERROR);
                    return false;
                }

            }
            return true;
        } else {
            trigger_error("RouteConfigId Not Found", E_USER_WARNING);
            return false;
        }
    }

    /**
     * Function used to delete given list of route specific country
     *
     * @param int $routeConfigId  Hold unique id of the route configuration
     * @param array $aCountryId   Hold an array of supported route country
     * @return bool               success / failure response
     */
    public function deleteRouteCountry(int $routeConfigId, array $aCountryId) : bool
    {
        if(empty($aCountryId) === false) {
            if(empty($routeConfigId) === false) {
                try {
                    $sql = "DELETE FROM Client".sSCHEMA_POSTFIX.".RouteCountry_Tbl
                            WHERE routeconfigid = ". $routeConfigId ." 
                            AND countryid IN (" . implode(",", $aCountryId) . ")";
                    return is_resource($this->objDB->query($sql) );
                } catch (SQLQueryException $e) {
                    trigger_error($e->getMessage(), E_USER_ERROR);
                }
            }else {
                trigger_error("RouteConfigId Not Found", E_USER_WARNING);
                return false;
            }
        }
        return true;
    }

}
?>