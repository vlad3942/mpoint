<?php
namespace api\classes\merchantservices\Services;


use AddonServiceTypeIndex;
use api\classes\merchantservices\configuration\AddonServiceType;
use api\classes\merchantservices\configuration\PropertyInfo;
use api\classes\merchantservices\configuration\ProviderConfig;
use api\classes\merchantservices\MerchantConfigInfo;
use api\classes\merchantservices\MerchantOnboardingException;
use api\classes\merchantservices\MetaData\ClientServiceStatus;
use api\classes\merchantservices\Repositories\MerchantConfigRepository;

class ConfigurationService
{

    private MerchantConfigRepository $merchantConfigRepository;
    private MerchantConfigInfo $merchantAggregateRoot;
    
    public function __construct(\RDB &$conn,int $iClientId)
    {
        $this->merchantConfigRepository = new MerchantConfigRepository($conn,$iClientId);
        $this->merchantAggregateRoot = new MerchantConfigInfo();
    }

    private function getRepository():MerchantConfigRepository
    {
        return $this->merchantConfigRepository;
    }

    private function getAggregateRoot() : MerchantConfigInfo
    {
        return $this->merchantAggregateRoot;
    }
    public function getClientInfo() : \ClientConfig
    {
        return $this->merchantConfigRepository->getClientInfo();
    }

    public function getAddonConfig( $additionalParams = []) : string
    {
        $addonServiceType = null;
        if(isset($additionalParams['type']) === true)
        {
            $serviceTypeid = AddonServiceTypeIndex::valueOf($additionalParams['type']);
            if($serviceTypeid !== 0)
            {
                $addonServiceType = AddonServiceType::produceAddonServiceTypebyId($serviceTypeid,$additionalParams['type']);
            }

        }
        $aAddonConf = $this->getAggregateRoot()->getAllAddonConfig($this->getRepository(),$addonServiceType);
        $responseXml = "<addon_configuration_response>";
        $sFraudXML ='';
        $sSplitPaymentXML ='';

        foreach ($aAddonConf as &$addonconfig)
        {
            if($addonconfig->getServiceType()->getID() === AddonServiceTypeIndex::eFraud)
            {
                $sFraudXML .= $addonconfig->toXML();
                continue;
            }
            else if($addonconfig->getServiceType()->getID() === AddonServiceTypeIndex::eSPLIT_PAYMENT)
            {
                $sSplitPaymentXML .= $addonconfig->toXML();
                continue;
            }
              $responseXml .= $addonconfig->toXML();
        }
        if(empty($sFraudXML) === false)
        {
            $addonType = AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::eFraud,'');
            $responseXml .= sprintf("<%ss>",strtolower(str_replace('config','_config',strtolower($addonType->getClassName()))));
            $responseXml .=$sFraudXML;
            $responseXml .= sprintf("</%ss>",strtolower(str_replace('config','_config',strtolower($addonType->getClassName()))));
        }

