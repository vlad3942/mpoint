<?php
/**
 * Created by IntelliJ IDEA.
 * User: Chaitenya Yadav
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:SecurityHashResponse.php
 */

namespace api\classes;
use JsonSerializable;

/**
 * Class SecurityHashResponse
 *
 * @package api\classes
 * @xmlName security_token_detail
 */
use api\interfaces\XMLSerializable;

class SecurityHashResponse implements JsonSerializable, XMLSerializable
{
    private string $unique_reference_identifier;
    private string $token;
    
    /**
     * SecurityHashResponse constructor.
     *
     * @param string $token
     * @param string $unique_reference_identifier
     */
    public function __construct(string $token = null, string $unique_reference_identifier = null)
    {
        if(empty($token) ===FALSE)
        {
            $this->token = $token;
        }        
        if(empty($unique_reference_identifier) ===FALSE)
        {
            $this->unique_reference_identifier = $unique_reference_identifier;
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
