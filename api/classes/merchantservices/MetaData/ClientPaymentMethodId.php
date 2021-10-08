<?php
namespace api\classes\merchantservices\MetaData;

/**
   * Client Payment Method Id
   * 
   * 
   * @package    Mechantservices
   * @subpackage PaymentMethodId
   * @author     Vikas.gupta <vikas.gupta@cellpointmobile.com>
 */

class ClientPaymentMethodId
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
     * Constructor function
     */
    public function __construct(){ }

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
     * @return ClientPaymentMethodId
     */
    public function setId(int $id): ClientPaymentMethodId
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
     * @return ClientPaymentMethodId
     */
    public function setName(string $name): ClientPaymentMethodId
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
        return sprintf("<payment_method_id>%s</payment_method_id>",$this->getId());;
    }

}