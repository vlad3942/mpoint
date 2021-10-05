<?php
namespace api\classes\merchantservices\MetaData;

/**
   * PSP Info
   * 
   * 
   * @package    Mechantservices
   * @subpackage PSP Info Class
   * @author     Ijaj Inamdar <ijaj.inamdar@cellpointmobile.com>
 */
class PSPInfo
{

    /**
     * PSP Info Id
     *
     * @var int
     */
    private $_id;

    /**
     * PSP Info name
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
     * Get PSP Info Id
     *
     * @return integer
     */
    public function getId(): int
    {
        return $this->_id;
    }

    /**
     * Get PSP Info Name
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
     * Set PSP Info Id
     * 
     * @param int $id
     * @return PSPInfo
     */
    public function setId(int $id): PSPInfo
    {
        $this->_id = $id;
        return $this;
    }


    /**
     * Set PSPInfo Name
     *
     * @param string $name
     * @return PSPInfo
     */
    public function setName(string $name): PSPInfo
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
        $xml .= '<psp>';
        $xml .= sprintf("<id>%s</id>",$this->getId());
        $xml .= sprintf("<name>%s</name>",$this->getName());        
        $xml .= '</psp>';

        return $xml;

    }

}