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
     * AirlineData constructor.
     *
     * @param \RDB $oDB
     * @param int $order_id
     */
    public function __construct(\RDB $oDB, int $order_id)
    {
        $this->profiles = \PassengerInfo::produceConfigurations($oDB, $order_id);
        $this->trips = \FlightInfo::produceConfigurations($oDB, $order_id);
        $this->billing_summary = new BillingSummaryData($oDB, $order_id);
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