<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package: api\.classes.splitpayment.config
 * File Name: Combination
 */

namespace api\classes\splitpayment\config;


class Combination
{
    /**
     * @var PaymentType[]
     */
    private array $paymentTypes = [];

    /**
     * Combination constructor.
     *
     * @param PaymentTypes[] $paymentTypes Array of PaymentTypes used
     */
    public function __construct(array $paymentTypes = NULL)
    {
        if($paymentTypes !== NULL)
        {
            $this->paymentTypes = $paymentTypes;
        }
    }

    /**
     * @return PaymentType[]|null
     */
    public function getPaymentTypes(): ?array
    {
        return $this->paymentTypes;
    }

    /**
     * @param PaymentType $paymentTypes
     */
    public function setPaymentType(PaymentType $paymentTypes): void
    {
        //array_push($this->paymentTypes,$paymentTypes);
        $this->paymentTypes[]=$paymentTypes;
    }

    /**
     * @return string
     */
    public function toXML(): string
    {
        $xml = '';
        if(count($this->paymentTypes) > 0) {
            $xml = '<combination>';
            foreach ($this->paymentTypes as $paymentType) {
                $xml .= $paymentType->toXML();
            }
            $xml .= '</combination>';
        }
        return $xml;
    }

}