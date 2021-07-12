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

use api\interfaces\XMLSerializable;
use JsonSerializable;

/**
 * Class AdditionalData
 *
 * @package api\classes
 * @xmlName params
 */
class AdditionalData implements JsonSerializable, XMLSerializable
{
    public string $key;

    public string $value;

    /**
     * AdditionalData constructor.
     *
     * @param $key
     * @param $value
     */
    public function __construct(string $key, string $value)
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

    /**
     * @return array
     */
    public function xmlSerialize()
    {
        $vars = get_object_vars($this);
        return array_filter($vars, "Callback::EmptyValueComparator");
    }
}