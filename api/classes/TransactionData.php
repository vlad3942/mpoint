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

    public int $id;

    public string $order_id;

    public string $description;

    public int $fee;

    public string $hmac;

    public string $approval_code;

    public int $wallet_id;

    public string $payment_method;

    public string $payment_type;

    public string $short_code;

    public string $date_time;

    public string $local_date_time;

    public string $issuing_bank;

    public int $foreign_exchange_id;

    public Amount $amount;

    public StateInfo $status;

    public PSPData $psp;

    public \Card $card;

    public \CustomerInfo $customer_info;

    /**
     * @var \AdditionalData[]
     */
    public array $additional_data;

    /**
     * @var \AdditionalData[]
     */
    public array $client_data;

    /**
     * @var \ProductInfo[]
     */
    public array $product_info;

    /**
     * @var \AdditionalData[]
     */
    public array $delivery_info;

    /**
     * @var \AdditionalData[]
     */
    public array $shipping_info;

    /**
     * @var \AdditionalData[]
     */
    public array $billing_address;

    /**
     * TransactionData constructor.
     *
     * @param int $id
     * @param string $order_id
     * @param string $payment_method
     * @param string $payment_type
     * @param Amount $amount
     * @param StateInfo $status
     * @param PSPData $psp
     * @param \Card $card
     * @param \CustomerInfo $customer_info
     */
    public function __construct(int $id, string $order_id, string $payment_method, string $payment_type, Amount $amount, StateInfo $status, PSPData $psp, \Card $card, \CustomerInfo $customer_info)
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
    public function setDescription(string $description)
    {
        if (empty($description) === FALSE) {
            $this->description = $description;
        }
    }

    /**
     * @param int $fee
     */
    public function setFee(int $fee)
    {
        $this->fee = $fee;
    }

    /**
     * @param string $hmac
     */
    public function setHmac(string $hmac)
    {
        $this->hmac = $hmac;
    }

    /**
     * @param string $approval_code
     */
    public function setApprovalCode(string $approval_code)
    {
        if (empty($approval_code) === FALSE) {
            $this->approval_code = $approval_code;
        }
    }

    /**
     * @param int $wallet_id
     */
    public function setWalletId(int $wallet_id)
    {
        if (empty($wallet_id) === FALSE && $wallet_id > 0) {
            $this->wallet_id = $wallet_id;
        }
    }

    /**
     * @param string $short_code
     */
    public function setShortCode(string $short_code)
    {
        if (empty($short_code) === FALSE) {
            $this->short_code = $short_code;
        }
    }

    /**
     * @param string $date_time
     */
    public function setDateTime(string $date_time)
    {
        $this->date_time = $date_time;
    }

    /**
     * @param string $local_date_time
     */
    public function setLocalDateTime(string $local_date_time)
    {
        $this->local_date_time = $local_date_time;
    }

    /**
     * @param string $issuing_bank
     */
    public function setIssuingBank(string $issuing_bank)
    {
        if (empty($issuing_bank) === FALSE) {
            $this->issuing_bank = $issuing_bank;
        }
    }

    /**
     * @param int $foreign_exchange_id
     */
    public function setForeignExchangeId(int $foreign_exchange_id)
    {
        $this->foreign_exchange_id = $foreign_exchange_id;
    }

    /**
     * @param \AdditionalData[] $additional_data
     */
    public function setAdditionalData(array $additional_data)
    {
        $this->additional_data = $additional_data;
    }

    /**
     * @param \AdditionalData[] $client_data
     */
    public function setClientData(array $client_data)
    {
        $this->client_data = $client_data;
    }

    /**
     * @param \ProductInfo[] $product_info
     */
    public function setProductInfo(array $product_info)
    {
        $this->product_info = $product_info;
    }

    /**
     * @param \AdditionalData[] $delivery_info
     */
    public function setDeliveryInfo(array $delivery_info)
    {
        $this->delivery_info = $delivery_info;
    }

    /**
     * @param \AdditionalData[] $shipping_info
     */
    public function setShippingInfo(array $shipping_info)
    {
        $this->shipping_info = $shipping_info;
    }

    /**
     * @param \AdditionalData[] $billing_address
     */
    public function setBillingAddress(array $billing_address)
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