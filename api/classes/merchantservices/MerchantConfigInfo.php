<?php
namespace api\classes\merchantservices;

use AddonServiceTypeIndex;
use api\classes\merchantservices\configuration\AddonServiceType;
use api\classes\merchantservices\MetaData\ClientServiceStatus;
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
        return $configRepository->getPM("ROUTE",$id);
    }

    public function getClientPM(MerchantConfigRepository $configRepository) : array
    {
        return $configRepository->getPM("CLIENT");
    }

    /**
     * @throws MerchantOnboardingException
     */
    public function saveClientPM(MerchantConfigRepository $configRepository, array $aPMIDs)
    {
         $configRepository->savePM("CLIENT",$aPMIDs);
    }
    /**
     * @throws MerchantOnboardingException
     */
    public function updateClientPM(MerchantConfigRepository $configRepository, array $aPMIDs)
    {
        $configRepository->updatePM("CLIENT",$aPMIDs);
    }

    /**
     * @throws MerchantOnboardingException
     * @throws \SQLQueryException
     */
    public function updateClientdetails(MerchantConfigRepository $configRepository, array $aClientParam)
    {
        if($aClientParam['SSO_PREFERENCE'] || $aClientParam['TIMEZONE'])
        {
            $aClientProperty = $configRepository->getPropertyConfig("CLIENT","ALL",-1,array("'SSO_PREFERENCE'","'TIMEZONE'"),false);
            $aAddProperty = array();
            $aUpdateProperty = array();
            foreach ($aClientProperty as $ClientProperty)
            {
                if($aClientParam['SSO_PREFERENCE'] && $ClientProperty->getName() === 'SSO_PREFERENCE' )
                {
                    if(empty($ClientProperty->getValue()) === false) array_push($aUpdateProperty,$ClientProperty);
                    if(empty($ClientProperty->getValue()) === true) array_push($aAddProperty,$ClientProperty);
                    $ClientProperty->setValue($aClientParam['SSO_PREFERENCE']);
                    unset($aClientParam['SSO_PREFERENCE']);
                }
                if($aClientParam['TIMEZONE'] && $ClientProperty->getName() === 'TIMEZONE')
                {
                    if(empty($ClientProperty->getValue()) === false) array_push($aUpdateProperty,$ClientProperty);
                    if(empty($ClientProperty->getValue()) === true) array_push($aAddProperty,$ClientProperty);
                    $ClientProperty->setValue($aClientParam['TIMEZONE']);
                    unset($aClientParam['TIMEZONE']);
                }
            }

            $configRepository->updatePropertyConfig("CLIENT",$aUpdateProperty);
            $configRepository->savePropertyConfig("CLIENT",$aAddProperty);
        }

        $configRepository->updateClientdetails($aClientParam);
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
        $value =  $additionalParams['p_id'];
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
     * @throws MerchantOnboardingException
     */
    public function saveVelocityURL(MerchantConfigRepository $configRepository, array $urls)
    {
        $configRepository->saveVelocityURL($urls);
    }

    /**
     * @throws MerchantOnboardingException
     * @throws \SQLQueryException
     */
    public function saveClientUrls(MerchantConfigRepository $configRepository, array $urls)
    {
        $configRepository->saveClientUrls($urls);
    }

    /**
     * @throws MerchantOnboardingException
     */
    public function updateVelocityURL(MerchantConfigRepository $configRepository, array $urls)
    {
        $configRepository->updateVelocityURL($urls);
    }
    /**
     * @throws MerchantOnboardingException
     * @throws \SQLQueryException
     */
    public function updateClientUrls(MerchantConfigRepository $configRepository, array $urls)
    {
        $configRepository->saveClientUrls($urls,'UPDATE');
    }

    /**
     * @throws MerchantOnboardingException
     * @throws \SQLQueryException
     */
    public function updateAddonServiceStatus(MerchantConfigRepository $configRepository, ClientServiceStatus  $clService)
    {
        $configRepository->updateAddonServiceStatus($clService);
    }

    /**
     * @throws MerchantOnboardingException
     * @throws \SQLQueryException
     */
    public function updateAccountConfig(MerchantConfigRepository $configRepository, array $aClAccountConfig)
    {
        $configRepository->updateAccountConfig($aClAccountConfig);
    }


}