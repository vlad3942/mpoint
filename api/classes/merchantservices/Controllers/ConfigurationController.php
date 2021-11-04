<?php
namespace api\classes\merchantservices\Controllers;

// include services
use api\classes\merchantservices\configuration\BaseConfig;
use api\classes\merchantservices\Helpers\Helpers;
use api\classes\merchantservices\configuration\PropertyInfo;
use api\classes\merchantservices\MerchantOnboardingException;
use api\classes\merchantservices\MetaData\ClientServiceStatus;
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
            $this->getConfigService()->saveVelocityURL($urls);
        }
        $urls = array();

        if(empty($request->merchant_urls) === false && count($request->merchant_urls)>0)
        {
            foreach ($request->merchant_urls->client_url as $url)
            {
                array_push($urls, \ClientURLConfig::produceFromXML($url));
            }
        }

        if(empty($request->hpp_urls) === false && count($request->hpp_urls)>0)
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
        if(is_object($request->pm_configurations->pm_configuration))
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

        if(empty($request->properties->property) === false && count($request->properties->property) > 0)
        {
            $aProperty = array();
            foreach ($request->properties->property as $property)
            {
                array_push($aProperty, PropertyInfo::produceFromXML($property));
            }
            $this->getConfigService()->updatePropertyConfig("CLIENT",$aProperty);
        }

        if(empty($request->client_urls) === false && count($request->client_urls) > 0)
        {
            $urls = array();
            foreach ($request->client_urls->client_url as $url)
            {
                array_push($urls, \ClientURLConfig::produceFromXML($url));
            }
            $this->getConfigService()->updateVelocityURL($urls);
        }
        $urls = array();

        if(empty($request->merchant_urls) === false && count($request->merchant_urls) > 0)
        {
            foreach ($request->merchant_urls->client_url as $url)
            {
                array_push($urls, \ClientURLConfig::produceFromXML($url));
            }
        }

        if(empty($request->hpp_urls) === false && count($request->hpp_urls) > 0)
        {
            foreach ($request->hpp_urls->client_url as $url)
            {
                array_push($urls, \ClientURLConfig::produceFromXML($url));
            }
        }
        if(empty($urls) === false) $this->getConfigService()->updateClientUrls($urls);

        if(empty($request->services) === false && count($request->services) > 0)
        {
            $clService = ClientServiceStatus::produceFromXML($request->services);
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

        $iPSPID = $additionalParams['psp_id']??-1;

        if($iPSPID < 0)
        {
            $xml = "<client_psp_configurations>";
            $aPSPDetails = $this->getConfigService()->getAllPSPCredentials();
            foreach ($aPSPDetails as $PSPDetail)
            {
                $xml .= "<client_psp_configuration>";
                $xml .= "<name>" . $PSPDetail['NAME'] . "</name>";
                $xml .= "<credentials>";
                $xml .= "<username>" . $PSPDetail['USERNAME'] . "</username>";
                $xml .= "<password>" . $PSPDetail['PASSWD'] . "</password>";
                $xml .= "</credentials>";
                $aPM = $this->getConfigService()->getPSPPM($PSPDetail['PSPID']);
                foreach ($aPM as $pm)
                {
                    $xml .="<pm_configuration>";
                    $xml .="<pm_id>".$pm."</pm_id>";
                    $xml .="<enabled>true</enabled>";
                    $xml .="</pm_configuration>";
                }
                $xml .= "</client_psp_configuration>";
            }
            $xml .= "</client_psp_configurations>";
            return $xml;
        }


        $xml = "<client_psp_configuration>";

        $aCredentials = $this->getConfigService()->getPSPCredentials($additionalParams['psp_id'])[0];
        if(count($aCredentials) > 0) {
            $xml .= "<name>" . $aCredentials['NAME'] . "</name>";
            $xml .= "<credentials>";
            $xml .= "<username>" . $aCredentials['USERNAME'] . "</username>";
            $xml .= "<password>" . $aCredentials['PASSWD'] . "</password>";
            $xml .= "</credentials>";
        }
        $xml .=  Helpers::getPropertiesXML($this->getConfigService()->getPropertyConfig("PSP","ALL",$iPSPID));

        $aPM = $this->getConfigService()->getPSPPM($additionalParams['psp_id']);
        $xml .="<pm_configurations>";
        foreach ($aPM as $pm)
        {
            $xml .="<pm_configuration>";
            $xml .="<pm_id>".$pm."</pm_id>";
            $xml .="<enabled>true</enabled>";
            $xml .="</pm_configuration>";
        }
        $xml .="</pm_configurations>";

        $xml .= "</client_psp_configuration>";
        return $xml;
    }

    public function savePSPConfig($request, $additionalParams = [])
    {
        $psp_id =(int) $request->psp_id;

        if(isset($request->credentials) === true)
        {
            if(isset($request->name) === true && isset($request->psp_id) === true)
            {
                $aCredentials = array((string) $request->credentials->username, (string) $request->credentials->password);
                $this->getConfigService()->saveCredential('PSP', (int)$request->psp_id, (string)$request->name, $aCredentials);
            } else {
                throw new MerchantOnboardingException(MerchantOnboardingException::INVALID_REQUEST_PARAM,'PSP Name Param Not Found');
            }
        } else if(isset($request->psp_id)  === false) {
            throw new MerchantOnboardingException(MerchantOnboardingException::INVALID_REQUEST_PARAM,'PSP ID Param Not Found');
        }

        $aPropertyInfo = array();
        foreach ($request->properties->property as $property)  array_push($aPropertyInfo,PropertyInfo::produceFromXML($property));
        $aPMIds = array();
        if(count($request->pm_configurations)>0)
        {
            foreach ($request->pm_configurations->pm_configuration as $pm_configuration)
            {
                array_push($aPMIds, (int)$pm_configuration->pm_id);
            }
        }
        $this->getConfigService()->savePropertyConfig('PSP',$aPropertyInfo,$psp_id,$aPMIds);

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
        $this->getConfigService()->deletePropertyConfig('PSP',$additionalParams,(int)$additionalParams['psp_id']);
    }

    public function getRouteConfig($additionalParams = [])
    {
        $xml = "<client_route_configuration>";
        $aCredentials = $this->getConfigService()->getRouteCredentials($additionalParams['route_conf_id'])[0];
        if(is_array($aCredentials) && count($aCredentials) > 0)
        {
            $xml .="<name>" . $aCredentials['NAME'] . "</name>";
            $xml .="<credentials>";
            $xml .="<mid>". $aCredentials['MID'] ."</mid>";
            $xml .="<username>". $aCredentials['USERNAME'] ."</username>";
            $xml .="<password>". $aCredentials['PASSWORD'] ."</password>";
            $xml .="<capturetype>". $aCredentials['CAPTURETYPE'] ."</capturetype>";
            $xml .= "</credentials>";
        }

        $xml .=  Helpers::getPropertiesXML($this->getConfigService()->getPropertyConfig("ROUTE","ALL",$additionalParams['route_conf_id']));

        $aPM = $this->getConfigService()->getRoutePM($additionalParams['route_conf_id']);
        if(is_array($aPM) && count($aPM) > 0) {
            $xml .= "<pm_configurations>";
            foreach ($aPM as $pm) {
                $xml .= "<pm_configuration>";
                $xml .= "<pm_id>" . $pm . "</pm_id>";
                $xml .= "<enabled>true</enabled>";
                $xml .= "</pm_configuration>";
            }
            $xml .= "</pm_configurations>";
        }

        $aFeatures = $this->getConfigService()->getRouteFeatures($additionalParams['route_conf_id']);
        if(is_array($aFeatures) && count($aFeatures) > 0) {
            $xml .= "<route_features>";
            foreach ($aFeatures as $feature) {
                $xml .= "<route_feature>";
                $xml .= "<id>" . $feature . "</id>";
                $xml .= "<enabled>true</enabled>";
                $xml .= "</route_feature>";
            }
            $xml .= "</route_features>";
        }

        $aCountries = $this->getConfigService()->getRouteCountries($additionalParams['route_conf_id']);
        if(is_array($aCountries) && count($aCountries) > 0) {
            $xml .= "<country_details>";
            foreach ($aCountries as $country) {
                $xml .= "<country_detail>";
                $xml .= "<id>" . $country . "</id>";
                $xml .= "<enabled>true</enabled>";
                $xml .= "</country_detail>";
            }
            $xml .= "</country_details>";
        }

        $aCurrencies = $this->getConfigService()->getRouteCurrencies($additionalParams['route_conf_id']);
        if(is_array($aCurrencies) && count($aCurrencies) > 0) {
            $xml .= "<currency_details>";
            foreach ($aCurrencies as $currency) {
                $xml .= "<currency_detail>";
                $xml .= "<id>" . $currency . "</id>";
                $xml .= "<enabled>true</enabled>";
                $xml .= "</currency_detail>";
            }
            $xml .= "</currency_details>";
        }
        $xml .=  "</client_route_configuration>";

        return $xml;
    }

    public function saveRouteConfig($request, $additionalParams = [])
    {
        $routeConfId = 0;
        if(isset($request->credentials) === true)
        {
            if(isset($request->name) === true && isset($request->psp_id) === true && isset($request->route_config_id) === false )
            {
                $aCredentials = array($request->credentials->mid, $request->credentials->username, $request->credentials->password, $request->credentials->capturetype);
                $routeConfId = $this->getConfigService()->saveCredential('ROUTE', (int)$request->psp_id, (string)$request->name, $aCredentials);
            } else {
                throw new MerchantOnboardingException(MerchantOnboardingException::INVALID_REQUEST_PARAM,'Missing required nodes');
            }
        } else if(isset($request->route_config_id)  === true) {
            $routeConfId =(int) $request->route_config_id;
        } else {
            throw new MerchantOnboardingException(MerchantOnboardingException::INVALID_REQUEST_PARAM,'Missing required nodes');
        }

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
        $aFeatureIds = [];
        if(count($request->route_features) > 0)
        {
            foreach ($request->route_features->route_feature as $route_feature)
            {
                array_push($aFeatureIds, (int)$route_feature->id);
            }
        }
        $this->getConfigService()->saveFeatures('ROUTE',$aFeatureIds,$routeConfId);

        $aCountries = [];
        if(count($request->country_details) > 0)
        {
            foreach($request->country_details->country_detail as $country)
            {
                array_push($aCountries, (int)$country->id);
            }
        }
        $this->getConfigService()->saveCountry('ROUTE',$aCountries,$routeConfId);

        $aCurrencies = [];
        if(count($request->currency_details) > 0)
        {
            foreach($request->currency_details->currency_detail as $currency)
            {
                array_push($aCurrencies, (int)$currency->id);
            }
        }
        $this->getConfigService()->saveCurrency('ROUTE',$aCurrencies,$routeConfId);
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