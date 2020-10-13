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
    public int $code;

    public ?int $sub_code;

    public ?string $message;

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
}