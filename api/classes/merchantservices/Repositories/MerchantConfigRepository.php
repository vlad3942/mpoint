<?php

namespace api\classes\merchantservices\Repositories;

use AddonServiceTypeIndex;
use api\classes\merchantservices\configuration\AddonServiceType;
use api\classes\merchantservices\configuration\BaseConfig;
use api\classes\merchantservices\configuration\DCCConfig;
use api\classes\merchantservices\configuration\FraudConfig;
use api\classes\merchantservices\configuration\MCPConfig;
use api\classes\merchantservices\configuration\MPIConfig;
use api\classes\merchantservices\configuration\PCCConfig;
use api\classes\merchantservices\configuration\PropertyInfo;
use api\classes\merchantservices\configuration\ProviderConfig;
use api\classes\merchantservices\configuration\ServiceConfig;
use api\classes\merchantservices\Helpers\Helpers;
use api\classes\merchantservices\MerchantOnboardingException;

use api\classes\merchantservices\MetaData\ClientServiceStatus;
use api\classes\merchantservices\commons\BaseInfo;

use ClientConfig;
use General;
use RDB;

use Constants;

/**
 * Repository Class
 *
 *
 * @package    Mechantservices
 * @subpackage DB Services
 */

class MerchantConfigRepository
{
    /**
     * @var RDB
     */
    private \RDB $_conn;

    /**
     * @var ClientConfig|null
     */
    private \ClientConfig $_clientConfig;


    /**
     * @param RDB $conn
     * @param int $iClientId
     * @param ClientConfig|null $clientConfig
     */
    public function __construct(RDB  &$conn, int $iClientId,?ClientConfig  &$clientConfig = null )
    {
        $this->_conn = $conn;
        if($clientConfig !== null) $this->_clientConfig = $clientConfig;
        else $this->_clientConfig = ClientConfig::produceConfig($conn, $iClientId);
    }

    /**
     * @return ClientConfig
     */
    public function getClientInfo() : ClientConfig
    {
        $this->_clientConfig->getAccountsConfigurations($this->_conn);
        return $this->_clientConfig;
    }

    /**
     * @return RDB
     */
    private function getDBConn():\RDB { return $this->_conn;}

    /**
     * @param AddonServiceType $addonServiceType
     * @param array $aWhereCls
     * @param bool $isPropertyOnly
     * @return mixed
     */
    public function getAddonConfig(AddonServiceType $addonServiceType,array $aWhereCls = array(),bool $isPropertyOnly = false)
    {
        $SQL ="";
        $aServiceConfig = array();
        if($isPropertyOnly === false)
        {
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
            if($addonServiceType->getID() ===AddonServiceTypeIndex::eFraud )
            {
                $sColumns .= ',providerid,typeoffraud ';
                if($addonServiceType->getSubType() === 'pre_auth') array_push($aWhereCls ,'typeoffraud=1');
                else array_push($aWhereCls ,'typeoffraud=2');
            }
            elseif($addonServiceType->getID() ===AddonServiceTypeIndex::eTOKENIZATION )
            {
                $sColumns .= ',providerid ';
            }
            elseif($addonServiceType->getID() ===AddonServiceTypeIndex::eSPLIT_PAYMENT)
            {
                $sColumns = 'id,payment_type, sequence_no,enabled ';
            }
            elseif($addonServiceType->getID() ===AddonServiceTypeIndex::ePCC) $sColumns = 'id,pmid,sale_currency_id,is_presentment,settlement_currency_id,created,modified,enabled ';
            elseif($addonServiceType->getID() ===AddonServiceTypeIndex::eMPI) $sColumns = 'id, clientid, pmid, providerid,version,created,modified,enabled ';
            $sSQL = sprintf($SQL,$sColumns,$sTableName) ;
            if(empty($aWhereCls) === false)
            {
                $sSQL.= " AND ".implode(" AND ",$aWhereCls);
            }
            $aRS = $this->getDBConn()->getAllNames ( $sSQL );
            if (empty($aRS) === false)
            {
                foreach ($aRS as $rs)
                {
                    array_push($aServiceConfig, ServiceConfig::produceFromResultSet($rs));
                }
            }
        }

        $className =   'api\\classes\\merchantservices\\configuration\\'.$addonServiceType->getClassName();
        $aProperty = array();
        if($addonServiceType->getID() === AddonServiceTypeIndex::eFraud || $addonServiceType->getID() === AddonServiceTypeIndex::eSPLIT_PAYMENT)
        {
            $sColumns = "is_rollback";
            $sTableName = ".fraud_property_tbl";
            if($addonServiceType->getID() === AddonServiceTypeIndex::eSPLIT_PAYMENT )
            {
                $sTableName = ".split_property_tbl";
                $sColumns .= ",is_reoffer";
            }
            $SQL = 'SELECT '.$sColumns.' FROM client'. sSCHEMA_POSTFIX.$sTableName.' where enabled=true and clientid='.$this->_clientConfig->getID();
            $aRS = $this->getDBConn()->getName ( sprintf($SQL,$sColumns,$sTableName) );
            if(empty($aRS) === false) $aProperty = array_change_key_case($aRS,CASE_LOWER);
        }
        return new $className($aServiceConfig,$aProperty,$addonServiceType->getSubType());

    }

    /**
     * @return array
     */
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

