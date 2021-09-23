<?php

// include services
require_once('ConfigurationService.php');


class ConfigurationController 
{

    // Define Repository object
    private $merchantConfigRepositry;

    // Define Service class objects
    private $objConfigurationService;
    

    public function __construct(IRepository $merchantConfigRepositry)
    {
        $this->merchantConfigRepositry = $merchantConfigRepositry;
        $this->objConfigurationService = new ConfigurationService($merchantConfigRepositry);
    }

/*  Sample function for accesing Repositry */
    public function getClientConfig($request, $additionalParams = []) {
        print_r($additionalParams);
        echo $request->asXML();
        return $this->merChantConfig->find(array());
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