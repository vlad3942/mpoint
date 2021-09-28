<?php
namespace api\classes\merchantservices\Services;


use api\classes\merchantservices\Repositories\MerchantConfigRepository;

class ConfigurationService
{

    private MerchantConfigRepository $merchantConfigRepository;
    
    public function __construct(MerchantConfigRepository $merchantConfigRepository)
    {
        $this->merchantConfigRepository = $merchantConfigRepository;
    }

    public function getAddonConfig($request, $additionalParams = []) : array
    {
        $this->merchantConfigRepository->getAllAddonConfig();
                
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