<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anna Lagad
 * Copyright: Cellpoint Digital
 * Link: http://www.cellpointdigital.com
 * Project: mPoint
 * File Name: AdditionalProperties.php
 */

class AdditionalProperties
{
    /**
     * Holds name of the merchant property key
     * @var string
     */
    private string $_sKey;

    /**
     * Holds name of the merchant property value
     * @var string
     */
    private string $_sValue;

    /**
     * Default Constructor
     *
     * @param   string $key             Hold additional property key
     * @param   string $value           Hold additional property value
     */
	public function __construct(string $key = null, string $value = null)
	{
        $this->_sKey = $key;
        $this->_sValue = $value;
	}

    /**
     * Hold additional property key
     * @return string
     */
    public function getKey() : string
    {
        return $this->_sKey;
    }

    /**
     * Hold additional property key
     * @return string
     */
    public function getValue() : string
    {
        return $this->_sValue;
    }

    /**
     * Returns the XML payload of Additional Property Configurations
     *
     * @return    String
     */
    public function toXML():string
    {
        $xml = '<param>';
        $xml .= '<key>'.$this->getKey().'</key>';
        $xml .= '<value>'.$this->getValue().'</value>';
        $xml .= '</param>';
        return $xml;
    }

    /**
     * Produce additional property configuration
     *
     * @param RDB $oDB  Reference to the Database Object that holds the active connection to the mPoint Database
     * @param int $routeConfigId   Hold unique id of the route configuration
     * @return AdditionalProperties  $aObj_Configurations    Hold additional property configuration
     */
    public static function produceConfig(RDB $oDB, int $routeConfigId): array
    {
        $aObj_Configurations = array();
        $sql  = "SELECT key,value
                 FROM Client". sSCHEMA_POSTFIX .".AdditionalProperty_tbl
                 WHERE externalid = ". $routeConfigId ." and type='merchant' and enabled=true" ;
        try {
            $res = $oDB->query($sql);
            while ($RS = $oDB->fetchName($res)) {
                $aObj_Configurations[] = new AdditionalProperties ( (string) $RS["KEY"], (string) $RS["VALUE"]);
            }
        } catch (SQLQueryException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
        return $aObj_Configurations;
    }


}
?>