<?php

namespace api\classes\merchantservices\Repositories;


use AddonServiceTypeIndex;
use api\classes\merchantservices\configuration\AddonServiceType;
use api\classes\merchantservices\configuration\DCCConfig;
use api\classes\merchantservices\configuration\FraudConfig;
use api\classes\merchantservices\configuration\MCPConfig;
use api\classes\merchantservices\configuration\MPIConfig;
use api\classes\merchantservices\configuration\PCCConfig;
use api\classes\merchantservices\configuration\ServiceConfig;
use api\classes\merchantservices\MerchantOnboardingException;
use api\classes\merchantservices\MetaData\PSPInfo;
use api\classes\merchantservices\MetaData\PaymentType;
use api\classes\merchantservices\MetaData\Country;
use api\classes\merchantservices\MetaData\Currency;
use api\classes\merchantservices\MetaData\CaptureType;
use api\classes\merchantservices\MetaData\PropertyAttribute;
use api\classes\merchantservices\MetaData\PropertyDetail;
use api\classes\merchantservices\MetaData\ProviderDetail;
use api\classes\merchantservices\MetaData\ServiceInfo;
use api\classes\merchantservices\MetaData\ServiceSubType;
use api\classes\merchantservices\MetaData\UrlInfo;


class MerchantConfigRepository
{
    private \RDB $_conn;
    private \ClientConfig $_clientConfig;

    public function __construct(\RDB  $conn, int $iClientId)
    {
        $this->_conn = $conn;
        $this->_clientConfig = \ClientConfig::produceConfig($conn, $iClientId);
    }

    private function getDBConn():\RDB { return $this->_conn;}


