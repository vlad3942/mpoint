<?php
include_once('IConfig.php');

class MCPConfig implements IConfig
{

    private array $_aConfig;
    public function __construct(array $config)
    {
        $this->_aConfig = $config;
    }

    public function getConfiguration() : array
    {
       return $this->_aConfig;
    }

    public function getServiceType()
    {
        // TODO: Implement getServiceType() method.
    }

    public function getProperties()
    {
        // TODO: Implement getProperties() method.
    }
}

