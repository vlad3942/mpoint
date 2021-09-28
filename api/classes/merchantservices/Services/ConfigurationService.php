<?php
namespace api\classes\merchantservices\Services;


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

    public function getAddonConfig( $additionalParams = []) : string
    {
        $aAddonConf = $this->merchantAggregateRoot->getAllAddonConfig($this->merchantConfigRepository);
        $responseXml = "";
        foreach ($aAddonConf as $addonconfig)
        {

            $responseXml .= $addonconfig->toXML();
        }
        return $responseXml;
    }

    public function saveAddonConfig($request, $additionalParams = []) {
        

    }

    public function updateAddonConfig($request, $additionalParams = []) {
        

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