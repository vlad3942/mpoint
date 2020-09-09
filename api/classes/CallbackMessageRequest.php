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
    /**
     * @var integer
     */
    public $client_id;

    /**
     * @var integer
     */
    public $account_id;

    /**
     * @var integer
     */
    public $session_id;

    /**
     * @var \Amount
     */
    public $sale_amount;

    /**
     * @var \StateInfo
     */
    public $status;

    /**
     * @var \TransactionData[]
     */
    public $transactions = [];

    /**
     * CallbackMessageRequest constructor.
     *
     * @param int                $client_id
     * @param int                $account_id
     * @param int                $session_id
     * @param \Amount            $sale_amount
     * @param \StateInfo         $status
     * @param \TransactionData[] $transactions
     */
    public function __construct($client_id, $account_id, $session_id, $sale_amount, \StateInfo $status, array $transactions)
    {
        $this->client_id = $client_id;
        $this->account_id = $account_id;
        $this->session_id = (int)$session_id;
        $this->sale_amount = $sale_amount;
        $this->status = $status;
        $this->transactions = $transactions;
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
