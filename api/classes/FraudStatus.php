<?php
/**
 * Created by IntelliJ IDEA.
 * User: Kalpesh Parikh
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package: api\.classes
 * File Name:FraudStatus.php
 */

namespace api\classes;

use JsonSerializable;

/**
 * Class Amount
 *
 * @package api\classes
 */
class FraudStatus implements JsonSerializable
{
    private int $status_code;

    private string $status_desc;

    private string $pre_auth_ext_id;

    private int $pre_auth_ext_status_code;

    private string $post_auth_ext_id;

    private int $post_auth_ext_status_code;

    /**
     * Amount constructor.
     *
     * @param \TxnInfo $obj_TxnInfo
     */
    public function __construct($status_code=null, $status_desc=null, $pre_auth_ext_id=null, $pre_auth_ext_status_code=null,$post_auth_ext_id=null,$post_auth_ext_status_code=null)
    {
        $this->status_code = $status_desc;
        $this->status_desc = $status_desc;
        $this->pre_auth_ext_id = $pre_auth_ext_id;
        $this->pre_auth_ext_status_code = $pre_auth_ext_status_code;
        $this->post_auth_ext_id = $post_auth_ext_id;
        $this->post_auth_ext_status_code = $post_auth_ext_status_code;
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