<?php
namespace api\classes\merchantservices\configuration;


use AddonServiceTypeIndex;
use SimpleXMLElement;
use function PHPUnit\Framework\isEmpty;

class FraudConfig extends BaseConfig
{

    private array $_aConfig;
    private AddonServiceType $_iServiceType;
    private array $_aProperty;
    public function __construct(array $config,array $property,string $subType='Fraud')
    {
        $this->_aConfig = $config;
        $this->_iServiceType = AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::eFraud,$subType);
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
        if(count($oXML->is_rollback)>0)
        {
            $this->_aProperty = array("is_rollback"=>\General::xml2bool((string)$oXML->is_rollback));
        }
    }

}

