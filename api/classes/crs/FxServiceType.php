<?php
/**
 *
 * @author Anna Lagad
 * @copyright Cellpoint Digital
 * @link http://www.cellpointdigital.com
 * @version 1.01
 */

/**
 * Data class for holding Foreign Exchange Service Type Configurations
 *
 */
class FxServiceType
{
    /**
     * Hold unique ID of Foreign Exchange Service Type
     *
     * @var integer
     */
    private int $_iID;
    /**
     * Hold name of the Foreign Exchange Service Type
     *
     * @var integer
     */
    private string $_iName;
    /**
     * Hold Foreign Exchange Service Type status
     *
     * @var boolean
     */
    private bool $_bEnabled;

    public function __construct(int $id, string $name, bool $enabled)
    {
        $this->_iID = $id;
        $this->_iName = $name;
        $this->_bEnabled = $enabled;
    }

    /**
     * Returns unique ID of Foreign Exchange Service Type
     * @return    integer
     */
    public function getID(): int
    {
        return $this->_iID;
    }

    /**
     * Returns name of the Foreign Exchange Service Type
     * @return    integer
     */
    public function getName(): string
    {
        return $this->_iName;
    }

    /**
     * Returns transaction Foreign Exchange Service Type status
     * @return    boolean
     */
    public function getEnabled(): bool
    {
        return $this->_bEnabled;
    }

    /**
     * Returns the XML payload of Configurations for Foreign Exchange Service Type.
     *
     * @return    String
     */
    public function toXML(): string
    {
        $xml = '<fx_service_type>';
        $xml .= '<id>' . $this->getID() . '</id>';
        $xml .= '<name>' . $this->getName() . '</name>';
        $xml .= '<enabled>' . General::bool2xml($this->getEnabled()) . '</enabled>';
        $xml .= '</fx_service_type>';
        return $xml;
    }


    /**
     * Creates a list of al Foreign Exchange Service Type configuration instances that are available in the database
     *
     * @param RDB $oDB  Reference to the Database Object that holds the active connection to the mPoint Database
     * @return CardState  $aObj_Configurations    List of Foreign Exchange Service Type Configurations
     */
    public static function produceConfig(RDB $oDB): array
    {
        $aObj_Configurations = array();
        $sql = "SELECT id,name,enabled FROM System". sSCHEMA_POSTFIX .".FxServiceType_Tbl ORDER BY id ASC";
        try {
            $res = $oDB->query($sql);
            while ($RS = $oDB->fetchName($res)) {
                $aObj_Configurations[] = new FxServiceType ($RS["ID"], $RS["NAME"], $RS["ENABLED"]);
            }
        } catch (SQLQueryException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
        return $aObj_Configurations;
    }
}
?>