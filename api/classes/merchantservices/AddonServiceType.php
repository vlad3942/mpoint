<?php

namespace api\classes\merchantservices;

use AddonServiceTypeIndex;

class AddonServiceType
{
    private int $_iID;
    private string $_sType;
    private string $_sSubType;
    private string $_sTableName;
    public function __construct(int $id,string $sType,string $sSubType,string $sTableName)
    {
        $this->_iID = $id;
        $this->_sType = $sType;
        $this->_sSubType = $sSubType;
        $this->_sTableName = $sTableName;

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

    public static function produceAddonServiceTypebyId(int $iType) : ?AddonServiceType
    {
        switch ($iType)
        {
            case AddonServiceTypeIndex::eDCC:
                return new AddonServiceType(AddonServiceTypeIndex::eDCC, "FX", "DCC","DCC_config_tbl");
            case AddonServiceTypeIndex::ePCC:
                return new AddonServiceType(AddonServiceTypeIndex::ePCC, "FX", "PCC","PCC_config_tbl");
            case AddonServiceTypeIndex::eMCP:
                return new AddonServiceType(AddonServiceTypeIndex::eMCP, "FX", "MCP","MCP_config_tbl");
            case AddonServiceTypeIndex::eFraud:
                return new AddonServiceType(AddonServiceTypeIndex::eFraud, "FRAUD", "FRAUD","Fraud_config_tbl");
            case AddonServiceTypeIndex::eMPI:
                return new AddonServiceType(AddonServiceTypeIndex::eMPI, "FRAUD", "MPI","MPI_config_tbl");
            default:
               return null;
        }
    }


}

