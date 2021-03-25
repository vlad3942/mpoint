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
     * Hold unique ID of the client route currency
     *
     * @var integer
     */
    private int $_iID;

    /**
     * Hold unique ID of route config
     *
     * @var integer
     */
    private int $_iCurrencyId;

    public function __construct(int $id, int $currencyId)
    {
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
                $aObj_Configurations[] = new ClientRouteCurrency ((int) $RS["ID"], (int) $RS["CURRENCYID"]);
            }
        } catch (SQLQueryException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
        return $aObj_Configurations;
    }
}
?>