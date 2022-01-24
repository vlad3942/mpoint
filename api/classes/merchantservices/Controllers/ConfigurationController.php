<?php
namespace api\classes\merchantservices\Controllers;

// include services
use api\classes\merchantservices\configuration\BaseConfig;
use api\classes\merchantservices\configuration\ProviderConfig;
use api\classes\merchantservices\Helpers\Helpers;
use api\classes\merchantservices\configuration\PropertyInfo;
use api\classes\merchantservices\MerchantOnboardingException;
use api\classes\merchantservices\MetaData\ClientServiceStatus;
use api\classes\merchantservices\Services\ConfigurationService;
use Constants;

/**
 *
 * @package    Mechantservices
 * @subpackage Configuration Controller Class
 */
class ConfigurationController 
{

    // Define Service class objects
    private ConfigurationService $objConfigurationService;

    /**
     * @param \RDB $conn
     * @param int $iClientId
     */
    public function __construct(\RDB &$conn,int $iClientId)
    {
        $this->objConfigurationService = new ConfigurationService($conn,$iClientId);
    }

    /**
     * @return ConfigurationService
     */
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
        if(empty($request->pm_configurations->pm_configuration) === false && count($request->pm_configurations->pm_configuration)>0)
        {
            $aPMIDs = array();
            foreach ($request->pm_configurations->pm_configuration as $pm)
            {
                array_push($aPMIDs, (int)$pm->pm_id);
            }
            $this->getConfigService()->saveClientPM($aPMIDs);
        }
        if(empty($request->properties->property) === false && count($request->properties->property)>0)
        {
            $aProperty = array();
            foreach ($request->properties->property as $property)
            {
                array_push($aProperty, PropertyInfo::produceFromXML($property));
            }
            $this->getConfigService()->savePropertyConfig("CLIENT",$aProperty);
        }

        if(empty($request->client_urls) === false && count($request->client_urls)>0)
        {
            $urls = array();
            foreach ($request->client_urls->client_url as $url)
            {
                array_push($urls, \ClientURLConfig::produceFromXML($url));
            }
            $this->getConfigService()->saveClientURL($urls);
        }
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

        if(is_object($request->pm_configurations->pm_configuration))
        {
            $aPMIDs = array();
            foreach ($request->pm_configurations->pm_configuration as $pm_configuration)
            {
                array_push($aPMIDs,(int)$pm_configuration->pm_id);
            }
            $this->getConfigService()->saveClientPM($aPMIDs, true);
        }

        if(empty($request->properties->property) === false && count($request->properties->property) > 0)
        {
            $aProperty = array();
            foreach ($request->properties->property as $property)
            {
                array_push($aProperty, PropertyInfo::produceFromXML($property));
            }
            $this->getConfigService()->savePropertyConfig("CLIENT",$aProperty, -1, array(), true);
        }

        if(empty($request->client_urls) === false && count($request->client_urls) > 0)
        {
            $urls = array();
            foreach ($request->client_urls->client_url as $url)
            {
                array_push($urls, \ClientURLConfig::produceFromXML($url));
            }
            $this->getConfigService()->saveClientURL($urls , true);
        }
        $urls = array();

        if(empty($request->services) === false && count($request->services) > 0)
        {
            $clService = ClientServiceStatus::produceFromXML($request->services, $this->getConfigService()->getClientInfo());
            $this->getConfigService()->updateAddonServiceStatus($clService);
        }

