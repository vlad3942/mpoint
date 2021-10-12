<?php

namespace api\classes\merchantservices\Repositories;


use AddonServiceTypeIndex;
use api\classes\merchantservices\configuration\AddonServiceType;
use api\classes\merchantservices\configuration\DCCConfig;
use api\classes\merchantservices\configuration\FraudConfig;
use api\classes\merchantservices\configuration\MCPConfig;
use api\classes\merchantservices\configuration\MPIConfig;
use api\classes\merchantservices\configuration\PCCConfig;
use api\classes\merchantservices\configuration\PropertyInfo;
use api\classes\merchantservices\configuration\ServiceConfig;
use api\classes\merchantservices\MerchantOnboardingException;

use api\classes\merchantservices\MetaData\Client;
use api\classes\merchantservices\commons\BaseInfo;
use api\classes\merchantservices\MetaData\ClientUrl;
use api\classes\merchantservices\MetaData\ClientPaymentMethodId;
use api\classes\merchantservices\MetaData\StoreFront;


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
        if($addonServiceType->getID() === AddonServiceTypeIndex::eFraud || $addonServiceType->getID() === AddonServiceTypeIndex::eSPLIT_PAYMENT)
        {
            $sTableName = ".fraud_property_tbl";
            if($addonServiceType->getID() === AddonServiceTypeIndex::eSPLIT_PAYMENT ) $sTableName = ".split_property_tbl";
            $SQL = 'SELECT is_rollback FROM client'. sSCHEMA_POSTFIX.$sTableName.' where enabled=true and clientid='.$this->_clientConfig->getID();
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
                $SQL ="INSERT INTO client". sSCHEMA_POSTFIX ;
                $sPropTableName = '';
                if ($addonConfig->getServiceType()->getID()=== AddonServiceTypeIndex::eFraud) $sPropTableName = '.fraud_property_tbl';
                else if ($addonConfig->getServiceType()->getID()=== AddonServiceTypeIndex::eSPLIT_PAYMENT) $sPropTableName = '.split_property_tbl';
                $SQL .=$sPropTableName." (is_rollback,clientid) values (".\General::bool2xml($addonConfig->getProperties()["is_rollback"]).",".$this->_clientConfig->getID().")";
                $SQL .=" ON CONFLICT (clientid) do update set is_rollback =".\General::bool2xml($addonConfig->getProperties()["is_rollback"]);
                $result = $this->getDBConn()->executeQuery($SQL);
                if ($result == FALSE)
                {
                    throw new MerchantOnboardingException(MerchantOnboardingException::SQL_EXCEPTION,'Failed to Update '.$addonConfig->getServiceType()->getName().' is_rollback property');
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
                $SQL ="INSERT INTO client". sSCHEMA_POSTFIX ;
                $sPropTableName = '';
                if ($addonConfig->getServiceType()->getID()=== AddonServiceTypeIndex::eFraud) $sPropTableName = '.fraud_property_tbl';
                else if ($addonConfig->getServiceType()->getID()=== AddonServiceTypeIndex::eSPLIT_PAYMENT) $sPropTableName = '.split_property_tbl';
                $SQL .=$sPropTableName." (is_rollback,clientid) values (".\General::bool2xml($addonConfig->getProperties()["is_rollback"]).",".$this->_clientConfig->getID().")";
                $SQL .=" ON CONFLICT (clientid) do update set is_rollback =".\General::bool2xml($addonConfig->getProperties()["is_rollback"]);
                $result = $this->getDBConn()->executeQuery($SQL);
                if ($result == FALSE)
                {
                    throw new MerchantOnboardingException(MerchantOnboardingException::SQL_EXCEPTION,'Failed to Update '.$addonConfig->getServiceType()->getName().' is_rollback property');
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
     * @throws MerchantOnboardingException
     * @throws \SQLQueryException
     */
    public function updatePropertyConfig(string $type, array $aPropertyInfo,int $id=-1,array $aPMIds=array())
    {
        $this->getDBConn()->query("START TRANSACTION");

        if(empty($aPMIds) === false)
        {
            $SQL = "INSERT INTO client". sSCHEMA_POSTFIX.".routepm_tbl (routeconfigid, pmid) VALUES ($1,$2)";
            foreach ($aPMIds as $PMId)
            {
                $aParam = array($id,$PMId);
                $rs = $this->getDBConn()->executeQuery($SQL, $aParam);
                if($rs == false)
                {
                    $statusCode = MerchantOnboardingException::SQL_EXCEPTION;
                    if(strpos($this->getDBConn()->getErrMsg(),'duplicate key value violates unique constraint') !== false)
                    {
                        $statusCode = MerchantOnboardingException::SQL_DUPLICATE_EXCEPTION;
                    }
                    $this->getDBConn()->query("ROLLBACK");

                    throw new MerchantOnboardingException($statusCode,"Failed to Insert Payment Method Id:".$PMId);
                }
            }
        }

        if(empty($aPropertyInfo) === false)
        {
            $sTableName = '';
            $sWhereClase = '';
            if($type === 'CLIENT')
            {
                $sTableName = 'client_property_tbl';
                $sWhereClase = ' WHERE propertyid=$2 and clientid='.$this->_clientConfig->getID();
            }
            else if($type === 'PSP')
            {
                $sTableName = 'psp_property_tbl';
                $sWhereClase = ' FROM SYSTEM'. sSCHEMA_POSTFIX .'.psp_property_tbl SP  WHERE cp.propertyid =sp.id and propertyid=$2 and pspid='.$id;
            }
            else if($type === 'ROUTE')
            {
                $sTableName = 'route_property_tbl';
                $SQL = "SELECT r.providerid as id FROM CLIENT". sSCHEMA_POSTFIX .".routeconfig_tbl rt INNER JOIN CLIENT". sSCHEMA_POSTFIX .".route_tbl r ON R.id = rt.routeid WHERE rt.id=".$id;
                $aRS = $this->getDBConn()->getName ( $SQL );
                if(empty($aRS) === false)
                {
                    $sWhereClase = ' FROM SYSTEM'. sSCHEMA_POSTFIX .'.route_property_tbl SP WHERE cp.propertyid =sp.id and propertyid=$2 and pspid='.$aRS['ID'];
                }
                else throw new MerchantOnboardingException(MerchantOnboardingException::SQL_EXCEPTION,"Failed to retrieve PSPID for routeconfigid:".$id);
            }
            $SQL = "UPDATE client". sSCHEMA_POSTFIX.".".$sTableName." CP SET value=$1 ".$sWhereClase;
            foreach ($aPropertyInfo as $propertyInfo)
            {
                $aParam = array($propertyInfo->getValue(),$propertyInfo->getId());
                $rs = $this->getDBConn()->executeQuery($SQL, $aParam);
                if($rs == false || $this->getDBConn()->countAffectedRows($rs) < 1)
                {
                    $statusCode = MerchantOnboardingException::SQL_EXCEPTION;
                    if(strpos($this->getDBConn()->getErrMsg(),'duplicate key value violates unique constraint') !== false)
                    {
                        $statusCode = MerchantOnboardingException::SQL_DUPLICATE_EXCEPTION;
                    }
                    $this->getDBConn()->query("ROLLBACK");

                    throw new MerchantOnboardingException($statusCode,"Failed to update ".strtolower($type)." Config Property  {id:".$propertyInfo->getId()." value:".$propertyInfo->getValue()."}");
                }
            }
        }
        $this->getDBConn()->query("COMMIT");

    }

    /**
     * @throws MerchantOnboardingException
     * @throws \SQLQueryException
     */
    public function savePropertyConfig(string $type, array $aPropertyInfo,int $id=-1,array $aPMIds=array())
    {
        $this->getDBConn()->query("START TRANSACTION");

        if(empty($aPMIds) === false)
      {
          $SQL = "INSERT INTO client". sSCHEMA_POSTFIX.".routepm_tbl (routeconfigid, pmid) VALUES ($1,$2)";
          foreach ($aPMIds as $PMId)
          {
              $aParam = array($id,$PMId);
              $rs = $this->getDBConn()->executeQuery($SQL, $aParam);
              if($rs == false)
              {
                  $statusCode = MerchantOnboardingException::SQL_EXCEPTION;
                  if(strpos($this->getDBConn()->getErrMsg(),'duplicate key value violates unique constraint') !== false)
                  {
                      $statusCode = MerchantOnboardingException::SQL_DUPLICATE_EXCEPTION;
                  }
                  $this->getDBConn()->query("ROLLBACK");

                  throw new MerchantOnboardingException($statusCode,"Failed to Insert Payment Method Id:".$PMId);
              }
          }
      }

      if(empty($aPropertyInfo) === false)
      {
          $sTableName = '';
          $sColumnName = 'clientid,propertyid, value';
          $sValues = 'VALUES ($1,$2,$3)';
          if($type === 'CLIENT')
          {
              $sTableName = 'client_property_tbl';
          }
          else if($type === 'PSP')
          {
              $sTableName = 'psp_property_tbl';
              $sValues = ' SELECT $1,$2,$3 FROM SYSTEM'. sSCHEMA_POSTFIX.'.psp_property_tbl WHERE id=$2 and PSPID='.$id;
              $id = $this->_clientConfig->getID();
          }
          else if($type === 'ROUTE')
          {
              $sTableName = 'route_property_tbl';
              $SQL = "SELECT r.providerid as id FROM CLIENT". sSCHEMA_POSTFIX .".routeconfig_tbl rt INNER JOIN CLIENT". sSCHEMA_POSTFIX .".route_tbl r ON R.id = rt.routeid WHERE rt.id=".$id;
              $aRS = $this->getDBConn()->getName ( $SQL );
              if(empty($aRS) === false)
              {
                  $sValues = ' SELECT $1,$2,$3 FROM SYSTEM'. sSCHEMA_POSTFIX.'.route_property_tbl WHERE id=$2 and PSPID='.$aRS['ID'];
              }
              else throw new MerchantOnboardingException(MerchantOnboardingException::SQL_EXCEPTION,"Failed to retrieve PSPID for routeconfigid:".$id);

              $sColumnName = 'routeconfigid, propertyid, value';
          }
          $SQL = "INSERT INTO client". sSCHEMA_POSTFIX.".".$sTableName." (".$sColumnName.") ".$sValues;
          foreach ($aPropertyInfo as $propertyInfo)
          {
              $aParam = array($id,$propertyInfo->getId(),$propertyInfo->getValue());
              $rs = $this->getDBConn()->executeQuery($SQL, $aParam);
              if($rs == false || $this->getDBConn()->countAffectedRows($rs) < 1)
              {
                  $statusCode = MerchantOnboardingException::SQL_EXCEPTION;
                  if(strpos($this->getDBConn()->getErrMsg(),'duplicate key value violates unique constraint') !== false)
                  {
                      $statusCode = MerchantOnboardingException::SQL_DUPLICATE_EXCEPTION;
                  }
                  $this->getDBConn()->query("ROLLBACK");

                  throw new MerchantOnboardingException($statusCode,"Failed to save ".strtolower($type)." Config Property  {id:".$propertyInfo->getId()." value:".$propertyInfo->getValue()."}");
              }
          }
      }
        $this->getDBConn()->query("COMMIT");

    }
    public function getRoutePM(int $id) : array
    {
        $aPM = array();
        $sSQL = "SELECT pmid FROM CLIENT". sSCHEMA_POSTFIX .".routepm_tbl WHERE enabled=true and routeconfigid = ".$id;
        $aRS = $this->getDBConn()->getAllNames ( $sSQL );
        if (empty($aRS) === false)
        {
            foreach ($aRS as $rs) array_push($aPM,$rs["PMID"]);
        }
        return $aPM;
    }
    public function getPropertyConfig(string $type,string $source,int $id=-1) : array
    {
        $sTableName = '';
        $sWhereArgs = '';
        if($type === 'CLIENT')
        {
            $sTableName = 'client_property_tbl';
            $sWhereArgs = " AND cp.enabled=true AND sp.enabled AND clientid =".$this->_clientConfig->getID();
        }
        else if($type === 'PSP')
        {
            $sTableName = 'psp_property_tbl';
            $sWhereArgs = " AND cp.enabled=true AND sp.enabled AND clientid =".$this->_clientConfig->getID();
        }
        else if($type === 'ROUTE')
        {
            $sTableName = 'route_property_tbl';
            $sWhereArgs = " AND cp.enabled=true AND sp.enabled AND cp.routeconfigid =".$id;

        }
        $sJoin = "";
        $sColumn = ",cp.value";
        $sMetaDataJoin = "";
        if($source === 'METADATA')
        {
            $sColumn = "";
            if($id>-1 && $type !== 'CLIENT') $sMetaDataJoin = " AND sp.enabled AND sp.pspid=".$id." ";
        }
        elseif($source === 'ALL')
        {
            $sJoin ="LEFT JOIN CLIENT". sSCHEMA_POSTFIX . ".".$sTableName." cp on cp.propertyid = sp.id ".$sWhereArgs;
            if($type === 'ROUTE') $sMetaDataJoin = " AND sp.pspid=(SELECT r.providerid FROM CLIENT". sSCHEMA_POSTFIX .".routeconfig_tbl rt INNER JOIN CLIENT". sSCHEMA_POSTFIX .".route_tbl r ON R.id = rt.routeid WHERE rt.id=".$id.")";
            if($type === 'PSP') $sMetaDataJoin = " AND sp.pspid=".$id;
        }
        else if($source === 'CLIENT') $sJoin ="INNER JOIN CLIENT". sSCHEMA_POSTFIX . ".".$sTableName." cp on cp.propertyid = sp.id ".$sWhereArgs;

        $sSQL = "SELECT sp.id,sp.name,sp.datatype ,sp.ismandatory".$sColumn.",pc.name as category,pc.scope, true as enabled from SYSTEM". sSCHEMA_POSTFIX . ".".$sTableName." sp 
         ".$sJoin." INNER JOIN SYSTEM". sSCHEMA_POSTFIX . ".property_category_tbl pc on sp.category = pc.id ".$sMetaDataJoin."
         ORDER BY sp.name ";

        $aRS = $this->getDBConn()->getAllNames ( $sSQL );
        $aPropertyInfo = array();
        if (empty($aRS) === false)
        {
            foreach ($aRS as $rs)
            {
                $propertyInfo = PropertyInfo::produceFromResultSet($rs);
                if(isset($aPropertyInfo[$propertyInfo->getCategory()]) === true)
                {
                    array_push($aPropertyInfo[$propertyInfo->getCategory()], $propertyInfo);
                }
                else
                {
                    $aPropInfo = array();
                    array_push($aPropInfo, $propertyInfo);
                    $aPropertyInfo[$propertyInfo->getCategory()] =$aPropInfo;
                }
            }
        }
     return $aPropertyInfo;
    }

    /**
     * Generate Payment Metadata
     *
     * @return array
     */
    public function getAllPaymentMetaDataInfo(): array
    {
        $aPaymentMetaData = [];

        $aPaymentMetaData['pms'] = $this->getMetaDataInfo('pm', 'card_tbl', true, array('paymenttype as type_id'));
        $aPaymentMetaData['payment_providers'] = $this->getpaymentProviders();
        $aPaymentMetaData['route_features'] = $this->routeFeaturesInfo();
        $aPaymentMetaData['transaction_types'] = $this->getMetaDataInfo('transaction_type', 'type_tbl', true);
        $aPaymentMetaData['card_states'] = $this->getMetaDataInfo('card_state', 'cardstate_tbl', true);
        $aPaymentMetaData['fx_service_types'] = $this->getMetaDataInfo('fx_service_type', 'fxservicetype_tbl', true);
        
        return $aPaymentMetaData;
    }

    /**
     * Generate Payment provider data
     *
     * @return void
     */
    private function paymentProvidersData() : array
    {
        $iClientId = $this->_clientConfig->getID();        

        $SQL = "SELECT rt.id, psp.name, rc.id as rcid, rc.name as rcname
        FROM CLIENT" . sSCHEMA_POSTFIX . ".route_tbl rt 
        inner join SYSTEM" . sSCHEMA_POSTFIX . ".psp_tbl psp on rt.providerid  = psp.id
        inner join CLIENT" . sSCHEMA_POSTFIX . ".routeconfig_tbl rc on rc.routeid = rt.id
        WHERE clientid = $iClientId
        order by rt.id";

        return $this->getDBConn()->getAllNames($SQL);
    }

    /**
     * Generate payment provider Info
     *
     * @return array
     */
    private function getpaymentProviders() : array
    {
        $aPaymentProviders = [];
        $iPaymentProviderId = -1;
        $aRouteConfigs = [];
        $aPaymentProvider = [];
        $aRouteConfigData = [];

        $aRS = $this->paymentProvidersData();

        foreach ($aRS as $rs) {

            if ($iPaymentProviderId === $rs["ID"]) {
                array_push($aRouteConfigData, array('ID' => $rs["RCID"], 'NAME' => $rs['RCNAME']));
            } else {

                if (count($aRouteConfigData)) {
                    $aRouteConfigs = BaseInfo::produceFromDataSet($aRouteConfigData, 'route_configuration', array('name' => 'route_name'));
                    $PaymentProvider->additionalProp['route_configurations'] = $aRouteConfigs;
                }

                $aRouteConfigs = [];
                $aPaymentProvider = [];

                $iPaymentProviderId = $rs["ID"];

                $aPaymentProvider[] =  array('ID' => $rs["ID"], 'NAME' => $rs['NAME']);
                $PaymentProvider = BaseInfo::produceFromDataSet(
                    $aPaymentProvider,
                    'payment_provider'
                )[0];

                array_push($aRouteConfigData, array('ID' => $rs["RCID"], 'NAME' => $rs['RCNAME']));

                array_push($aPaymentProviders, $PaymentProvider);
            }
        }
        if (count($aRouteConfigData)) {
            $aRouteConfigs = BaseInfo::produceFromDataSet($aRouteConfigData, 'route_configuration', array('name' => 'route_name'));
            $PaymentProvider->additionalProp['route_configurations'] = $aRouteConfigs;
        }        
        return $aPaymentProviders;
    }


    /**
     * Generate route Feature Info
     *
     * @return array
     */
    private function routeFeaturesInfo(): array
    {
        $aRouteFeatureInfo = [];

        $SQL = "SELECT id, featurename as name FROM SYSTEM" . sSCHEMA_POSTFIX . ".routefeature_tbl  WHERE enabled = true ";
        $aRS = $this->getDBConn()->getAllNames($SQL);

        $aRouteFeatureInfo = BaseInfo::produceFromDataSet($aRS, 'route_feature');        

        return $aRouteFeatureInfo;
    }

    /**
     * Generate System MetaData
     *
     * @return array
     */
    public function getAllSystemMetaDataInfo(): array
    {
        $aSystemMetaData = [];

        /* 
        *   getMetaDataInfo parameters
            1. rootnode of the section
            2. table name 
            3. check nabled flag
        */
        $aSystemMetaData['psps'] = $this->getMetaDataInfo('psp', 'psp_tbl', true, array('system_type as type_id'));
        $aSystemMetaData['pms'] = $this->getMetaDataInfo('pm', 'paymenttype_tbl');
        $aSystemMetaData['country_details'] = $this->getMetaDataInfo('country_detail', 'country_tbl', true);
        $aSystemMetaData['currency_details'] = $this->getMetaDataInfo('currency_detail', 'currency_tbl', true);
        $aSystemMetaData['capture_types'] = $this->getMetaDataInfo('capture_type', 'capturetype_tbl', true);
        $aSystemMetaData['capture_types'] = $this->getMetaDataInfo('capture_type', 'capturetype_tbl', true);
        $aSystemMetaData['client_urls'] = $this->getMetaDataInfo('client_url', 'urltype_tbl', true);
        $aSystemMetaData['provider_details'] = $this->getMetaDataInfo('provider_detail', 'processortype_tbl');

        $aSystemMetaData['addon_types'] = $this->getServicesInfo();

        return $aSystemMetaData;
    }

    /**
     * Generic function to generate Metadata response structure
     *
     * @param string $rootNode
     * @param string $sTableName
     * @param boolean $bCheckEnabled
     * @return array
     */
    private function getMetaDataInfo(string $rootNode, string $sTableName, bool $bCheckEnabled = false, array $aAddtionalFields = []): array
    {
        $aMetaServiceConfig = [];
        $sAddtionalFields = '';

        if ($bCheckEnabled) {
            $sEnableCheck = ' AND enabled = true';
        }

        if(!empty($aAddtionalFields))
        {
            $sAddtionalFields = ', '.implode(',',$aAddtionalFields);
        }

        $SQL = "SELECT id, name $sAddtionalFields FROM SYSTEM" . sSCHEMA_POSTFIX . "." . $sTableName . "  WHERE true " . $sEnableCheck;
        $aRS = $this->getDBConn()->getAllNames($SQL);

        $aMetaServiceConfig = BaseInfo::produceFromDataSet($aRS, $rootNode);        

        return $aMetaServiceConfig;
    }

    /**
     *  Fetch Services master data
     *
     * @return array
     */
    public function getSerivesData(): array
    {
        $SQL = "SELECT st.id as id, st.name ,sst.id stid, sst.name as stname
        FROM SYSTEM" . sSCHEMA_POSTFIX . ".services_tbl st
        inner join SYSTEM" . sSCHEMA_POSTFIX . ".service_type_tbl sst on st.id = sst.serviceid
        order by st.id";

        return $this->getDBConn()->getAllNames($SQL);
    }

    /**
     * Generate Services response structure
     *
     * @return array
     */
    public function getServicesInfo(): array
    {
        $aServices = [];
        $iServiceId = 0;
        $aSubtypes = [];
        $aTypes = [];

        $aRS = $this->getSerivesData();

        foreach ($aRS as $rs) {

            if ($iServiceId === $rs["ID"]) {
                array_push($aSubtypes, array('ID' => $rs["STID"], 'NAME' => $rs['STNAME']));
            } else {

                if (count($aSubtypes)) {
                    $aServiceSubTypes = BaseInfo::produceFromDataSet($aSubtypes, 'addon_subtype', array('name' => 'addon_subtype'));
                    $Service->additionalProp['addon_subtypes'] = $aServiceSubTypes;
                }

                $aSubtypes = [];
                $aTypes = [];

                $iServiceId = $rs["ID"];

                $aTypes[] =  array('ID' => $rs["ID"], 'NAME' => $rs['NAME']);
                $Service = BaseInfo::produceFromDataSet(
                    $aTypes,
                    'addon_type',
                    array('name' => 'addon_type')
                )[0];

                array_push($aSubtypes, array('ID' => $rs["STID"], 'NAME' => $rs['STNAME']));

                array_push($aServices, $Service);
            }
        }
        if (count($aSubtypes)) {
            $aServiceSubTypes = BaseInfo::produceFromDataSet($aSubtypes, 'addon_subtype', array('name' => 'addon_subtype'));
            $Service->additionalProp['addon_subtypes'] = $aServiceSubTypes;
        }

        return $aServices;
    }


    ////////// For Client Configuration //////////
    /**
     * Function used to get the client detail By client ID
     *
     * @return Client Object Of Client
     */
    public function getClientDetailById(): Client
    {
        return Client::produceFromResultSet([
            'ID' => $this->_clientConfig->getID(),
            'NAME' => $this->_clientConfig->getName(),
            'SALT' => $this->_clientConfig->getSalt(),
            'MAXAMOUNT' => $this->_clientConfig->getMaxAmount(),
            'COUNTRYID' => $this->_clientConfig->getCountryConfig()->getID(),
            'EMAILRCPT' => \General::bool2xml($this->_clientConfig->emailReceiptEnabled()),
            'SMSRCPT' => \General::bool2xml($this->_clientConfig->smsReceiptEnabled()),
            'USERNAME' => $this->_clientConfig->getUsername()
        ]);
    }

    /**
     * Function used to get the client URL Details by Client ID
     *
     * @return ?array
     */
    public function getClientURLByClientId(): ?array
    {
        $sColumns = 'CLIURL.ID, CLIURL.urltypeid as type_id, SYSURL.name, CLIURL.url as value, CLIURL.enabled, CLIURL.created, CLIURL.modified';
        $SQL = "SELECT %s FROM CLIENT" . sSCHEMA_POSTFIX . ".url_tbl CLIURL INNER JOIN SYSTEM" . sSCHEMA_POSTFIX . ".urltype_tbl SYSURL ON CLIURL.urltypeid = SYSURL.id WHERE CLIURL.enabled = true and CLIURL.clientid = " . $this->_clientConfig->getID();
        $aRS = $this->getDBConn()->getAllNames(sprintf($SQL, $sColumns));

        if (empty($aRS) === true) { return NULL; }

        $aClientURLs = array();
        foreach ($aRS as $rs){
            array_push($aClientURLs, ClientUrl::produceFromResultSet($rs));
        }
        return $aClientURLs;
    }

    /**
     * Get Client StoreFront ID
     *
     * @return array
     */
    public function getStoreFrontByClientId(): ?array
    {
        $sColumns = "ACC.id, ACC.markup as NAME, ACC.businesstype as DOMAIN";
        $SQL = "SELECT %s FROM CLIENT" . sSCHEMA_POSTFIX . ".account_tbl ACC                     
                    WHERE ACC.enabled = true and ACC.clientid = " . $this->_clientConfig->getID();
        $aRS = $this->getDBConn()->getAllNames(sprintf($SQL, $sColumns));

        if (empty($aRS) === true) { return NULL; }

        $aStoreFront = [];
        foreach ($aRS as $rs) {
            array_push($aStoreFront, StoreFront::produceFromResultSet($rs));
        }
        return $aStoreFront;
    }

    /**
     * Get Client Payment Method by ClientID
     *
     * @return ?array
     */
    public function getPMIdsByClientId(): ?array
    {
        $sColumns = "PM.id payment_method_id, C.name";
        $SQL = "SELECT %s FROM CLIENT" . sSCHEMA_POSTFIX . ".pm_tbl PM
                    INNER JOIN SYSTEM" . sSCHEMA_POSTFIX . ".card_tbl C ON PM.pmid = C.id
                    WHERE PM.enabled = true AND PM.clientid = " . $this->_clientConfig->getID();

        $aRS = $this->getDBConn()->getAllNames(sprintf($SQL, $sColumns));

        if (empty($aRS) === true) { return NULL; }
        $aPMIds = [];
        foreach ($aRS as $rs) {
            array_push($aPMIds, ClientPaymentMethodId::produceFromResultSet($rs));
        }
        return $aPMIds;
    }
}
