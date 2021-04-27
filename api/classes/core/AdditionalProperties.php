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
     * Holds name of the merchant property scope
     * @var integer
     */
    private int $_iScope;

    /**
     * Default Constructor
     *
     * @param   string $key             Hold additional property key
     * @param   string $value           Hold additional property value
     */
	public function __construct(string $key, string $value, int $scope)
	{
        $this->_sKey = $key;
        $this->_sValue = $value;
        $this->_iScope = $scope;
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
     * Hold additional property scope
     * @return integer
     */
    public function getScope() : string
    {
        return $this->_iScope;
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
        $xml .= '<value>'.htmlentities($this->getValue()).'</value>';
        $xml .= '<scope>'.$this->getScope().'</scope>';
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
    public static function produceConfig(RDB $oDB, int $externalId, string $type): array
    {
        $aObj_Configurations = array();
        $sql  = "SELECT key,value, scope
                 FROM Client". sSCHEMA_POSTFIX .".AdditionalProperty_tbl
                 WHERE externalid = ". $externalId ." and type='".$type."' and enabled=true" ;
        try {
            $res = $oDB->query($sql);
            while ($RS = $oDB->fetchName($res)) {
                $aObj_Configurations[] = new AdditionalProperties ( (string) $RS["KEY"], (string) $RS["VALUE"], (int) $RS["SCOPE"]);
            }
        } catch (SQLQueryException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
        return $aObj_Configurations;
    }


}
?>