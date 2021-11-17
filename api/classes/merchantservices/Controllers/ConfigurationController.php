<?php
namespace api\classes\merchantservices\Controllers;

// include services
use api\classes\merchantservices\configuration\BaseConfig;
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
        $this->getConfigService()->updateAddonConfig($addOnConfig,$additionalParams);

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

    /**
     * @param array $additionalParams
     * @return string
     */
    public function getPSPConfig($additionalParams = [])
    {

        $iPSPID = $additionalParams['psp_id']??-1;

        if($iPSPID < 0)
        {
            $psptypeid = (int) $additionalParams['psp_type']??-1;
            $xml = "<client_psp_configurations>";
            if($psptypeid === Constants::iPROCESSOR_TYPE_TOKENIZATION || $psptypeid === Constants::iPROCESSOR_TYPE_PRE_FRAUD_GATEWAY
             || $psptypeid === Constants::iPROCESSOR_TYPE_POST_FRAUD_GATEWAY || $psptypeid === Constants::iPROCESSOR_TYPE_MPI)
            {
                $aPSPDetails = $this->getConfigService()->getAllPSPCredentials(-1,$psptypeid);
            }
            else
            {
                $aPSPDetails = $this->getConfigService()->getRoutes($psptypeid);
            }
            foreach ($aPSPDetails as $PSPDetail)
            {
                $body = "";
                if(isset($PSPDetail['NAME']) === true)
                {
                    $body .= "<name>" . $PSPDetail['NAME'] . "</name>";
                    $body .= "<credentials>";
                    $body .= "<username>" . $PSPDetail['USERNAME'] . "</username>";
                    $body .= "<password>" . $PSPDetail['PASSWD'] . "</password>";
                    $body .= "</credentials>";
                }

                $aPM = $this->getConfigService()->getPSPPM($PSPDetail['PSPID']);
                foreach ($aPM as $pm)
                {
                    $body .="<pm_configuration>";
                    $body .="<pm_id>".$pm."</pm_id>";
                    $body .="<enabled>true</enabled>";
                    $body .="</pm_configuration>";
                }
                if(empty($body) === false)
                {
                    $xml .= "<client_psp_configuration>";
                    $xml .= "<psp_id>".$PSPDetail['PSPID']."</psp_id>";
                    $xml .= $body;
                    $xml .= "</client_psp_configuration>";
                }
            }
            $xml .= "</client_psp_configurations>";
            return $xml;
        }


        $xml = "<client_psp_configuration>";

        $aCredentials = $this->getConfigService()->getAllPSPCredentials($additionalParams['psp_id']);
        if(count($aCredentials) > 0) {
            $xml .= "<name>" . $aCredentials[0]['NAME'] . "</name>";
            $xml .= "<credentials>";
            $xml .= "<username>" . $aCredentials[0]['USERNAME'] . "</username>";
            $xml .= "<password>" . $aCredentials[0]['PASSWD'] . "</password>";
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

    /**
     * @param $request
     * @param array $additionalParams
     * @throws MerchantOnboardingException
     */
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

    /**
     * @param $request
     * @param array $additionalParams
     * @throws MerchantOnboardingException
     */
    public function updatePSPConfig($request, $additionalParams = [])
    {
        $psp_id =(int) $request->psp_id;

        if(isset($request->credentials) === true)
        {
            if(isset($request->name) === true && isset($request->psp_id) === true)
            {
                $aCredentials = array((string) $request->credentials->username, (string) $request->credentials->password);
                $this->getConfigService()->updateCredential('PSP', (int)$request->psp_id, (string)$request->name, $aCredentials);
            } else {
                throw new MerchantOnboardingException(MerchantOnboardingException::INVALID_REQUEST_PARAM,'PSP Name Param Not Found');
            }
        } else if(isset($request->psp_id)  === false) {
            throw new MerchantOnboardingException(MerchantOnboardingException::INVALID_REQUEST_PARAM,'PSP ID Param Not Found');
        }

        $aPMIds = array();
        if(count($request->pm_configurations)>0)
        {
            foreach ($request->pm_configurations->pm_configuration as $pm_configuration)
            {
                array_push($aPMIds, array((int)$pm_configuration->pm_id, (string) $pm_configuration->enabled));
            }
        }

        $aPropertyInfo = array();
        foreach ($request->properties->property as $property)  array_push($aPropertyInfo,PropertyInfo::produceFromXML($property));
        $this->getConfigService()->updatePropertyConfig('PSP',$aPropertyInfo,$psp_id,$aPMIds);
    }

    /**
     * @param $request
     * @param array $additionalParams
     */
    public function deletePSPConfig($request, $additionalParams = [])
    {
        $this->getConfigService()->deletePropertyConfig('PSP',$additionalParams,(int)$additionalParams['psp_id']);
    }

    /**
     * @param array $additionalParams
     * @return string
     */
    public function getRouteConfig($additionalParams = []) : string
    {
        $aRouteConfigId = array();
        $xml = "";
        if(isset($additionalParams['route_conf_id']) === true)
        {
            array_push($aRouteConfigId,$additionalParams['route_conf_id']);
        }
        else if(isset($additionalParams['psp_id']) === true)
        {
            $aRouteConfigId = $this->getConfigService()->getRouteConfigIdByPSP((int)$additionalParams['psp_id']);
        }
        $count = count($aRouteConfigId);
        foreach ($aRouteConfigId as $routeconfigId)
        {
            $aCredentials = $this->getConfigService()->getRouteCredentials($routeconfigId)[0];

            $xml .= "<client_route_configuration>";
            if(is_array($aCredentials) && count($aCredentials) > 0)
            {
                $xml .= "<id>".$aCredentials['ID']."</id>";
                $xml .="<name>" . $aCredentials['NAME'] . "</name>";
                $xml .="<credentials>";
                $xml .="<mid>". $aCredentials['MID'] ."</mid>";
                $xml .="<username>". $aCredentials['USERNAME'] ."</username>";
                $xml .="<password>". $aCredentials['PASSWORD'] ."</password>";
                $xml .="<capturetype>". $aCredentials['CAPTURETYPE'] ."</capturetype>";
                $xml .= "</credentials>";
            }

            if($count === 1)
            {
                $xml .=  Helpers::getPropertiesXML($this->getConfigService()->getPropertyConfig("ROUTE","ALL",$routeconfigId));
            }

            $aPM = $this->getConfigService()->getRoutePM($routeconfigId);
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

            if($count === 1)
            {
                $aFeatures = $this->getConfigService()->getRouteFeatures($routeconfigId);
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

                $aCountries = $this->getConfigService()->getRouteCountries($routeconfigId);
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

                $aCurrencies = $this->getConfigService()->getRouteCurrencies($routeconfigId);
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
            }

            $xml .=  "</client_route_configuration>";
        }

        if(count($aRouteConfigId) > 1 || empty($xml) === true )
        {
            $xml = "<client_route_configurations>".$xml."<client_route_configurations/>";
        }
        return $xml;
    }

    /**
     * @param $request
     * @param array $additionalParams
     * @throws MerchantOnboardingException
     */
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
                throw new MerchantOnboardingException(MerchantOnboardingException::INVALID_REQUEST_PARAM,'Request should either have credentials or Route Config ID');
            }
        } else if(isset($request->route_config_id)  === true) {
            $routeConfId =(int) $request->route_config_id;
        } else {
            throw new MerchantOnboardingException(MerchantOnboardingException::INVALID_REQUEST_PARAM,'Request should either have credentials or Route Config ID');
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

    /**
     * @param $request
     * @param array $additionalParams
     * @throws MerchantOnboardingException
     */
    public function updateRouteConfig($request, $additionalParams = [])
    {
        $routeConfId =(int) $request->route_config_id;
        if(isset($request->credentials) === true)
        {
            if(isset($request->name) === true && isset($request->route_config_id) === true )
            {
                $aCredentials = array($request->credentials->mid, $request->credentials->username, $request->credentials->password, $request->credentials->capturetype);
                $this->getConfigService()->updateCredential('ROUTE', $routeConfId, (string)$request->name, $aCredentials);
            } else {
                throw new MerchantOnboardingException(MerchantOnboardingException::INVALID_REQUEST_PARAM,'Missing required nodes');
            }
        }
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

        $aFeatureIds = [];
        if(count($request->route_features) > 0)
        {
            foreach ($request->route_features->route_feature as $route_feature)
            {
                array_push($aFeatureIds, array((int)$route_feature->id, (string)$route_feature->enabled));
            }
        }
        $this->getConfigService()->updateFeatures('ROUTE',$aFeatureIds,$routeConfId);

        $aCountries = [];
        if(count($request->country_details) > 0)
        {
            foreach($request->country_details->country_detail as $country)
            {
                array_push($aCountries, array((int)$country->id , (string)$country->enabled));
            }
        }
        $this->getConfigService()->updateCountry('ROUTE',$aCountries,$routeConfId);

        $aCurrencies = [];
        if(count($request->currency_details) > 0)
        {
            foreach($request->currency_details->currency_detail as $currency)
            {
                array_push($aCurrencies, array((int)$currency->id, (string) $currency->enabled));
            }
        }
        $this->getConfigService()->updateCurrency('ROUTE',$aCurrencies,$routeConfId);

    }

    /**
     * @param $request
     * @param array $additionalParams
     */
    public function deleteRouteConfig($request, $additionalParams = [])
    {
        $this->getConfigService()->deletePropertyConfig('ROUTE',$additionalParams,(int)$additionalParams['route_conf_id']);
    }

}