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
         $configRepository->saveAddonConfig($aAddonConfig);
    }

    public function updateAddonConfig(MerchantConfigRepository $configRepository,array $aAddonConfig)
    {
         $configRepository->updateAddonConfig($aAddonConfig);
    }

    public function getPropertyConfig(MerchantConfigRepository $configRepository, string $type, string $source,int $id=-1) : array
    {
       return $configRepository->getPropertyConfig($type,$source,$id);
    }

    public function getRoutePM(MerchantConfigRepository $configRepository, int $id=-1) : array
    {
        return $configRepository->getRoutePM($id);
    }

    public function savePropertyConfig(MerchantConfigRepository $configRepository,string $type,  array $aPropertyInfo,int $id=-1,array $aPMIds=array())
    {
         $configRepository->savePropertyConfig($type,$aPropertyInfo,$id,$aPMIds);

    }

    public function updatePropertyConfig(MerchantConfigRepository $configRepository,string $type,  array $aPropertyInfo,int $id=-1,array $aPMIds=array())
    {
        $configRepository->updatePropertyConfig($type,$aPropertyInfo,$id,$aPMIds);

    }

    /**
     * Get Client Configuration details
     * @param \api\classes\merchantservices\Repositories\MerchantConfigRepository $configRepository
     *
     * @return array
     */
    public function getClientConfigurations(MerchantConfigRepository $configRepository): array
    {
        return [
            'info'                  => $configRepository->getClientDetailById(),
            'client_urls'           => $configRepository->getClientURLByClientId(),
            'payment_method_ids'    => $configRepository->getPMIdsByClientId(),
            'storefronts'           => $configRepository->getStoreFrontByClientId(),
            'property_details'      => $configRepository->getPropertyConfig('CLIENT', 'ALL'),
            'services'               => $configRepository->getServiceStatusByClientId()
        ];
    }
}