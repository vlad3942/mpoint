<?php
namespace api\classes\merchantservices\Controllers;

// include services
use api\classes\merchantservices\configuration\BaseConfig;
use api\classes\merchantservices\Services\ConfigurationService;


class ConfigurationController 
{



    // Define Service class objects
    private ConfigurationService $objConfigurationService;
    

    public function __construct(\RDB &$conn,int $iClientId)
    {
        $this->objConfigurationService = new ConfigurationService($conn,$iClientId);
    }

    private function getConfigService():ConfigurationService { return $this->objConfigurationService;}

/*  Sample function for accesing Repositry */
    public function getClientConfig($request, $additionalParams = []) {
        print_r($additionalParams);
        echo $request->asXML();
        return $this->merchantConfigRepository->find(array());
    }
/*  Sample function for accesing Repositry */    
    
    public function getAddonConfig( $additionalParams = [])
    {
       return $this->getConfigService()->getAddonConfig($additionalParams);
                
    }

    public function saveAddonConfig($request, $additionalParams = [])
    {

       $addOnConfig = BaseConfig::produceFromXML($request);
       return $this->getConfigService()->saveAddonConfig($addOnConfig,$additionalParams);

    }

    public function updateAddonConfig($request, $additionalParams = [])
    {
        $addOnConfig = BaseConfig::produceFromXML($request);

        return $this->getConfigService()->updateAddonConfig($addOnConfig,$additionalParams);

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