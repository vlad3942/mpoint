<?php

namespace api\classes\merchantservices\configuration;

use api\classes\merchantservices\commons\BaseInfo;

class PropertyInfo extends BaseInfo
{

    private string $_sValue = "";
    private string $_sCategory;
    private int $_iDataType;
    private int $_iScope;
    private bool $_bMandatory;

    public function __construct() {  }

    /**
     * @return int
     */
    public function getScope(): int
    {
        return $this->_iScope;
    }

    /**
     * @param int $iScope
     * @return PropertyInfo
     */
    public function setScope(int $iScope): PropertyInfo
    {
        $this->_iScope = $iScope;
        return $this;
    }

    /**
     * @return int
     */
    public function getDataType(): int
    {
        return $this->_iDataType;
    }

    /**
     * @param int $iDataType
     * @return PropertyInfo
     */
    public function setDataType(int $iDataType): PropertyInfo
    {
        $this->_iDataType = $iDataType;
        return $this;
    }

    /**
     * @return bool
     */
    public function isMandatory(): bool
    {
        return $this->_bMandatory;
    }

    /**
     * @param bool $bMandatory
     * @return PropertyInfo
     */
    public function setMandatory(bool $bMandatory): PropertyInfo
    {
        $this->_bMandatory = $bMandatory;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->_sValue;
    }

    /**
     * @param string $sValue
     * @return PropertyInfo
     */
    public function setValue(string $sValue): PropertyInfo
    {
        $this->_sValue = $sValue;
        return $this;
    }

    /**
     * @return string
     */
    public function getCategory(): string
    {
        return $this->_sCategory;
    }

    /**
     * @param string $sCategory
     * @return PropertyInfo
     */
    public function setCategory(string $sCategory): PropertyInfo
    {
        $this->_sCategory = $sCategory;
        return $this;
    }

    public function toXML()
    {
        $xml = "<property>";
        $xml .= parent::toXML();
        if(empty($this->getValue()) === false) $xml .= "<value>".$this->getValue()."</value>";
        $xml .= "<data_type>".$this->getDataType()."</data_type>";
        $xml .= "<mandatory>".\General::bool2xml($this->isMandatory())."</mandatory>";
        $xml .= "</property>";

        return $xml;
    }

    public static function produceFromXML( &$oXML) : PropertyInfo
    {
        $propertyInfo = new PropertyInfo();
        if(count($oXML->id)>0) $propertyInfo->setId((int)$oXML->id);
        if(count($oXML->name)>0) $propertyInfo->setName((string)$oXML->name);
        if(count($oXML->value)>0) $propertyInfo->setValue((string)$oXML->value);
        return $propertyInfo;
    }

    public static function produceFromResultSet($rs):PropertyInfo
    {
        $propertyInfo = new PropertyInfo();
        if(isset($rs["ID"])) $propertyInfo->setId($rs["ID"]);
        if(isset($rs['NAME'])) $propertyInfo->setName($rs['NAME']);
        if(isset($rs["DATATYPE"])) $propertyInfo->setDataType($rs["DATATYPE"]);
        if(isset($rs["CATEGORY"])) $propertyInfo->setCategory($rs["CATEGORY"]);
        if(isset($rs["ISMANDATORY"])) $propertyInfo->setMandatory($rs["ISMANDATORY"]);
        if(isset($rs["VALUE"])) $propertyInfo->setValue($rs["VALUE"]);
        if(isset($rs['SCOPE'])) $propertyInfo->setScope($rs['SCOPE']);


        return $propertyInfo;
    }

}