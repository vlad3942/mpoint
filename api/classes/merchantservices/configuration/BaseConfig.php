<?php

namespace api\classes\merchantservices\configuration;


use AddonServiceTypeIndex;
use SimpleXMLElement;

/**
 *
 * @package    Mechantservices
 * @subpackage Common Base Class
 */

Abstract class BaseConfig
{

    public Abstract function getConfiguration() : array;
    public Abstract function getServiceType() : AddonServiceType;
    public Abstract function getProperties();
    public Abstract function getName():string;

    /**
     * @param string $keyfun
     * @return array
     */
    public function  toKeyValueConfigArray(string $keyfun) :array
    {
        $aConfig = $this->getConfiguration();
        $aKeyValueConfig = array();
        foreach ($aConfig as $config)
        {
            if(isset($aKeyValueConfig[$config->$keyfun()]) === true) {
                array_push($aKeyValueConfig[$config->$keyfun()], $config);
            }
            else {
                $aKeyValueConfig[$config->$keyfun()] =array($config);
            }
        }
      return $aKeyValueConfig;
    }

    protected function setPropertiesFromXML(SimpleXMLElement &$oXML){}

    /**
     * @return string
     */
    public function toXML():string
    {
        $xml = sprintf("<%s>",strtolower(str_replace('config','_config',strtolower($this->getServiceType()->getClassName()))));

        if($this->getServiceType()->getID() == AddonServiceTypeIndex::eSPLIT_PAYMENT || $this->getServiceType()->getID() == AddonServiceTypeIndex::eFraud)
        {
            $xml .= sprintf('<sub_type>%s</sub_type>',$this->getServiceType()->getSubType());
            if(empty($this->getName()) === false)
            {
                $xml .= "<name>".$this->getName()."</name>";
            }
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

    /**
     * @param SimpleXMLElement $oXML
     * @return array
     */
    public static function produceFromXML(SimpleXMLElement &$oXML):array
    {
        $aBaseconfig = array();

        foreach ($oXML as $key => $addon_config_detail) {
            $addonSubType = (string)$addon_config_detail->sub_type;
            $name = (string)$addon_config_detail->name;
            if (strpos($key, '_configs') !== false) {
                $aConfigs = BaseConfig::produceFromXML($addon_config_detail);
                $aBaseconfig = array_merge($aBaseconfig, $aConfigs);
                continue;
            }
            $addonServiceTYpe = AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::valueOf(str_replace('_config', '', $key)), '');

            $aServiceCon = array();
            foreach ($addon_config_detail->addon_configurations->addon_configuration as $addon_configuration) {

                $serviceConfig = ServiceConfig::produceFromXML($addon_configuration);
                array_push($aServiceCon, $serviceConfig);
            }

            $className = __NAMESPACE__ . '\\' . $addonServiceTYpe->getClassName();
            $config = new $className($aServiceCon, array(), $addonSubType,$name);
            $config->setPropertiesFromXML($addon_config_detail);
            array_push($aBaseconfig, $config);
        }
        return $aBaseconfig;
    }
}