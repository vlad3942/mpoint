<?php
namespace api\classes\merchantservices\MetaData;

/**
   * Currency Info
   * 
   * 
   * @package    Mechantservices
   * @subpackage Currency Class
   * @author     Ijaj Inamdar <ijaj.inamdar@cellpointmobile.com>
 */

class Currency
{


    /**
     * Currency Info Id
     *
     * @var int
     */
    private $_id;

    /**
     * Currency Info name
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
     * Get Currency Info Id
     *
     * @return integer
     */
    public function getId(): int
    {
        return $this->_id;
    }

    /**
     * Get Currency Info Name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->_name;
    }

    /**
     * Configuration set
     *
     * @var array
     */
    private array $aConfig;


    /**
     * Set Currency Info Id
     * 
     * @param int $id
     * @return Currency
     */
    public function setId(int $id): Currency
    {
        $this->_id = $id;
        return $this;
    }


    /**
     * Set Currency Name
     *
     * @param string $name
     * @return Currency
     */
    public function setName(string $name): Currency
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
        $xml .= '<currency>';
        $xml .= sprintf("<id>%s</id>",$this->getId());
        $xml .= sprintf("<name>%s</name>",$this->getName());        
        $xml .= '</currency>';

        return $xml;

    }

}