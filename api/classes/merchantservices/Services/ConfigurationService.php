<?php
namespace api\classes\merchantservices\Services;


use AddonServiceTypeIndex;
use api\classes\merchantservices\configuration\AddonServiceType;
use api\classes\merchantservices\MerchantConfigInfo;
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
       return "";
    }

    public function updateAddonConfig($addonConfig, $additionalParams = [])
    {
         $this->getAggregateRoot()->updateAddonConfig($this->getRepository(),$addonConfig);
    }

    public function deleteAddonConfig($request, $additionalParams = []) {
        

    }

    public function getPSPConfig($request, $additionalParams = []) {
        
                
    }

    public function savePSPConfig($request, $additionalParams = []) {
        

    }

    public function updatePSPConfig($request, $additionalParams = []) {
        

    }

    public function deletePSPConfig($request, $additionalParams = []) {
        

    }

    public function getRouteConfig($request, $additionalParams = []) {
        
                
    }

    public function saveRouteConfig($request, $additionalParams = []) {
        

    }

    public function updateRouteConfig($request, $additionalParams = []) {
        

    }

    public function deleteRouteConfig($request, $additionalParams = []) {
        

    }    
}