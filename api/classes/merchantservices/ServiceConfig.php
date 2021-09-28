<?php
namespace api\classes\merchantservices;

use AddonServiceTypeIndex;
use General;

class ServiceConfig extends SQLOperation
{
    private int $_id = -1;
    private int $_iPMId = -1;
    private int $_iCountryId = -1;
    private int $_iCurrencyId = -1;
    private int $_iSettlementCurrencyId = -1;
    private bool $_bPresentment;
    private string $_dCreated;
    private string $_dModified;
    private bool $_bEnabled;
    private int $_iProviderId = -1;
    private int $_iType = -1;
    private string $_sVersion = '' ;


    /**
     * Default Constructor
     */
    public function __construct() {}


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->_id;
    }

    /**
     * @return int
     */
    public function getPaymentMethodId(): int
    {
        return $this->_iPMId;
    }

    /**
     * @return int
     */
    public function getCountryId(): int
    {
        return $this->_iCountryId;
    }

    /**
     * @return int
     */
    public function getCurrencyId(): int
    {
        return $this->_iCurrencyId;
    }

    /**
     * @return int
     */
    public function getSettlementCurrencyId(): int
    {
        return $this->_iSettlementCurrencyId;
    }

    /**
     * @return bool
     */
    public function isPresentment(): bool
    {
        return $this->_bPresentment;
    }

    /**
     * @return string
     */
    public function getCreated(): string
    {
        return $this->_dCreated;
    }

    /**
     * @return string
     */
    public function getModified(): string
    {
        return $this->_dModified;
    }

    /**
     * @return bool
     */
    public function getEnabled(): bool
    {
        return $this->_bEnabled;
    }

    /**
     * @return int
     */
    public function getProviderId(): int
    {
        return $this->_iProviderId;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->_iType;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->_sVersion;
    }

    /**
     * @param int $id
     * @return ServiceConfig
     */
    public function setId(int $id): ServiceConfig
    {
        $this->_id = $id;
        return $this;
    }

    /**
     * @param int $iCardId
     * @return ServiceConfig
     */
    public function setPaymentMethodId(int $iPMId): ServiceConfig
    {
        $this->_iPMId = $iPMId;
        return $this;
    }

    /**
     * @param int $iCountryId
     * @return ServiceConfig
     */
    public function setCountryId(int $iCountryId): ServiceConfig
    {
        $this->_iCountryId = $iCountryId;
        return $this;
    }

    /**
     * @param int $iCurrencyId
     * @return ServiceConfig
     */
    public function setCurrencyId(int $iCurrencyId): ServiceConfig
    {
        $this->_iCurrencyId = $iCurrencyId;
        return $this;
    }

    /**
     * @param int $iSettlementCurrencyId
     * @return ServiceConfig
     */
    public function setSettlementCurrencyId(int $iSettlementCurrencyId): ServiceConfig
    {
        $this->_iSettlementCurrencyId = $iSettlementCurrencyId;
        return $this;
    }

    /**
     * @param bool $bPresentment
     * @return ServiceConfig
     */
    public function setPresentment(bool $bPresentment): ServiceConfig
    {
        $this->_bPresentment = $bPresentment;
        return $this;
    }

    /**
     * @param string $dCreated
     * @return ServiceConfig
     */
    public function setCreated(string $dCreated): ServiceConfig
    {
        $this->_dCreated = $dCreated;
        return $this;
    }

    /**
     * @param string $dModified
     * @return ServiceConfig
     */
    public function setModified(string $dModified): ServiceConfig
    {
        $this->_dModified = $dModified;
        return $this;
    }

    /**
     * @param string $dEnabled
     * @return ServiceConfig
     */
    public function setEnabled(string $dEnabled): ServiceConfig
    {
        $this->_bEnabled = $dEnabled;
        return $this;
    }

    /**
     * @param int $iProviderId
     * @return ServiceConfig
     */
    public function setProviderId(int $iProviderId): ServiceConfig
    {
        $this->_iProviderId = $iProviderId;
        return $this;
    }

    /**
     * @param int $iType
     * @return ServiceConfig
     */
    public function setType(int $iType): ServiceConfig
    {
        $this->_iType = $iType;
        return $this;
    }

    /**
     * @param string $sVersion
     * @return ServiceConfig
     */
    public function setVersion(string $sVersion): ServiceConfig
    {
        $this->_sVersion = $sVersion;
        return $this;
    }

    public static function getInsertSQL(AddonServiceType $addonServiceType):string
    {
        $sql = "INSERT INTO CLIENT". sSCHEMA_POSTFIX .".%s (%s) values (%s)";
        if ($addonServiceType->getID() === AddonServiceTypeIndex::eMCP || $addonServiceType->getID() === AddonServiceTypeIndex::eDCC)
        {
            $sTableName = $addonServiceType->getTableName();
            return sprintf($sql,$sTableName,"clientid,pmid,countryid,currencyid","$1,$2,$3,$4");
        }
        else if ($addonServiceType->getID() === AddonServiceTypeIndex::ePCC)
        {
            return sprintf($sql,"MCP_config_tbl","clientid,pmid,sale_currency_id,is_presentment,settlement_currency_id","$1,$2,$3,$4,$5");
        }
    }

    public function getParam(AddonServiceType $addonServiceType,int $iClientId):array
    {
        if($addonServiceType->getID() === AddonServiceTypeIndex::eMCP || $addonServiceType->getID() === AddonServiceTypeIndex::eDCC)
        {
            return array($iClientId,$this->getPaymentMethodId(),$this->getCountryId(),$this->getCurrencyId());

        }else  if($addonServiceType->getID() === AddonServiceTypeIndex::ePCC)
        {
            return array($iClientId,$this->getPaymentMethodId(),$this->getCurrencyId(),$this->isPresentment(),$this->getSettlementCurrencyId());

        }
        else return array();

    }

    public function toXML():string
    {
        $xml = "<addon_configuration>";
        $xml .= sprintf("<id>%s</id>",$this->getId());
        $xml .= sprintf("<enabled>%s</enabled>",General::bool2xml($this->getEnabled()));
        if($this->getPaymentMethodId()>-1) $xml .= sprintf("<pm_id>%s</pm_id>",$this->getPaymentMethodId());
        if($this->getCurrencyId()>-1) $xml .= sprintf("<currency_id>%s</currency_id>",$this->getCurrencyId());
        if($this->getCountryId()>-1) $xml .= sprintf("<country_id>%s</country_id>",$this->getCountryId());
        if($this->getSettlementCurrencyId()>-1)
        {
            $xml .= sprintf("<settlement_currency_id>%s</settlement_currency_id>",$this->getSettlementCurrencyId());
            $xml .= sprintf("<is_presentment>%s</is_presentment>",General::bool2xml($this->isPresentment()));

        }
        if($this->getProviderId()>-1) $xml .= sprintf("<provider_id>%s</provider_id>",$this->getProviderId());
        if($this->getType()>-1) $xml .= sprintf("<type>%s</type>",$this->getType());
        if(empty($this->getVersion()) === false) $xml .= sprintf("<version>%s</version>",$this->getVersion());
        $xml .= "</addon_configuration>";

        return $xml;
    }

    public static function produceFromXML(SimpleXMLElement &$oXML):ServiceConfig
    {
        $serviceConf = new ServiceConfig();
        if(count($oXML->id)>0) $serviceConf->setId((int)$oXML->id);
        if(count($oXML->enabled)>0) $serviceConf->setId(General::xml2bool($oXML->enabled));
        if(count($oXML->pm_id)>0) $serviceConf->setPaymentMethodId((int)$oXML->pm_id);
        if(count($oXML->currency_id)>0) $serviceConf->setCurrencyId((int)$oXML->currency_id);
        if(count($oXML->country_id)>0) $serviceConf->setCountryId((int)$oXML->country_id);
        if(count($oXML->settlement_currency_id)>0) $serviceConf->setSettlementCurrencyId((int)$oXML->settlement_currency_id);
        if(count($oXML->is_presentment)>0) $serviceConf->setPresentment(General::xml2bool($oXML->is_presentment));
        if(count($oXML->provider_id)>0) $serviceConf->setProviderId((int)$oXML->provider_id);
        if(count($oXML->type)>0) $serviceConf->setType((int)$oXML->type);
        if(count($oXML->version)>0) $serviceConf->setVersion($oXML->version);

        return $serviceConf;
    }

    public static function produceFromResultSet($rs):ServiceConfig
    {
        $serviceConf = new ServiceConfig();
        if(isset($rs["ID"])) $serviceConf->setId($rs["ID"]);
        if(isset($rs['ENABLED'])) $serviceConf->setEnabled($rs['ENABLED']);
        if(isset($rs["PMID"])) $serviceConf->setPaymentMethodId($rs["PMID"]);
        if(isset($rs["CURRENCYID"])) $serviceConf->setCurrencyId($rs["CURRENCYID"]);
        if(isset($rs["SALE_CURRENCY_ID"])) $serviceConf->setCurrencyId($rs["SALE_CURRENCY_ID"]);
        if(isset($rs["COUNTRYID"])) $serviceConf->setCountryId($rs["COUNTRYID"]);
        if(isset($rs['CREATED'])) $serviceConf->setCreated($rs['CREATED']);
        if(isset($rs['MODIFIED'])) $serviceConf->setModified($rs["MODIFIED"]);
        if(isset($rs["SETTLEMENT_CURRENCY_ID"])) $serviceConf->setSettlementCurrencyId($rs["SETTLEMENT_CURRENCY_ID"]);
        if(isset($rs['IS_PRESENTMENT'])) $serviceConf->setPresentment($rs['IS_PRESENTMENT']);
        if(isset($rs["PROVIDERID"])) $serviceConf->setProviderId($rs["PROVIDERID"]);
        if(isset($rs["TYPEOFFRAUD"])) $serviceConf->setType($rs["TYPEOFFRAUD"]);
        if(isset($rs["VERSION"])) $serviceConf->setVersion($rs["VERSION"]);

        return $serviceConf;
    }

}