<?php
namespace api\classes\merchantservices\MetaData;

/**
   * Store Front
   * 
   * 
   * @package    Mechantservices
   * @subpackage StoreFront Class
   * @author     Vikas.gupta <vikas.gupta@cellpointmobile.com>
 */

class StoreFront
{
    /**
     * Id
     *
     * @var int
     */
    private int $_id;

    /**
     * Name
     *
     * @var string
     */
    private string $_name;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->_id;
    }

    /**
     * @param int $id
     *
     * @return StoreFront
     */
    public function setId(int $id): StoreFront
    {
        $this->_id = $id;
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
     * @return StoreFront
     */
    public function setName(string $name): StoreFront
    {
        $this->_name = $name;
        return $this;
    }



    /**
     * Generate XML
     *
     * @return string
     */
    public function toXML(): string
    {
        $xml = '<storefront>';
        $xml .= sprintf("<id>%s</id>",$this->getId());
        $xml .= sprintf("<name>%s</name>",$this->getName());        
        $xml .= '</storefront>';
        return $xml;
    }

}