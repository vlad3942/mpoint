<?php
namespace api\classes\merchantservices\MetaData;

/**
   * ClientUrl
   * 
   * 
   * @package    Mechantservices
   * @subpackage ClientUrl Class
   * @author     Vikas Gupta <vikas.gupta@cellpointmobile.com>
 */
class ClientUrl
{

    /**
     * Url Type Id
     *
     * @var integer
     */
    private int $_typeId;

    /**
     * Url name
     *
     * @var string
     */
    private string $_name;

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
    public function getName(): string
    {
        return $this->_name;
    }

    /**
     * @param string $name
     *
     * @return ClientUrl
     */
    public function setName(string $name): ClientUrl
    {
        $this->_name = $name;
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
        $xml .= sprintf("<type_id>%s</type_id>",$this->getTypeId());
        $xml .= sprintf("<name>%s</name>",$this->getName());
        $xml .= sprintf("<value>%s</value>",$this->getValue());
        $xml .= '</client_url>';
        return $xml;
    }
}

