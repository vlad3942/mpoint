<?php
namespace api\classes\merchantservices\MetaData;

use api\classes\merchantservices\commons\BaseInfo;

/**
   * Client Payment Method Id
   * 
   * 
   * @package    Mechantservices
   * @subpackage PaymentMethodId
   * @author     Vikas.gupta <vikas.gupta@cellpointmobile.com>
 */

class ClientPaymentMethodId extends BaseInfo
{
    /**
     * Constructor function
     */
    public function __construct(){ }

    /**
     * Generate XML
     *
     * @return string
     */
    public function toXML(): string
    {
        return sprintf("<payment_method_id>%s</payment_method_id>",$this->getId());;
    }

    /**
     * @param array $rs
     *
     * @return \api\classes\merchantservices\MetaData\ClientPaymentMethodId
     */
    public static function produceFromResultSet(array $rs): ClientPaymentMethodId
    {
        $objPMId = new ClientPaymentMethodId();
        if(isset($rs["PAYMENT_METHOD_ID"])) $objPMId->setId($rs["PAYMENT_METHOD_ID"]);
        if(isset($rs["NAME"])) $objPMId->setName($rs["NAME"]);
        return $objPMId;
    }
}