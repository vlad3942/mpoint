<?php

class ServiceConfig extends SQLOperation
{
    private int $_id;
    private int $_iPMId;
    private int $_iCountryId;
    private int $_iCurrencyId;
    private int $_iSettlementCurrencyId;
    private bool $_bPresentment;
    private string $_dCreated;
    private string $_dModified;
    private string $_bEnabled;


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

    public static function getInsertSQL(int $addonServiceType):string
    {
        $sql = "INSERT INTO CLIENT". sSCHEMA_POSTFIX ." %s (%s) values (%s)";
        switch ($addonServiceType)
        {
            case AddonServiceType::eMCP || AddonServiceType::eDCC:
                $sTableName = "DCC_config_tbl";
                if($addonServiceType == AddonServiceType::eMCP) { $sTableName = "MCP_config_tbl";}
                return sprintf($sql,$sTableName,"clientid,pmid,countryid,currencyid","$1,$2,$3,$4");
            case AddonServiceType::ePCC:
                return sprintf($sql,"MCP_config_tbl","clientid,pmid,sale_currency_id,is_presentment,settlement_currency_id","$1,$2,$3,$4,$5");
            case AddonServiceType::eSplitPayment:
                return "";
            default:
                return "";
        }

    }

    public function getParam(AddonServiceType $addonServiceType,int $iClientId):array
    {
        switch ($addonServiceType)
        {
            case AddonServiceType::eMCP || AddonServiceType::eDCC:
                return array($iClientId,$this->getPaymentMethodId(),$this->getCountryId(),$this->getCurrencyId());
            case AddonServiceType::ePCC:
                return array($iClientId,$this->getPaymentMethodId(),$this->getCurrencyId(),$this->isPresentment(),$this->getSettlementCurrencyId());
            case AddonServiceType::eSplitPayment:
                return array();
            default:
                return array();
        }
    }

}