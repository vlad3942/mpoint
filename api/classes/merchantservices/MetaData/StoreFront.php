<?php
namespace api\classes\merchantservices\MetaData;

use api\classes\merchantservices\commons\BaseInfo;

/**
   * Store Front
   * 
   * 
   * @package    Mechantservices
   * @subpackage StoreFront Class
   * @author     Vikas.gupta <vikas.gupta@cellpointmobile.com>
 */

class StoreFront extends BaseInfo
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
        $xml .= parent::toXML();
        $xml .= '</storefront>';
        return $xml;
    }

    /**
     * @param array $rs
     *
     * @return \api\classes\merchantservices\MetaData\StoreFront
     */
    public static function produceFromResultSet(array $rs): StoreFront
    {
        $StoreFront = new StoreFront();
        if(isset($rs["ID"])) $StoreFront->setId($rs["ID"]);
        if(isset($rs['NAME'])) $StoreFront->setName($rs['NAME']);
        return $StoreFront;
    }

}