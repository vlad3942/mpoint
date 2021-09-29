<?php
namespace api\classes\merchantservices\configuration;


use AddonServiceTypeIndex;

class MCPConfig extends BaseConfig
{

    private array $_aConfig;
    private AddonServiceType $_iServiceType;
    private array $_aProperty;
    public function __construct(array $config,array $property)
    {
        $this->_aConfig = $config;
        $this->_iServiceType = AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::eMCP);
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


}

