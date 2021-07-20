<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package: api\.classes
 * File Name:Amount.php
 */

namespace api\classes;

use api\interfaces\XMLSerializable;
use JsonSerializable;

/**
 * Class PendingAmount
 *
 * @package api\classes
 * @xmlName pending_amount
 */
class PendingAmount implements JsonSerializable, XMLSerializable
{
    private int $value;

    private int $currency_id;

    private int $decimals;

    private string $alpha3code;

    private float $conversion_rate;

    /**
     * Pending Amount constructor.
     *
     * @param Amount
     */
    public function __construct(Amount $pendingAmt)
    {

        if($pendingAmt->getValue() > 0 && $pendingAmt->getCurrency() > 0 && $pendingAmt->getDecimal() > 0) {
            $this->value = $pendingAmt->getValue();
            $this->currency_id = $pendingAmt->getCurrency();
            $this->decimals = $pendingAmt->getDecimal();
            if(strlen($pendingAmt->getCode()) > 0){
                $this->alpha3code = $pendingAmt->getCode();
            }
            if (empty($pendingAmt->getConversionRate()) === false){
                $this->conversion_rate = $pendingAmt->getConversionRate();
            }
        }
    }


    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        $vars = get_object_vars($this);
        return array_filter($vars, "Callback::EmptyValueComparator");
    }

    /**
     * @return array
     */
    public function xmlSerialize()
    {
        $vars = get_object_vars($this);
        return array_filter($vars, "Callback::EmptyValueComparator");
    }
}