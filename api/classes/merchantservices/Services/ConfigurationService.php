<?php
namespace api\classes\merchantservices\Services;


use AddonServiceTypeIndex;
use api\classes\merchantservices\configuration\AddonServiceType;
use api\classes\merchantservices\configuration\PropertyInfo;
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

    public function getAddonConfig( $additionalParams = [])
    {
        $aAddonConf = $this->getAggregateRoot()->getAllAddonConfig($this->getRepository());
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

    public function saveAddonConfig(&$addonConfig, $additionalParams = [])
    {
      $this->getAggregateRoot()->saveAddonConfig($this->getRepository(),$addonConfig);
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

    public function getPropertyConfig(string $type,string $source,int $id=-1) : array
    {
        return $this->getAggregateRoot()->getPropertyConfig($this->getRepository(),$type,$source,$id);
    }


    public function getRoutePM(int $routeConfigId) : array
    {
        return $this->getAggregateRoot()->getRoutePM($this->getRepository(),$routeConfigId);
    }

    public function getPSPPM(int $pspConfigId): array
    {
        return $this->getAggregateRoot()->getPSPPM($this->getRepository(),$pspConfigId);
    }

    public function getRouteFeatures(int $routeConfigId)
    {
        return $this->getAggregateRoot()->getRouteFeatures($this->getRepository(), $routeConfigId);
    }

    public function getRouteCountries(int $routeConfigId)
    {
        return $this->getAggregateRoot()->getRouteCountries($this->getRepository(), $routeConfigId);
    }

    public function getRouteCurrencies(int $routeConfigId)
    {
        return $this->getAggregateRoot()->getRouteCurrencies($this->getRepository(), $routeConfigId);
    }


    public function getRouteCredentials(int $routeConfigId)
    {
        return $this->getAggregateRoot()->getRouteCredentials($this->getRepository(), $routeConfigId);
    }

    public function getPSPCredentials(int $pspConfigId)
    {
        return $this->getAggregateRoot()->getPSPCredentials($this->getRepository(), $pspConfigId);
    }

    public function getAllPSPCredentials()
    {
        return $this->getAggregateRoot()->getAllPSPCredentials($this->getRepository());
    }

    public function getClientPM() : array
    {
        return $this->getAggregateRoot()->getClientPM($this->getRepository());

    }

    /**
     * @throws MerchantOnboardingException
     */
    public function saveClientPM(array &$aPMIDs)
    {
        $this->getAggregateRoot()->saveClientPM($this->getRepository(),$aPMIDs);
    }
    /**
     * @throws MerchantOnboardingException
     */
    public function updateClientPM(array &$aPMIDs)
    {
        $this->getAggregateRoot()->updateClientPM($this->getRepository(),$aPMIDs);
    }

    public function updateClientdetails(array &$aClientParam)
    {
        $this->getAggregateRoot()->updateClientdetails($this->getRepository(),$aClientParam);
    }

    public function savePropertyConfig(string $type,array &$aPropertyInfo,int $id=-1, array $aPMIds=array())
    {
       $this->getAggregateRoot()->savePropertyConfig($this->getRepository(),$type,$aPropertyInfo,$id,$aPMIds);
    }
    public function saveFeature(string $type, array $aFeatures, $id)
    {
        return $this->getAggregateRoot()->saveCredential($this->getRepository(),$type,$id, $aFeatures);
    }

    public function saveCredential(string $type, int $id, string $name, array $aCredentials)
    {
        return $this->getAggregateRoot()->saveCredential($this->getRepository(),$type,$id, $name,$aCredentials);
    }

    public function updateCredential(string $type, int $id, string $name, array $aCredentials)
    {
        return $this->getAggregateRoot()->updateCredential($this->getRepository(),$type,$id, $name,$aCredentials);
    }

    public function saveCountry(string $type, array $aCountries, int $id)
    {
        $this->getAggregateRoot()->saveCountry($this->getRepository(),$type, $aCountries, $id);
    }

    public function saveFeatures(string $type, array $aFeatures, int $id)
    {
        $this->getAggregateRoot()->saveFeatures($this->getRepository(),$type, $aFeatures, $id);
    }


    public function saveCurrency(string $type, array $aCurrencies, int $id)
    {
        $this->getAggregateRoot()->saveCurrency($this->getRepository(),$type,$aCurrencies, $id);
    }

    public function updateCountry(string $type, array $aCountries, int $id)
    {
        $this->getAggregateRoot()->updateCountry($this->getRepository(),$type, $aCountries, $id);
    }

    public function updateFeatures(string $type, array $aFeatures, int $id)
    {
        $this->getAggregateRoot()->updateFeatures($this->getRepository(),$type, $aFeatures, $id);
    }


    public function updateCurrency(string $type, array $aCurrencies, int $id)
    {
        $this->getAggregateRoot()->updateCurrency($this->getRepository(),$type,$aCurrencies, $id);
    }


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

    public function deletePropertyConfig(string $type,array $additionalParams,int $id=-1)
    {
         $this->getAggregateRoot()->deletePropertyConfig($this->getRepository(),$type,$additionalParams,$id);
    }

    public function saveVelocityURL(array &$urls)
    {
      $this->getAggregateRoot()->saveVelocityURL($this->getRepository(),$urls);
    }
    public function saveClientUrls(array &$urls)
    {
        $this->getAggregateRoot()->saveClientUrls($this->getRepository(),$urls);

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

    public function updateAddonServiceStatus(ClientServiceStatus &$clService)
    {
        $this->getAggregateRoot()->updateAddonServiceStatus($this->getRepository(),$clService);

    }
    public function updateAccountConfig(array &$aClAccountConfig)
    {
        $this->getAggregateRoot()->updateAccountConfig($this->getRepository(),$aClAccountConfig);

    }

}