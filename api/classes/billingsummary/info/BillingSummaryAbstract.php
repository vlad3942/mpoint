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
    private $_iID;
    /**
     * Value for the journey reference
     *
     * @var string
     */
    private $_JourneyRef;
    /**
     * Value of the Bill Type
     *
     * @var string
     */
    private $_BillType;
    /**
     * Value of the Type Id
     *
     * @var integer
     */
    private $_TypeId;
    /**
     * Value of Description
     *
     * @var string
     */
    private $_Description;
    /**
     * Value of Amount
     *
     * @var string
     */
    private $_Amount;
    /**
     * Value of Currency
     *
     * @var string
     */
    private $_Currency;
    /**
     * Value of Profile sequence
     *
     * @var integer
     */
    private $_ProfileSeq;
    /**
     * Value of Trip Tag
     *
     * @var integer
     */
    private $_TripTag;
    /**
     * Value of Trip Sequence
     *
     * @var integer
     */
    private $_TripSeq;

    /**
     * Value of Product Code
     *
     * @var string
     */
    private $_ProductCode;

    /**
     * Value of Product Category
     *
     * @var string
     */
    private $_ProductCategory;

    /**
     * Value of Product Item
     *
     * @var string
     */
    private $_ProductItem;


    /**
     * Default Constructor
     */
    public function __construct($id, $ref, $type, $typeId, $desc, $amt, $curr, $pseq, $tripTag, $tripSeq, $pCode, $pCategory, $pItem) {
        $this->_iID = ( integer ) $id;
        $this->_JourneyRef = $ref;
        $this->_BillType = $type;
        $this->_TypeId = $typeId;
        $this->_Description = $desc;
        $this->_Amount = $amt;
        $this->_Currency = $curr;
        $this->_ProfileSeq = $pseq;
        $this->_TripTag = $tripTag;
        $this->_TripSeq = $tripSeq;
        $this->_ProductCode = $pCode;
        $this->_ProductCategory = $pCategory;
        $this->_ProductItem = $pItem;
    }

    /**
     * Returns the Unique ID for the Billing Summary
     *
     * @return integer
     */
    public function getID() {
        return $this->_iID;
    }
    /**
     * Returns the journey reference of Billing Summary
     *
     * @return string
     */
    public function getJourneyRef() {
        return $this->_JourneyRef;
    }
    /**
     * Returns the bill type of Billing Summary
     *
     * @return string
     */
    public function getBillType() {
        return $this->_BillType;
    }
    /**
     * Returns the type id of Billing Summary
     *
     * @return integer
     */
    public function getTypeId() {
        return $this->_TypeId;
    }
    /**
     * Returns the description of Billing Summary
     *
     * @return string
     */
    public function getDescription() {
        return $this->_Description;
    }

    /**
     * Returns the amount of Billing Summary
     *
     * @return string
     */
    public function getAmount() {
        return $this->_Amount;
    }
    /**
     * Returns currency of Billing Summary
     *
     * @return string
     */
    public function getCurrency() {
        return $this->_Currency;
    }
    /**
     * Returns profile sequence of Billing Summary
     *
     * @return integer
     */
    public function getProfileSeqence() {
        return $this->_ProfileSeq;
    }
    /**
     * Returns trip tag of Billing Summary
     *
     * @return integer
     */
    public function getTripTag() {
        return $this->_TripTag;
    }
    /**
     * Returns trip sequence of Billing Summary
     *
     * @return integer
     */
    public function getTripSeq() {
        return $this->_TripSeq;
    }

    /**
     * Returns product code of Billing Summary
     *
     * @return string
     */
    public function getProductCode() {
        return $this->_ProductCode;
    }

    /**
     * Returns product category of Billing Summary
     * @return string
     */
    public function getProductCategory()
    {
        return $this->_ProductCategory;
    }

    /**
     * Returns product item of Billing Summary
     * @return string
     */
    public function getProductItem()
    {
        return $this->_ProductItem;
    }

    abstract public function toXML();
}