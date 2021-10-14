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

        foreach ($aAddonConf as $addonconfig)
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

    public function saveAddonConfig($addonConfig, $additionalParams = [])
    {
      $this->getAggregateRoot()->saveAddonConfig($this->getRepository(),$addonConfig);
    }

    public function updateAddonConfig($addonConfig, $additionalParams = [])
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
    public function getClientPM() : array
    {
        return $this->getAggregateRoot()->getClientPM($this->getRepository());

    }

    /**
     * @throws MerchantOnboardingException
     */
    public function saveClientPM(array $aPMIDs)
    {
        $this->getAggregateRoot()->saveClientPM($this->getRepository(),$aPMIDs);
    }
    /**
     * @throws MerchantOnboardingException
     */
    public function updateClientPM(array $aPMIDs)
    {
        $this->getAggregateRoot()->updateClientPM($this->getRepository(),$aPMIDs);
    }

    public function updateClientdetails(array $aClientParam)
    {
        $this->getAggregateRoot()->updateClientdetails($this->getRepository(),$aClientParam);
    }

    public function savePropertyConfig(string $type,array $aPropertyInfo,int $id=-1, array $aPMIds=array() )
    {
       $this->getAggregateRoot()->savePropertyConfig($this->getRepository(),$type,$aPropertyInfo,$id,$aPMIds);
    }

    public function updatePropertyConfig(string $type,array $aPropertyInfo,int $id=-1, array $aPMIds=array() )
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

    public function saveVelocityURL(array $urls)
    {
      $this->getAggregateRoot()->saveVelocityURL($this->getRepository(),$urls);
    }
    public function saveClientUrls(array $urls)
    {
        $this->getAggregateRoot()->saveClientUrls($this->getRepository(),$urls);

    }
    /**
     * @throws MerchantOnboardingException
     */
    public function updateVelocityURL( array $urls)
    {
        $this->getAggregateRoot()->updateVelocityURL($this->getRepository(),$urls);
    }
    /**
     * @throws MerchantOnboardingException
     * @throws \SQLQueryException
     */
    public function updateClientUrls(array $urls)
    {
        $this->getAggregateRoot()->updateClientUrls($this->getRepository(),$urls);
    }

    public function updateAddonServiceStatus(ClientServiceStatus $clService)
    {
        $this->getAggregateRoot()->updateAddonServiceStatus($this->getRepository(),$clService);

    }
    public function updateAccountConfig(array $aClAccountConfig)
    {
        $this->getAggregateRoot()->updateAccountConfig($this->getRepository(),$aClAccountConfig);

    }

}