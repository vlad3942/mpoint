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
 */
class CallbackMessageRequest implements JsonSerializable
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
    public function __construct(int $client_id, int $account_id, int $session_id, Amount $sale_amount, StateInfo $status, array $transactions,string $callback_url)
    {
        $this->client_id = $client_id;
        $this->account_id = $account_id;
        $this->session_id = $session_id;
        $this->sale_amount = $sale_amount;
        $this->status = $status;
        $this->transactions = $transactions;
        $this->callback_url = $callback_url;
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
