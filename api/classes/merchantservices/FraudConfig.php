<?php
namespace api\classes\merchantservices;


use AddonServiceTypeIndex;
use function PHPUnit\Framework\isEmpty;

class FraudConfig implements IConfig
{

    private array $_aConfig;
    private AddonServiceType $_iServiceType;
    private array $_aProperty;
    public function __construct(array $config,array $property)
    {
        $this->_aConfig = $config;
        $this->_iServiceType = AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::eFraud);
        $this->_aProperty = $property;
    }

    public function getConfiguration() : array
    {
        return $this->_aConfig;
    }

    public function getServiceType() : AddonServiceType
    {
        return $this->_iServiceType;
    }

    public function getProperties()
    {
        return $this->_aProperty;
    }

    public function toXML():string
    {
        $xml = "<addon_configurations>";
        $xml .= sprintf("<addon_type>%s</addon_type>",$this->getServiceType()->getType());
        $xml .= sprintf("<addon_subtype>%s</addon_subtype>",$this->getServiceType()->getSubType());
        foreach ($this->_aConfig as $config)
        {
            $xml .= $config->toXML();
        }
        if(empty($this->_aProperty) === false)
        {
            $xml .= "<properties>";
            foreach ($this->_aProperty AS $key => $value)
            {
                $xml .= "<property>";
                $xml .= "<name>".$key."</name>";
                $xml .= "<value>".$value."</value>";
                $xml .= "</property>";

            }
        }

        $xml .= "</properties>";

        $xml .= "</addon_configurations>";


        return $xml;
    }
}