       array_push($aAddonConfig,$this->getAddonConfig(AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::eTOKENIZATION,'Tokenization')));

       return  $aAddonConfig;
    }

    /**
     * @throws MerchantOnboardingException
     */
    public function saveAddonConfig(array &$aAddonConfig, $isDeleteOldConfig = false)
    {
        foreach ($aAddonConfig as $addonConfig)
        {
            if($isDeleteOldConfig === true)  $this->deleteAllAddonConfig($addonConfig->getServiceType());

            if(empty($addonConfig->getProperties()) === false)
            {
                $SQL ="INSERT INTO client". sSCHEMA_POSTFIX ;
                $sPropTableName = '';
                if ($addonConfig->getServiceType()->getID()=== AddonServiceTypeIndex::eFraud) $sPropTableName = '.fraud_property_tbl';
                else if ($addonConfig->getServiceType()->getID()=== AddonServiceTypeIndex::eSPLIT_PAYMENT) $sPropTableName = '.split_property_tbl';
                foreach ($addonConfig->getProperties() as $key => $value)
                {
                    $SQL ="INSERT INTO client". sSCHEMA_POSTFIX .$sPropTableName." (".$key.",clientid) values (".\General::bool2xml($value).",".$this->_clientConfig->getID().")";
                    $SQL .=" ON CONFLICT (clientid) do update set ".$key." =".\General::bool2xml($value);
                    $result = $this->getDBConn()->executeQuery($SQL);
                    if ($result === FALSE)
                    {
                        throw new MerchantOnboardingException(MerchantOnboardingException::SQL_EXCEPTION,'Failed to Update '.$addonConfig->getServiceType()->getName().' property');
                    }
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
                        if($rs === false) return array();
                        else $id = $this->getDBConn()->fetchName($rs)['ID'];

                    }
                }
                else $id = $this->_clientConfig->getID();

                foreach ($aServiceConf as $serviceConf)
                {
                    $aParams = $serviceConf->getParam($addonConfig->getServiceType(),$id);
                    $result = $this->getDBConn()->executeQuery($sql, $aParams);

                    if ($result === FALSE)
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
     * @param AddonServiceType $addonServiceType
     * @throws MerchantOnboardingException
     */
    public function deleteAllAddonConfig(AddonServiceType $addonServiceType)
    {

        $sWhereClause = " WHERE clientid = " . $this->_clientConfig->getID();

        if($addonServiceType->getID() === AddonServiceTypeIndex::eSPLIT_PAYMENT)
        {
            $sWhereClause = " WHERE split_config_id in (SELECT id from CLIENT".sSCHEMA_POSTFIX.".split_configuration_tbl WHERE  client_id = " .$this->_clientConfig->getID() . " AND name = '" . $addonServiceType->getSubType() . "')";
        } else if($addonServiceType->getID() === AddonServiceTypeIndex::eFraud) {
            if($addonServiceType->getSubType() === 'pre_auth') {
                $sWhereClause .= " AND typeofFraud = 1 ";
            } else if($addonServiceType->getSubType() === 'post_auth'){
                $sWhereClause .= " AND typeofFraud = 2 ";
            }
        }

        $SQL = 'DELETE FROM CLIENT'.sSCHEMA_POSTFIX.'.'. $addonServiceType->getTableName() . ' ' . $sWhereClause;
        $rs = $this->getDBConn()->executeQuery($SQL);

        if($rs === false)
        {
            $statusCode = MerchantOnboardingException::SQL_EXCEPTION;
            throw new MerchantOnboardingException($statusCode,"Failed to Delete ".$addonServiceType->getType()." Config ");
        }

    }

    /**
     * @throws MerchantOnboardingException
     * @throws \SQLQueryException
     */
    public function deleteAddonConfig(array $additionalParams)
    {

        foreach ($additionalParams as $params ) {
            $addonServiceType = $params[1];
            $Ids = $params[2];

            if ($Ids === "-1" && $addonServiceType->getSubType() === 'cashless' || $addonServiceType->getSubType() === 'conventional' || $addonServiceType->getSubType() === 'hybrid')
            {
                $SQL = "DELETE FROM CLIENT".sSCHEMA_POSTFIX.".". $addonServiceType->getTableName() ." WHERE split_config_id in (SELECT id FROM CLIENT". sSCHEMA_POSTFIX .".split_configuration_tbl WHERE client_id=".$this->getClientInfo()->getID()."  AND name = '" . $addonServiceType->getSubType() . "')";

            }
            else if ($Ids === "-1" && $addonServiceType->getSubType() === 'post_auth' || $addonServiceType->getSubType() === 'pre_auth')
            {
                $typeoffraud =2;
                if($addonServiceType->getSubType() === 'pre_auth')
                {
                    $typeoffraud = 1;
                }
                $SQL = "DELETE FROM CLIENT".sSCHEMA_POSTFIX.".". $addonServiceType->getTableName() ." WHERE clientid=".$this->getClientInfo()->getID()." AND typeoffraud=".$typeoffraud;
            }
            else if ($Ids === "-1")
            {
                $SQL = "DELETE FROM CLIENT".sSCHEMA_POSTFIX.".". $addonServiceType->getTableName() ." WHERE clientid=".$this->getClientInfo()->getID();
            }
            else
            {
                $SQL = 'DELETE FROM CLIENT'.sSCHEMA_POSTFIX.'.'. $addonServiceType->getTableName() .' WHERE ID in ('.$Ids.')';

            }
           $rs = $this->getDBConn()->executeQuery($SQL);

           if($rs === false || $this->getDBConn()->countAffectedRows($rs) < 1)
           {
               $statusCode = MerchantOnboardingException::SQL_EXCEPTION;
               throw new MerchantOnboardingException($statusCode,"Failed to Delete ".$addonServiceType->getType()." Config  {value:".$Ids."}");
           }
       }
    }

    /**
     * @throws MerchantOnboardingException
     */
    public function updateAddonConfig(array &$aAddonConfig)
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
                if ($result === FALSE)
                {
                    throw new MerchantOnboardingException(MerchantOnboardingException::SQL_EXCEPTION,'Failed to Update '.$addonConfig->getServiceType()->getName().' is_rollback property');
                }
            }
            if(empty($addonConfig->getConfiguration()) === false)
            {
                $aServiceConf = $addonConfig->getConfiguration();
                foreach ($aServiceConf as &$serviceConf)
                {
                    $sql = $serviceConf->getUpdateSQL($addonConfig->getServiceType());

                    $result = $this->getDBConn()->executeQuery($sql);

                    if ($result === FALSE || $this->getDBConn()->countAffectedRows($result) < 1)
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
     * @param string $type
     * @param int $id
     * @throws MerchantOnboardingException
     */
    public function deleteAllRouteConfig(string $type,  int $id)
    {
        $sWhereCls = " AND routeconfigid = " . $id;

        $SQL = "DELETE FROM client". sSCHEMA_POSTFIX.".routepm_tbl WHERE true ".$sWhereCls;
        $rs = $this->getDBConn()->executeQuery($SQL);
        if($rs === false)
        {
            throw new MerchantOnboardingException(MerchantOnboardingException::SQL_EXCEPTION,"Failed to delete ".strtolower($type)." PM for IDs {".$id."}");
        }

        $SQL = "DELETE FROM client". sSCHEMA_POSTFIX.".route_property_tbl WHERE true ".$sWhereCls;
        $rs = $this->getDBConn()->executeQuery($SQL);
        if($rs === false)
        {
            throw new MerchantOnboardingException(MerchantOnboardingException::SQL_EXCEPTION,"Failed to delete ".strtolower($type)." Properties for IDs {".$id."}");
        }

        $SQL = "DELETE FROM client". sSCHEMA_POSTFIX.".routefeature_tbl WHERE true ".$sWhereCls;
        $rs = $this->getDBConn()->executeQuery($SQL);
        if($rs === false)
        {
            throw new MerchantOnboardingException(MerchantOnboardingException::SQL_EXCEPTION,"Failed to delete ".strtolower($type)." Features for IDs {".$id."}");
        }

        $SQL = "DELETE FROM client". sSCHEMA_POSTFIX.".routecountry_tbl WHERE true ".$sWhereCls;
        $rs = $this->getDBConn()->executeQuery($SQL);
        if($rs === false)
        {
            throw new MerchantOnboardingException(MerchantOnboardingException::SQL_EXCEPTION,"Failed to delete ".strtolower($type)." Countries for IDs {".$id."}");
        }

        $SQL = "DELETE FROM client". sSCHEMA_POSTFIX.".routecurrency_tbl WHERE true ".$sWhereCls;
        $rs = $this->getDBConn()->executeQuery($SQL);
        if($rs === false)
        {
            throw new MerchantOnboardingException(MerchantOnboardingException::SQL_EXCEPTION,"Failed to delete ".strtolower($type)." Currencies for IDs {".$id."}");
        }

        $SQL = "DELETE FROM client". sSCHEMA_POSTFIX.".routeconfig_tbl WHERE id = ".$id;
        $rs = $this->getDBConn()->executeQuery($SQL);
        if($rs === false || $this->getDBConn()->countAffectedRows($rs) < 1)
        {
            throw new MerchantOnboardingException(MerchantOnboardingException::SQL_EXCEPTION,"Failed to delete ".strtolower($type)." RouteConfig for IDs {".$id."}");
        }
    }

    /**
     * @throws MerchantOnboardingException
     * @throws \SQLQueryException
     */
    public function deletePropertyConfig(string $type, ?string $ids,int $id=-1,?string $pms='', string $features = '', string $countries = '', string $currencies = '' )
    {
        $sWhereCls = " clientid = ".$this->_clientConfig->getID();
        if(empty($pms) === false)
        {
            $sWhereCls = " routeconfigid = ".$id;
            $sTableName = ".routepm_tbl";
            if($type === 'CLIENT')
            {
                $sTableName = ".pm_tbl";
                $sWhereCls = " clientid = ".$this->_clientConfig->getID();
            } else if($type === 'PSP'){
                $sTableName = ".providerpm_tbl";
                $iRouteId = $this->getRouteIDByProvider( $id,false);
                $sWhereCls = " routeid = " . $iRouteId;
            }

            if($pms !== '-1') {
                $sWhereCls .= " AND pmid in (".$pms.") ";
            }

            $SQL = "DELETE FROM client". sSCHEMA_POSTFIX.$sTableName." WHERE ".$sWhereCls;
            $rs = $this->getDBConn()->executeQuery($SQL);

            if($rs === false || $this->getDBConn()->countAffectedRows($rs) < 1)
            {
                throw new MerchantOnboardingException(MerchantOnboardingException::SQL_EXCEPTION,"Failed to delete ".strtolower($type)." PM for IDs {".$pms."}");
            }
        }
        if(empty($ids) === false)
        {
            $sTableName = '';
            if($type === "CLIENT")  {
                $sTableName = 'client_property_tbl';
                $sWhereCls = " clientid = ".$this->_clientConfig->getID();
            }
            else if($type === 'PSP') {
                $sTableName = 'psp_property_tbl';
                $sWhereCls = " clientid = ".$this->_clientConfig->getID();
            }
            else if($type === 'ROUTE')
            {
                $sTableName = 'route_property_tbl';
                $sWhereCls = " routeconfigid = ".$id;
            }

            if($ids !== '-1') {
                $sWhereCls .= " AND propertyid IN ( ".$ids.") ";
            }

            $SQL = "DELETE FROM client". sSCHEMA_POSTFIX.".".$sTableName." WHERE ".$sWhereCls;
            $rs = $this->getDBConn()->executeQuery($SQL);

            if($rs === false || $this->getDBConn()->countAffectedRows($rs) < 1)
            {
                throw new MerchantOnboardingException(MerchantOnboardingException::SQL_EXCEPTION,"Failed to delete ".strtolower($type)." Config Property  for IDs {".$ids."}");
            }

        }
        if(empty($features) === false)
        {
            $sTableName = 'routefeature_tbl';
            $sWhereCls = " routeconfigid = ".$id;

            if($features !== '-1') {
                $sWhereCls .= " AND featureid IN ( ".$features.") ";
            }

            $SQL = "DELETE FROM client". sSCHEMA_POSTFIX.".".$sTableName." WHERE ".$sWhereCls;
            $rs = $this->getDBConn()->executeQuery($SQL);

            if($rs === false || $this->getDBConn()->countAffectedRows($rs) < 1)
            {
                throw new MerchantOnboardingException(MerchantOnboardingException::SQL_EXCEPTION,"Failed to delete ".strtolower($type)." Feature  for IDs {".$features."}");
            }
        }
        if(empty($countries) === false)
        {
            $sTableName = 'routecountry_tbl';
            $sWhereCls = " routeconfigid = ".$id;

            if($countries !== '-1') {
                $sWhereCls .= " AND countryid IN ( ".$countries.") ";
            }

            $SQL = "DELETE FROM client". sSCHEMA_POSTFIX.".".$sTableName." WHERE ".$sWhereCls;
            $rs = $this->getDBConn()->executeQuery($SQL);

            if($rs === false || $this->getDBConn()->countAffectedRows($rs) < 1)
            {
                throw new MerchantOnboardingException(MerchantOnboardingException::SQL_EXCEPTION,"Failed to delete ".strtolower($type)." Country  for IDs {".$countries."}");
            }
        }
        if(empty($currencies) === false)
        {
            $sTableName = 'routecurrency_tbl';
            $sWhereCls = " routeconfigid = ".$id;

            if($currencies !== '-1') {
                $sWhereCls .= " AND currencyid IN ( ".$currencies.") ";
            }

            $SQL = "DELETE FROM client". sSCHEMA_POSTFIX.".".$sTableName." WHERE ".$sWhereCls;
            $rs = $this->getDBConn()->executeQuery($SQL);

            if($rs === false || $this->getDBConn()->countAffectedRows($rs) < 1)
            {
                throw new MerchantOnboardingException(MerchantOnboardingException::SQL_EXCEPTION,"Failed to delete ".strtolower($type)." Currency  for IDs {".$currencies."}");
            }
        }
    }

    /**
     * @param string $type
     * @param array $aPMIds
     * @param int $id
     * @throws MerchantOnboardingException
     */
    public function updatePM(string $type,array $aPMIds,int $id=-1)
    {
        $sTableName = "routepm_tbl";
        $sWhereCls = "routeconfigid=$2";
        if($type === 'CLIENT')
        {
            $id = $this->_clientConfig->getID();
            $sWhereCls = "clientid=$2";
            $sTableName ='pm_tbl';
        }
        else if($type === 'PSP')
        {

            $id = $this->getRouteIDByProvider($id);
            $sWhereCls = "routeid=$2";
            $sTableName ='providerpm_tbl';
        }
        $SQL = "UPDATE client". sSCHEMA_POSTFIX.".".$sTableName." SET enabled={replace} where pmid=$1 and ".$sWhereCls;

        foreach ($aPMIds as $PMId)
        {
            $aParam = array($PMId[0],$id);

            $rs = $this->getDBConn()->executeQuery(str_replace("{replace}",$PMId[1],$SQL), $aParam);
            if($rs === false || $this->getDBConn()->countAffectedRows($rs) < 1)
            {
                $statusCode = MerchantOnboardingException::SQL_EXCEPTION;
                if(strpos($this->getDBConn()->getErrMsg(),'duplicate key value violates unique constraint') !== false)
                {
                    $statusCode = MerchantOnboardingException::SQL_DUPLICATE_EXCEPTION;
                }

                throw new MerchantOnboardingException($statusCode,"Failed to Update Payment Method Id:".$PMId[0]);
            }
        }
    }

    /**
     * @param array $aClientParam
     * @throws MerchantOnboardingException
     */
    public function updateClientdetails(array $aClientParam)
    {
        $SQL = "UPDATE client". sSCHEMA_POSTFIX.".CLIENT_tbl SET ";
        $index = 1;
        foreach ($aClientParam as $key => $value)
        {
            $SQL .= $key."='".$value."',";
            $index++;
        }
        $SQL = substr($SQL,0,-1) ." WHERE id=".$this->_clientConfig->getID();
        $rs = $this->getDBConn()->executeQuery($SQL);
        if($rs === false || $this->getDBConn()->countAffectedRows($rs) < 1)
        {
            throw new MerchantOnboardingException(MerchantOnboardingException::SQL_EXCEPTION,"Failed to update Client Details");
        }
    }

    /**
     * @throws MerchantOnboardingException
     * @throws \SQLQueryException
     */
    public function updatePropertyConfig(string $type, array $aPropertyInfo,int $id=-1,array $aPMIds=array())
    {

        if(empty($aPMIds) === false)
        {
           $this->updatePM($type,$aPMIds,$id);
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
                    $sWhereClase = ' FROM SYSTEM'. sSCHEMA_POSTFIX .'.route_property_tbl SP WHERE cp.propertyid =sp.id and propertyid=$2 and pspid='.$aRS['ID']." AND CP.routeconfigid=".$id;
                }
                else throw new MerchantOnboardingException(MerchantOnboardingException::SQL_EXCEPTION,"Failed to retrieve PSPID for routeconfigid:".$id);
            }
            $SQL = "UPDATE client". sSCHEMA_POSTFIX.".".$sTableName." CP SET value=$1,ENABLED= {replace} ".$sWhereClase;
            foreach ($aPropertyInfo as $propertyInfo)
            {
                $SQL = str_replace("{replace}",General::bool2xml($propertyInfo->isEnabled()),$SQL);
                $aParam = array($propertyInfo->getValue(),$propertyInfo->getId());
                $rs = $this->getDBConn()->executeQuery($SQL, $aParam);
                if($rs === false || $this->getDBConn()->countAffectedRows($rs) < 1)
                {
                    $statusCode = MerchantOnboardingException::SQL_EXCEPTION;
                    if(strpos($this->getDBConn()->getErrMsg(),'duplicate key value violates unique constraint') !== false)
                    {
                        $statusCode = MerchantOnboardingException::SQL_DUPLICATE_EXCEPTION;
                    }

                    throw new MerchantOnboardingException($statusCode,"Failed to update ".strtolower($type)." Config Property  {id:".$propertyInfo->getId()." value:".$propertyInfo->getValue()."}");
                }
            }
        }
    }

    /**
     * @param string $type
     * @param array $aPMIds
     * @param int $id
     * @throws MerchantOnboardingException
     */
    public function savePM(string $type,array $aPMIds=array(),int $id=-1 , $isDeleteOldConfig = false)
    {
        $sColumns = "routeconfigid, pmid";
        $sTableName = "routepm_tbl";
        $sValues  = 'VALUES ($1,$2)';

        if($type === 'CLIENT')
        {
            if($isDeleteOldConfig === true)  $this->deleteAllClientConfig('pm');

            $sColumns = "clientid, pmid";
            $sTableName = "pm_tbl";
            $id = $this->_clientConfig->getID();
        }
        if($type === "PSP")
        {
            $sColumns = "routeid, pmid";
            $sTableName = "providerpm_tbl";
            $iClientId = $this->_clientConfig->getID();
            $iRouteId = $this->getRouteIDByProvider($id);
            $id = $iRouteId;
        }
        $SQL = "INSERT INTO client". sSCHEMA_POSTFIX.".".$sTableName." (".$sColumns.") $sValues";
        foreach ($aPMIds as $PMId)
        {
            $aParam = array($id,$PMId);
            $rs = $this->getDBConn()->executeQuery($SQL, $aParam);
            if($rs === false)
            {
                $statusCode = MerchantOnboardingException::SQL_EXCEPTION;
                if(strpos($this->getDBConn()->getErrMsg(),'duplicate key value violates unique constraint') !== false)
                {
                    $statusCode = MerchantOnboardingException::SQL_DUPLICATE_EXCEPTION;
                }
                throw new MerchantOnboardingException($statusCode,"Failed to Insert Payment Method Id:".$PMId);
            }
        }
    }

    /**
     * @param string $sClientAttr
     * @param string $urlType
     * @throws MerchantOnboardingException
     */
    public function deleteAllClientConfig(string $sClientAttr, string $urlType = '') {

        $sWhereClause  = " WHERE clientid = " . $this->_clientConfig->getID();

        switch (strtolower($sClientAttr)) {
            case 'pm':
                $sTableName = 'pm_tbl';
                break;
            case 'property':
                $sTableName = 'client_property_tbl';
                break;
            case 'urls':
                $sTableName = 'url_tbl';
                break;
        }

        $SQL = 'DELETE FROM CLIENT'.sSCHEMA_POSTFIX.'.'. $sTableName . ' ' . $sWhereClause;
        $rs = $this->getDBConn()->executeQuery($SQL);

        if($rs === false)
        {
            $statusCode = MerchantOnboardingException::SQL_EXCEPTION;
            throw new MerchantOnboardingException($statusCode,"Failed to Delete Client ".$sClientAttr." Config ");
        }
    }

    /**
     * @param string $type
     * @param array $aConfigDetails
     * @param int $id
     * @param string $entity
     * @throws MerchantOnboardingException
     */
    public function updateConfigDetails(string $type,array $aConfigDetails=array(),int $id=-1, $entity = '')
    {

        $sWhereCls = " AND routeconfigid = " . $id;
        switch (strtolower($entity)) {
            case 'feature':
                $sColumns = "featureid";
                $sTableName = "routefeature_tbl";
                break;

            case 'country':
                $sTableName = 'routecountry_tbl';
                $sColumns = 'countryid';
                break;

            case 'currency':
                $sTableName = 'routecurrency_tbl';
                $sColumns = 'currencyid';
                break;

            default:
                // Throw Exception
        }

        $SQL = "UPDATE client". sSCHEMA_POSTFIX.".".$sTableName." SET enabled={replace} where " .  $sColumns . " = $1 ".$sWhereCls;
        foreach ($aConfigDetails as $configDetail) {
            $aParam = array($configDetail[0]);
            $rs = $this->getDBConn()->executeQuery(str_replace("{replace}",$configDetail[1],$SQL), $aParam);
            if ($rs === false) {
                $statusCode = MerchantOnboardingException::SQL_EXCEPTION;
                if (strpos($this->getDBConn()->getErrMsg(), 'duplicate key value violates unique constraint') !== false) {
                    $statusCode = MerchantOnboardingException::SQL_DUPLICATE_EXCEPTION;
                }

                throw new MerchantOnboardingException($statusCode, "Failed to Update $entity Method Id:" . $configDetail[0]);
            }
        }
    }

    /**
     * @throws MerchantOnboardingException
     * @throws \SQLQueryException
     */
    public function saveConfigDetails(string $type,array $aConfigDetails=array(),int $id=-1, $entity = '')
    {
        $iClientId = $this->_clientConfig->getID();
        switch(strtolower($entity)){
            case 'feature':
                $sColumns = "clientid, routeconfigid, featureid";
                $sTableName = "routefeature_tbl";
                $sValues = 'VALUES ($1,$2, $3)';
                $aParam = array($iClientId, $id);
                break;

            case 'country':
                $sTableName = 'routecountry_tbl';
                $sColumns = 'routeconfigid, countryid';
                $sValues = 'VALUES ($1,$2)';
                $aParam = array($id);
                break;

            case 'currency':
                $sTableName = 'routecurrency_tbl';
                $sColumns = 'routeconfigid, currencyid';
                $sValues = 'VALUES ($1,$2)';
                $aParam = array($id);
                break;

            default:
                // Throw Exception
        }


        $SQL = "INSERT INTO client". sSCHEMA_POSTFIX.".".$sTableName." (".$sColumns.") $sValues";
        foreach ($aConfigDetails as $configDetail)
        {
            array_push($aParam, $configDetail);
            $rs = $this->getDBConn()->executeQuery($SQL, $aParam);
            array_pop($aParam);
            if($rs === false)
            {
                $statusCode = MerchantOnboardingException::SQL_EXCEPTION;
                if(strpos($this->getDBConn()->getErrMsg(),'duplicate key value violates unique constraint') !== false)
                {
                    $statusCode = MerchantOnboardingException::SQL_DUPLICATE_EXCEPTION;
                }

                throw new MerchantOnboardingException($statusCode,"Failed to Insert $entity Method Id:".$configDetail);
            }
        }
    }


    /**
     * @throws MerchantOnboardingException
     * @throws \SQLQueryException
     */
    public function deleteConfigDetails(int $routeConfigId, $entity)
    {
        $SQL = "";
        switch(strtolower($entity))
        {
            case 'feature':
                $SQL = "DELETE FROM CLIENT". sSCHEMA_POSTFIX.".routefeature_tbl WHERE routeconfigid=".$routeConfigId;
                break;
            case 'country':
                $SQL = "DELETE FROM CLIENT". sSCHEMA_POSTFIX.".routecountry_tbl WHERE routeconfigid=".$routeConfigId;
                break;

            case 'currency':
                $SQL = "DELETE FROM CLIENT". sSCHEMA_POSTFIX.".routecurrency_tbl WHERE routeconfigid=".$routeConfigId;
                break;

            default:
                // Throw Exception
        }
        $rs = $this->getDBConn()->executeQuery($SQL);
        if($rs === false)
        {
            $statusCode = MerchantOnboardingException::SQL_EXCEPTION;
            if(strpos($this->getDBConn()->getErrMsg(),'duplicate key value violates unique constraint') !== false)
            {
                $statusCode = MerchantOnboardingException::SQL_DUPLICATE_EXCEPTION;
            }

            throw new MerchantOnboardingException($statusCode,"Failed to DELETE  $entity Method ");
        }
    }

    /**
     * @param int $iProviderId
     * @param bool $isCreateRoute
     * @return mixed
     * @throws MerchantOnboardingException
     */
    private function getRouteIDByProvider(int $iProviderId,bool $isCreateRoute = true)
    {
        $iClientId = $this->getClientInfo()->getID();
        $sSQL = "SELECT id FROM CLIENT". sSCHEMA_POSTFIX .".route_tbl WHERE enabled=true AND providerid = $iProviderId AND  clientid = $iClientId";
        $RS = $this->getDBConn()->executeQuery ( $sSQL );
        $iRouteId = -1;
        if($this->getDBConn()->countAffectedRows($RS) > 0)
        {
            $iRouteId = $this->getDBConn()->fetchName($RS)["ID"];
        }
        else if($iRouteId === -1 && $isCreateRoute === true)
        {
            $aParam = array($iClientId, $iProviderId);
            $SQL = "INSERT INTO CLIENT". sSCHEMA_POSTFIX.".route_tbl (clientid, providerid) values ($1, $2)  RETURNING id ";
            $rs = $this->getDBConn()->executeQuery($SQL, $aParam);

            if($rs === false || $this->getDBConn()->countAffectedRows($rs) < 1)
            {
                $statusCode = MerchantOnboardingException::SQL_EXCEPTION;
                if(strpos($this->getDBConn()->getErrMsg(),'duplicate key value violates unique constraint') !== false)
                {
                    $statusCode = MerchantOnboardingException::SQL_DUPLICATE_EXCEPTION;
                }
                throw new MerchantOnboardingException($statusCode,"Failed to generate route for  ".$iProviderId);
            }
            $RS = $this->getDBConn()->fetchName($rs);
            $iRouteId = $RS["ID"];
        }
        return $iRouteId;
    }

    public function getRouteConfigIdByProvider(int $iProviderId) : array
    {
        $iClientId = $this->getClientInfo()->getID();
        $sSQL = "SELECT rc.id FROM CLIENT". sSCHEMA_POSTFIX .".route_tbl r INNER JOIN  CLIENT". sSCHEMA_POSTFIX .".routeconfig_tbl rc
        On rc.routeid=r.id AND rc.enabled=true  WHERE r.enabled=true AND providerid = $iProviderId AND  clientid = $iClientId";
        $aRS = $this->getDBConn()->getAllNames($sSQL);
        $aRouteConfigId = array();
        if(is_array($aRS) && count($aRS)>0)
        {
            foreach ($aRS as $rs)
            {
                array_push($aRouteConfigId,$rs["ID"]);
            }
        }
        return $aRouteConfigId;
    }
    /**
     * @param string $type
     * @param int $id
     * @param string $name
     * @param array $aCredentials
     * @return mixed
     * @throws MerchantOnboardingException
     */
    public function updateCredential(string $type, int $id, string $name, array $aCredentials)
    {

        $sWhereCls = '';
        if($type === 'ROUTE')
        {
            $sTableName = 'routeconfig_tbl';
            $sColumnName = 'name=$1, mid=$2, username=$3, password=$4, capturetype=$5';
            $sWhereCls = " WHERE id=".$id;
            $aParam = array( $name);

        } else if($type === 'PSP')
        {
            $iClientId = $this->_clientConfig->getID();
            $sTableName = 'merchantaccount_tbl';
            $sColumnName = 'name=$1, username=$2, passwd=$3';
            $sWhereCls = " WHERE pspid=".$id." AND clientid=".$iClientId;
            $aParam = array($name);

        } 

        foreach($aCredentials as $credential){
            $aParam[] = (string) $credential;
        }

        $SQL = "UPDATE CLIENT". sSCHEMA_POSTFIX.".".$sTableName." SET ".$sColumnName.$sWhereCls;
        $rs = $this->getDBConn()->executeQuery($SQL, $aParam);

        if($rs === false || $this->getDBConn()->countAffectedRows($rs) < 1)
        {
            $statusCode = MerchantOnboardingException::SQL_EXCEPTION;
            if(strpos($this->getDBConn()->getErrMsg(),'duplicate key value violates unique constraint') !== false)
            {
                $statusCode = MerchantOnboardingException::SQL_DUPLICATE_EXCEPTION;
            }
            throw new MerchantOnboardingException($statusCode,"Failed to save ".strtolower($type)." Credentials  {name:". $name . "}");
        }

        $RS = $this->getDBConn()->fetchName($rs);
    }

    /**
     * @throws MerchantOnboardingException
     * @throws \SQLQueryException
     */
    public function saveCredential(string $type, int $id, string $name, array $aCredentials) : int
    {
        $iRouteId = 0;
        $iClientId = $this->_clientConfig->getID();
        $iProviderId = $id;

        if($type === 'ROUTE')
        {
            $sTableName = 'routeconfig_tbl';
            $sColumnName = 'routeid, name, mid, username, password, capturetype';
            $sValues = 'VALUES ($1,$2,$3,$4,$5,$6)';
            $iRouteId = $this->getRouteIDByProvider($iProviderId);
            $aParam = array($iRouteId, $name);

        } else if($type === 'PSP')
        {
            $iClientId = $this->_clientConfig->getID();
            $sTableName = 'merchantaccount_tbl';
            $sColumnName = 'clientid, pspid, name, username, passwd';
            $sValues = 'VALUES ($1,$2,$3,$4,$5)';
            $aParam = array($iClientId, $id, $name);

        } else {
            // Throw exception
        }

        foreach($aCredentials as $credential){
            $aParam[] = (string) $credential;
        }

        $SQL = "INSERT INTO CLIENT". sSCHEMA_POSTFIX.".".$sTableName." (".$sColumnName.") ".$sValues ." RETURNING id ";
        $rs = $this->getDBConn()->executeQuery($SQL, $aParam);
        if($rs === false || $this->getDBConn()->countAffectedRows($rs) < 1)
        {
            $statusCode = MerchantOnboardingException::SQL_EXCEPTION;
            if(strpos($this->getDBConn()->getErrMsg(),'duplicate key value violates unique constraint') !== false)
            {
                $statusCode = MerchantOnboardingException::SQL_DUPLICATE_EXCEPTION;
            }
            throw new MerchantOnboardingException($statusCode,"Failed to save ".strtolower($type)." Credentials  {name:". $name . "}");
        }

        $RS = $this->getDBConn()->fetchName($rs);
        return $RS["ID"];
    }

    /**
     * @throws MerchantOnboardingException
     * @throws \SQLQueryException
     */
    public function savePropertyConfig(string $type, array $aPropertyInfo,int $id=-1,array $aPMIds=array(), $isDeleteOldConfig = false)
    {

        if(empty($aPMIds) === false)
        {
          $this->savePM($type,$aPMIds,$id);
        }
        if(empty($aPropertyInfo) === false)
        {
          $sTableName = '';
          $sColumnName = 'clientid,propertyid, value';
          $sValues = 'VALUES ($1,$2,$3)';
          if($type === 'CLIENT')
          {
              if($isDeleteOldConfig === true)  { $this->deleteAllClientConfig('property'); }
              $id = $this->_clientConfig->getID(); // Get Client ID
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
              if($rs === false || $this->getDBConn()->countAffectedRows($rs) < 1)
              {
                  $statusCode = MerchantOnboardingException::SQL_EXCEPTION;
                  if(strpos($this->getDBConn()->getErrMsg(),'duplicate key value violates unique constraint') !== false)
                  {
                      $statusCode = MerchantOnboardingException::SQL_DUPLICATE_EXCEPTION;
                  }
                  throw new MerchantOnboardingException($statusCode,"Failed to save ".strtolower($type)." Config Property  {id:".$propertyInfo->getId()." value:".$propertyInfo->getValue()."}");
              }
          }
      }
    }

    /**
     * @param string $type
     * @param int $id
     * @return array
     * @throws MerchantOnboardingException
     */
    public function getPM(string $type,int $id=-1) : array
    {
        $aPM = array();
        $sTableName = 'routepm_tbl';
        $sWhereCls = "and routeconfigid = ".$id;
        if($type === 'CLIENT')
        {
            $sTableName = 'pm_tbl';
            $sWhereCls = "and clientid = ".$this->_clientConfig->getID();
        } else if($type === 'PSP')
        {
            $sTableName = 'providerpm_tbl';
            $iRouteId = $this->getRouteIDByProvider( $id,false);
            $sWhereCls = "and routeid = ".$iRouteId;
        }

        $sSQL = "SELECT pmid FROM CLIENT". sSCHEMA_POSTFIX .".".$sTableName." WHERE enabled=true ".$sWhereCls;
        $aRS = $this->getDBConn()->getAllNames ( $sSQL );
        if (empty($aRS) === false)
        {
            foreach ($aRS as $rs) array_push($aPM,$rs["PMID"]);
        }
        return $aPM;
    }

    /**
     * @param string $type
     * @param int $id
     * @param string $entity
     * @return array
     */
    public function getConfigDetails(string $type, int $id = -1, $entity = '') : array
    {
        $aConfigDetails = [];

        switch (strtolower($entity)){
            case 'feature':
                $sTableName = "routefeature_tbl";
                $sWhereCls = " and routeconfigid = ". $id;
                $sSelectId = "FEATUREID";
                $sSELECTFields = $sSelectId;
                break;

            case 'country':
                $sTableName = "routecountry_tbl";
                $sWhereCls = " and routeconfigid = ". $id;
                $sSelectId = "COUNTRYID";
                $sSELECTFields = $sSelectId;
                break;

            case 'currency':
                $sTableName = "routecurrency_tbl";
                $sWhereCls = " and routeconfigid = ". $id;
                $sSelectId = "CURRENCYID";
                $sSELECTFields = $sSelectId;
                break;

            default:
                // Throw Exception
        }

        $sSQL = "SELECT $sSELECTFields FROM CLIENT". sSCHEMA_POSTFIX .".".$sTableName." WHERE enabled=true ".$sWhereCls;
        $aRS = $this->getDBConn()->getAllNames ( $sSQL );
        if (empty($aRS) === false)
        {
            foreach ($aRS as $rs) array_push($aConfigDetails,$rs[$sSelectId]);
        }
        return $aConfigDetails;
    }

    public function getRoutes(int $pspType=-1,int $pspid=-1):array
    {
        $sSQL = "SELECT providerid as pspid FROM CLIENT". sSCHEMA_POSTFIX .".route_tbl r INNER JOIN
                SYSTEM". sSCHEMA_POSTFIX .".PSP_tbl p on r.providerid = p.id   Where clientid  = ".$this->_clientConfig->getID();
        if($pspType>0)  { $sSQL .= " AND p.system_type = $pspType"; }
        if($pspid>0) { $sSQL .= " AND p.id = $pspid"; }
        $aRS = $this->getDBConn()->getAllNames ( $sSQL );
        return $aRS;
    }

    /**
     * @return array
     */
    public function getAllPSPCredentials(int $pspid=-1,int $pspType=-1): array
    {
        $sSQL = "SELECT pspid as id, m.name, username, passwd as password FROM CLIENT". sSCHEMA_POSTFIX .".merchantaccount_tbl m
                    INNER JOIN SYSTEM". sSCHEMA_POSTFIX .".PSP_tbl p on m.clientid  = ".$this->_clientConfig->getID()." and p.id  = m.pspid ";

        $aWhereCls = [];

        if($pspType>0)  { $aWhereCls[] = " p.system_type = $pspType"; }
        if($pspid>0)  { $aWhereCls[] = " m.pspid = $pspid"; }

        if(empty($aWhereCls) === false)
        {
            $sSQL .= " WHERE " . implode( " AND " , $aWhereCls);
        }

        $aPSPDetails = [];
        $aRS = $this->getDBConn()->getAllNames ( $sSQL );
        if (empty($aRS) === false)
        {
            foreach ($aRS as $rs)
            {
                array_push($aPSPDetails,ProviderConfig::produceFromResultSet($rs));
            }
        }
        return $aPSPDetails;
    }

    /**
     * @param string $type
     * @param int $id
     * @return ProviderConfig|null
     */
    public function getRouteConfiguration(int $id,bool $bAllConfig) : ?ProviderConfig
    {

        $sSQL = "SELECT id,name, capturetype, mid, username, password FROM CLIENT". sSCHEMA_POSTFIX .".routeconfig_tbl WHERE id = ". $id;
        $aPSPDetails = [];
        $rs = $this->getDBConn()->getName( $sSQL );
        if (empty($rs) === false)
        {
           $id = $rs["ID"];
           $provider = ProviderConfig::produceFromResultSet($rs);
           $provider->setPm($this->getPM("ROUTE",$id));
           if($bAllConfig === true)
           {
              $provider->setProperty($this->getPropertyConfig("ROUTE","ALL",$id));
              $provider->setFeatureId($this->getConfigDetails("ROUTE", $id, 'feature'));
              $provider->setCountryIds($this->getConfigDetails("ROUTE", $id, 'country'));
              $provider->setCurrencyIds($this->getConfigDetails("ROUTE", $id, 'currency'));
           }
           return $provider;
        }

        return null;
    }

    /**
     * @param string $type
     * @param string $source
     * @param int $id
     * @param array $aNames
     * @param bool $byCategory
     * @return array
     */
    public function getPropertyConfig(string $type,string $source,int $id=-1,array $aNames= array(),bool $byCategory = true) : array
    {
        $sTableName = "";
        $sWhereArgs = "";
        $sOuterWhereArgs = "";
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
        if(empty($aNames) === false)
        {
            $sOuterWhereArgs .= " WHERE sp.name in(".implode(',',$aNames).')';
        }
        $sJoin = "";
        $sColumn = ",cp.value";
        $sMetaDataJoin = "";
        if($source === 'METADATA')
        {
            $sColumn = "";
            if($id>-1 && $type !== 'CLIENT')  { $sMetaDataJoin = " AND sp.enabled AND sp.pspid=".$id." "; }
        }
        elseif($source === 'ALL')
        {
            $sJoin ="LEFT JOIN CLIENT". sSCHEMA_POSTFIX . ".".$sTableName." cp on cp.propertyid = sp.id ".$sWhereArgs;
            if($type === 'ROUTE')  { $sMetaDataJoin = " AND sp.pspid=(SELECT r.providerid FROM CLIENT". sSCHEMA_POSTFIX .".routeconfig_tbl rt INNER JOIN CLIENT". sSCHEMA_POSTFIX .".route_tbl r ON R.id = rt.routeid WHERE rt.id=".$id.")"; }
            if($type === 'PSP' && $id > -1 ) { $sMetaDataJoin = " AND sp.pspid=".$id; }
        }
        else if($source === 'CLIENT') { $sJoin ="INNER JOIN CLIENT". sSCHEMA_POSTFIX . ".".$sTableName." cp on cp.propertyid = sp.id ".$sWhereArgs; }

        $sSQL = "SELECT sp.id,sp.name,sp.datatype ,sp.ismandatory".$sColumn.",pc.name as category,pc.scope, true as enabled from SYSTEM". sSCHEMA_POSTFIX . ".".$sTableName." sp 
         ".$sJoin." INNER JOIN SYSTEM". sSCHEMA_POSTFIX . ".property_category_tbl pc on sp.category = pc.id ".$sMetaDataJoin.$sOuterWhereArgs."
         ORDER BY sp.name ";

        $aRS = $this->getDBConn()->getAllNames ( $sSQL );
        $aPropertyInfo = array();
        if (empty($aRS) === false)
        {
            foreach ($aRS as $rs)
            {
                $propertyInfo = PropertyInfo::produceFromResultSet($rs);
                if($byCategory === true)
                {
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
                else { array_push($aPropertyInfo, $propertyInfo); }
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
        $aPaymentMetaData['versions'] = $this->get3dsVersion();

        return $aPaymentMetaData;
    }

    /**
     * Get 3Ds Versions
     *
     * @return array
     */
    public function get3dsVersion() : array
    {
        $a3DSVersion =  array(
                        0 => array('ID' => '1', 'NAME' => '1.0'),
                        1 => array('ID' => '2', 'NAME' => '2.0')
                    );

        $a3DSConfigs = BaseInfo::produceFromDataSet($a3DSVersion, 'version');
        
        return $a3DSConfigs;
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
                    $aRouteConfigs = BaseInfo::produceFromDataSet($aRouteConfigData, 'route_configuration');
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
            $aRouteConfigs = BaseInfo::produceFromDataSet($aRouteConfigData, 'route_configuration');
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

        if (is_array($aRS) && count($aRS) > 0)
        {
            $aRouteFeatureInfo = BaseInfo::produceFromDataSet($aRS, 'route_feature');
        }

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
        $aSystemMetaData['pm_types'] = $this->getMetaDataInfo('pm_type', 'paymenttype_tbl');
        $aSystemMetaData['country_details'] = $this->getMetaDataInfo('country_detail', 'country_tbl', true);
        $aSystemMetaData['currency_details'] = $this->getMetaDataInfo('currency_detail', 'currency_tbl', true);
        $aSystemMetaData['capture_types'] = $this->getMetaDataInfo('capture_type', 'capturetype_tbl', true);
        $urlCategory = "(CASE WHEN id in (1,2,3,4,12) THEN 'CLIENT' WHEN id in (14,16,5,6,10,17) THEN 'HPP' WHEN id in (7,8,9,11) THEN 'MERCHANT' WHEN id in (15) THEN 'SDK' ELSE '' END) as url_category";
        $aSystemMetaData['client_urls'] = $this->getMetaDataInfo('client_url', 'urltype_tbl', true,array($urlCategory),array("id"=>"type_id"));
        $aSystemMetaData['payment_processors'] = $this->getMetaDataInfo('payment_processor', 'processortype_tbl');

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
    private function getMetaDataInfo(string $rootNode, string $sTableName, bool $bCheckEnabled = false, array $aAddtionalFields = [],$nodeAlias = []): array
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

        if (is_array($aRS) && count($aRS) > 0)
        {
            $aMetaServiceConfig = BaseInfo::produceFromDataSet($aRS, $rootNode,$nodeAlias);
        }

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
        WHERE st.enabled=true and sst.enabled=true
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
                    // To rename the nodes in response pass additional parameter(3rd) for the node as key and value as exeptected string in response
                    // $aRouteConfigs = BaseInfo::produceFromDataSet($aRouteConfigData, 'route_configuration', array('name' => 'route_name'));
                    $aServiceSubTypes = BaseInfo::produceFromDataSet($aSubtypes, 'addon_subtype');
                    $Service->additionalProp['addon_subtypes'] = $aServiceSubTypes;
                }

                $aSubtypes = [];
                $aTypes = [];

                $iServiceId = $rs["ID"];

                $aTypes[] =  array('ID' => $rs["ID"], 'NAME' => $rs['NAME']);
                $Service = BaseInfo::produceFromDataSet(
                    $aTypes,
                    'addon_type'
                )[0];

                array_push($aSubtypes, array('ID' => $rs["STID"], 'NAME' => $rs['STNAME']));

                array_push($aServices, $Service);
            }
        }
        if (count($aSubtypes)) {
            $aServiceSubTypes = BaseInfo::produceFromDataSet($aSubtypes, 'addon_subtype');
            $Service->additionalProp['addon_subtypes'] = $aServiceSubTypes;
        }

        return $aServices;
    }


    /**
     * @param ClientServiceStatus $clService
     * @throws MerchantOnboardingException
     */
    public function updateAddonServiceStatus(ClientServiceStatus  $clService)
    {
        $SQL = "INSERT INTO CLIENT".sSCHEMA_POSTFIX.".services_tbl (clientid, dcc_enabled, mcp_enabled, pcc_enabled, fraud_enabled, tokenization_enabled, splitpayment_enabled, callback_enabled, void_enabled)
         values(".$this->_clientConfig->getID().",".General::bool2xml($clService->isDcc()).",".General::bool2xml($clService->isMcp()).",".General::bool2xml($clService->isPcc()).",".General::bool2xml($clService->isFraud()).",".General::bool2xml($clService->isTokenization()).",".General::bool2xml($clService->isSplitPayment())."
         ,".General::bool2xml($clService->isCallback()).",".General::bool2xml($clService->isVoid()).") ON CONFLICT(clientid) DO UPDATE SET dcc_enabled=EXCLUDED.dcc_enabled,mcp_enabled=EXCLUDED.mcp_enabled,pcc_enabled=EXCLUDED.pcc_enabled,fraud_enabled=EXCLUDED.fraud_enabled,tokenization_enabled=EXCLUDED.tokenization_enabled
         ,splitpayment_enabled=EXCLUDED.splitpayment_enabled,callback_enabled=EXCLUDED.callback_enabled,void_enabled=EXCLUDED.void_enabled";
        $rs = $this->getDBConn()->executeQuery($SQL);
        if($rs === false || $this->getDBConn()->countAffectedRows($rs) < 1)
        {
                throw new MerchantOnboardingException(MerchantOnboardingException::SQL_EXCEPTION,"Failed To update Addon Service status");
        }
    }

    /**
     * @param array $aClAccountConfig
     * @throws MerchantOnboardingException
     */
    public function updateAccountConfig(array $aClAccountConfig)
    {
        $SQL = "UPDATE CLIENT".sSCHEMA_POSTFIX.".Account_tbl set name=$1,mobile=$2,markup=$3 WHERE id=$4";
        foreach ($aClAccountConfig as $clAccountConfig)
        {
            $param = array($clAccountConfig->getName(),$clAccountConfig->getMobile(),$clAccountConfig->getMarkupLanguage(),$clAccountConfig->getID());

            $rs = $this->getDBConn()->executeQuery($SQL,$param);
            if($rs === false || $this->getDBConn()->countAffectedRows($rs) < 1)
            {
               throw new MerchantOnboardingException(MerchantOnboardingException::SQL_EXCEPTION,"Failed to save account {ID=".$clAccountConfig->getID().",Name=".$clAccountConfig->getName().",Mobile=".$clAccountConfig->getMobile().",MarkUp=".$clAccountConfig->getMarkupLanguage()."}");
            }
        }
    }

    /**
     * @param array $urls
     * @throws MerchantOnboardingException
     */
    public function saveClientURL(array $urls, $isDeleteOldConfig = false)
    {
        if($isDeleteOldConfig === true) $this->deleteAllClientConfig('urls');
        $urlTableTypeid = array(ClientConfig::iCUSTOMER_IMPORT_URL,ClientConfig::iAUTHENTICATION_URL,ClientConfig::iNOTIFICATION_URL,ClientConfig::iMESB_URL,ClientConfig::iPARSE_3DSECURE_CHALLENGE_URL,ClientConfig::iMERCHANT_APP_RETURN_URL,ClientConfig::iBASE_IMAGE_URL,ClientConfig::iTHREED_REDIRECT_URL,ClientConfig::iBASE_ASSET_URL);
        foreach ($urls as $url)
        {
            $column = "";
            switch ($url->getTypeID())
            {
                case ClientConfig::iLOGO_URL:
                    $column = "LOGOURL = $2";
                    break;
                case ClientConfig::iCSS_URL:
                    $column = "CSSURL = $2";
                    break;
                case ClientConfig::iACCEPT_URL:
                    $column = "ACCEPTURL = $2";
                    break;
                case ClientConfig::iCANCEL_URL:
                    $column = "CANCELURL = $2";
                    break;
                case ClientConfig::iDECLINE_URL:
                    $column = "DECLINEURL = $2";
                    break;
                case ClientConfig::iCALLBACK_URL:
                    $column = "CALLBACKURL = $2";
                    break;
                case ClientConfig::iICON_URL:
                    $column = "ICONURL = $2";
                    break;
                default:
                {
                    $SQL = "INSERT INTO client".sSCHEMA_POSTFIX.".url_tbl (urltypeid,clientid,url) values ($1,$2,$3)";
                    $param = array($url->getTypeID(),$this->_clientConfig->getID(),$url->getURL());
                }
            }
            if(empty($column) === false)
            {
                $SQL = "UPDATE client".sSCHEMA_POSTFIX.".client_tbl SET $column WHERE id=$1";
                $param = array($this->_clientConfig->getID(),$url->getURL());
            }
            $rs = $this->getDBConn()->executeQuery($SQL,$param);
            if($rs === false || $this->getDBConn()->countAffectedRows($rs) < 1)
            {
                $statusCode = MerchantOnboardingException::SQL_EXCEPTION;
                if(strpos($this->getDBConn()->getErrMsg(),'duplicate key value violates unique constraint') !== false)
                {
                    $statusCode = MerchantOnboardingException::SQL_DUPLICATE_EXCEPTION;
                }
                throw new MerchantOnboardingException($statusCode,"Failed to save url {typeid:".$url->getTypeID()."}");
            }
        }
    }

    /**
     * @param array $urls
     * @throws MerchantOnboardingException
     */
    public function updateVelocityURL(array $urls)
    {
        foreach ($urls as $url)
        {
            $SQL = "UPDATE client".sSCHEMA_POSTFIX.".url_tbl set URL = $1 WHERE clientid=$2 and urltypeid=$3";
            $param = array($url->getURL(),$this->_clientConfig->getID(),$url->getTypeID());

            $rs = $this->getDBConn()->executeQuery($SQL,$param);
            if($rs === false || $this->getDBConn()->countAffectedRows($rs) < 1)
            {
                throw new MerchantOnboardingException(MerchantOnboardingException::SQL_EXCEPTION,"Failed to save url {typeid:".$url->getTypeID()."}");
            }
        }
    }

    public function saveProviders(array $aProvider)
    {
        foreach ($aProvider as $provider)
        {
            $aUpdateColumns =array();
            $aValues = array();
            $aColumns = array();
            if(empty($provider->getName()) === false)
            {
                array_push($aUpdateColumns,"name='".$provider->getName()."'");
                array_push($aValues,"'".$provider->getName()."'");
                array_push($aColumns,"name");
            }
            if(empty($provider->getPassword()) === false)
            {
                array_push($aUpdateColumns,"passwd='".$provider->getPassword()."'");
                array_push($aValues,"'".$provider->getPassword()."'");
                array_push($aColumns,"passwd");
            }
            if(empty($provider->getUserName()) === false)
            {
                array_push($aUpdateColumns,"username='".$provider->getUserName()."'");
                array_push($aValues,"'".$provider->getUserName()."'");
                array_push($aColumns,"username");
            }
            $updateColumns = implode(" , ",$aUpdateColumns);
            $SQL = "UPDATE client".sSCHEMA_POSTFIX.".merchantaccount_tbl  SET ".$updateColumns." WHERE clientid = ".$this->getClientInfo()->getID()." AND pspid=".$provider->getId().";";
            $rs = $this->getDBConn()->executeQuery($SQL);
            if($rs === false || $this->getDBConn()->countAffectedRows($rs) < 1)
            {
                array_push($aColumns,"pspid","clientid");
                array_push($aValues,$provider->getId(),$this->getClientInfo()->getID());

                $updateColumns = implode(" , ",$aColumns);
                $values = implode(" , ",$aValues);

                $SQL ="insert into client".sSCHEMA_POSTFIX.".merchantaccount_tbl (".$updateColumns.") VALUES (".$values.");";
                $rs = $this->getDBConn()->executeQuery($SQL);
                if($rs === false || $this->getDBConn()->countAffectedRows($rs) < 1)
                {
                    $statusCode = MerchantOnboardingException::SQL_EXCEPTION;
                    if(strpos($this->getDBConn()->getErrMsg(),'duplicate key value violates unique constraint') !== false)
                    {
                        $statusCode = MerchantOnboardingException::SQL_DUPLICATE_EXCEPTION;
                    }
                    throw new MerchantOnboardingException($statusCode,"Failed to save Provider Credentials  {id:". $provider->getId() . "}");
                }

            }
        }

    }

    public function updateRouteConfig(ProviderConfig $provider)
    {
       $aUpdateColumns =array();
       $aValues = array();
       $aColumns = array();
       if(empty($provider->getName()) === false)
       {
           array_push($aUpdateColumns,"name='".$provider->getName()."'");
           array_push($aValues,"'".$provider->getName()."'");
           array_push($aColumns,"name");
       }
       if(empty($provider->getMid()) === false)
       {
           array_push($aUpdateColumns,"mid='".$provider->getMid()."'");
           array_push($aValues,"'".$provider->getMid()."'");
           array_push($aColumns,"mid");
       }
       if(empty($provider->getPassword()) === false)
       {
           array_push(          $aUpdateColumns,"password='".$provider->getPassword()."'");
           array_push($aValues,"'".$provider->getPassword()."'");
           array_push($aColumns,"password");
       }
       if(empty($provider->getUserName()) === false)
       {
           array_push($aUpdateColumns,"username='".$provider->getUserName()."'");
           array_push($aValues,"'".$provider->getUserName()."'");
           array_push($aColumns,"username");
       }
        if($provider->getCaptureType() !== -1)
        {
            array_push($aUpdateColumns,"capturetype=".$provider->getCaptureType());
            array_push($aValues,$provider->getCaptureType());
            array_push($aColumns,"capturetype");
        }
       $rs = null;
       if(count($aColumns) > 0 || count($aUpdateColumns) > 0)
       {
           if($provider->getId() === -1)
           {
               $routeId = $this->getRouteIDByProvider($provider->getPspId(),true);

               array_push($aColumns,"routeid");
               array_push($aValues,$routeId);
               $updateColumns = implode(" , ",$aColumns);
               $values = implode(" , ",$aValues);

               $SQL ="insert into client".sSCHEMA_POSTFIX.".routeconfig_tbl (".$updateColumns.") VALUES (".$values.") RETURNING id;";
           }
           else
           {
               $updateColumns = implode(" , ",$aUpdateColumns);
               $SQL = "UPDATE client".sSCHEMA_POSTFIX.".routeconfig_tbl  SET ".$updateColumns." WHERE id = ".$provider->getId().";";
           }
           $rs = $this->getDBConn()->executeQuery($SQL);
       }

       if($rs!= null && ($rs === false || $this->getDBConn()->countAffectedRows($rs) < 1))
       {
         $statusCode = MerchantOnboardingException::SQL_EXCEPTION;
         if(strpos($this->getDBConn()->getErrMsg(),'duplicate key value violates unique constraint') !== false)
         {
             $statusCode = MerchantOnboardingException::SQL_DUPLICATE_EXCEPTION;
         }
         throw new MerchantOnboardingException($statusCode,"Failed to save Provider Credentials  {id:". $provider->getId() . "}");
       }
       else
       {
           if($provider->getId() === -1)
           {
               $provider->setId((int)$this->getDBConn()->fetchName($rs)["ID"]);
           }
           if(empty($provider->getFeatureId()) === false)
           {
               $this->deleteConfigDetails($provider->getId(),"FEATURE");
               $this->saveConfigDetails("", $provider->getFeatureId(), $provider->getId(), 'FEATURE');
           }
           if(empty($provider->getCountryIds()) === false)
           {
               $this->deleteConfigDetails($provider->getId(),"COUNTRY");
               $this->saveConfigDetails("", $provider->getFeatureId(), $provider->getId(), 'COUNTRY');
           }
           if(empty($provider->getCurrencyIds()) === false)
           {
               $this->deleteConfigDetails($provider->getId(),"CURRENCY");
               $this->saveConfigDetails("", $provider->getCurrencyIds(), $provider->getId(), 'CURRENCY');
           }
           if(empty($provider->getProperty()) === false)
           {
               $this->deleteAllProperty("ROUTE",$provider->getId());
               $this->savePropertyConfig("ROUTE", $provider->getProperty(),$provider->getId());
           }
           if(empty($provider->getPm()) === false)
           {
               $this->deleteAllPM("ROUTE",$provider->getId());
               $this->savePM("ROUTE", $provider->getPm(),$provider->getId());
           }
       }
    }

    public function updatePSPConfig(ProviderConfig $provider)
    {
        $routeId = $this->getRouteIDByProvider($provider->getPspId());
        if(empty($provider->getProperty()) === false)
        {
            $this->deleteAllProperty("PSP",$provider->getPspId());
            $this->savePropertyConfig("PSP", $provider->getProperty(),$provider->getPspId());
        }
        if(empty($provider->getPm()) === false)
        {
            $this->deleteAllPM("PSP",$routeId);
            $this->savePM("PSP", $provider->getPm(),$provider->getPspId());
        }
    }

    private function deleteAllPM($type,$id)
    {
        $sTableName = "routepm_tbl";
        $sWhereCls  = "routeconfigid = ".$id;

        if($type === 'CLIENT')
        {
            $sWhereCls  = "clientid = ".$id;
            $sTableName = "pm_tbl";
        }
        if($type === "PSP")
        {
            $sTableName = "providerpm_tbl";
            $sWhereCls  = "routeid = ".$id;
        }
        $SQL = "DELETE FROM CLIENT". sSCHEMA_POSTFIX.".".$sTableName." WHERE ".$sWhereCls;
        $rs = $this->getDBConn()->executeQuery($SQL);
        if($rs === false)
        {
            $statusCode = MerchantOnboardingException::SQL_EXCEPTION;
            if(strpos($this->getDBConn()->getErrMsg(),'duplicate key value violates unique constraint') !== false)
            {
                $statusCode = MerchantOnboardingException::SQL_DUPLICATE_EXCEPTION;
            }
            throw new MerchantOnboardingException($statusCode," Failed to delete PM for ".$type." ");
        }
    }

    private function deleteAllProperty($type,$id)
    {
        $sTableName = '';
        if($type === "CLIENT")  {
            $sTableName = 'client_property_tbl';
            $sWhereCls = " clientid = ".$this->_clientConfig->getID();
        }
        else if($type === 'PSP') {
            $sTableName = 'psp_property_tbl';
            $sWhereCls = " clientid = ".$this->_clientConfig->getID();
        }
        else if($type === 'ROUTE')
        {
            $sTableName = 'route_property_tbl';
            $sWhereCls = " routeconfigid = ".$id;
        }

        $SQL = "DELETE FROM client". sSCHEMA_POSTFIX.".".$sTableName." WHERE ".$sWhereCls;
        $rs = $this->getDBConn()->executeQuery($SQL);

        if($rs === false)
        {
            throw new MerchantOnboardingException(MerchantOnboardingException::SQL_EXCEPTION,"Failed to delete ".strtolower($type)." Config Property  for ID {".$id."}");
        }
    }
}
