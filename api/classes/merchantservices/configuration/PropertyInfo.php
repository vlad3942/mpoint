<?php

namespace api\classes\merchantservices\configuration;

use api\classes\merchantservices\commons\BaseInfo;

/**
 *
 * @package    Mechantservices
 * @subpackage Property Info Base Class
 */
class PropertyInfo extends BaseInfo
{

    /**
     * @var string
     */
    private string $_sValue = "";

    /**
     * @var string
     */
    private string $_sCategory;

    /**
     * @var int
     */
    private int $_iDataType;

    /**
     * @var int
     */
    private int $_iScope;

    /**
     * @var bool
     */
    private bool $_bMandatory;

    /**
     * @var bool
     */
    private bool $_bEnabled ;

    public function __construct() {  }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->_bEnabled;
    }

    /**
     * @param bool $bEnabled
     * @return PropertyInfo
     */
    public function setEnabled(bool $bEnabled): PropertyInfo
    {
        $this->_bEnabled = $bEnabled;
        return $this;
    }

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

    /**
     * @return string
     */
    public function toXML(string $rootNode = '')
    {
        $xml = "<property>";
        $xml .= parent::toXML();
        if(empty($this->getValue()) === false) {
            $xml .= "<value>".$this->getValue()."</value>";
        }
        $xml .= "<data_type>".$this->getDataType()."</data_type>";
        $xml .= sprintf("<enabled>%s</enabled>",\General::bool2xml($this->isEnabled()));
        $xml .= "<mandatory>".\General::bool2xml($this->isMandatory())."</mandatory>";
        $xml .= "</property>";
        return $xml;
    }

    /**
     * @param $oXML
     * @return PropertyInfo
     */
    public static function produceFromXML( &$oXML) : PropertyInfo
    {
        $propertyInfo = new PropertyInfo();
        if(count($oXML->id)>0) {
            $propertyInfo->setId((int)$oXML->id);
        }
        if(count($oXML->enabled)>0) {
            $propertyInfo->setEnabled(\General::xml2bool($oXML->enabled));
        }
        if(count($oXML->name)>0) {
            $propertyInfo->setName((string)$oXML->name);
        }
        if(count($oXML->value)>0) {
            $propertyInfo->setValue((string)$oXML->value);
        }
        return $propertyInfo;
    }

    /**
     * @param $rs
     * @return PropertyInfo
     */
    public static function produceFromResultSet($rs):PropertyInfo
    {
        $propertyInfo = new PropertyInfo();
        if(isset($rs["ID"])) {
            $propertyInfo->setId($rs["ID"]);
        }
        if(isset($rs['NAME'])) {
            $propertyInfo->setName($rs['NAME']);
        }
        if(isset($rs["DATATYPE"])) {
            $propertyInfo->setDataType($rs["DATATYPE"]);
        }
        if(isset($rs["CATEGORY"])) {
            $propertyInfo->setCategory($rs["CATEGORY"]);
        }
        if(isset($rs["ISMANDATORY"])) {
            $propertyInfo->setMandatory($rs["ISMANDATORY"]);
        }
        if(isset($rs["VALUE"])) {
            $propertyInfo->setValue(htmlspecialchars($rs["VALUE"]));
        }
        if(isset($rs['SCOPE'])) {
            $propertyInfo->setScope($rs['SCOPE']);
        }
        if(isset($rs['ENABLED'])) {
            $propertyInfo->setEnabled($rs['ENABLED']);
        }

        return $propertyInfo;
    }
}