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

use JsonSerializable;

/**
 * Class Amount
 *
 * @package api\classes
 */
class Amount implements JsonSerializable
{
    private int $value;

    private int $currency_id;

    private int $decimals;

    private string $alpha3code;

    private float $conversion_rate;

    /**
     * Amount constructor.
     *
     * @param int $value
     * @param int $currency_id
     * @param int $decimals
     * @param string $code
     * @param float|null $conversion_rate
     */
    public function __construct(int $value, int $currency_id,int $decimals, string $code,?float $conversion_rate = NULL)
    {
        if($value > 0 && $currency_id > 0 && $decimals >= 0) {
            $this->value = $value;
            $this->currency_id = $currency_id;
            $this->decimals = $decimals;
            if(strlen($code) > 0){
                $this->alpha3code = $code;
            }
            if (empty($conversion_rate) === false){
                $this->conversion_rate = $conversion_rate;
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
}