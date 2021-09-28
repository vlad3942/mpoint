<?php
namespace api\classes\merchantservices;


use AddonServiceTypeIndex;

class SplitPaymentConfig implements IConfig
{

    private array $_aConfig;
    private AddonServiceType $_iServiceType;
    public function __construct(array $config)
    {
        $this->_aConfig = $config;
        $this->_iServiceType = AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::ePCC);
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

    public function toXML(): string
    {
       return "";
    }
}

