<?php
/**
 * Created by IntelliJ IDEA.
 * User: Kalpesh Parikh
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package: api\.classes
 * File Name:BillingAddress.php
 */

namespace api\classes;

use api\interfaces\XMLSerializable;
use JsonSerializable;

/**
 * Class BillingAddress
 *
 * @package api\classes
 * @xmlName billing_address
 */
class BillingAddress implements JsonSerializable, XMLSerializable
{
    private $first_name;
    private $last_name;
    private $street;
    private $street2;
    private $city;
    private $state;
    private $postal_code;
    private $country;
    private $mobile;
    private $email;
    private $billing_idc;
    private $alpha2code;
    /**
     * BillingAddress constructor.
     *
     * @param array $billingAddress
     */
    public function __construct(array $billingAddress)
    {
        $this->initializePropFromArray($billingAddress);
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

    private function initializePropFromArray($address)
    {
        if(empty($address['first_name']) === FALSE)
        {
            $this->first_name = $address['first_name'];
        }
        if(empty($address['last_name']) === FALSE)
        {
            $this->last_name = $address['last_name'];
        }
        if(empty($address['street']) === FALSE)
        {
            $this->street = $address['street'];
        }
        if(empty($address['city']) === FALSE)
        {
            $this->city = $address['city'];
        }
        if(empty($address['state']) === FALSE)
        {
            $this->state = $address['state'];
        }
        if(empty($address['country']) === FALSE)
        {
            $this->country = $address['country'];
        }
        if(empty($address['zip']) === FALSE)
        {
            $this->postal_code = $address['zip'];
        }
        if(empty($address['alpha2code']) === FALSE)
        {
            $this->alpha2code = $address['alpha2code'];
        }
        if(empty($address['mobile']) === FALSE)
        {
            $this->mobile = $address['mobile'];
        }
        if(empty($address['email']) === FALSE)
        {
            $this->email = $address['email'];
        }
        if(empty($address['billing_idc']) === FALSE)
        {
            $this->billing_idc = $address['billing_idc'];
        }
    }
}