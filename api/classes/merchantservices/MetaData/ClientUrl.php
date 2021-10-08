<?php
namespace api\classes\merchantservices\MetaData;

use api\classes\merchantservices\commons\BaseInfo;

/**
   * ClientUrl
   * 
   * 
   * @package    Mechantservices
   * @subpackage ClientUrl Class
   * @author     Vikas Gupta <vikas.gupta@cellpointmobile.com>
 */
class ClientUrl extends BaseInfo
{

    /**
     * Url Type Id
     *
     * @var integer
     */
    private int $_typeId;

    /**
     * Url value
     *
     * @var string
     */
    private string $_value;

    /**
     * @return int
     */
    public function getTypeId(): int
    {
        return $this->_typeId;
    }

    /**
     * @param int $typeId
     *
     * @return ClientUrl
     */
    public function setTypeId(int $typeId): ClientUrl
    {
        $this->_typeId = $typeId;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->_value;
    }

    /**
     * @param string $value
     *
     * @return ClientUrl
     */
    public function setValue(string $value): ClientUrl
    {
        $this->_value = $value;
        return $this;
    }

    // Return in String::XML
    public function toXML() : string
    {
        $xml = '<client_url>';
        $xml .= parent::toXML();
        $xml .= sprintf("<type_id>%s</type_id>",$this->getTypeId());
        $xml .= sprintf("<value>%s</value>",$this->getValue());
        $xml .= '</client_url>';
        return $xml;
    }

    /**
     * @param array $rs
     *
     * @return \api\classes\merchantservices\MetaData\ClientUrl
     */
    public static function produceFromResultSet(array $rs): ClientUrl
    {
        $objURL = new ClientUrl();
        if(isset($rs["ID"])) $objURL->setId($rs["ID"]);
        if(isset($rs["TYPE_ID"])) $objURL->setTypeId($rs["TYPE_ID"]);
        if(isset($rs["NAME"])) $objURL->setName($rs["NAME"]);
        if(isset($rs["VALUE"])) $objURL->setValue($rs["VALUE"]);
        return $objURL;
    }
}