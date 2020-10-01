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

class StateInfo implements JsonSerializable
{
    /**
     * @var int
     */
    public $code;

    /**
     * @var int|null
     */
    public $sub_code;

    /**
     * @var string|null
     */
    public $message;

    /**
     * StateInfo constructor.
     *
     * @param int         $code
     * @param int|null    $sub_code
     * @param string|null $message
     */
    public function __construct($code, $sub_code = NULL, $message = NULL)
    {
        if($code > 1000) {
            $this->code = (int)$code;
            if(empty($sub_code) === false)
            {
                $this->sub_code = (int)$sub_code;
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
}