    private function getAddonConfig(AddonServiceType $addonServiceType)
    {
        $SQL ="";
        if($addonServiceType->getID() === AddonServiceTypeIndex::eSPLIT_PAYMENT)
        {
            $SQL ="SELECT id FROM CLIENT". sSCHEMA_POSTFIX .".split_configuration_tbl WHERE client_id=".$this->_clientConfig->getID()." and name='".$addonServiceType->getSubType()."'";
            $aRS = $this->getDBConn()->getName ( $SQL );
            if (empty($aRS) === false)
            {
                $SQL = "SELECT %s FROM CLIENT". sSCHEMA_POSTFIX .".%s WHERE enabled = true and split_config_id=".$aRS['ID'];
            }
        }
        else
        $SQL = "SELECT %s FROM CLIENT". sSCHEMA_POSTFIX .".%s WHERE enabled = true and clientid=".$this->_clientConfig->getID();

        $sTableName = $addonServiceType->getTableName();
        $sColumns = "id,pmid,countryid,currencyid,created,modified,enabled";
        $sWhereCls ='';
        if($addonServiceType->getID() ===AddonServiceTypeIndex::eFraud )
        {
            $sColumns .= ',providerid,typeoffraud ';
            if($addonServiceType->getSubType() === 'pre_auth') $sWhereCls .= 'typeoffraud=1';
            else $sWhereCls .= 'typeoffraud=2';
        }
        elseif($addonServiceType->getID() ===AddonServiceTypeIndex::eSPLIT_PAYMENT)
        {
            $sColumns = 'id,payment_type, sequence_no ';
        }
        elseif($addonServiceType->getID() ===AddonServiceTypeIndex::ePCC) $sColumns = 'id,pmid,sale_currency_id,is_presentment,settlement_currency_id,created,modified,enabled ';
        elseif($addonServiceType->getID() ===AddonServiceTypeIndex::eMPI) $sColumns = 'id, clientid, pmid, providerid,version,created,modified,enabled ';
        $sSQL = sprintf($SQL,$sColumns,$sTableName) ;
        if(empty($sWhereCls) === false)
        {
            $sSQL.=' and '.$sWhereCls;
        }
        $aRS = $this->getDBConn()->getAllNames ( $sSQL );
        $aServiceConfig = array();
        if (empty($aRS) === false)
        {
            foreach ($aRS as $rs)
            {
                array_push($aServiceConfig, ServiceConfig::produceFromResultSet($rs));
            }
        }
        $className =   'api\\classes\\merchantservices\\configuration\\'.$addonServiceType->getClassName();
        $aProperty = array();
        if($addonServiceType->getID() === AddonServiceTypeIndex::eFraud)
        {
            $SQL = 'SELECT is_rollback FROM client.fraud_property_tbl where enabled=true and clientid='.$this->_clientConfig->getID();
            $aRS = $this->getDBConn()->getName ( sprintf($SQL,$sColumns,$sTableName) );
            if(empty($aRS) === false) $aProperty = array_change_key_case($aRS,CASE_LOWER);
        }
        return new $className($aServiceConfig,$aProperty,$addonServiceType->getSubType());

    }
    public function getAllAddonConfig() : array
    {
       $aAddonConfig = array();
       array_push($aAddonConfig,$this->getAddonConfig(AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::eDCC,'')));
       array_push($aAddonConfig,$this->getAddonConfig(AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::eMCP,'')));
       array_push($aAddonConfig,$this->getAddonConfig(AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::ePCC,'')));
       array_push($aAddonConfig,$this->getAddonConfig(AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::eFraud,'pre_auth')));
       array_push($aAddonConfig,$this->getAddonConfig(AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::eFraud,'post_auth')));
       array_push($aAddonConfig,$this->getAddonConfig(AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::eMPI,'')));

       array_push($aAddonConfig,$this->getAddonConfig(AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::eSPLIT_PAYMENT,'hybrid')));
       array_push($aAddonConfig,$this->getAddonConfig(AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::eSPLIT_PAYMENT,'cashless')));
       array_push($aAddonConfig,$this->getAddonConfig(AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::eSPLIT_PAYMENT,'conventional')));

       return  $aAddonConfig;
    }

    /**
     * @throws MerchantOnboardingException
     */
    public function saveAddonConfig(array $aAddonConfig)
    {

        foreach ($aAddonConfig as $addonConfig)
        {

            if(empty($addonConfig->getProperties()) === false)
            {
                $SQL ="INSERT INTO client.". sSCHEMA_POSTFIX ;
                if ($addonConfig->getServiceType()->getID()=== AddonServiceTypeIndex::eFraud)
                {
                    $SQL .="fraud_property_tbl (is_rollback,clientid) values (".\General::bool2xml($addonConfig->getProperties()["is_rollback"]).",".$this->_clientConfig->getID().")";
                }
                $SQL .=" ON CONFLICT (clientid) do update set is_rollback =".\General::bool2xml($addonConfig->getProperties()["is_rollback"]);
                $result = $this->getDBConn()->executeQuery($SQL);
                if ($result == FALSE)
                {
                    throw new MerchantOnboardingException(MerchantOnboardingException::SQL_EXCEPTION);
                }
            }
            if(empty($addonConfig->getConfiguration()) === false)
            {
                $sql = ServiceConfig::getInsertSQL($addonConfig->getServiceType());
                $aServiceConf = $addonConfig->getConfiguration();

                if($addonConfig->getServiceType()->getID() === AddonServiceTypeIndex::eSPLIT_PAYMENT)
                {
                    $SQL ="SELECT id FROM CLIENT". sSCHEMA_POSTFIX .".split_configuration_tbl WHERE client_id=".$this->_clientConfig->getID()." and name='".$addonConfig->getServiceType()->getSubType()."'";
                    $aRS = $this->getDBConn()->getName ( $SQL );
                    if (empty($aRS) === false)
                    {
                        $id = $aRS['ID'];
                    }
                    else
                    {
                        $SQL ="INSERT INTO CLIENT". sSCHEMA_POSTFIX .".split_configuration_tbl (client_id, name, is_one_step_auth) values ($1,$2,$3) RETURNING id";
                        $isOneStepAuth = 'false';
                        if($addonConfig->getServiceType()->getSubType() === 'hybrid')
                        {
                            $isOneStepAuth = 'true';
                        }
                        $aParam = array($this->_clientConfig->getID(),$addonConfig->getServiceType()->getSubType(),$isOneStepAuth);
                        $rs = $this->getDBConn()->executeQuery($SQL, $aParam);
                        if($rs == false) return array();
                        else $id = $this->getDBConn()->fetchName($rs)['ID'];

                    }
                }
                else $id = $this->_clientConfig->getID();

                foreach ($aServiceConf as $serviceConf)
                {
                    $aParams = $serviceConf->getParam($addonConfig->getServiceType(),$id);
                    $result = $this->getDBConn()->executeQuery($sql, $aParams);

                    if ($result == FALSE)
                    {
                        $statusCode = MerchantOnboardingException::SQL_EXCEPTION;
                        if(strpos($this->getDBConn()->getErrMsg(),'duplicate key value violates unique constraint') !== false)
                        {
                            $statusCode = MerchantOnboardingException::SQL_DUPLICATE_EXCEPTION;
                        }
                        throw new MerchantOnboardingException($statusCode,'Failed to Insert SubType '.$addonConfig->getServiceType()->getSubType().' For Config '.$serviceConf->toString());                    }
                }
            }
        }
    }

    /**
     * @throws MerchantOnboardingException
     */
    public function updateAddonConfig(array $aAddonConfig)
    {

        foreach ($aAddonConfig as $addonConfig)
        {
            if(empty($addonConfig->getProperties()) === false)
            {
                $SQL ="INSERT INTO client.". sSCHEMA_POSTFIX ;
                if ($addonConfig->getServiceType()->getID()=== AddonServiceTypeIndex::eFraud)
                {
                    $SQL .="fraud_property_tbl (is_rollback,clientid) values (".\General::bool2xml($addonConfig->getProperties()["is_rollback"]).",".$this->_clientConfig->getID().")";
                }
                $SQL .=" ON CONFLICT (clientid) do update set is_rollback =".\General::bool2xml($addonConfig->getProperties()["is_rollback"]);
                $result = $this->getDBConn()->executeQuery($SQL);
                if ($result == FALSE)
                {

                    throw new MerchantOnboardingException(MerchantOnboardingException::SQL_EXCEPTION,'Failed to Update Fraud is_rollback property');
                }
            }
            if(empty($addonConfig->getConfiguration()) === false)
            {
                $aServiceConf = $addonConfig->getConfiguration();
                foreach ($aServiceConf as $serviceConf)
                {
                    $sql = $serviceConf->getUpdateSQL($addonConfig->getServiceType());

                    $result = $this->getDBConn()->executeQuery($sql);

                    if ($result == FALSE)
                    {
                        $statusCode = MerchantOnboardingException::SQL_EXCEPTION;
                        if(strpos($this->getDBConn()->getErrMsg(),'duplicate key value violates unique constraint') !== false)
                        {
                            $statusCode = MerchantOnboardingException::SQL_DUPLICATE_EXCEPTION;
                        }
                        throw new MerchantOnboardingException($statusCode,'Failed to Update SubType '.$addonConfig->getServiceType()->getSubType().' For Config Id='.$serviceConf->getId().' '.$serviceConf->toString());
                    }
                }
            }
        }
    }

    /**
     * Generate PSPConfig Data
     *
     * @return void
     */
    public function getPSPInfo(): array
    {
        $aPSPConfig = [];

        $SQL = "SELECT id, name FROM SYSTEM" . sSCHEMA_POSTFIX . ".psp_tbl  WHERE enabled = true";
        $aRS = $this->getDBConn()->getAllNames($SQL);

        foreach ($aRS as $rs) {
            $PSPConfig = new PSPInfo();
            $PSPConfig->setId($rs["ID"])
                ->setName($rs["NAME"]);
            array_push($aPSPConfig, $PSPConfig);
        }
        return $aPSPConfig;
    }

    /**
     * Get All Payment Methods
     *
     * @return array
     */
    public function getPaymentMethods(): array
    {
        $aPaymentMethods = [];

        $SQL = "SELECT id, name FROM SYSTEM" . sSCHEMA_POSTFIX . ".paymenttype_tbl";
        $aRS = $this->getDBConn()->getAllNames($SQL);

        foreach ($aRS as $rs) {
            $PaymentType = new PaymentType();
            $PaymentType->setId($rs["ID"])
                ->setName($rs["NAME"]);
            array_push($aPaymentMethods, $PaymentType);
        }
        return $aPaymentMethods;
    }

    /**
     * Get All Countries
     *
     * @return array
     */
    public function getCountries(): array
    {
        $aCountries = [];

        $SQL = "SELECT id, name FROM SYSTEM" . sSCHEMA_POSTFIX . ".country_tbl WHERE enabled = true";
        $aRS = $this->getDBConn()->getAllNames($SQL);

        foreach ($aRS as $rs) {
            $Country = new Country();
            $Country->setId($rs["ID"])
                ->setName($rs["NAME"]);
            array_push($aCountries, $Country);
        }
        return $aCountries;
    }

    /**
     * Get All Currencies
     *
     * @return array
     */
    public function getCurrencies(): array
    {
        $aCurrencies = [];

        $SQL = "SELECT id, name FROM SYSTEM" . sSCHEMA_POSTFIX . ".currency_tbl WHERE enabled = true";
        $aRS = $this->getDBConn()->getAllNames($SQL);

        foreach ($aRS as $rs) {
            $Currency = new Currency();
            $Currency->setId($rs["ID"])
                ->setName($rs["NAME"]);
            array_push($aCurrencies, $Currency);
        }
        return $aCurrencies;
    }

    /**
     * Get All Capture Types
     *
     * @return array
     */
    public function getCaptureTypes(): array
    {
        $aCaptureTypes = [];

        $SQL = "SELECT id, name FROM SYSTEM" . sSCHEMA_POSTFIX . ".capturetype_tbl WHERE enabled = true";
        $aRS = $this->getDBConn()->getAllNames($SQL);

        foreach ($aRS as $rs) {
            $CaptureType = new CaptureType();
            $CaptureType->setId($rs["ID"])
                ->setName($rs["NAME"]);
            array_push($aCaptureTypes, $CaptureType);
        }
        return $aCaptureTypes;
    }

    public function getProviderDetails(): array
    {
        $aProviderDetails = [];

        $SQL = "SELECT 1 as id,1 as type_id,'test_provider_detail' as name";
        $aRS = $this->getDBConn()->getAllNames($SQL);

        foreach ($aRS as $rs) {
            $ProviderDetail = new ProviderDetail();
            $ProviderDetail->setId($rs["ID"])
                ->setTypeId($rs['TYPE_ID'])
                ->setName($rs["NAME"]);
            array_push($aProviderDetails, $ProviderDetail);
        }

        return $aProviderDetails;
    }

    public function getUrlInfo(): array
    {
        $aUrlInfo = [];

        $SQL = "SELECT 1 as type_id,'url_category' as category,'url_name' as name, 'url_value' as value";
        $aRS = $this->getDBConn()->getAllNames($SQL);

        foreach ($aRS as $rs) {
            $UrlInfo = new UrlInfo();
            $UrlInfo->setTypeId($rs["TYPE_ID"])
                ->setCategory($rs['CATEGORY'])
                ->setName($rs["NAME"])
                ->setValue($rs["VALUE"]);
            array_push($aUrlInfo, $UrlInfo);
        }

        return $aUrlInfo;
    }

    public function getServices(): array
    {
        $aServices = [];

        $SQL = "select id, name, stid, stname
        from (
        select
            1 as id,
            'service_name' as name,
            1 as stid ,
            'subtype_name' as stname
        union
        select
            1 as id,
            'service_name' as name,
            2 as stid ,
            'subtype_name2' as stname
        union
        select
            2 as id,
            'service_name2' as name,
            21 as stid ,
            'subtype_name21' as stname
        ) as a 
        order by 1"; // order by id
        $aRS = $this->getDBConn()->getAllNames($SQL);

        $iServiceId = 0;

        $aSubtypes = [];

        foreach ($aRS as $rs) {

            if ($iServiceId === $rs["ID"]) {
                array_push($aSubtypes, new ServiceSubType($rs["STID"], $rs['STNAME']));
            } else {

                if (count($aSubtypes)) {
                    $Service->setSubTypes($aSubtypes);
                }

                $aSubtypes = [];

                $iServiceId = $rs["ID"];

                $Service = new ServiceInfo();
                $Service->setId($rs["ID"])
                    ->setName($rs['NAME']);

                array_push($aSubtypes, new ServiceSubType($rs["STID"], $rs['STNAME']));

                array_push($aServices, $Service);
            }
        }
        if (count($aSubtypes)) {
            $Service->setSubTypes($aSubtypes);
        }

        return $aServices;
    }

    public function getProperties(): array
    {

        $aProperties = [];

        $SQL = "select property_id, category, sub_category, reference_id, pa_id, pa_key, pa_datatype, pa_mandatory
        from (
        select
            1 as property_id,
            'category1' as category,
            'sub_category1' as sub_category,
            'reference_id1' as reference_id ,
            '1' as pa_id,
            'pakey1' as pa_key,
            'padatatype1' as pa_datatype,
            'pamandatory1' as pa_mandatory
        union
        select
            1 as property_id,
            'category1' as category,
            'sub_category1' as sub_category,
            'reference_id1' as reference_id ,
            '2' as pa_id,
            'pakey2' as pa_key,
            'padatatype2' as pa_datatype,
            'pamandatory2' as pa_mandatory
        union
        select
            2 as property_id,
            'category2' as category,
            'sub_category2' as sub_category,
            'reference_id2' as reference_id ,
            '3' as pa_id,
            'pakey3' as pa_key,
            'padatatype3' as pa_datatype,
            'pamandatory3' as pa_mandatory
        ) as a 
        order by 1"; // order by id
        $aRS = $this->getDBConn()->getAllNames($SQL);

        $iPropertyId = 0;

        $aPropertyAttributes = [];

        foreach ($aRS as $rs) {

            if ($iPropertyId === $rs["PROPERTY_ID"]) {
                array_push($aPropertyAttributes, new PropertyAttribute($rs["PA_ID"], $rs['PA_KEY'], $rs['PA_DATATYPE'], $rs['PA_MANDATORY']));
            } else {

                if (count($aPropertyAttributes)) {
                    $PropertyDetail->setProperties($aPropertyAttributes);
                }

                $aPropertyAttributes = [];

                $iPropertyId = $rs["PROPERTY_ID"];

                $PropertyDetail = new PropertyDetail();
                $PropertyDetail->setCategory($rs["CATEGORY"])
                    ->setSubCategory($rs['SUB_CATEGORY'])
                    ->setRefernceId($rs['REFERENCE_ID']);

                array_push($aPropertyAttributes, new PropertyAttribute($rs["PA_ID"], $rs['PA_KEY'], $rs['PA_DATATYPE'], $rs['PA_MANDATORY']));

                array_push($aProperties, $PropertyDetail);
            }
        }
        if (count($aPropertyAttributes)) {
            $PropertyDetail->setProperties($aPropertyAttributes);
        }


        return $aProperties;
    }
}
