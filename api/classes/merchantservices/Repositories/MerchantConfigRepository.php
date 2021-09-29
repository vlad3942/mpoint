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
use api\classes\merchantservices\OperationStatus;
use api\classes\merchantservices\ResponseTemplate;
use HTTP;

class MerchantConfigRepository
{
    private \RDB $_conn;
    private \ClientConfig $_clientConfig;

    public function __construct(\RDB  $conn,int $iClientId)
    {
        $this->_conn = $conn;
        $this->_clientConfig = \ClientConfig::produceConfig($conn,$iClientId);
    }

    private function getDBConn():\RDB { return $this->_conn;}


    private function getAddonConfig(AddonServiceType $addonServiceType)
    {
        $SQL = "SELECT %s FROM CLIENT". sSCHEMA_POSTFIX .".%s WHERE enabled = true and clientid=".$this->_clientConfig->getID();

           $sTableName = $addonServiceType->getTableName();
            $sColumns = "id,pmid,countryid,currencyid,created,modified,enabled";
            if($addonServiceType->getID() ===AddonServiceTypeIndex::eFraud) $sColumns .= ',providerid,"typeOfFraud" ';
            if($addonServiceType->getID() ===AddonServiceTypeIndex::ePCC) $sColumns = 'id,pmid,sale_currency_id,is_presentment,settlement_currency_id,created,modified,enabled ';
            if($addonServiceType->getID() ===AddonServiceTypeIndex::eMPI) $sColumns = 'id, clientid, pmid, providerid,version,created,modified,enabled ';
            $aRS = $this->getDBConn()->getAllNames ( sprintf($SQL,$sColumns,$sTableName) );
            $aServiceConfig = array();
            if (empty($aRS) === false)
            {
                foreach ($aRS as $rs)
                {
                    array_push($aServiceConfig, ServiceConfig::produceFromResultSet($rs));
                }
            }
            if($addonServiceType->getID() === AddonServiceTypeIndex::eDCC) { return new DCCConfig($aServiceConfig,array());}
            if($addonServiceType->getID() === AddonServiceTypeIndex::eFraud)
            {
                $SQL = 'SELECT "isRollback" FROM client.fraud_property_tbl where enabled=true and clientid='.$this->_clientConfig->getID();
                $aRS = $this->getDBConn()->getName ( sprintf($SQL,$sColumns,$sTableName) );

                return new FraudConfig($aServiceConfig,$aRS);
            }
            if($addonServiceType->getID() === AddonServiceTypeIndex::ePCC) { return new PCCConfig($aServiceConfig,array());}
            if($addonServiceType->getID() === AddonServiceTypeIndex::eMPI) { return new MPIConfig($aServiceConfig,array());}
            else   return new MCPConfig($aServiceConfig,array());

    }
    public function getAllAddonConfig() : array
    {
       $aAddonConfig = array();
       array_push($aAddonConfig,$this->getAddonConfig(AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::eDCC)));
       array_push($aAddonConfig,$this->getAddonConfig(AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::eMCP)));
       array_push($aAddonConfig,$this->getAddonConfig(AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::ePCC)));
       array_push($aAddonConfig,$this->getAddonConfig(AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::eFraud)));
       array_push($aAddonConfig,$this->getAddonConfig(AddonServiceType::produceAddonServiceTypebyId(AddonServiceTypeIndex::eMPI)));
       return  $aAddonConfig;
    }

    public function saveAddonConfig(array $aAddonConfig)
    {
        $response = new ResponseTemplate();
        $response->setHttpStatusCode(ResponseTemplate::CREATED);

        foreach ($aAddonConfig as $addonConfig)
        {
            if(empty($addonConfig->getConfiguration()) === false)
            {
                $sql = ServiceConfig::getInsertSQL($addonConfig->getServiceType());
                $aServiceConf = $addonConfig->getConfiguration();
                foreach ($aServiceConf as $serviceConf)
                {
                    $aParams = $serviceConf->getParam($addonConfig->getServiceType(),$this->_clientConfig->getID());
                    $result = $this->getDBConn()->executeQuery($sql, $aParams);

                    if ($result == FALSE)
                    {
                        if(strpos($this->getDBConn()->getErrMsg(),'duplicate key value violates unique constraint') !== false) $serviceConf->setOperationStatus(OperationStatus::eDuplicate);
                       else $serviceConf->setOperationStatus(OperationStatus::eFailed);
                        $response->setHttpStatusCode(ResponseTemplate::MULTI_STATUS);
                    }
                    else $serviceConf->setOperationStatus(OperationStatus::eSuccessful);
                }
            }
        }
        $response->setResponse($aAddonConfig);
     return $response;
    }

    public function updateAddonConfig(array $aAddonConfig)
    {
        $response = new ResponseTemplate();

        $response->setHttpStatusCode(ResponseTemplate::CREATED);

        foreach ($aAddonConfig as $addonConfig)
        {
            if(empty($addonConfig->getConfiguration()) === false)
            {
                $aServiceConf = $addonConfig->getConfiguration();
                foreach ($aServiceConf as $serviceConf)
                {
                    $sql = $serviceConf->getUpdateSQL($addonConfig->getServiceType());

                    $result = $this->getDBConn()->executeQuery($sql);

                    if ($result == FALSE)
                    {    if(strpos($this->getDBConn()->getErrMsg(),'duplicate key value violates unique constraint') !== false) $serviceConf->setOperationStatus(OperationStatus::eDuplicate);
                         else $serviceConf->setOperationStatus(OperationStatus::eFailed);
                        $response->setHttpStatusCode(ResponseTemplate::MULTI_STATUS);
                    }
                    else $serviceConf->setOperationStatus(OperationStatus::eSuccessful);
                }
            }
        }
        $response->setResponse($aAddonConfig);
        return $response;
    }




}