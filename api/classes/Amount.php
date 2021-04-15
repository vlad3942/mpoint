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
    public int $value;

    public int $currency_id;

    public float $conversion_rate;

    /**
     * Amount constructor.
     *
     * @param int $value
     * @param int $currency_id
     * @param float|null $conversion_rate
     */
    public function __construct(int $value, int $currency_id, ?float $conversion_rate = NULL)
    {
        if($value > 0 && $currency_id > 0) {
            $this->value = $value;
            $this->currency_id = $currency_id;
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