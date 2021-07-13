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
    public string $name;

    public string $text;

    /**
     * AdditionalData constructor.
     *
     * @param $name
     * @param $text
     */
    public function __construct(string $name, string $text)
    {
        if(empty($name) === FALSE) {
            $this->name = $name;
            $this->text = $text;
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