<?php
namespace api\classes\merchantservices;

use AddonServiceTypeIndex;
use api\classes\merchantservices\configuration\AddonServiceType;
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

    /**
     * @throws MerchantOnboardingException|\SQLQueryException
     *
     */
    public function deleteAddonConfig(MerchantConfigRepository $configRepository, array $additionalParams)
    {
        unset($additionalParams['client_id']);
        $aDeleteConfig = array();
        foreach ($additionalParams as $key => $value)
        {
            $addonServiceType = AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::valueOf($key),'');
            if($addonServiceType === null) throw new MerchantOnboardingException(MerchantOnboardingException::INVALID_PARAMETER,"Invalid parameter {param:".$key."}");
            else
            {
                if(empty($value) === true ) throw new MerchantOnboardingException(MerchantOnboardingException::INVALID_PARAMETER_VALUE,"No parameters for ".$key);
                $aIds = explode(',', $value);
                foreach ($aIds as $id)
                {
                    if(is_numeric($id) === false) { throw new MerchantOnboardingException(MerchantOnboardingException::INVALID_PARAMETER_VALUE,"Invalid parameter for ".$key." {param:".$id."}"); }
                }

                $aDeleteConfig[] = array(1=>$addonServiceType,2=>$value);
            }

        }
        $configRepository->deleteAddonConfig($aDeleteConfig);
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
     * @throws MerchantOnboardingException
     * @throws \SQLQueryException
     */
    public function deletePropertyConfig(MerchantConfigRepository $configRepository, string $type, array $additionalParams,int $rid=-1)
    {
        $value =  $additionalParams['id'];
        $pms = $additionalParams['pm'];

        if(empty($value) === true && empty($pms) === true) throw new MerchantOnboardingException(MerchantOnboardingException::INVALID_PARAMETER_VALUE,"No parameters for ID");
        if(empty($value) === false)
        {
            $aIds = explode(',', $value);
            foreach ($aIds as $id)
            {
                if(is_numeric($id) === false) { throw new MerchantOnboardingException(MerchantOnboardingException::INVALID_PARAMETER_VALUE,"Invalid parameter for ID {param:".$id."}"); }
            }
        }

        if(empty($pms) === false)
        {
            foreach ($pms as $pm)
            {
                if(is_numeric($pm) === false) { throw new MerchantOnboardingException(MerchantOnboardingException::INVALID_PARAMETER_VALUE,"Invalid parameter for PM {param:".$pm."}"); }
            }
        }
        $configRepository->deletePropertyConfig($type,$value,$rid,$pms);
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

    /***
     * @param \api\classes\merchantservices\Repositories\MerchantConfigRepository $configRepository
     * @param array                                                               $aData
     *
     * @throws \SQLQueryException
     * @throws \api\classes\merchantservices\MerchantOnboardingException
     */
    public function addClientConfigurationsData(MerchantConfigRepository $configRepository, array $aProperty): void
    {
        // Add Properties
        $configRepository->savePropertyConfig('CLIENT', $aProperty);
    }

    /***
     * Modify Collection against Client
     *
     * 1. Properties
     * 2. Urls
     * 3. StoreFront
     *
     * @param \api\classes\merchantservices\Repositories\MerchantConfigRepository $configRepository
     * @param array                                                               $aModifyData
     *
     * @return void
     * @throws \SQLQueryException
     * @throws \api\classes\merchantservices\MerchantOnboardingException
     */
    public function modifyClientConfigurationsData(MerchantConfigRepository $configRepository, array $aModifyData): void
    {
        $configRepository->modifyClientConfigurationsData($aModifyData);
    }
}