<?php
namespace api\classes\merchantservices\configuration;


use AddonServiceTypeIndex;
use SimpleXMLElement;

class Split_PaymentConfig extends BaseConfig
{

    private array $_aConfig;
    private AddonServiceType $_iServiceType;
    private array $_aProperty;
    public function __construct(array $config,array $property,string $subType='Split_payment')
    {
        $this->_aConfig = $config;
        $this->_iServiceType = AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::eSPLIT_PAYMENT,$subType);
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

    protected function setPropertiesFromXML(SimpleXMLElement &$oXML)
    {
        $this->_aProperty = array();
        if(count($oXML->is_rollback)>0)
        {
            $this->_aProperty["is_rollback"] = \General::xml2bool((string)$oXML->is_rollback);
        }
        if(count($oXML->is_reoffer)>0)
        {
            $this->_aProperty["is_reoffer"] = \General::xml2bool((string)$oXML->is_reoffer);
        }
    }
}

