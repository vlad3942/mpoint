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
    /**
     * @var integer
     */
    public $value;

    /**
     * @var integer
     */
    public $currency_id;

    /**
     * @var long
     */
    public $conversion_rate;

    /**
     * Amount constructor.
     *
     * @param int  $value
     * @param int  $currency_id
     * @param long $conversion_rate
     */
    public function __construct($value, $currency_id, $conversion_rate = NULL)
    {
        if($value > 0 && strlen($currency_id) === 3) {
            $this->value = $value;
            $this->currency_id = $currency_id;
            $this->conversion_rate = $conversion_rate;
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