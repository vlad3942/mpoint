<?php
/**
 * Created by IntelliJ IDEA.
 * User: Kalpesh Patikh
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package: api\.classes
 * File Name:BillingSummaryData.php
 */

namespace api\classes;

use JsonSerializable;
use api\classes\billingsummary\info\AddonInfo;
use api\classes\billingsummary\info\FareInfo;

/**
 * Class TransactionData
 *
 * @package api\classes
 * @xmlName billing_summary
 */
class BillingSummaryData implements JsonSerializable
{
    private array $fare_details;

    private array $add_on;

    /**
     * BillingSummaryData constructor.
     *
     * @param int $order_id
     */
    public function __construct(\RDB $oDB, int $order_id)
    {
        $this->fare_details = FareInfo::produceConfigurations($oDB, $order_id);
        $this->add_on = AddonInfo::produceConfigurations($oDB, $order_id);
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