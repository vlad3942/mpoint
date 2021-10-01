<?php

namespace api\classes\merchantservices\MetaData;

/**
 * Serivce Sub Type Info
 * 
 * 
 * @package    Mechantservices
 * @subpackage ServiceSubType Class
 * @author     Ijaj Inamdar <ijaj.inamdar@cellpointmobile.com>

 */

class ServiceSubType
{

    /**
     * Service Sub Type Id
     *
     * @var integer
     */
    private int $id;

    /**
     * Service Sub Type Name
     *
     * @var string
     */
    private string $name;


    public function __construct(int $id = 0, string $name = '')
    {
        $this->setId($id);
        $this->setName($name);
    }

    /**
     * Get service Sub Type Id
     *
     * @return  integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set service Sub Type Id
     *
     * @param  integer  $id  Service Sub Type Id
     *
     * @return  self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get service Sub Type Name
     *
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set service Sub Type Name
     *
     * @param  string  $name  Service Sub Type Name
     *
     * @return  self
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Generate XML Response
     *
     * @return string
     */
    public function toXml(): string
    {
        $xml = '';
        $xml .= '<subtype>';
        $xml .= sprintf("<id>%s</id>", $this->getId());
        $xml .= sprintf("<name>%s</name>", $this->getName());
        $xml .= '</subtype>';

        return $xml;
    }
}
