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
     * Default Constructor
     *
     * @param 	integer $id 			The unique ID for the client's Merchant Account configuration for the Payment Service Provider
     * @param 	string $name 			Param name of the GoMobile configuration.
     * @param 	string $value 		    Param value as configured with GoMobile
     * @param 	string $channel	 		Channel used for sending the GoMobile communication
     */
    public function __construct($id, $name, $value)
    {
        parent::__construct($id, $name);

        $this->_sValue = trim($value);
    }
    public function getValue() { return $this->_sValue; }

    public function toXML()
    {
        $xml = '<gomobile-configuration-param id="' . $this->getID() . '" name="' . $this->getName() . '">'. $this->_sValue .'</gomobile-configuration-param>';

        return $xml;
    }

    public static function produceConfigurations(array $aAdditionalProperty)
    {
        foreach ($aAdditionalProperty as $additionalProperty)
        {
            if(strpos($additionalProperty['key'],'GOMOBILE') !== false)
            {
                $aObj_Configurations[] = new ClientGoMobileConfig(0, $additionalProperty['key'], $additionalProperty['value']);
            }
        }


        return $aObj_Configurations;
    }
}
