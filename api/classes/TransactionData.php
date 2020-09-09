<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package: api\.classes
 * File Name:TransactionData.php
 */

namespace api\classes;

use JsonSerializable;

/**
 * Class TransactionData
 *
 * @package api\classes
 */
class TransactionData implements JsonSerializable
{

    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $order_id;

    /**
     * @var string
     */
    public $description;

    /**
     * @var integer
     */
    public $fee;

    /**
     * @var string
     */
    public $hmac;

    /**
     * @var string
     */
    public $approval_code;

    /**
     * @var integer
     */
    public $wallet_id;

    /**
     * @var string
     */
    public $payment_method;

    /**
     * @var string
     */
    public $payment_type;

    /**
     * @var string
     */
    public $short_code;

    /**
     * @var string
     */
    public $date_time;

    /**
     * @var string
     */
    public $local_date_time;

    /**
     * @var string
     */
    public $issuing_bank;

    /**
     * @var integer
     */
    public $foreign_exchange_id;

    /**
     * @var \Amount
     */
    public $amount;

    /**
     * @var \StateInfo
     */
    public $status;

    /**
     * @var \PSPData
     */
    public $psp;

    /**
     * @var \Card
     */
    public $card;

    /**
     * @var \CustomerInfo
     */
    public $customer_info;

    /**
     * @var \AdditionalData[]
     */
    public $additional_data;

    /**
     * @var \AdditionalData[]
     */
    public $client_data;

    /**
     * @var \ProductInfo[]
     */
    public $product_info;

    /**
     * @var \AdditionalData[]
     */
    public $delivery_info;

    /**
     * @var \AdditionalData[]
     */
    public $shipping_info;

    /**
     * @var \AdditionalData[]
     */
    public $billing_address;

    /**
     * TransactionData constructor.
     *
     * @param int        $id
     * @param string     $order_id
     * @param string     $payment_method
     * @param string     $payment_type
     * @param \Amount    $amount
     * @param \StateInfo $status
     * @param \PSPData   $psp
     * @param \Card      $card
     */
    public function __construct($id, $order_id, $payment_method, $payment_type, \Amount $amount, \StateInfo $status, \PSPData $psp, \Card $card, \CustomerInfo $customer_info)
    {
        $this->id = $id;
        $this->order_id = $order_id;
        $this->payment_method = $payment_method;
        $this->payment_type = $payment_type;
        $this->amount = $amount;
        $this->status = $status;
        $this->psp = $psp;
        $this->card = $card;
        $this->customer_info = $customer_info;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        if (empty($description) === FALSE) {
            $this->description = $description;
        }
    }

    /**
     * @param int $fee
     */
    public function setFee($fee)
    {
        $this->fee = $fee;
    }

    /**
     * @param string $hmac
     */
    public function setHmac($hmac)
    {
        $this->hmac = $hmac;
    }

    /**
     * @param string $approval_code
     */
    public function setApprovalCode($approval_code)
    {
        if (empty($approval_code) === FALSE) {
            $this->approval_code = $approval_code;
        }
    }

    /**
     * @param int $wallet_id
     */
    public function setWalletId($wallet_id)
    {
        if (empty($wallet_id) === FALSE && $wallet_id > 0) {
            $this->wallet_id = $wallet_id;
        }
    }

    /**
     * @param string $short_code
     */
    public function setShortCode($short_code)
    {
        if (empty($short_code) === FALSE) {
            $this->short_code = $short_code;
        }
    }

    /**
     * @param string $date_time
     */
    public function setDateTime($date_time)
    {
        $this->date_time = $date_time;
    }

    /**
     * @param string $local_date_time
     */
    public function setLocalDateTime($local_date_time)
    {
        $this->local_date_time = $local_date_time;
    }

    /**
     * @param string $issuing_bank
     */
    public function setIssuingBank($issuing_bank)
    {
        if (empty($issuing_bank) === FALSE) {
            $this->issuing_bank = $issuing_bank;
        }
    }

    /**
     * @param int $foreign_exchange_id
     */
    public function setForeignExchangeId($foreign_exchange_id)
    {
        $this->foreign_exchange_id = $foreign_exchange_id;
    }

    /**
     * @param \AdditionalData[] $additional_data
     */
    public function setAdditionalData($additional_data)
    {
        $this->additional_data = $additional_data;
    }

    /**
     * @param \AdditionalData[] $client_data
     */
    public function setClientData($client_data)
    {
        $this->client_data = $client_data;
    }

    /**
     * @param \ProductInfo[] $product_info
     */
    public function setProductInfo($product_info)
    {
        $this->product_info = $product_info;
    }

    /**
     * @param \AdditionalData[] $delivery_info
     */
    public function setDeliveryInfo($delivery_info)
    {
        $this->delivery_info = $delivery_info;
    }

    /**
     * @param \AdditionalData[] $shipping_info
     */
    public function setShippingInfo($shipping_info)
    {
        $this->shipping_info = $shipping_info;
    }

    /**
     * @param \AdditionalData[] $billing_address
     */
    public function setBillingAddress($billing_address)
    {
        $this->billing_address = $billing_address;
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