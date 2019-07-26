<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:passbookentry.php
 */

class PassbookEntry implements JsonSerializable
{
    /**
     * @var int
     */
    private $_id;

    /**
     * @var int
     */
    private $_amount;

    /**
     * @var int
     */
    private $_currencyId;

    /**
     * @var int
     */
    private $_requestedOperation;

    /**
     * @var int
     */
    private $_performedOperation;

    /**
     * @var string
     */
    private $_status;

    /**
     * @var bool
     */
    private $_enabled;

    /**
     * @var DateTime
     */
    private $_created;

    /**
     * @var DateTime
     */
    private $_modified;

    /**
     * @var string
     */
    private $_externalReference;

    /**
     * @var string
     */
    private $_externalReferenceIdentifier;

    /**
     * PassbookEntry constructor.
     *
     * @param $id
     * @param $amount
     * @param $currencyId
     * @param $requestedOperation
     * @param $performedOperation
     * @param $status
     * @param $enabled
     * @param $created
     * @param $modified
     * @param $externalReference
     * @param $externalReferenceIdentifier
     */
    public function __construct($id, $amount, $currencyId, $requestedOperation, $externalReference = '', $externalReferenceIdentifier = '', $performedOperation = 0, $status = '', $enabled = TRUE, $created = NULL, $modified= NULL)
    {
        $this->_id = (int)$id;
        $this->_amount = (int)$amount;
        $this->_currencyId = (int)$currencyId;
        $this->_requestedOperation = (int)$requestedOperation;
        $this->_performedOperation = (int)$performedOperation;
        $this->_status = (string)$status;
        $this->_enabled = (boolean)$enabled;
        $this->_created = $created;
        $this->_modified = $modified;
        $this->_externalReference = (string)$externalReference;
        $this->_externalReferenceIdentifier = (string)$externalReferenceIdentifier;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->_amount;
    }

    /**
     * @return int
     */
    public function getCurrencyId()
    {
        return $this->_currencyId;
    }

    /**
     * @return int|null
     */
    public function getRequestedOperation()
    {
        if($this->_requestedOperation === 0)
        {
            return null;
        }
        return $this->_requestedOperation;
    }


    /**
     * @return int|null
     */
    public function getPerformedOperation()
    {
        if($this->_performedOperation === 0)
        {
            return null;
        }
        return $this->_performedOperation;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        if ($this->_status == '')
            $this->_status = 'pending';
        return $this->_status;
    }

    /**
     * @param string $status
     *
     * @throws Exception
     */
    public function setStatus($status)
    {
        if (Constants::sPassbookStatusPending === $status || Constants::sPassbookStatusInProgress === $status || Constants::sPassbookStatusDone === $status || Constants::sPassbookStatusInvalid === $status || Constants::sPassbookStatusError === $status) {
            $this->_status = $status;
        }
        else
        {
            throw new Exception('Invalid entry status  : ' . $status, E_USER_ERROR);
        }

    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_enabled;
    }

    /**
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->_created;
    }

    /**
     * @return DateTime
     */
    public function getModified()
    {
        return $this->_modified;
    }

    /**
     * @return string
     */
    public function getExternalReference()
    {
        return $this->_externalReference;
    }

    /**
     * @return string
     */
    public function getExternalReferenceIdentifier()
    {
        return $this->_externalReferenceIdentifier;
    }

    /**
     * To convert Object to JSON using json_encode function
     * e.g. json_encode($passBookEntryObj)
     *
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}