<?php

/*
interface BaseConfig
{

    public function getConfiguration();

    public function getServiceType();

    public function getProperties();

}
*/
namespace api\classes\merchantservices\configuration;


use AddonServiceTypeIndex;
use SimpleXMLElement;

Abstract class BaseConfig
{

    public Abstract function getConfiguration() : array;
    public Abstract function getServiceType() : AddonServiceType;
    public Abstract function getProperties();
    public function toXML():string
    {
        $xml = "<addon_config_detail>";
        $xml .= $this->getServiceType()->toXML();
        $xml .= "<addon_configurations>";
        foreach ($this->getConfiguration() as $config)
        {
            $xml .= $config->toXML();
        }
        $xml .= "</addon_configurations>";

        if(empty($this->getProperties()) === false)
        {
            $xml .= "<properties>";
            foreach ($this->getProperties() AS $key => $value)
            {
                $xml .= "<property>";
                $xml .= "<name>".$key."</name>";
                $xml .= "<value>".$value."</value>";
                $xml .= "</property>";

            }
            $xml .= "</properties>";
        }


        $xml .= "</addon_config_detail>";


        return $xml;
    }

    public static function produceFromXML(SimpleXMLElement &$oXML):array
    {
        $aBaseconfig = array();

        foreach ($oXML->addon_config_detail as $addon_config_detail)
        {
            $addonSubType = (string)$addon_config_detail->addon_subtype;
            $addonServiceTYpe = AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::valueOf($addonSubType));
            $aServiceCon = array();
            foreach ($addon_config_detail->addon_configurations->addon_configuration as $addon_configuration)
            {
                $serviceConfig = ServiceConfig::produceFromXML($addon_configuration);
                array_push($aServiceCon,$serviceConfig);
            }
            $className = __NAMESPACE__ . '\\' . $addonServiceTYpe->getClassName();
            $config = new $className($aServiceCon,array());
            array_push($aBaseconfig,$config);

        }

        return $aBaseconfig;
    }



}