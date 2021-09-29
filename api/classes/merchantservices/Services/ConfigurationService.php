<?php
namespace api\classes\merchantservices\Services;


use api\classes\merchantservices\MerchantConfigInfo;
use api\classes\merchantservices\Repositories\MerchantConfigRepository;
use api\classes\merchantservices\ResponseTemplate;

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
        $responseXml = "<addon_config_details>";
        foreach ($aAddonConf as $addonconfig)
        {

            $responseXml .= $addonconfig->toXML();
        }
        $responseXml .= "</addon_config_details>";
        $responseTemplate = new ResponseTemplate();
        $responseTemplate->setResponse($responseXml);
        $responseTemplate->setHttpStatusCode(ResponseTemplate::OK);
        return $responseTemplate;
    }

    public function saveAddonConfig($addonConfig, $additionalParams = [])
    {
        $responseTemplate = $this->getAggregateRoot()->saveAddonConfig($this->getRepository(),$addonConfig);
        $aAddonConf = $responseTemplate->getResponse();
        $responseXml = "<addon_config_details>";
        foreach ($aAddonConf as $addonconfig)
        {

            $responseXml .= $addonconfig->toXML();
        }
        $responseXml .= "</addon_config_details>";
        $responseTemplate->setResponse($responseXml);
       return $responseTemplate;
    }

    public function updateAddonConfig($addonConfig, $additionalParams = [])
    {
        $responseTemplate = $this->getAggregateRoot()->updateAddonConfig($this->getRepository(),$addonConfig);
        $aAddonConf = $responseTemplate->getResponse();
        $responseXml = "<addon_config_details>";
        foreach ($aAddonConf as $addonconfig)
        {

            $responseXml .= $addonconfig->toXML();
        }
        $responseXml .= "</addon_config_details>";
        $responseTemplate->setResponse($responseXml);
        return $responseTemplate;
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