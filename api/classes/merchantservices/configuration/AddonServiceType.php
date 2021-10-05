<?php

namespace api\classes\merchantservices\configuration;

use AddonServiceTypeIndex;

class AddonServiceType
{
    private int $_iID;
    private string $_sType;
    private string $_sSubType;
    private string $_sTableName;
    private string $_sClassName;
    public function __construct(int $id,string $sType,string $sSubType,string $sTableName,string $sClassName)
    {
        $this->_iID = $id;
        $this->_sType = $sType;
        $this->_sSubType = $sSubType;
        $this->_sTableName = $sTableName;
        $this->_sClassName = $sClassName;

    }

    /**
     * @return int
     */
    public function getID(): int
    {
        return $this->_iID;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->_sType;
    }

    /**
     * @return string
     */
    public function getSubType(): string
    {
        return $this->_sSubType;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->_sTableName;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->_sClassName;
    }

    public static function produceAddonServiceTypebyId(int $iType,string $subType) : ?AddonServiceType
    {
        switch ($iType)
        {
            case AddonServiceTypeIndex::eDCC:
                return new AddonServiceType(AddonServiceTypeIndex::eDCC, "FX", "DCC","DCC_config_tbl","DCCConfig");
            case AddonServiceTypeIndex::ePCC:
                return new AddonServiceType(AddonServiceTypeIndex::ePCC, "FX", "PCC","PCC_config_tbl","PCCConfig");
            case AddonServiceTypeIndex::eMCP:
                return new AddonServiceType(AddonServiceTypeIndex::eMCP, "FX", "MCP","MCP_config_tbl","MCPConfig");
            case AddonServiceTypeIndex::eFraud:
                return new AddonServiceType(AddonServiceTypeIndex::eFraud, "FRAUD", $subType,"Fraud_config_tbl","FraudConfig");
            case AddonServiceTypeIndex::eMPI:
                return new AddonServiceType(AddonServiceTypeIndex::eMPI, "FRAUD", "MPI","MPI_config_tbl","MPIConfig");
            case AddonServiceTypeIndex::eSPLIT_PAYMENT:
                return new AddonServiceType(AddonServiceTypeIndex::eSPLIT_PAYMENT, "split_payment", $subType,"split_combination_tbl","Split_PaymentConfig");
            default:
                return null;
        }
    }




    public function toXML():string
    {
        $xml = sprintf("<addon_type>%s</addon_type>",$this->getType());
        $xml .= sprintf("<addon_subtype>%s</addon_subtype>",$this->getSubType());
        return $xml;
    }


}

