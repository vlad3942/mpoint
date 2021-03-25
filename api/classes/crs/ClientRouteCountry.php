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
     * Hold unique ID of the client route country
     *
     * @var integer
     */
    private int $_iID;

    /**
     * Hold unique ID of route config
     *
     * @var integer
     */
    private int $_iCountryId;

    public function __construct(int $id, int $countryid)
    {
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
                $aObj_Configurations[] = new ClientRouteCountry ((int) $RS["ID"], (int) $RS["COUNTRYID"]);
            }
        } catch (SQLQueryException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
        return $aObj_Configurations;
    }
}
?>