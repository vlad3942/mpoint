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
use api\interfaces\XMLSerializable;

/**
 * Class TransactionData
 *
 * @package api\classes
 */
class TransactionData implements JsonSerializable, XMLSerializable
{

    private int $id;

    private string $order_id;

    private string $description;

    private int $fee;

    private string $hmac;

    private int $product_type;

    private string $approval_code;

    private int $wallet_id;

    private string $payment_method;

    private string $payment_type;

    private string $short_code;

    private string $date_time;

    private string $local_date_time;

    private string $issuing_bank;

    private int $foreign_exchange_id;

    private Amount $amount;

    private StateInfo $status;

    private PSPData $psp;

    private \Card $card;

    private \CustomerInfo $customer_info;

    /**
     * @var \AdditionalData[]
     */
    private array $additional_data;

    /**
     * @var \AdditionalData[]
     */
    private array $client_data;

    /**
     * @var \ProductInfo[]
     */
    private array $product_info;

    /**
     * @var \AdditionalData[]
     */
    private array $delivery_info;

    /**
     * @var \AdditionalData[]
     */
    private array $shipping_info;

    /**
     * @var \BillingAddress
     */
    private BillingAddress $billing_address;

    private int $service_type_id;

    private int $pos;

    private string $ip_address;

    private string $fraud_status_code;

    private string $fraud_status_desc;

    private int $route_config_id;

    private FraudStatus $fraud;

    private int $installment;

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
     * @param int $product_type
     */
    public function setProductType(int $product_type)
    {
        $this->product_type = $product_type;
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
     * @param \BillingAddress $billing_address
     */
    public function setBillingAddress(BillingAddress $billing_address)
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

    /**
     * @return int
     */
    public function getServiceTypeId(): int
    {
        return $this->service_type_id;
    }

    /**
     * @param int $service_type_id
     */
    public function setServiceTypeId(int $service_type_id): void
    {
        if($service_type_id > 0) {
            $this->service_type_id = $service_type_id;
        }
    }

    /**
     * @param int $pos
     */
    public function setPos(int $pos):void
    {
        $this->pos = $pos;
    }
    /**
     * @param string $ip_address
     */
    public function setIpAddress(string $ip_address):void
    {
        $this->ip_address = $ip_address;
    }
    /**
     * @return string
     */
    public function getFraudStatusCode(): string
    {
        return $this->fraud_status_code;
    }

    /**
     * @param string $fraud_status_code
     */
    public function setFraudStatusCode(string $fraud_status_code): void
    {
        $this->fraud_status_code = $fraud_status_code;
    }

    /**
     * @return string
     */
    public function getFraudStatusDesc(): string
    {
        return $this->fraud_status_desc;
    }

    /**
     * @param string $fraud_status_desc
     */
    public function setFraudStatusDesc(string $fraud_status_desc): void
    {
        $this->fraud_status_desc = $fraud_status_desc;
    }

    /**
     * @param int $route_config_id
     */
    public function setRouteConfigId(int $route_config_id): void
    {
        $this->route_config_id = $route_config_id;
    }

    /**
     * @return int
     */
    public function getSRouteConfigId(): int
    {
        return $this->route_config_id;
    }

    /**
     * @param FraudStatus
     */
    public function setFraudStatus(FraudStatus $fraud): void
    {
        $this->fraud = $fraud;
    }

    /**
     * @return FraudStatus
     */
    public function getFraudStatus(): FraudStatus
    {
        return $this->fraud;
    }

    /**
     * @param int
     */
    public function setInstallment(int $installment): void
    {
        $this->installment = $installment;
    }

    /**
     * @return int
     */
    public function getInstallment(): int
    {
        return $this->installment;
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