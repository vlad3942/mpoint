<?php
namespace api\classes\merchantservices\Controllers;

// include services
use api\classes\merchantservices\configuration\BaseConfig;
use api\classes\merchantservices\Helpers\Helpers;
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
        $lClientConfigs = $this->getConfigService()->getClientConfiguration($additionalParams);
        return $this->getClientConfigurationXML($lClientConfigs);
    }

    /**
     * Process array and prepare XML for client configuration
     *
     * @param array $aClientConfigData
     *
     * @return string Prepare final string for array
     */
    private function getClientConfigurationXML(array $aClientConfigData): string {

        $XML = '<client_configuration>';
        $XML .=  Helpers::generateXML(
            [
                'info'                  =>  $aClientConfigData['info'],
                'client_urls'           =>  $aClientConfigData['client_urls'],
                'payment_method_ids'    =>  $aClientConfigData['payment_method_ids'],
                'storefronts'           =>  $aClientConfigData['storefronts'],
            ]
        );
        $XML .=  Helpers::getPropertiesXML($aClientConfigData['property_details']);
        $XML .= '</client_configuration>';
        return $XML;
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