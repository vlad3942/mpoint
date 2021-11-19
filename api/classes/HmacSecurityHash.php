<?php
/**
 * Created by IntelliJ IDEA.
 * User: Chaitenya Yadav
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:HmacSecurityHash.php
 */

namespace api\classes;
use JsonSerializable;

/**
 * Class HmacSecurityHash
 *
 * @package api\classes
 * @xmlName hmac-security-hash
 */
use api\interfaces\XMLSerializable;

class HmacSecurityHash implements JsonSerializable, XMLSerializable
{
    private string $unique_reference;
    private string $init_token;
    private string $hmac;
    
    /**
     * HmacSecurityHash constructor.
     *
     * @param string $hmac
     * @param string $unique_reference
     * @param string $init_token
     */
    // public function __construct(int $client_id, int $account_id, int $session_id, Amount $sale_amount, StateInfo $status, array $transactions,string $callback_url, $session_type=null, $additional_data=null)
    public function __construct(string $hmac, string $unique_reference = null, string $init_token = null)
    {
        $this->hmac = $hmac;
        if(empty($unique_reference) ===FALSE)
        {
            $this->unique_reference = $unique_reference;
        }
        if(empty($init_token) ===FALSE)
        {
            $this->init_token = $init_token;
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
