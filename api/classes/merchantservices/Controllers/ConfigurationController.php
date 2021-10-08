<?php
namespace api\classes\merchantservices\Controllers;

// include services
use api\classes\merchantservices\configuration\BaseConfig;
use api\classes\merchantservices\configuration\PropertyInfo;
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

    public function savePSPConfig($request, $additionalParams = [])
    {
        $psp_id =(int) $request->psp_id;
        $aPropertyInfo = array();
        foreach ($request->properties->property as $property)  array_push($aPropertyInfo,PropertyInfo::produceFromXML($property));
        $this->getConfigService()->savePropertyConfig('PSP',$aPropertyInfo,$psp_id);

    }

    public function updatePSPConfig($request, $additionalParams = [])
    {
        $psp_id =(int) $request->psp_id;
        $aPropertyInfo = array();
        foreach ($request->properties->property as $property)  array_push($aPropertyInfo,PropertyInfo::produceFromXML($property));
        $this->getConfigService()->updatePropertyConfig('PSP',$aPropertyInfo,$psp_id);
    }

    public function deletePSPConfig($request, $additionalParams = []) {
        

    }

    public function getRouteConfig($additionalParams = []) {
        return $this->getConfigService()->getRouteConfig($additionalParams);


    }

    public function saveRouteConfig($request, $additionalParams = [])
    {
        $routeConfId =(int) $request->route_config_id;
        $aPropertyInfo = array();
        foreach ($request->properties->property as $property)
        {
            array_push($aPropertyInfo,PropertyInfo::produceFromXML($property));
        }
        $aPMIds = array();
        foreach ($request->pm_configurations->pm_configuration as $pm_configuration)
        {
            array_push($aPMIds,(int)$pm_configuration->pm_id);
        }
        $this->getConfigService()->savePropertyConfig('ROUTE',$aPropertyInfo,$routeConfId,$aPMIds);
    }

    public function updateRouteConfig($request, $additionalParams = []) {
        $routeConfId =(int) $request->route_config_id;
        $aPropertyInfo = array();
        foreach ($request->properties->property as $property)
        {
            array_push($aPropertyInfo,PropertyInfo::produceFromXML($property));
        }
        $aPMIds = array();
        foreach ($request->pm_configurations->pm_configuration as $pm_configuration)
        {
            array_push($aPMIds,(int)$pm_configuration->pm_id);
        }
        $this->getConfigService()->updatePropertyConfig('ROUTE',$aPropertyInfo,$routeConfId,$aPMIds);

    }

    public function deleteRouteConfig($request, $additionalParams = []) {
        

    }


}