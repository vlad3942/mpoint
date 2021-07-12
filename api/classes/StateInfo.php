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

    /**
     * StateInfo constructor.
     *
     * @param int $code
     * @param int|null $sub_code
     * @param string|null $message
     */
    public function __construct(int $code, ?int $sub_code = NULL, ?string $message = NULL)
    {
        if($code > 1000) {
            $this->code = $code;
            if(empty($sub_code) === false)
            {
                $this->sub_code = $sub_code;
            }
            $this->message = $message;
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