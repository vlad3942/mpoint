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
    private ?bool $isOneStepAuth= null;

    /**
     * Combination constructor.
     *
     * @param PaymentTypes[] $paymentTypes Array of PaymentTypes used
     * @param null $isOneStepAuth One step authorization
     */
    public function __construct(array $paymentTypes = NULL,$isOneStepAuth=NULL)
    {
        if($paymentTypes !== NULL)
        {
            $this->paymentTypes = $paymentTypes;
        }
        if($isOneStepAuth !== NULL)
        {
            $this->isOneStepAuth = $isOneStepAuth;
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
     * @return void
     */
    public function setPaymentType(PaymentType $paymentTypes): void
    {
        //array_push($this->paymentTypes,$paymentTypes);
        $this->paymentTypes[]=$paymentTypes;
    }

    /**
     * @return bool|null
     */
    public function getIsOneStepAuth(): ?bool
    {
        return $this->isOneStepAuth;
    }

    /**
     * @param bool isOneStepAuth
     * @return void
     */
    public function setIsOneStepAuth(bool $isOneStepAuth): void
    {
        $this->isOneStepAuth = $isOneStepAuth;
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
            $xml .= '<is_one_step_authorization>'.($this->isOneStepAuth ? 'true' : 'false').'</is_one_step_authorization>';
            $xml .= '</combination>';
        }
        return $xml;
    }

}