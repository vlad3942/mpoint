<?php

use api\classes\merchantservices\Repositories\MerchantConfigRepository;

class MerchantConfigInfo
{
    private MerchantConfigRepository $_configRepository;
    private ClientConfig $_clientConfig;
    public function __construct(MerchantConfigRepository $configRepository,ClientConfig $clientConfig)
    {
        $this->_configRepository = $configRepository;
        $this->_clientConfig = $clientConfig;
    }

    private function getMerchantConfigRepo():MerchantConfigRepository { return $this->_configRepository; }

    public function getAllAddonConfig() : array
    {
        return $this->getMerchantConfigRepo()->getAllAddonConfig();
    }

    public function saveAddonConfig(array $aAddonConfig) : array
    {
        return $this->getMerchantConfigRepo()->saveAddonConfig($aAddonConfig);
    }
}