        if(empty($request->account_configurations->account_config) === false && count($request->account_configurations->account_config) > 0)
        {
            $aClAccountConfig = array();
            foreach ($request->account_configurations->account_config as $account_config)
            {
                $clAccountConfig = \AccountConfig::produceFromXML($account_config);
                array_push($aClAccountConfig,$clAccountConfig);
            }
            $this->getConfigService()->updateAccountConfig($aClAccountConfig);
        }

    }

    /**
     * @param $request
     * @param array $additionalParams
     */
    function deleteClientConfig($request, $additionalParams = []) : void
    {
        $this->getConfigService()->deletePropertyConfig('CLIENT',$additionalParams);

    }

    /**
     * @param array $additionalParams
     * @return string
     */
    public function getAddonConfig( $additionalParams = []) : string
    {
       return $this->getConfigService()->getAddonConfig($additionalParams);
                
    }

    /**
     * @param $request
     * @param array $additionalParams
     */
    public function saveAddonConfig($request, $additionalParams = [])
    {

       $addOnConfig = BaseConfig::produceFromXML($request);
       $this->getConfigService()->saveAddonConfig($addOnConfig,$additionalParams);

    }

    /**
     * @param $request
     * @param array $additionalParams
     */
    public function updateAddonConfig($request, $additionalParams = [])
    {
        $addOnConfig = BaseConfig::produceFromXML($request);
        $this->getConfigService()->saveAddonConfig($addOnConfig,$additionalParams,$isDeleteOldConfig = true);
    }

    /**
     * @param $request
     * @param array $additionalParams
     * @throws MerchantOnboardingException
     * @throws \SQLQueryException
     */
    public function deleteAddonConfig($request, $additionalParams = []) {

        $this->getConfigService()->deleteAddonConfig($additionalParams);

    }

    public function getProviderConfig($additionalParams = []) : string
    {
        $psptypeid = (int) $additionalParams['provider_type']??-1;
        $providerId = (int) $additionalParams['provider_id']??-1;
        $aPSPDetails = $this->getConfigService()->getAllPSPCredentials($providerId,$psptypeid);
        $xml = "";
        foreach ($aPSPDetails as $PSPDetail)
        {
            $xml .= $PSPDetail->toXML("client_provider_configuration");
        }
        if(count($aPSPDetails) > 1)
        {
            $xml = "<client_provider_configurations>".$xml."</client_provider_configurations>";
        }
        return $xml;

    }

    public function updateProviderConfig($request)
    {
        $aProviderConfig = array();
        if(count($request->client_provider_configuration) > 0)
        {
            foreach ($request->client_provider_configuration as $provider_config)
            {
                array_push($aProviderConfig,ProviderConfig::produceFromXML($provider_config));
            }
        }
        else
        {
            array_push($aProviderConfig,ProviderConfig::produceFromXML($request));
        }
        $this->getConfigService()->saveProvider($aProviderConfig);

    }

    public function deleteProviderConfig($request, $additionalParams = []) {

        if(isset($additionalParams['provider_type'])  === false) {
            throw new MerchantOnboardingException(MerchantOnboardingException::INVALID_REQUEST_PARAM,'Provider Type Param Not Found');
        }
        $this->getConfigService()->deleteProviderConfig($additionalParams);

    }

    /**
     * @param array $additionalParams
     * @return string
     */
    public function getPSPConfig($additionalParams = [])
    {

        $iPSPID = $additionalParams['id']??-1;
        $psptypeid = (int) $additionalParams['psp_type']??-1;
        $aPSPDetails = $this->getConfigService()->getRoutes($psptypeid,$iPSPID);
        $xml = "<client_psp_configuration_response><client_psp_configurations>";
         foreach ($aPSPDetails as $PSPDetail)
         {
             $body = "";
             $aPM = $this->getConfigService()->getPSPPM($PSPDetail['PSPID']);
             $body .="<pm_configurations>";

             foreach ($aPM as $pm)
             {
                 $body .="<pm_configuration>";
                 $body .="<pm_id>".$pm."</pm_id>";
                 $body .="<enabled>true</enabled>";
                 $body .="</pm_configuration>";
             }
             $body .="</pm_configurations>";


             if(count($aPSPDetails) === 1)
             {
                 $body .=  Helpers::getPropertiesXML($this->getConfigService()->getPropertyConfig("PSP","ALL",$PSPDetail['PSPID']));
             }

             $xml .= "<client_psp_configuration>";
             $xml .= "<id>".$PSPDetail['PSPID']."</id>";
             $xml .= $body;
             $xml .= "</client_psp_configuration>";

         }
        $xml .= "</client_psp_configurations></client_psp_configuration_response>";


        return $xml;
    }

    /**
     * @param $request
     * @param array $additionalParams
     * @throws MerchantOnboardingException
     */
    public function savePSPConfig($request, $additionalParams = [])
    {
        $aPSPs = array();

        foreach ($request->client_psp_configurations->client_psp_configuration as $client_psp_configuration)
        {
            $provider = ProviderConfig::produceFromXML($client_psp_configuration);
            array_push($aPSPs,$provider);
        }
        $this->getConfigService()->updatePSPConfigs($aPSPs,false);


    }

    /**
     * @param $request
     * @param array $additionalParams
     * @throws MerchantOnboardingException
     */
    public function updatePSPConfig($request, $additionalParams = [])
    {
        $aPSPs = array();

        foreach ($request->client_psp_configurations->client_psp_configuration as $client_psp_configuration)
        {
            $provider = ProviderConfig::produceFromXML($client_psp_configuration);
            array_push($aPSPs,$provider);
        }
        $this->getConfigService()->updatePSPConfigs($aPSPs);

    }

    /**
     * @param $request
     * @param array $additionalParams
     */
    public function deletePSPConfig($request, $additionalParams = [])
    {
        $this->getConfigService()->deletePropertyConfig('PSP',$additionalParams,(int)$additionalParams['id']);
    }

    /**
     * @param array $additionalParams
     * @return string
     */
    public function getRouteConfig($additionalParams = []) : string
    {
        $aRouteConfigId = array();
        $xml = "<route_configurations_response><route_configurations>";
        if(isset($additionalParams['id']) === true)
        {
            array_push($aRouteConfigId,$additionalParams['id']);
        }
        else if(isset($additionalParams['psp_id']) === true)
        {
            $aRouteConfigId = $this->getConfigService()->getRouteConfigIdByPSP((int)$additionalParams['psp_id']);
        }
        else
        {
            throw new MerchantOnboardingException(MerchantOnboardingException::INVALID_REQUEST_PARAM,'Request should either have pspid or Route Config ID');
        }
        $bAllConfig = count($aRouteConfigId) === 1;
        foreach ($aRouteConfigId as $routeconfigId)
        {
            $provider = $this->getConfigService()->getRouteConfiguration($routeconfigId,$bAllConfig);

            if($provider !== null ) { $xml .= $provider->toXML("route_configuration"); }
        }


        $xml .= "</route_configurations></route_configurations_response>";

        return $xml;
    }

    /**
     * @param $request
     * @param array $additionalParams
     * @throws MerchantOnboardingException
     */
    public function saveRouteConfig($request, $additionalParams = [])
    {
        $aRoutes = array();
        foreach ($request->route_configurations->route_configuration as $route_configuration)
        {
            $provider = ProviderConfig::produceFromXML($route_configuration);
            array_push($aRoutes,$provider);
        }
        $this->getConfigService()->updateRouteConfigs($aRoutes,false);

    }

    /**
     * @param $request
     * @param array $additionalParams
     * @throws MerchantOnboardingException
     */
    public function updateRouteConfig($request, $additionalParams = [])
    {
        $aRoutes = array();
        foreach ($request->route_configurations->route_configuration as $route_configuration)
        {
            $provider = ProviderConfig::produceFromXML($route_configuration);
            array_push($aRoutes,$provider);
        }
        $this->getConfigService()->updateRouteConfigs($aRoutes);

    }

    /**
     * @param $request
     * @param array $additionalParams
     */
    public function deleteRouteConfig($request, $additionalParams = [])
    {
        $this->getConfigService()->deletePropertyConfig('ROUTE',$additionalParams,(int)$additionalParams['id']);
    }

}