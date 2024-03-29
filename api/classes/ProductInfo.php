<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package: api\.classes
 * File Name:ProductInfo.php
 */

namespace api\classes;

use JsonSerializable;
use api\interfaces\XMLSerializable;

class ProductInfo implements JsonSerializable, XMLSerializable
{
    public string $name;

    public int $quantity;

    public int $price;

    /**
     * ProductInfo constructor.
     *
     * @param string $name
     * @param int $quantity
     * @param int $price
     */
    public function __construct(string $name, int $quantity, int $price)
    {
        if(empty($name) === FALSE && $quantity > 0 && $price > 0) {
            $this->name = $name;
            $this->quantity = $quantity;
            $this->price = $price;
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