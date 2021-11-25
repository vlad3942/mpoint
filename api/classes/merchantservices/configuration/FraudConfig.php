<?php
namespace api\classes\merchantservices\configuration;


use AddonServiceTypeIndex;
use SimpleXMLElement;
use function PHPUnit\Framework\isEmpty;

/**
 *
 * @package    Mechantservices
 * @subpackage Fraud Config
 */

class FraudConfig extends BaseConfig
{

    /**
     * @var array
     */
    private array $_aConfig;

    /**
     * @var AddonServiceType|null
     */
    private AddonServiceType $_iServiceType;

    /**
     * @var array
     */
    private array $_aProperty;

    /**
     * @param array $config
     * @param array $property
     * @param string $subType
     */
    public function __construct(array $config,array $property,string $subType='Fraud')
    {
        $this->_aConfig = $config;
        $this->_iServiceType = AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::eFraud,$subType);
        $this->_aProperty = $property;
    }

    /**
     * @return array
     */
    public function getConfiguration() : array
    {
        return $this->_aConfig;
    }

    /**
     * @return AddonServiceType
     */
    public function getServiceType() : AddonServiceType
    {
        return $this->_iServiceType;
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->_aProperty;
    }

    /**
     * @param SimpleXMLElement $oXML
     */
    protected function setPropertiesFromXML(SimpleXMLElement &$oXML)
    {
        if(count($oXML->is_rollback)>0)
        {
            $this->_aProperty = array("is_rollback"=>\General::xml2bool((string)$oXML->is_rollback));
        }
    }
}

