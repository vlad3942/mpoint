<?php
namespace api\classes\merchantservices;
use api\classes\merchantservices\Repositories\MerchantConfigRepository;

class MerchantConfigInfo
{

    public function __construct()
    {

    }


    public function getAllAddonConfig(MerchantConfigRepository $configRepository) : array
    {
        return $configRepository->getAllAddonConfig();
    }

    public function saveAddonConfig(MerchantConfigRepository $configRepository,array $aAddonConfig)
    {
        return $configRepository->saveAddonConfig($aAddonConfig);
    }

    public function updateAddonConfig(MerchantConfigRepository $configRepository,array $aAddonConfig)
    {
        return $configRepository->updateAddonConfig($aAddonConfig);
    }
}