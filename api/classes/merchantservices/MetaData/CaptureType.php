<?php
namespace api\classes\merchantservices\MetaData;

/**
   * CaptureType Info
   * 
   * 
   * @package    Mechantservices
   * @subpackage CaptureType Class
   * @author     Ijaj Inamdar <ijaj.inamdar@cellpointmobile.com>
 */

class CaptureType
{


    /**
     * CaptureType Info Id
     *
     * @var int
     */
    private $_id;

    /**
     * CaptureType Info name
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
     * Get CaptureType Info Id
     *
     * @return integer
     */
    public function getId(): int
    {
        return $this->_id;
    }

    /**
     * Get CaptureType Info Name
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
     * Set CaptureType Info Id
     * 
     * @param int $id
     * @return CaptureType
     */
    public function setId(int $id): CaptureType
    {
        $this->_id = $id;
        return $this;
    }


    /**
     * Set CaptureType Name
     *
     * @param string $name
     * @return CaptureType
     */
    public function setName(string $name): CaptureType
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
        $xml .= '<capture_type>';
        $xml .= sprintf("<id>%s</id>",$this->getId());
        $xml .= sprintf("<name>%s</name>",$this->getName());        
        $xml .= '</capture_type>';

        return $xml;

    }

}