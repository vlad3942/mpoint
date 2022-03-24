<?php
/**
 * Created by IntelliJ IDEA.
 * User: Kalpesh Parikh
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package: api\.classes.billingsummary.info
 * File Name: BillingSummaryInfo
 */

namespace api\classes\billingsummary\info;


abstract class BillingSummaryAbstract
{
    /**
     * Unique ID for the billing summary
     *
     * @var integer
     */
    private $id;
    /**
     * Value for the journey reference
     *
     * @var string
     */
    private $journey_ref;
    /**
     * Value of the Bill Type
     *
     * @var string
     */
    private $bill_type;
    /**
     * Value of Description
     *
     * @var string
     */
    private $description;
    /**
     * Value of Amount
     *
     * @var string
     */
    private $amount;
    /**
     * Value of Currency
     *
     * @var string
     */
    private $currency;
    /**
     * Value of Profile sequence
     *
     * @var integer
     */
    private $profile_seq;
    /**
     * Value of Trip Tag
     *
     * @var integer
     */
    private $trip_tag;
    /**
     * Value of Trip Sequence
     *
     * @var integer
     */
    private $trip_seq;

    /**
     * Value of Product Code
     *
     * @var string
     */
    private $product_code;

    /**
     * Value of Product Category
     *
     * @var string
     */
    private $product_category;

    /**
     * Value of Product Item
     *
     * @var string
     */
    private $product_item;


    /**
     * Default Constructor
     */
    public function __construct($id, $ref, $type, $desc, $amt, $curr, $pseq, $tripTag, $tripSeq, $pCode, $pCategory, $pItem) {
        $this->id = ( integer ) $id;
        $this->journey_ref = $ref;
        $this->bill_type = $type;
        $this->description = $desc;
        $this->amount = $amt;
        $this->currency = $curr;
        $this->profile_seq = $pseq;
        $this->trip_tag = $tripTag;
        $this->trip_seq = $tripSeq;
        $this->product_code = $pCode;
        $this->product_category = $pCategory;
        $this->product_item = $pItem;
    }

    /**
     * Returns the Unique ID for the Billing Summary
     *
     * @return integer
     */
    public function getID() {
        return $this->id;
    }
    /**
     * Returns the journey reference of Billing Summary
     *
     * @return string
     */
    public function getJourneyref() {
        return $this->journey_ref;
    }
    /**
     * Returns the bill type of Billing Summary
     *
     * @return string
     */
    public function getBillType() {
        return $this->bill_type;
    }
    /**
     * Returns the description of Billing Summary
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Returns the amount of Billing Summary
     *
     * @return string
     */
    public function getAmount() {
        return $this->amount;
    }
    /**
     * Returns currency of Billing Summary
     *
     * @return string
     */
    public function getCurrency() {
        return $this->currency;
    }
    /**
     * Returns profile sequence of Billing Summary
     *
     * @return integer
     */
    public function getProfileSeqence() {
        return $this->profile_seq;
    }
    /**
     * Returns trip tag of Billing Summary
     *
     * @return integer
     */
    public function getTripTag() {
        return $this->trip_tag;
    }
    /**
     * Returns trip sequence of Billing Summary
     *
     * @return integer
     */
    public function getTripSeq() {
        return $this->trip_seq;
    }

    /**
     * Returns product code of Billing Summary
     *
     * @return string
     */
    public function getProductCode() {
        return $this->product_code;
    }

    /**
     * Returns product category of Billing Summary
     * @return string
     */
    public function getProductCategory()
    {
        return $this->product_category;
    }

    /**
     * Returns product item of Billing Summary
     * @return string
     */
    public function getProductItem()
    {
        return $this->product_item;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        $vars = get_object_vars($this);
        return array_filter($vars, "Callback::EmptyValueComparator");
    }

    abstract public function toXML();
}