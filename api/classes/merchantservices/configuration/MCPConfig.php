<?php
namespace api\classes\merchantservices\configuration;


use AddonServiceTypeIndex;

/**
 *
 * @package    Mechantservices
 * @subpackage MCP config
 */
class MCPConfig extends BaseConfig
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
    public function __construct(array $config,array $property,string $subType='MCP',string $name='')
    {
        $this->_aConfig = $config;
        $this->_iServiceType = AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::eMCP,$subType);
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

