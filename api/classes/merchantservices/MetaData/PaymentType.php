<?php
namespace api\classes\merchantservices\MetaData;

/**
 * Payment Types class 
   * 
   * @package    Mechantservices
   * @subpackage Payment Type Config Class
   * @author     Ijaj Inamdar <ijaj.inamdar@cellpointmobile.com>
 */
class PaymentType
{

        /**
     * Payment Type Id
     *
     * @var int
     */
    private $_id;

    /**
     * Payment Type name
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
     * Get Payment Type Id
     *
     * @return integer
     */
    public function getId(): int
    {
        return $this->_id;
    }

    /**
     * Get Payment Type Name
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
     * Set Payment Type Id
     * 
     * @param int $id
     * @return PaymentType
     */
    public function setId(int $id): PaymentType
    {
        $this->_id = $id;
        return $this;
    }


    /**
     * Set PaymentType Name
     *
     * @param string $name
     * @return PaymentType
     */
    public function setName(string $name): PaymentType
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
        $xml .= '<payment_method>';
        $xml .= sprintf("<id>%s</id>",$this->getId());
        $xml .= sprintf("<name>%s</name>",$this->getName());        
        $xml .= '</payment_method>';

        return $xml;

    }

}