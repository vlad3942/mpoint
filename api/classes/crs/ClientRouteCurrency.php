<?php
/**
 *
 * @author Anna Lagad
 * @copyright Cellpoint Digital
 * @link http://www.cellpointdigital.com
 * @version 1.01
 */

/**
 * Data class for holding Client Route Currency Configurations
 *
 */
class ClientRouteCurrency
{
    /**
     * Handles the active database connection
     * @var RDB
     */
    private $_objDB;

    /**
     * Hold unique ID of the client route currency
     *
     * @var integer
     */
    private ?int $_iID;

    /**
     * Hold unique ID of route config
     *
     * @var integer
     */
    private ?int $_iCurrencyId;

    /**
     * Default Constructor
     * @param   RDB $oDB 		    Reference to the Database Object that holds the active connection to the mPoint Database
     * @param 	integer $id 	    Hold unique ID of the currency list
     * @param 	integer $currencyId	Hold unique ID of the currency
     */
    public function __construct(RDB $oDB, ?int $id = null, ?int $currencyId = null)
    {
        $this->objDB = $oDB;
        $this->_iID = $id;
        $this->_iCurrencyId = $currencyId;
    }

    /**
     * Returns route currency id
     * @return    integer
     */
    public function getCurrencyID(): int
    {
        return $this->_iCurrencyId;
    }

    /**
     * Returns the XML payload of Configurations for client route currency.
     *
     * @return    String
     */
    public function toXML(): string
    {
        $xml = '<currency_id>' . $this->getCurrencyID() . '</currency_id>';
        return $xml;
    }

    /**
     * Creates a list of all route client currency configuration instances that are available in the database
     *
     * @param RDB $oDB  Reference to the Database Object that holds the active connection to the mPoint Database
     * @param int $routeConfigId   Hold unique id of the route configuration
     * @return ClientRouteCountry  $aObj_Configurations    List of client currency configuration
     */
    public static function produceConfig(RDB $oDB, int $routeConfigId): array
    {
        $aObj_Configurations = array();
        $sql = "SELECT id, currencyid
                FROM Client". sSCHEMA_POSTFIX .".RouteCurrency_Tbl
                WHERE routeconfigid = $routeConfigId
                AND enabled = '1'
                ORDER BY id ASC";
        try {
            $res = $oDB->query($sql);
            while ($RS = $oDB->fetchName($res)) {
                $aObj_Configurations[] = new ClientRouteCurrency ($oDB, (int) $RS["ID"], (int) $RS["CURRENCYID"]);
            }
        } catch (SQLQueryException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
        return $aObj_Configurations;
    }

    /**
     * Function used to add supported currency for the given route
     *
     * @param int $routeConfigId   Hold unique id of the route configuration
     * @param array $aCurrencyId   Hold an array of supported route currency
     * @return bool                success / failure response
     * @throws Exception
     */
    public function addRouteCurrency(int $routeConfigId, array $aCurrencyId): bool
    {
        if(empty($routeConfigId) === false) {
            foreach ($aCurrencyId as $currencyId){
                $sql = "INSERT INTO Client" . sSCHEMA_POSTFIX . ".RouteCurrency_Tbl
                    (routeconfigid, currencyid)
                    values ($1, $2)";

                $aParam = array( $routeConfigId, $currencyId );
                $result = $this->objDB->executeQuery($sql, $aParam);
                if ($result === false) {
                    trigger_error("Unable to build query for add route currency", E_USER_WARNING);
                    throw new Exception("Unable to add route currency", E_USER_ERROR);
                }

            }
            return true;
        } else {
            trigger_error("RouteConfigId Not Found", E_USER_WARNING);
            return false;
        }
    }

    /**
     * Function used to delete given list of route specific currency
     * 
     * @param int $routeConfigId  Hold unique id of the route configuration
     * @param array $aCurrencyId   Hold an array of supported route country
     * @return bool               success / failure response
     */
    public function deleteRouteCurrency(int $routeConfigId, array $aCurrencyId) : bool
    {
        if(empty($routeConfigId) === false && empty($aCurrencyId) === false) {
            try {
                $sql = "DELETE FROM Client".sSCHEMA_POSTFIX.".RouteCurrency_Tbl
                        WHERE routeconfigid = ". $routeConfigId ." 
                        AND currencyid IN (" . implode(",", $aCurrencyId) . ")";
                return is_resource($this->objDB->query($sql) );
            } catch (SQLQueryException $e) {
                trigger_error($e->getMessage(), E_USER_ERROR);
            }
        }
        return true;
    }

}
?>