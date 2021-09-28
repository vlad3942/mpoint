<?php
namespace api\classes\merchantservices\Controllers;

// include services
use api\classes\merchantservices\Repositories\MerchantConfigRepository;
use api\classes\merchantservices\Services\ConfigurationService;


class ConfigurationController 
{

    // Define Repository object
    private MerchantConfigRepository $merchantConfigRepository;

    // Define Service class objects
    private ConfigurationService $objConfigurationService;
    

    public function __construct(MerchantConfigRepository $merchantConfigRepository)
    {
        $this->merchantConfigRepository = $merchantConfigRepository;
        $this->objConfigurationService = new ConfigurationService($merchantConfigRepository);
    }

/*  Sample function for accesing Repositry */
    public function getClientConfig($request, $additionalParams = []) {
        print_r($additionalParams);
        echo $request->asXML();
        return $this->merchantConfigRepository->find(array());
    }
/*  Sample function for accesing Repositry */    
    
    public function getAddonConfig($request, $additionalParams = []) {

                
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