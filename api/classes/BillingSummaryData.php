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
     * @param $fare_details
     * @param $add_on
     */
    public function __construct($fare_details, $add_on)
    {
        $this->fare_details = $fare_details;
        $this->add_on = $add_on;
    }

    /**
     * BillingSummaryData produceConfigurations.
     * @param \RDB $oDB
     * @param int $order_id
     */
    public static function produceConfigurations(\RDB $oDB, int $order_id)
    {
        $fare_details = FareInfo::produceConfigurations($oDB, $order_id);
        $add_on = AddonInfo::produceConfigurations($oDB, $order_id);

        return new BillingSummaryData($fare_details, $add_on);
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