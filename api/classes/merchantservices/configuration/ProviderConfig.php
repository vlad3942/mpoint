<?php

namespace api\classes\merchantservices\configuration;

use api\classes\merchantservices\commons\BaseInfo;
use api\classes\merchantservices\Helpers\Helpers;

class ProviderConfig extends BaseInfo
{

    private array $aPm = array();
    private array $aProperty = array();
    private string $userName = '';
    private string $password = '';
    private string $mid = '';
    private int $capture_type = -1;
    private int $pspId = -1;
    private int $provider_id = -1;
    private array $aFeatureId = array() ;
    private array $aCountryIds = array();
    private array $aCurrencyIds = array();

    public function __construct()
    {
    }

    /**
     * @return array
     */
    public function getPm(): array
    {
        return $this->aPm;
    }

    /**
     * @param array $aPm
     * @return ProviderConfig
     */
    public function setPm(array $aPm): ProviderConfig
    {
        $this->aPm = $aPm;
        return $this;
    }

    /**
     * @return array
     */
    public function getProperty(): array
    {
        return $this->aProperty;
    }

    /**
     * @param array $aProperty
     * @return ProviderConfig
     */
    public function setProperty(array $aProperty): ProviderConfig
    {
        $this->aProperty = $aProperty;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * @param string $userName
     * @return ProviderConfig
     */
    public function setUserName(string $userName): ProviderConfig
    {
        $this->userName = $userName;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return ProviderConfig
     */
    public function setPassword(string $password): ProviderConfig
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function getMid(): string
    {
        return $this->mid;
    }

    /**
     * @param string $mid
     * @return ProviderConfig
     */
    public function setMid(string $mid): ProviderConfig
    {
        $this->mid = $mid;
        return $this;
    }

    /**
     * @return int
     */
    public function getCaptureType(): int
    {
        return $this->capture_type;
    }

    /**
     * @param int $capture_type
     * @return ProviderConfig
     */
    public function setCaptureType(int $capture_type): ProviderConfig
    {
        $this->capture_type = $capture_type;
        return $this;
    }

    /**
     * @return array
     */
    public function getFeatureId(): array
    {
        return $this->aFeatureId;
    }

    /**
     * @param array $aFeatureId
     * @return ProviderConfig
     */
    public function setFeatureId(array $aFeatureId): ProviderConfig
    {
        $this->aFeatureId = $aFeatureId;
        return $this;
    }

    /**
     * @return array
     */
    public function getCountryIds(): array
    {
        return $this->aCountryIds;
    }

    /**
     * @param array $aCountryIds
     * @return ProviderConfig
     */
    public function setCountryIds(array $aCountryIds): ProviderConfig
    {
        $this->aCountryIds = $aCountryIds;
        return $this;
    }

    /**
     * @return array
     */
    public function getCurrencyIds(): array
    {
        return $this->aCurrencyIds;
    }

    /**
     * @param array $aCurrencyIds
     * @return ProviderConfig
     */
    public function setCurrencyIds(array $aCurrencyIds): ProviderConfig
    {
        $this->aCurrencyIds = $aCurrencyIds;
        return $this;
    }

    /**
     * @return int
     */
    public function getPspId(): int
    {
        return $this->pspId;
    }

    /**
     * @param int $pspId
     * @return ProviderConfig
     */
    public function setPspId(int $pspId): ProviderConfig
    {
        $this->pspId = $pspId;
        return $this;
    }

    /**
     * @return int
     */
    public function getProviderId(): int
    {
        return $this->provider_id;
    }

    /**
     * @param int $providerid
     * @return ProviderConfig
     */
    public function setProviderId(int $providerid): ProviderConfig
    {
        $this->provider_id = $providerid;
        return $this;
    }

    /**
     * @param $oXML
     * @return ProviderConfig
     */
    public static function produceFromXML( &$oXML) : ProviderConfig
    {

        $providerConfig = new ProviderConfig();
        if(count($oXML->id)>0) { $providerConfig->setId((int)$oXML->id); }
        else { $providerConfig->setId(-1); }
        if(count($oXML->psp_id)>0) { $providerConfig->setPspId((int)$oXML->psp_id); }
        if(count($oXML->provider_id)>0) { $providerConfig->setProviderId((int)$oXML->provider_id); }
        if(count($oXML->name)>0) { $providerConfig->setName((string)$oXML->name); }
        else { $providerConfig->setName(""); }
        if(count($oXML->mid)>0) { $providerConfig->setMid((string)$oXML->mid); }
        if(count($oXML->username)>0) { $providerConfig->setUserName((string)$oXML->username); }
        if(count($oXML->password)>0) { $providerConfig->setPassword((string)$oXML->password); }
        if(count($oXML->capture_type)>0) { $providerConfig->setCaptureType((int)$oXML->capture_type); }
        if(count($oXML->properties)>0)
        {
            $aPropertyInfo = array();
            foreach ($oXML->properties->property as $property)
            {
                array_push($aPropertyInfo,PropertyInfo::produceFromXML($property));
            }
            $providerConfig->setProperty($aPropertyInfo);
        }
        if(count($oXML->pm_configurations)>0)
        {
            $aPMIds = array();
            foreach ($oXML->pm_configurations->pm_configuration as $pm_configuration)
            {
                array_push($aPMIds, (int)$pm_configuration->pm_id);
            }
            $providerConfig->setPm($aPMIds);
        }
        if(count($oXML->route_features)>0)
        {
            $aFeatureIds = array();
            foreach ($oXML->route_features->route_feature as $route_feature)
            {
                array_push($aFeatureIds, (int)$route_feature->id);
            }
            $providerConfig->setFeatureId($aFeatureIds);
        }
        if(count($oXML->country_details)>0)
        {
            $aCountryIds = array();
            foreach ($oXML->country_details->country_detail as $country_detail)
            {
                array_push($aCountryIds, (int)$country_detail->id);
            }
            $providerConfig->setCountryIds($aCountryIds);
        }
        if(count($oXML->currency_details)>0)
        {
            $aCurrencyIds = array();
            foreach ($oXML->currency_details->currency_detail as $currency_detail)
            {
                array_push($aCurrencyIds, (int)$currency_detail->id);
            }
            $providerConfig->setCurrencyIds($aCurrencyIds);
        }
        return $providerConfig;
    }

    /**
     * @param $rs
     * @return PropertyInfo
     */
    public static function produceFromResultSet($rs,$property = array(),$aCountryId =array(),$aCurrencyId =array(),$aFeatureId =array(),$aPmIds =array() ):ProviderConfig
    {
        $providerConfig = new ProviderConfig();
        if(isset($rs["ID"])) { $providerConfig->setId($rs["ID"]); }
        if(isset($rs["PSPID"])) { $providerConfig->setPspId($rs["PSPID"]); }
        if(isset($rs["NAME"])) { $providerConfig->setName($rs["NAME"]); }
        if(isset($rs["MID"])) { $providerConfig->setMid($rs["MID"]); }
        if(isset($rs["USERNAME"])) { $providerConfig->setUserName($rs["USERNAME"]); }
        if(isset($rs["PASSWORD"])) { $providerConfig->setPassword($rs["PASSWORD"]); }
        if(isset($rs["CAPTURETYPE"])) { $providerConfig->setCaptureType($rs["CAPTURETYPE"]); }
        $providerConfig->setCountryIds($aCountryId);
        $providerConfig->setCurrencyIds($aCurrencyId);
        $providerConfig->setFeatureId($aFeatureId);
        $providerConfig->setPm($aPmIds);
        $providerConfig->setProperty($property);
        return $providerConfig;

    }

    /**
     * @return string
     */
    public function toXML(string $rootNode = '',array $aNodeAlias=array())
    {
        $xml = "<$rootNode>";
        $this->setNodeAlias($aNodeAlias);
        $xml .= parent::toXML();
        if($this->getPspId() > 0) { $xml .= "<psp_id>".$this->getPspId()."</psp_id>"; }
        if(empty($this->getMid()) === false) { $xml .= "<mid>".$this->getMid()."</mid>"; }
        if(empty($this->getUserName()) === false) { $xml .= "<username>".$this->getUserName()."</username>"; }
        if(empty($this->getPassword()) === false) { $xml .= "<password>".$this->getPassword()."</password>"; }
        if($this->getCaptureType() > 0) { $xml .= "<capture_type>".$this->getCaptureType()."</capture_type>"; }
        if($this->getProviderId() > 0) { $xml .= "<provider_id>".$this->getProviderId()."</provider_id>"; }
        if(empty($this->getProperty()) === false)
        {
            $xml .= Helpers::getPropertiesXML($this->getProperty());
        }
        if(empty($this->getPm()) === false)
        {
            $xml .= "<pm_configurations>";
            foreach ($this->getPm() as $pm)
            {
                $xml .= "<pm_configuration>";
                $xml .= "<pm_id>" . $pm . "</pm_id>";
                $xml .= "</pm_configuration>";
            }
            $xml .= "</pm_configurations>";
        }

        if(empty($this->getFeatureId()) === false)
        {
            $xml .= "<route_features>";
            foreach ($this->getFeatureId() as $id)
            {
                $xml .= "<route_feature>";
                $xml .= "<id>" . $id . "</id>";
                $xml .= "</route_feature>";
            }
            $xml .= "</route_features>";
        }
        if(empty($this->getCountryIds()) === false)
        {
            $xml .= "<country_details>";
            foreach ($this->getCountryIds() as $id)
            {
                $xml .= "<country_detail>";
                $xml .= "<id>" . $id . "</id>";
                $xml .= "</country_detail>";
            }
            $xml .= "</country_details>";
        }

        if(empty($this->getCurrencyIds()) === false)
        {
            $xml .= "<currency_details>";
            foreach ($this->getCurrencyIds() as $id)
            {
                $xml .= "<currency_detail>";
                $xml .= "<id>" . $id . "</id>";
                $xml .= "</currency_detail>";
            }
            $xml .= "</currency_details>";
        }
        $xml .= "</$rootNode>";

        return $xml;
    }


}