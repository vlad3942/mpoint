<?php
/**
 * Created by IntelliJ IDEA.
 * User: Kalpesh Parikh
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package: api\.classes
 * File Name:OrderData.php
 */

namespace api\classes;

use JsonSerializable;

/**
 * Class AirlineData
 *
 * @package api\classes
 * @xmlName order_data
 */
class OrderData implements JsonSerializable
{

    protected array $profiles;

    protected array $trips;

    protected BillingSummaryData $billing_summary;


    /**
     * OrderData constructor.
     *
     * @param $profiles
     * @param $trips
     * @param $billing_summary
     */
    public function __construct($profiles, $trips, $billing_summary)
    {
        $this->profiles = $profiles;
        $this->trips = $trips;
        $this->billing_summary = $billing_summary;
    }

    /**
     * OrderData produceConfigurations.
     *
     * @param \RDB $oDB
     * @param int $order_id
     */
    public static function produceConfigurations(\RDB $oDB, int $order_id) {
        $profiles = \PassengerInfo::produceConfigurations($oDB, $order_id);
        $trips = \FlightInfo::produceConfigurations($oDB, $order_id);
        $billing_summary = new BillingSummaryData($oDB, $order_id);

        return new OrderData($profiles, $trips, $billing_summary);
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