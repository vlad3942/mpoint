<?php
namespace api\classes\merchantservices\Controllers;

// include services
use api\classes\merchantservices\configuration\BaseConfig;
use api\classes\merchantservices\Helpers\Helpers;
use api\classes\merchantservices\configuration\PropertyInfo;
use api\classes\merchantservices\MerchantOnboardingException;
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
        $xml = $this->getConfigService()->getClientInfo()->toAttributeLessXML();
        $aPM = $this->getConfigService()->getClientPM();
        $aClientProperty = $this->getConfigService()->getPropertyConfig("CLIENT","ALL");
        $additionalXml = '';
        $aAllClientProperty = array_merge($aClientProperty['Basic'],$aClientProperty['Technical']);

        foreach ($aAllClientProperty as $clientProperty)
        {
            if($clientProperty->getName() === 'TIMEZONE' && empty($clientProperty->getValue()) === false)
            {
                $additionalXml .= "<timezone>".$clientProperty->getValue()."</timezone>";
            }

            if($clientProperty->getName() === 'SSO_PREFERENCE' && empty($clientProperty->getValue()) === false)
            {
                $additionalXml .= "<authentication_mode>".$clientProperty->getValue()."</authentication_mode>";
            }
        }

        $additionalXml .="<pm_configurations>";
        foreach ($aPM as $pm)
        {
           $additionalXml .="<pm_configuration>";
           $additionalXml .="<pm_id>".$pm."</pm_id>";
           $additionalXml .="<enabled>true</enabled>";
           $additionalXml .="</pm_configuration>";
        }
        $additionalXml .="</pm_configurations>";

        $xml = str_replace('</client_configuration>', $additionalXml.Helpers::getPropertiesXML($aClientProperty).'</client_configuration>',$xml);
        return $xml;
    }


    /***
     * Function used to process data for POST RQ for adding details against Client ID
     *
     * @param \SimpleDOMElement $request
     *
     * @throws \SQLQueryException
     * @throws \api\classes\merchantservices\MerchantOnboardingException
     */
    public function postClientConfig(\SimpleDOMElement $request): void
    {
        if(count($request->pm_configurations->pm_configuration)>0)
        {
            $aPMIDs = array();
            foreach ($request->pm_configurations->pm_configuration as $pm)
            {
                array_push($aPMIDs, (int)$pm->pm_id);
            }
            $this->getConfigService()->saveClientPM($aPMIDs);
        }
        if(count($request->properties->property)>0)
        {
            $aProperty = array();
            foreach ($request->properties->property as $property)
            {
                array_push($aProperty, PropertyInfo::produceFromXML($property));
            }
            $this->getConfigService()->savePropertyConfig("CLIENT",$aProperty);
        }

        if(count($request->client_urls)>0)
        {
            $urls = array();
            foreach ($request->client_urls->client_url as $url)
            {
                array_push($urls, \ClientURLConfig::produceFromXML($url));
            }
            $this->getConfigService()->saveVelocityURL($urls);
        }
        $urls = array();

        if(count($request->merchant_urls)>0)
        {
            foreach ($request->merchant_urls->client_url as $url)
            {
                array_push($urls, \ClientURLConfig::produceFromXML($url));
            }
        }

        if(count($request->hpp_urls)>0)
        {
            foreach ($request->hpp_urls->client_url as $url)
            {
                array_push($urls, \ClientURLConfig::produceFromXML($url));
            }
        }
        if(empty($urls) === false) $this->getConfigService()->saveClientUrls($urls);
    }

    /***
     * Update Collections as per the client ID
     * @package MerchantOnboarding
     * @api [PUT] merchantservices/api/clientconfig/client_id/{:id}
     *
     *
     * @param \SimpleDOMElement $request
     *
     * @throws \SQLQueryException
     * @throws \api\classes\merchantservices\MerchantOnboardingException
     */
    public function putClientConfig(\SimpleDOMElement $request): void
    {
        if(count($request->pm_configurations->pm_configuration)>0)
        {
            $aPMIDs = array();
            foreach ($request->pm_configurations->pm_configuration as $pm_configuration)
            {
                array_push($aPMIDs,array((int)$pm_configuration->pm_id,(string)$pm_configuration->enabled));
            }
            $this->getConfigService()->updateClientPM($aPMIDs);
        }
        $aClientParam = array();
        if(count($request->name)>0) $aClientParam["name"] =(string)$request->name;
        if(count($request->salt)>0) $aClientParam["salt"] =(string)$request->salt;
        if(count($request->max_amount)>0) $aClientParam["maxamount"] =(int)$request->max_amount;
        if(count($request->country_id)>0) $aClientParam["countryid"] =(int)$request->country_id;
        if(count($request->email_notification)>0) $aClientParam["emailrcpt"] =(string)$request->email_notification;
        if(count($request->sms_notification)>0) $aClientParam["smsrcpt"] =(string)$request->sms_notification;
        if(count($request->timezone)>0) $aClientParam["TIMEZONE"] =(string)$request->timezone;
        if(count($request->authentication_mode)>0) $aClientParam["SSO_PREFERENCE"] =(string)$request->authentication_mode;
        if(empty($aClientParam) === false)
        {
            $this->getConfigService()->updateClientdetails($aClientParam);
        }

        if(count($request->properties->property)>0)
        {
            $aProperty = array();
            foreach ($request->properties->property as $property)
            {
                array_push($aProperty, PropertyInfo::produceFromXML($property));
            }
            $this->getConfigService()->updatePropertyConfig("CLIENT",$aProperty);
        }

        if(count($request->client_urls)>0)
        {
            $urls = array();
            foreach ($request->client_urls->client_url as $url)
            {
                array_push($urls, \ClientURLConfig::produceFromXML($url));
            }
            $this->getConfigService()->updateVelocityURL($urls);
        }
        $urls = array();

        if(count($request->merchant_urls)>0)
        {
            foreach ($request->merchant_urls->client_url as $url)
            {
                array_push($urls, \ClientURLConfig::produceFromXML($url));
            }
        }

        if(count($request->hpp_urls)>0)
        {
            foreach ($request->hpp_urls->client_url as $url)
            {
                array_push($urls, \ClientURLConfig::produceFromXML($url));
            }
        }
        if(empty($urls) === false) $this->getConfigService()->updateClientUrls($urls);
    }
    function deleteClientConfig($request, $additionalParams = [])
    {
        $this->getConfigService()->deletePropertyConfig('CLIENT',$additionalParams);

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

        $this->getConfigService()->deleteAddonConfig($additionalParams);

    }

    public function getPSPConfig($additionalParams = [])
    {

        $xml = "<client_psp_configuration>";
        $xml .=  Helpers::getPropertiesXML($this->getConfigService()->getPropertyConfig("PSP","ALL",$additionalParams['psp_id']));
        $xml .= "</client_psp_configuration>";
        return $xml;


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

    public function deletePSPConfig($request, $additionalParams = [])
    {
        $this->getConfigService()->deletePropertyConfig('PSP',$additionalParams);
    }

    public function getRouteConfig($additionalParams = [])
    {
        $xml = "<client_route_configuration>";
        $xml .=  Helpers::getPropertiesXML($this->getConfigService()->getPropertyConfig("ROUTE","ALL",$additionalParams['route_conf_id']));
        $aPM = $this->getConfigService()->getRoutePM($additionalParams['route_conf_id']);
        $xml .="<pm_configurations>";
        foreach ($aPM as $pm)
        {
            $xml .="<pm_configuration>";
            $xml .="<pm_id>".$pm."</pm_id>";
            $xml .="<enabled>true</enabled>";
            $xml .="</pm_configuration>";
        }
        $xml .="</pm_configurations>";
        $xml .=  "</client_route_configuration>";
        return $xml;

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
        if(count($request->pm_configurations)>0)
        {
            foreach ($request->pm_configurations->pm_configuration as $pm_configuration)
            {
                array_push($aPMIds, (int)$pm_configuration->pm_id);
            }
        }
        $this->getConfigService()->savePropertyConfig('ROUTE',$aPropertyInfo,$routeConfId,$aPMIds);
    }

    public function updateRouteConfig($request, $additionalParams = [])
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
            array_push($aPMIds,array((int)$pm_configuration->pm_id,(string)$pm_configuration->enabled));
        }
        $this->getConfigService()->updatePropertyConfig('ROUTE',$aPropertyInfo,$routeConfId,$aPMIds);

    }

    public function deleteRouteConfig($request, $additionalParams = [])
    {
        $this->getConfigService()->deletePropertyConfig('ROUTE',$additionalParams,(int)$additionalParams['route_conf_id']);
    }


}