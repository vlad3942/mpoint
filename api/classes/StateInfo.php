<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package: api\.classes
 * File Name:StateInfo.php
 */

namespace api\classes;

use JsonSerializable;
use api\interfaces\XMLSerializable;

/**
 * Class StateInfo
 *
 * @package api\classes
 * @xmlName status
 */
class StateInfo implements JsonSerializable, XMLSerializable
{
    private int $code;

    private ?int $sub_code;

    private ?string $message;

    private ?string $provider_status_code;

    private ?string $provider_message;

    /**
     * StateInfo constructor.
     *
     * @param int $code
     * @param int|null $sub_code
     * @param string|null $message
     * @param string|null $provider_status_code
     * @param string|null $provider_message
     */
    public function __construct(int $code, ?int $sub_code = NULL, ?string $message = NULL, ?string $provider_message=NULL, ?string $provider_status_code = NULL)
    {
        if($code > 1000) {
            $this->code = $code;
            if(empty($sub_code) === false)
            {
                $this->sub_code = $sub_code;
            }
            $this->message = $message;
            if(empty($provider_message) === false)
            {
                $this->provider_message = $provider_message;
            }
            if(empty($provider_status_code) === false)
            {
                $this->provider_status_code = $provider_status_code;
            }
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