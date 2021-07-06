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
    public function __construct(\TxnInfo $obj_TxnInfo)
    {
        $getFraudStatusCode = $this->getFraudDetails($obj_TxnInfo->getID());
        $aTxnAdditionalData = $obj_TxnInfo->getAdditionalData();
        if (empty($getFraudStatusCode) === FALSE) {

            if (isset($aTxnAdditionalData['pre_auth_ext_id'])) {
                $this->pre_auth_ext_id = $aTxnAdditionalData['pre_auth_ext_id'];
            }
            if (isset($aTxnAdditionalData['pre_auth_ext_status_code'])) {
                $this->pre_auth_ext_status_code = $aTxnAdditionalData['pre_auth_ext_status_code'];
            }
            if (isset($aTxnAdditionalData['post_auth_ext_id'])) {
                $this->post_auth_ext_id = $aTxnAdditionalData['post_auth_ext_id'];
            }
            if (isset($aTxnAdditionalData['post_auth_ext_status_code'])) {
                $this->post_auth_ext_status_code = $aTxnAdditionalData['post_auth_ext_status_code'];
            }

            $this->status_code = $getFraudStatusCode['status_code'];
            $this->status_desc = $getFraudStatusCode['status_desc'];
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