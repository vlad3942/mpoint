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


    /**
     * Function is used to get Client Config Details.
     * Based on client ID will get all details related to merchant client
     *
     * @param array $additionalParams
     *
     * @return string ClientConfiguration XML String
     */
    public function getClientConfig(array $additionalParams): string
    {
        return $this->getConfigService()->getClientConfiguration($additionalParams);
    }

    
    public function getAddonConfig( $additionalParams = [])
    {
       return $this->getConfigService()->getAddonConfig($additionalParams);
                
    }

    public function saveAddonConfig($request, $additionalParams = [])
    {

       $addOnConfig = BaseConfig::produceFromXML($request);
       $this->getConfigService()->saveAddonConfig($addOnConfig,$additionalParams);

    }

    public function updateAddonConfig($request, $additionalParams = [])
    {
        $addOnConfig = BaseConfig::produceFromXML($request);
        $this->getConfigService()->updateAddonConfig($addOnConfig,$additionalParams);

    }

    public function deleteAddonConfig($request, $additionalParams = []) {
        

    }

    public function getPSPConfig($additionalParams = [])
    {
        return $this->getConfigService()->getClientPSPConfig($additionalParams);


    }

    public function savePSPConfig($request, $additionalParams = []) {
        

    }

    public function updatePSPConfig($request, $additionalParams = []) {
        

    }

    public function deletePSPConfig($request, $additionalParams = []) {
        

    }

    public function getRouteConfig($additionalParams = []) {
        return $this->getConfigService()->getRouteConfig($additionalParams);


    }

    public function saveRouteConfig($request, $additionalParams = [])
    {
        $this->getConfigService()->saveRouteConfig($request);
    }

    public function updateRouteConfig($request, $additionalParams = []) {
        

    }

    public function deleteRouteConfig($request, $additionalParams = []) {
        

    }


}