        if(empty($sSplitPaymentXML) === false)
        {
            $addonType = AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::eSPLIT_PAYMENT,'');
            $responseXml .= sprintf("<%ss>",strtolower(str_replace('config','_config',strtolower($addonType->getClassName()))));
            $responseXml .=$sSplitPaymentXML;
            $responseXml .= sprintf("</%ss>",strtolower(str_replace('config','_config',strtolower($addonType->getClassName()))));
        }

        $responseXml .= "</addon_configuration_response>";
        return $responseXml;
    }

    public function saveAddonConfig(&$addonConfig, $additionalParams = [], $isDeleteOldConfig = false)
    {
      $this->getAggregateRoot()->saveAddonConfig($this->getRepository(),$addonConfig, $isDeleteOldConfig);
    }

    public function updateAddonConfig(&$addonConfig, $additionalParams = [])
    {
         $this->getAggregateRoot()->updateAddonConfig($this->getRepository(),$addonConfig);
    }

    /**
     * @throws \api\classes\merchantservices\MerchantOnboardingException
     * @throws \SQLQueryException
     */
    public function deleteAddonConfig($additionalParams = [])
    {
        $this->getAggregateRoot()->deleteAddonConfig($this->getRepository(),$additionalParams);
    }

    public function deleteProviderConfig($additionalParams = [])
    {
        $this->getAggregateRoot()->deleteProviderConfig($this->getRepository(), $additionalParams);
    }

    /**
     * @param string $type
     * @param string $source
     * @param int $id
     * @return array
     */
    public function getPropertyConfig(string $type,string $source,int $id=-1) : array
    {
        return $this->getAggregateRoot()->getPropertyConfig($this->getRepository(),$type,$source,$id);
    }


    /**
     * @param int $pspConfigId
     * @return array
     */
    public function getPSPPM(int $pspConfigId): array
    {
        return $this->getAggregateRoot()->getPSPPM($this->getRepository(),$pspConfigId);
    }

    public function getRoutes(int $pspType=-1,int $iPSPID=-1)
    {
        return $this->getAggregateRoot()->getRoutes($this->getRepository(),$pspType,$iPSPID);
    }

    /**
     * @return array
     */
    public function getAllPSPCredentials(int $pspid=-1,int $pspType=-1)
    {
        return $this->getAggregateRoot()->getAllPSPCredentials($this->getRepository(),$pspid,$pspType);
    }

    /**
     * @return array
     */
    public function getClientPM() : array
    {
        return $this->getAggregateRoot()->getClientPM($this->getRepository());

    }

    /**
     * @throws MerchantOnboardingException
     */
    public function saveClientPM(array &$aPMIDs, $isDeleteOldConfig = false)
    {
        $this->getAggregateRoot()->saveClientPM($this->getRepository(),$aPMIDs, $isDeleteOldConfig);
    }
    /**
     * @throws MerchantOnboardingException
     */
    public function updateClientPM(array &$aPMIDs)
    {
        $this->getAggregateRoot()->updateClientPM($this->getRepository(),$aPMIDs);
    }

    /**
     * @param array $aClientParam
     * @throws MerchantOnboardingException
     * @throws \SQLQueryException
     */
    public function updateClientdetails(array &$aClientParam)
    {
        $this->getAggregateRoot()->updateClientdetails($this->getRepository(),$aClientParam);
    }

    /**
     * @param string $type
     * @param array $aPropertyInfo
     * @param int $id
     * @param array $aPMIds
     */
    public function savePropertyConfig(string $type,array &$aPropertyInfo,int $id=-1, array $aPMIds=array(), $isDeleteOldConfig = false)
    {
       $this->getAggregateRoot()->savePropertyConfig($this->getRepository(),$type,$aPropertyInfo,$id,$aPMIds, $isDeleteOldConfig);
    }

    /**
     * @param string $type
     * @param int $id
     * @param string $name
     * @param array $aCredentials
     * @return int
     */
    public function saveCredential(string $type, int $id, string $name, array $aCredentials) : int
    {
        return $this->getAggregateRoot()->saveCredential($this->getRepository(),$type,$id, $name,$aCredentials);
    }

    /**
     * @param string $type
     * @param int $id
     * @param string $name
     * @param array $aCredentials
     * @return int
     */
    public function updateCredential(string $type, int $id, string $name, array $aCredentials)
    {
        $this->getAggregateRoot()->updateCredential($this->getRepository(),$type,$id, $name,$aCredentials);
    }

    /**
     * @param string $type
     * @param array $aCountries
     * @param int $id
     */
    public function saveCountry(string $type, array $aCountries, int $id)
    {
        $this->getAggregateRoot()->saveCountry($this->getRepository(),$type, $aCountries, $id);
    }

    /**
     * @param string $type
     * @param array $aFeatures
     * @param int $id
     */
    public function saveFeatures(string $type, array $aFeatures, int $id)
    {
        $this->getAggregateRoot()->saveFeatures($this->getRepository(),$type, $aFeatures, $id);
    }

    /**
     * @param string $type
     * @param array $aCurrencies
     * @param int $id
     */
    public function saveCurrency(string $type, array $aCurrencies, int $id)
    {
        $this->getAggregateRoot()->saveCurrency($this->getRepository(),$type,$aCurrencies, $id);
    }

    /**
     * @param string $type
     * @param array $aCountries
     * @param int $id
     */
    public function updateCountry(string $type, array $aCountries, int $id)
    {
        $this->getAggregateRoot()->updateCountry($this->getRepository(),$type, $aCountries, $id);
    }

    /**
     * @param string $type
     * @param array $aFeatures
     * @param int $id
     */
    public function updateFeatures(string $type, array $aFeatures, int $id)
    {
        $this->getAggregateRoot()->updateFeatures($this->getRepository(),$type, $aFeatures, $id);
    }

    /**
     * @param string $type
     * @param array $aCurrencies
     * @param int $id
     */
    public function updateCurrency(string $type, array $aCurrencies, int $id)
    {
        $this->getAggregateRoot()->updateCurrency($this->getRepository(),$type,$aCurrencies, $id);
    }

    /**
     * @param string $type
     * @param array $aPropertyInfo
     * @param int $id
     * @param array $aPMIds
     */
    public function updatePropertyConfig(string $type,array &$aPropertyInfo,int $id=-1, array $aPMIds=array() )
    {
        $this->getAggregateRoot()->updatePropertyConfig($this->getRepository(),$type,$aPropertyInfo,$id,$aPMIds);
    }

    /***
     * Get Client related configuration from AggregateRoute.
     *
     * @param array $additionalParams
     *
     * @return array
     */
    public function getClientConfiguration( array $additionalParams = []): array
    {
        return $this->getAggregateRoot()->getClientConfigurations($this->getRepository());
    }

    /**
     * @param string $type
     * @param array $additionalParams
     * @param int $id
     * @throws MerchantOnboardingException
     * @throws \SQLQueryException
     */
    public function deletePropertyConfig(string $type,array $additionalParams,int $id=-1)
    {
         $this->getAggregateRoot()->deletePropertyConfig($this->getRepository(),$type,$additionalParams,$id);
    }

    /**
     * @param array $urls
     * @throws MerchantOnboardingException
     */
    public function saveClientURL(array &$urls, $isDeleteOldConfig = false)
    {
      $this->getAggregateRoot()->saveClientURL($this->getRepository(),$urls, $isDeleteOldConfig);
    }

   /**
     * @throws MerchantOnboardingException
     */
    public function updateVelocityURL( array &$urls)
    {
        $this->getAggregateRoot()->updateVelocityURL($this->getRepository(),$urls);
    }
    /**
     * @throws MerchantOnboardingException
     * @throws \SQLQueryException
     */
    public function updateClientUrls(array &$urls)
    {
        $this->getAggregateRoot()->updateClientUrls($this->getRepository(),$urls);
    }

    /**
     * @param ClientServiceStatus $clService
     * @throws MerchantOnboardingException
     * @throws \SQLQueryException
     */
    public function updateAddonServiceStatus(ClientServiceStatus &$clService)
    {
        $this->getAggregateRoot()->updateAddonServiceStatus($this->getRepository(),$clService);

    }

    /**
     * @param array $aClAccountConfig
     * @throws MerchantOnboardingException
     * @throws \SQLQueryException
     */
    public function updateAccountConfig(array &$aClAccountConfig)
    {
        $this->getAggregateRoot()->updateAccountConfig($this->getRepository(),$aClAccountConfig);
    }

    public function getRouteConfigIdByPSP( int $id) :array
    {
        return $this->getAggregateRoot()->getRouteConfigIdByPSP($this->getRepository(),$id);
    }

    public function saveProvider(array $aProviderConfig)
    {
         $this->getAggregateRoot()->saveProvider($this->getRepository(),$aProviderConfig);
    }

    public function getRouteConfiguration(int $id,bool $bAllConfig):?ProviderConfig
    {
        return $this->getAggregateRoot()->getRouteConfiguration($this->getRepository(),$id,$bAllConfig);
    }

    public function updateRouteConfig(ProviderConfig &$providerConfig,bool $isDeleteOld=true)
    {
         $this->getAggregateRoot()->updateRouteConfig($this->getRepository(),$providerConfig,$isDeleteOld);
    }
    public function updateRouteConfigs(array $aProviderConfig,bool $isDeleteOld=true)
    {
        $this->getAggregateRoot()->updateRouteConfigs($this->getRepository(),$aProviderConfig,$isDeleteOld);
    }

    public function updatePSPConfig($providerConfig,bool $deleteOld=true)
    {
        $this->getAggregateRoot()->updatePSPConfig($this->getRepository(),$providerConfig,$deleteOld);

    }

    public function updatePSPConfigs(array $aProviderConfig,bool $deleteOld=true)
    {
        $this->getAggregateRoot()->updatePSPConfigs($this->getRepository(),$aProviderConfig,$deleteOld);
    }
}