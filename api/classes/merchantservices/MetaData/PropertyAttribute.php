<?php

namespace api\classes\merchantservices\MetaData;

/**
 * Property attributes
 * 
 * 
 * @package    Mechantservices
 * @subpackage Property Attribute Class
 * @author     Ijaj Inamdar <ijaj.inamdar@cellpointmobile.com>

 */

class PropertyAttribute
{

    /**
     * Property attribute Id
     *
     * @var integer
     */
    private int $id;

    /**
     * Property attribute Key 
     *
     * @var string
     */
    private string $key;

    /**
     * Property attribute data type
     *
     * @var string
     */
    private string $dataType;

    /**
     * Property attribute mandatory
     *
     * @var string
     */
    private string $mandatory;


    public function __construct(int $id, string $key, string $dataType, string $mandatory)
    {
        $this->setId($id);
        $this->setKey($key);
        $this->setDataType($dataType);
        $this->setMandatory($mandatory);
    }


    /**
     * Get property attribute Id
     *
     * @return  integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set property attribute Id
     *
     * @param  integer  $id  Property attribute Id
     *
     * @return  self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get property attribute Key
     *
     * @return  string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set property attribute Key
     *
     * @param  string  $key  Property attribute Key
     *
     * @return  self
     */
    public function setKey(string $key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get property attribute data type
     *
     * @return  string
     */
    public function getDataType()
    {
        return $this->dataType;
    }

    /**
     * Set property attribute data type
     *
     * @param  string  $dataType  Property attribute data type
     *
     * @return  self
     */
    public function setDataType(string $dataType)
    {
        $this->dataType = $dataType;

        return $this;
    }

    /**
     * Get property attribute mandatory
     *
     * @return  string
     */
    public function getMandatory()
    {
        return $this->mandatory;
    }

    /**
     * Set property attribute mandatory
     *
     * @param  string  $mandatory  Property attribute mandatory
     *
     * @return  self
     */
    public function setMandatory(string $mandatory)
    {
        $this->mandatory = $mandatory;

        return $this;
    }

    public function toXml(): string
    {

        $xml = '';
        $xml .= '<property>';
        $xml .= sprintf("<id>%s</id>", $this->getId());
        $xml .= sprintf("<key>%s</key>", $this->getKey());
        $xml .= sprintf("<dataType>%s</dataType>", $this->getDataType());
        $xml .= sprintf("<mandatory>%s</mandatory>", $this->getMandatory());

        $xml .= '</property>';

        return $xml;
    }
}
