<?php
namespace api\classes\merchantservices\Repositories;


use AddonServiceType;
use ServiceConfig;

class MerchantConfigRepository
{
    private \RDB $_conn;
    private \ClientConfig $_clientConfig;

    public function __construct(\RDB  $conn,\ClientConfig $clientConfig)
    {
        $this->_conn = $conn;
        $this->_clientConfig = $clientConfig;
    }

    private function getDBConn():\RDB { return $this->_conn;}


    private function getAddonConfig(int $addonServiceType)
    {
        $SQL = "SELECT %s FROM CLIENT". sSCHEMA_POSTFIX ." %s WHERE enabled = true and clientid=".$this->_clientConfig->getID();
        switch ($addonServiceType)
        {
            case AddonServiceType::eMCP || AddonServiceType::eDCC:
                $sTableName = "DCC_config_tbl";
                if($addonServiceType == AddonServiceType::eMCP) { $sTableName = "MCP_config_tbl";}

                $aRS = $this->getDBConn()->getAllNames ( stringf($SQL,"id,pmid,countryid,currencyid,created,modified,enabled",$sTableName) );
                $aServiceConfig = array();
                if (empty($aRS) === false)
                {
                    foreach ($aRS as $rs)
                    {
                        $serviceCon = new ServiceConfig();
                        $serviceCon->setId($rs["ID"])
                            ->setCountryId($rs["COUNTRYID"])
                            ->setCurrencyId($rs["CURRENCYID"])
                            ->setPaymentMethodId($rs["PMID"])
                            ->setCreated($rs['CREATED'])
                            ->setModified($rs['MODIFIED'])
                            ->setEnabled($rs['ENABLED']);
                        array_push($aServiceConfig, $serviceCon);

                    }
                }
                if($addonServiceType == AddonServiceType::eMCP) { return new \DCCConfig($aServiceConfig);}
                else
                return new \MCPConfig($aServiceConfig);

            case AddonServiceType::ePCC:
                $aRS = $this->getDBConn()->getAllNames ( stringf($SQL,"id,pmid,sale_currency_id,is_presentment,settlement_currency_id,created,modified,enabled","PCC_config_tbl") );
                $aServiceConfig = array();
                if (empty($aRS) === false)
                {
                    foreach ($aRS as $rs)
                    {
                        $serviceCon = new \ServiceConfig();
                        $serviceCon->setId($rs["ID"])
                            ->setCurrencyId($rs["SALE_CURRENCY_ID"])
                            ->setPaymentMethodId($rs["PMID"])
                            ->setPresentment($rs["IS_PRESENTMENT"])
                            ->setSettlementCurrencyId($rs["SETTLEMENT_CURRENCY_ID"])
                            ->setCreated($rs['CREATED'])
                            ->setModified($rs['MODIFIED'])
                            ->setEnabled($rs['ENABLED']);
                        array_push($aServiceConfig, $serviceCon);

                    }
                }
                return new \PCCConfig($aServiceConfig);
            case AddonServiceType::eSplitPayment:
                return null;
            default:
                return "";
        }
    }
    public function getAllAddonConfig() : array
    {
       $aAddonConfig = array();
       array_push($aAddonConfig,$this->getAddonConfig(\AddonServiceType::eDCC));
       array_push($aAddonConfig,$this->getAddonConfig(\AddonServiceType::eMCP));
       array_push($aAddonConfig,$this->getAddonConfig(\AddonServiceType::ePCC));
       return  $aAddonConfig;
    }

    public function saveAddonConfig(array $aAddonConfig) :array
    {
        foreach ($aAddonConfig as $addonConfig)
        {
            if(empty($addonConfig->getConfiguration()) === false)
            {
                $sql = ServiceConfig::getInsertSQL(AddonServiceType::eDCC);
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