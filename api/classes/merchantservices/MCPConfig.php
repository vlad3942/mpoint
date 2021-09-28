<?php
namespace api\classes\merchantservices;


use AddonServiceTypeIndex;

class MCPConfig implements IConfig
{

    private array $_aConfig;
    private AddonServiceType $_iServiceType;
    public function __construct(array $config)
    {
        $this->_aConfig = $config;
        $this->_iServiceType = AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::eMCP);
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
        // TODO: Implement getProperties() method.
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
        $xml .= "</addon_configurations>";


        return $xml;
    }
}

