<?php

namespace api\classes\merchantservices\configuration;

use AddonServiceTypeIndex;

/**
 *
 * @package    Mechantservices
 * @subpackage Tokenization Confog
 */
class TokenizationConfig extends BaseConfig
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
     * @var array
     */
    private string $_sName;

    /**
     * @param array $config
     * @param array $property
     * @param string $subType
     */
    public function __construct(array $config,array $property,string $subType='Tokenization',string $name='')
    {
        $this->_aConfig = $config;
        $this->_iServiceType = AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::eTOKENIZATION,"Tokenization");
        $this->_aProperty = $property;
        $this->_sName = $name;
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
     * @return string
     */
    public function getName() :string
    {
        return $this->_sName;
    }

}