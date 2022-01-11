<?php
namespace api\classes\merchantservices;

use AddonServiceTypeIndex;
use api\classes\merchantservices\configuration\AddonServiceType;
use api\classes\merchantservices\configuration\ProviderConfig;
use api\classes\merchantservices\MetaData\ClientServiceStatus;
use api\classes\merchantservices\Repositories\MerchantConfigRepository;

/**
 * Merchant Configuration Info
 *
 *
 * @package    Mechantservices
 * @subpackage Service Class
 */

class MerchantConfigInfo
{

    public function __construct()
    {

    }

    /**
     * @param MerchantConfigRepository $configRepository
     * @return array
     */
    public function getAllAddonConfig(MerchantConfigRepository $configRepository,AddonServiceType $addonServiceType=null) : array
    {
        return $configRepository->getAllAddonConfig($addonServiceType);
    }

    /**
     * @param MerchantConfigRepository $configRepository
     * @param array $aAddonConfig
     * @throws MerchantOnboardingException
     */
    public function saveAddonConfig(MerchantConfigRepository $configRepository,array $aAddonConfig, $isDeleteOldConfig = false)
    {
         $configRepository->saveAddonConfig($aAddonConfig, $isDeleteOldConfig);
    }

    /**
     * @param MerchantConfigRepository $configRepository
     * @param array $aAddonConfig
     * @throws MerchantOnboardingException
     */
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
            $addonServiceType = AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::valueOf($key),$key);
            if($addonServiceType === null)  { throw new MerchantOnboardingException(MerchantOnboardingException::INVALID_PARAMETER,"Invalid parameter {param:".$key."}");
            } else {
                if(empty($value) === true )  { throw new MerchantOnboardingException(MerchantOnboardingException::INVALID_PARAMETER_VALUE,"No parameters for ".$key); }
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

    public function deleteProviderConfig(MerchantConfigRepository $configRepository, $additionalParams = []){
        $id = $additionalParams['client_id'] ?? -1;
        $configRepository->deleteConfigDetails($id, 'provider');
    }

    /**
     * @param MerchantConfigRepository $configRepository
     * @param string $type
     * @param string $source
     * @param int $id
     * @return array
     */
    public function getPropertyConfig(MerchantConfigRepository $configRepository, string $type, string $source,int $id=-1) : array
    {
       return $configRepository->getPropertyConfig($type,$source,$id);
    }


    /**
     * @param MerchantConfigRepository $configRepository
     * @param int $id
     * @return array
     * @throws MerchantOnboardingException
     */
    public function getPSPPM(MerchantConfigRepository $configRepository, int $id=-1): array
    {
        return $configRepository->getPM("PSP",$id);
    }

    public function getRouteConfigIdByPSP(MerchantConfigRepository $configRepository, int $id) :array
    {
        return $configRepository->getRouteConfigIdByProvider($id);
    }

    public function getRoutes(MerchantConfigRepository $configRepository,int $pspType=-1,int $iPSPID=-1)
    {
        return $configRepository->getRoutes($pspType,$iPSPID);
    }



    /**
     * @param MerchantConfigRepository $configRepository
     * @return array
     */
    public function getAllPSPCredentials(MerchantConfigRepository $configRepository,int $pspid=-1,int $pspType=-1)
    {
        return $configRepository->getAllPSPCredentials($pspid,$pspType);
    }

    /**
     * @param MerchantConfigRepository $configRepository
     * @return array
     * @throws MerchantOnboardingException
     */
    public function getClientPM(MerchantConfigRepository $configRepository) : array
    {
        return $configRepository->getPM("CLIENT");
    }

    /**
     * @throws MerchantOnboardingException
     */
    public function saveClientPM(MerchantConfigRepository $configRepository, array $aPMIDs, $isDeleteOldConfig = false)
    {
         $configRepository->savePM("CLIENT",$aPMIDs, -1 ,$isDeleteOldConfig);
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
                    if(empty($ClientProperty->getValue()) === false)   { array_push($aUpdateProperty,$ClientProperty); }
                    if(empty($ClientProperty->getValue()) === true)  { array_push($aAddProperty,$ClientProperty); }
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

    /**
     * @param MerchantConfigRepository $configRepository
     * @param string $type
     * @param array $aPropertyInfo
     * @param int $id
     * @param array $aPMIds
     * @throws MerchantOnboardingException
     * @throws \SQLQueryException
     */
    public function savePropertyConfig(MerchantConfigRepository $configRepository,string $type,  array $aPropertyInfo,int $id=-1,array $aPMIds=array(), $isDeleteOldConfig = false)
    {
         $configRepository->savePropertyConfig($type,$aPropertyInfo,$id,$aPMIds, $isDeleteOldConfig);

    }

    /**
     * @param MerchantConfigRepository $configRepository
     * @param string $type
     * @param int $id
     * @param string $name
     * @param array $aCredentials
     * @return int
     * @throws MerchantOnboardingException
     * @throws \SQLQueryException
     */
    public function saveCredential(MerchantConfigRepository $configRepository,string $type, int $id, string $name, array $aCredentials)
    {
        return $configRepository->saveCredential($type, $id, $name, $aCredentials);
    }

    /**
     * @param MerchantConfigRepository $configRepository
     * @param string $type
     * @param int $id
     * @param string $name
     * @param array $aCredentials
     * @return mixed
     * @throws MerchantOnboardingException
     */
    public function updateCredential(MerchantConfigRepository $configRepository,string $type, int $id, string $name, array $aCredentials)
    {
         $configRepository->updateCredential($type, $id, $name, $aCredentials);
    }

    /**
     * @param MerchantConfigRepository $configRepository
     * @param string $type
     * @param array $aFeatures
     * @param int $id
     * @throws MerchantOnboardingException
     * @throws \SQLQueryException
     */
    public function saveFeatures(MerchantConfigRepository $configRepository,string $type, array $aFeatures, int $id)
    {
        $configRepository->saveConfigDetails($type, $aFeatures, $id, 'feature');
    }

    /**
     * @param MerchantConfigRepository $configRepository
     * @param string $type
     * @param array $aCountries
     * @param int $id
     * @throws MerchantOnboardingException
     * @throws \SQLQueryException
     */
    public function saveCountry(MerchantConfigRepository $configRepository,string $type, array $aCountries, int $id)
    {
        $configRepository->saveConfigDetails($type, $aCountries, $id,  'country');
    }

    /**
     * @param MerchantConfigRepository $configRepository
     * @param string $type
     * @param array $aCurrencies
     * @param int $id
     * @throws MerchantOnboardingException
     * @throws \SQLQueryException
     */
    public function saveCurrency(MerchantConfigRepository $configRepository,string $type, array $aCurrencies, int $id)
    {
        $configRepository->saveConfigDetails($type, $aCurrencies, $id, 'currency');
    }

    /**
     * @param MerchantConfigRepository $configRepository
     * @param string $type
     * @param array $aFeatures
     * @param int $id
     * @throws MerchantOnboardingException
     */
    public function updateFeatures(MerchantConfigRepository $configRepository,string $type, array $aFeatures, int $id)
    {
        $configRepository->updateConfigDetails($type, $aFeatures, $id, 'feature');
    }

    /**
     * @param MerchantConfigRepository $configRepository
     * @param string $type
     * @param array $aCountries
     * @param int $id
     * @throws MerchantOnboardingException
     */
    public function updateCountry(MerchantConfigRepository $configRepository,string $type, array $aCountries, int $id)
    {
        $configRepository->updateConfigDetails($type, $aCountries, $id,  'country');
    }

    /**
     * @param MerchantConfigRepository $configRepository
     * @param string $type
     * @param array $aCurrencies
     * @param int $id
     * @throws MerchantOnboardingException
     */
    public function updateCurrency(MerchantConfigRepository $configRepository,string $type, array $aCurrencies, int $id)
    {
        $configRepository->updateConfigDetails($type, $aCurrencies, $id, 'currency');
    }

    /**
     * @param MerchantConfigRepository $configRepository
     * @param string $type
     * @param array $aPropertyInfo
     * @param int $id
     * @param array $aPMIds
     * @throws MerchantOnboardingException
     * @throws \SQLQueryException
     */
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

        if($type === 'ROUTE')
        {
            if(count($additionalParams) === 2 && isset($additionalParams['client_id']) && isset($additionalParams['route_conf_id']))
            {
                $configRepository->deleteAllRouteConfig($type, $additionalParams['route_conf_id']);
                return true;
            }
        }

        $value =  $additionalParams['p_id'];
        $pms = $additionalParams['pm'];
        $features = $additionalParams['r_f']??'';
        $countries = $additionalParams['country']??'';
        $currencies = $additionalParams['currency']??'';



        if(empty($value) === true && empty($pms) === true) {
            throw new MerchantOnboardingException(MerchantOnboardingException::INVALID_PARAMETER_VALUE, "No parameters for ID");
        }
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
            $a_pms = explode(',', $pms); // Explode String
            foreach ($a_pms as $pm)
            {
                if(is_numeric($pm) === false) { throw new MerchantOnboardingException(MerchantOnboardingException::INVALID_PARAMETER_VALUE,"Invalid parameter for PM {param:".$pm."}"); }
            }
        }
        if(empty($features) === false)
        {
            $a_features = explode(',', $features);
            foreach ($a_features as $feature)
            {
                if(is_numeric($feature) === false) { throw new MerchantOnboardingException(MerchantOnboardingException::INVALID_PARAMETER_VALUE,"Invalid parameter for Feature {param:".$feature."}"); }
            }

        }
        if(empty($countries) === false)
        {
            $a_countries = explode(',', $countries);
            foreach ($a_countries as $country)
            {
                if(is_numeric($country) === false) { throw new MerchantOnboardingException(MerchantOnboardingException::INVALID_PARAMETER_VALUE,"Invalid parameter for Country {param:".$country."}"); }
            }
        }
        if(empty($currencies) === false)
        {
            $a_currencies = explode(',', $currencies);
            foreach ($a_currencies as $currency)
            {
                if(is_numeric($currency) === false) { throw new MerchantOnboardingException(MerchantOnboardingException::INVALID_PARAMETER_VALUE,"Invalid parameter for Currency {param:".$currency."}"); }
            }
        }
        $configRepository->deletePropertyConfig($type,$value,$rid,$pms,$features,$countries,$currencies);
    }

    /**
     * @throws MerchantOnboardingException
     */
    public function saveClientURL(MerchantConfigRepository $configRepository, array $urls, $isDeleteOldConfig = false)
    {
        $configRepository->saveClientURL($urls, $isDeleteOldConfig);
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

    public function saveProvider(MerchantConfigRepository $configRepository, array $aProviderConfig)
    {
        $configRepository->saveProviders($aProviderConfig);
    }

    public function getRouteConfiguration(MerchantConfigRepository $configRepository, int $id,bool $bAllConfig):ProviderConfig
    {
        return $configRepository->getRouteConfiguration($id,$bAllConfig);
    }

    public function updateRouteConfig(MerchantConfigRepository $configRepository, ProviderConfig $provider)
    {
        $configRepository->updateRouteConfig($provider);
    }

    public function updatePSPConfig(MerchantConfigRepository $configRepository, $providerConfig)
    {
        $configRepository->updatePSPConfig($providerConfig);
    }

}