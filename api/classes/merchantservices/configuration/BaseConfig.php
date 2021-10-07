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
    protected function setPropertiesFromXML(SimpleXMLElement &$oXML){}
    public function toXML():string
    {
        $xml = sprintf("<%s>",strtolower(str_replace('config','_config',strtolower($this->getServiceType()->getClassName()))));

        if($this->getServiceType()->getID() == AddonServiceTypeIndex::eSPLIT_PAYMENT || $this->getServiceType()->getID() == AddonServiceTypeIndex::eFraud)
        {
            $xml .= sprintf('<sub_type>%s</sub_type>',$this->getServiceType()->getSubType());
        }
        $xml .= "<addon_configurations>";
        foreach ($this->getConfiguration() as $config)
        {
            $xml .= $config->toXML();
        }
        $xml .= "</addon_configurations>";
        if(empty($this->getProperties()) === false)
        {
            foreach ($this->getProperties() AS $key => $value)
            {

                if(gettype($value) === 'boolean')
                {
                    $value = \General::bool2xml($value);
                }
                $xml .= sprintf('<%s>%s</%s>',strtolower($key),$value,strtolower($key));
            }
        }
        $xml .= sprintf("</%s>",strtolower(str_replace('config','_config',strtolower($this->getServiceType()->getClassName()))));


        return $xml;
    }

    public static function produceFromXML(SimpleXMLElement &$oXML):array
    {
        $aBaseconfig = array();

        foreach ($oXML as $key=>$addon_config_detail)
        {
            $addonSubType = (string)$addon_config_detail->sub_type;
            if(strpos($key, '_configs') !== false)
            {
                $aConfigs = BaseConfig::produceFromXML($addon_config_detail);
                $aBaseconfig = array_merge($aBaseconfig,$aConfigs);
                continue;
            }
            $addonServiceTYpe = AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::valueOf(str_replace('_config','',$key)),'');

            $aServiceCon = array();
            foreach ($addon_config_detail->addon_configurations->addon_confguration as $addon_configuration)
            {

                $serviceConfig = ServiceConfig::produceFromXML($addon_configuration);
                array_push($aServiceCon,$serviceConfig);
            }

            $className = __NAMESPACE__ . '\\' . $addonServiceTYpe->getClassName();
            $config = new $className($aServiceCon,array(),$addonSubType);
            $config->setPropertiesFromXML($addon_config_detail);
            array_push($aBaseconfig,$config);

        }

        return $aBaseconfig;
    }



}