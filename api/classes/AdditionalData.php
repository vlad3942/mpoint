<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package: api\.classes
 * File Name:AdditionalData.php
 */

namespace api\classes;

use JsonSerializable;

class AdditionalData implements JsonSerializable
{
    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $value;

    /**
     * AdditionalData constructor.
     *
     * @param $key
     * @param $value
     */
    public function __construct($key, $value)
    {
        if(empty($key) === FALSE) {
            $this->key = $key;
            $this->value = $value;
        }
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $vars = get_object_vars($this);
        return array_filter($vars, "Callback::EmptyValueComparator");
    }
}