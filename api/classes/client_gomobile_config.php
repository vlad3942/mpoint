<?php
/**
 * Class provides object structure for GoMobile Config for a client.
 *
 * @author Rohit Malhotra
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Client Config
 * @subpackage GoMobile Config
 * @version 1.00
 */

Class ClientGoMobileConfig extends BasicConfig
{
    /**
     * Param value as configured with GoMobile
     *
     * @var string
     */
    private $_sValue;
     /**
     * Channel used for sending the GoMobile communication
     *
     * @var string
     */
    private $_sChannel;

    /**
     * Default Constructor
     *
     * @param 	integer $id 			The unique ID for the client's Merchant Account configuration for the Payment Service Provider
     * @param 	string $name 			Param name of the GoMobile configuration.
     * @param 	string $value 		    Param value as configured with GoMobile
     * @param 	string $channel	 		Channel used for sending the GoMobile communication
     */
    public function __construct($id, $name, $value, $channel)
    {
        parent::__construct($id, $name);

        $this->_sValue = trim($value);
        $this->_sChannel = trim($channel);
    }
    public function getValue() { return $this->_sValue; }
    public function getChannel() { return $this->_sChannel; }

    public function toXML()
    {
        $xml = '<gomobile-configuration-param id="' . $this->getID() . '" name="' . $this->getName() . '" value="'. $this->_sValue .'" channel="'. $this->_sChannel .'" />';

        return $xml;
    }

    public static function produceConfig(RDB $oDB, $id)
    {
        $sql = "SELECT GC.id, GC.name, GC.value, GC.channel	
				FROM Client". sSCHEMA_POSTFIX .".GoMobileConfiguration_Tbl GC  				
				WHERE GC.id = ". intval($id) ." AND GC.enabled = '1'";
        //echo $sql ."\n";
        $RS = $oDB->getName($sql);
        if(is_array($RS) === true && count($RS) > 0)
        {
            return new ClientGoMobileConfig($RS["ID"], $RS["NAME"], $RS["VALUE"], $RS["CHANNEL"]);
        }
        else { return null; }
    }

    public static function produceConfigurations(RDB $oDB, $id)
    {
        $sql = "SELECT GC.id	
				FROM Client". sSCHEMA_POSTFIX .".GoMobileConfiguration_Tbl GC						
				WHERE GC.clientid = ". intval($id) ." AND GC.enabled = '1'
				ORDER BY GC.channel";
        //echo $sql ."\n";
        $aObj_Configurations = array();
        $res = $oDB->query($sql);
        while ($RS = $oDB->fetchName($res) )
        {
            $aObj_Configurations[] = self::produceConfig($oDB, $RS["ID"]);
        }

        return $aObj_Configurations;
    }
}
