<?php
namespace api\classes\merchantservices\Services;


use AddonServiceTypeIndex;
use api\classes\merchantservices\configuration\AddonServiceType;
use api\classes\merchantservices\MerchantConfigInfo;
use api\classes\merchantservices\Repositories\MerchantConfigRepository;

class ConfigurationService
{

    private MerchantConfigRepository $merchantConfigRepository;
    private MerchantConfigInfo $merchantAggregateRoot;
    
    public function __construct(\RDB &$conn,int $iClientId)
    {
        $this->merchantConfigRepository = new MerchantConfigRepository($conn,$iClientId);
        $this->merchantAggregateRoot = new MerchantConfigInfo();
    }

    private function getRepository():MerchantConfigRepository
    {
        return $this->merchantConfigRepository;
    }

    private function getAggregateRoot() : MerchantConfigInfo
    {
        return $this->merchantAggregateRoot;
    }

    public function getAddonConfig( $additionalParams = [])
    {
        $aAddonConf = $this->getAggregateRoot()->getAllAddonConfig($this->getRepository());
        $responseXml = "<addon_configuration_response>";
        $sFraudXML ='';
        $sSplitPaymentXML ='';

        foreach ($aAddonConf as $addonconfig)
        {
            if($addonconfig->getServiceType()->getID() === AddonServiceTypeIndex::eFraud)
            {
                $sFraudXML .= $addonconfig->toXML();
                continue;
            }
            else if($addonconfig->getServiceType()->getID() === AddonServiceTypeIndex::eSPLIT_PAYMENT)
            {
                $sSplitPaymentXML .= $addonconfig->toXML();
                continue;
            }
              $responseXml .= $addonconfig->toXML();
        }
        if(empty($sFraudXML) === false)
        {
            $addonType = AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::eFraud,'');
            $responseXml .= sprintf("<%ss>",strtolower(str_replace('config','_config',strtolower($addonType->getClassName()))));
            $responseXml .=$sFraudXML;
            $responseXml .= sprintf("</%ss>",strtolower(str_replace('config','_config',strtolower($addonType->getClassName()))));
        }

        if(empty($sSplitPaymentXML) === false)
        {
            $addonType = AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::eSPLIT_PAYMENT,'');
            $responseXml .= sprintf("<%ss>",strtolower(str_replace('config','_config',strtolower($addonType->getClassName()))));
            $responseXml .=$sSplitPaymentXML;
            $responseXml .= sprintf("</%ss>",strtolower(str_replace('config','_config',strtolower($addonType->getClassName()))));
        }

        $responseXml .= "</addon_configuration_response>";
        return $responseXml;
    }

    public function saveAddonConfig($addonConfig, $additionalParams = [])
    {
      $this->getAggregateRoot()->saveAddonConfig($this->getRepository(),$addonConfig);
    }

    public function updateAddonConfig($addonConfig, $additionalParams = [])
    {
         $this->getAggregateRoot()->updateAddonConfig($this->getRepository(),$addonConfig);
    }

    public function deleteAddonConfig($request, $additionalParams = []) {
        

    }

    public function getClientPSPConfig($additionalParams = []) : string
    {

        $xml = "<client_psp_configuration>";
        $xml .=  $this->getProperties("PSP","ALL",$additionalParams['psp_id']);
        $xml .= "</client_psp_configuration>";
        return $xml;
    }

    public function savePSPConfig($request, $additionalParams = []) {
        

    }

    public function updatePSPConfig($request, $additionalParams = []) {
        

    }

    public function deletePSPConfig($request, $additionalParams = []) {
        

    }

    private function getProperties(string $type,string $source,int $id=-1):string
    {
        $aCatPropertyInfo =  $this->getAggregateRoot()->getPropertyConfig($this->getRepository(),$type,$source,$id);
        $xml = "<property_details>";
        foreach ($aCatPropertyInfo as $category => $aPropertyInfo)
        {
            $xml .= "<property_detail>";
            $xml .= "<property_sub_category>".$category."</property_sub_category>";
            $xml .= "<properties>";
            foreach ($aPropertyInfo as $propertyInfo) $xml .=$propertyInfo->toXML();
            $xml .= "</properties>";
            $xml .= "</property_detail>";
        }
        $xml .= "</property_details>";
        return $xml;
    }
    public function getRouteConfig( $additionalParams = []) : string
    {
        $xml = "<client_route_configuration>";
        $xml .=  $this->getProperties("ROUTE","ALL",$additionalParams['route_conf_id']);
        $aPM = $this->getAggregateRoot()->getRoutePM($this->getRepository(),$additionalParams['route_conf_id']);
        $xml .="<pm_ids>";
        foreach ($aPM as $pm)  $xml .="<pm_id>".$pm."</pm_id>";
        $xml .="</pm_ids>";

        $xml .=  "</client_route_configuration>";
        return $xml;
    }

    public function saveRouteConfig($request, $additionalParams = []) {
        

    }

    public function updateRouteConfig($request, $additionalParams = []) {
        

    }

    public function deleteRouteConfig($request, $additionalParams = []) {
        

    }

    /***
     * Get Client related configuration from AggregateRoute.
     *
     * @param array $additionalParams
     *
     * @return String
     */
    public function getClientConfiguration( array $additionalParams = []): string
    {
        $aClientConfigData = $this->getAggregateRoot()->getClientConfigurations($this->getRepository());
        // Prepare XML
        $responseXml = '<client_configuration>';
        $responseXml .= $this->getClientConfigurationXML($aClientConfigData);
        $responseXml .=  $this->getProperties("CLIENT","CLIENT"); // Property Details
        $responseXml .= "</client_configuration>";
        return $responseXml;
    }

    /**
     * Process array and prepare XML for client configuration
     *
     * @param array $aClientConfigData
     *
     * @return string
     */
    private function getClientConfigurationXML(array $aClientConfigData): string{
        $responseXml = '';
        foreach ($aClientConfigData as $key => $metadata) {
            if($key == 'info') {
                $responseXml .= (is_object($metadata)) ? $metadata->toXML() : '';
            }
            else {
                if(empty($metadata) === true) continue;
                $responseXml .= "<{$key}>";
                foreach ($metadata as $data) {
                    $responseXml .= (is_object($data)) ? $data->toXML() : '';
                }
                $responseXml .= "</{$key}>";
            }
        }
        return $responseXml;
    }

}