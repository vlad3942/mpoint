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
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @return mixed
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @return mixed
     */
    public function getStreet2()
    {
        return $this->street2;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return mixed
     */
    public function getPostalCode()
    {
        return $this->postal_code;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return mixed
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getBillingIdc()
    {
        return $this->billing_idc;
    }

    /**
     * @return mixed
     */
    public function getAlpha2code()
    {
        return $this->alpha2code;
    }
    /**
     * BillingAddress constructor.
     *
     * @param array $billingAddress
     */
    public function __construct(array $billingAddress,bool $isSecure = false)
    {
        $this->initializePropFromArray($billingAddress,$isSecure);
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

    private function initializePropFromArray($address,bool $isSecure = false)
    {
        if($isSecure === true)
        {
            $this->first_name = $this->last_name = $this->street = $this->street2 = $this->city = $this->state = $this->country = $this->postal_code = $this->alpha2code = $this->mobile = $this->email = $this->billing_idc = "*****";
        }
        else
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
            if(empty($address['street2']) === FALSE)
            {
                $this->street2 = $address['street2'];
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
}