<?php
namespace api\classes\merchantservices\configuration;


use AddonServiceTypeIndex;
use SimpleXMLElement;

/**
 *
 * @package    Mechantservices
 * @subpackage MPI Config
 */
class MPIConfig extends BaseConfig
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
    public function __construct(array $config,array $property,string $subType='MPI')
    {
        $this->_aConfig = $config;
        $this->_iServiceType = AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::eMPI,$subType);
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
        $this->_aProperty = array();
        if(count($oXML->version)>0)
        {
            $this->_aProperty["version"] = (string)$oXML->version;
        }
    }
}

