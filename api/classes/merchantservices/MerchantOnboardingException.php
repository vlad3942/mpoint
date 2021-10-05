<?php

namespace api\classes\merchantservices;

use mPointControllerException;

class MerchantOnboardingException extends mPointControllerException
{
    private $_httpCode;
    private $_statusCode;
    const SQL_EXCEPTION = 100;
    const SQL_DUPLICATE_EXCEPTION = 101;

    public function __construct( $statusCode,$message='')
    {
        parent::__construct(0, $message);
        $this->_statusCode = $statusCode;
    }

    public function getHTTPCode() { return $this->_httpCode; }
    public function getStatusCode() { return $this->_statusCode; }
    public function statusNode()
    {
        $sStatus = '<status>';
        $sStatus .= '<code>'.self::getStatusCode().'</code>';
        $sStatus .= '<text_code>'.self::getStatusCode().'</text_code>';
        $sStatus .= '<description>'.self::getMessage().'</description>';
        $sStatus .= '</status>';

        return $sStatus;
    }
    public function getStatus()
    {
        switch ($this->_statusCode)
        {
            case self::SQL_EXCEPTION;
             return "Failed During Saving Record";
            case self::SQL_DUPLICATE_EXCEPTION;
             return "Duplicate Record";
        }
        return $this->_statusCode;
    }
}