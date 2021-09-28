<?php
namespace api\classes\merchantservices\Repositories;


use AddonServiceTypeIndex;
use api\classes\merchantservices\AddonServiceType;
use api\classes\merchantservices\DCCConfig;
use api\classes\merchantservices\FraudConfig;
use api\classes\merchantservices\MCPConfig;
use api\classes\merchantservices\MPIConfig;
use api\classes\merchantservices\PCCConfig;
use api\classes\merchantservices\ServiceConfig;

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
            if($addonServiceType->getID() === AddonServiceTypeIndex::eDCC) { return new DCCConfig($aServiceConfig);}
            if($addonServiceType->getID() === AddonServiceTypeIndex::eFraud)
            {
                $SQL = 'SELECT "isRollback" FROM client.fraud_property_tbl where enabled=true and clientid='.$this->_clientConfig->getID();
                $aRS = $this->getDBConn()->getName ( sprintf($SQL,$sColumns,$sTableName) );

                return new FraudConfig($aServiceConfig,$aRS);
            }
            if($addonServiceType->getID() === AddonServiceTypeIndex::ePCC) { return new PCCConfig($aServiceConfig);}
            if($addonServiceType->getID() === AddonServiceTypeIndex::eMPI) { return new MPIConfig($aServiceConfig);}
            else   return new MCPConfig($aServiceConfig);

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

    public function saveAddonConfig(array $aAddonConfig) :array
    {
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
                        $serviceConf->setOperation(\OperationStatus::eFailed);
                    }
                    else $serviceConf->setOperation(\OperationStatus::eSuccessful);
                }
            }
        }
     return $aAddonConfig;
    }




}