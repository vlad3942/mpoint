<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:CallbackMessageRequest.php
 */

namespace api\classes;
use JsonSerializable;

/**
 * Class CallbackMessageRequest
 *
 * @package api\classes
 * @xmlName session
 */
use api\interfaces\XMLSerializable;

class CallbackMessageRequest implements JsonSerializable, XMLSerializable
{
    public int $client_id;

    public int $account_id;

    public int $session_id;

    public Amount $sale_amount;

    public StateInfo $status;

    /**
     * @var TransactionData[]
     */
    public array $transactions = [];

    public string $callback_url;

    public $session_type;

    public $additional_data;

    private PendingAmount $pending_amount;

    /**
     * CallbackMessageRequest constructor.
     *
     * @param int $client_id
     * @param int $account_id
     * @param int $session_id
     * @param Amount $sale_amount
     * @param StateInfo $status
     * @param TransactionData[] $transactions
     * @param string $callback_url
     */
    public function __construct(int $client_id, int $account_id, int $session_id, Amount $sale_amount, StateInfo $status, array $transactions,string $callback_url, $session_type=null, $additional_data=null)
    {
        $this->client_id = $client_id;
        $this->account_id = $account_id;
        $this->session_id = $session_id;
        $this->sale_amount = $sale_amount;
        $this->status = $status;
        $this->transactions = $transactions;
        $this->callback_url = $callback_url;
        $this->session_type = $session_type;
        $this->additional_data = $additional_data;
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

    public function setPendingAmt(PendingAmount $pendingAmount)
    {
        $this->pending_amount = $pendingAmount;
    }

    public function getPendingAmount(): PendingAmount
    {
        return $this->pending_amount;
    }
}
