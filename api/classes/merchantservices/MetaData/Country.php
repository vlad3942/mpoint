<?php
namespace api\classes\merchantservices\MetaData;

/**
   * Country Info
   * 
   * 
   * @package    Mechantservices
   * @subpackage Country Class
   * @author     Ijaj Inamdar <ijaj.inamdar@cellpointmobile.com>
 */

class Country
{


    /**
     * Country Info Id
     *
     * @var int
     */
    private $_id;

    /**
     * Country Info name
     *
     * @var string
     */
    private $_name;

    /**
     * Constructor function
     */
    public function __construct()
    {
        
    }

    /**
     * Get Country Info Id
     *
     * @return integer
     */
    public function getId(): int
    {
        return $this->_id;
    }

    /**
     * Get Country Info Name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->_name;
    }

    /**
     * Set Country Info Id
     * 
     * @param int $id
     * @return Country
     */
    public function setId(int $id): Country
    {
        $this->_id = $id;
        return $this;
    }


    /**
     * Set Country Name
     *
     * @param string $name
     * @return Country
     */
    public function setName(string $name): Country
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

        $xml = '';
        $xml .= '<country>';
        $xml .= sprintf("<id>%s</id>",$this->getId());
        $xml .= sprintf("<name>%s</name>",$this->getName());        
        $xml .= '</country>';

        return $xml;

    }

}