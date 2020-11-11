<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anna Lagad
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointdigital.com
 * Project: server
 * File Name:fraudStatus.php
 */

class FraudStatus
{

    private $_iClientId;

    private $_iTransactionId;

    private $_iStauts;

    private $_sComment;


    private $_objDB;

    private  $_obj_mPoint;

    private  $_obj_mConsole;

    private $_aHTTP_CONN_INFO;

    public function __construct($aHTTP_CONN_INFO, $_OBJ_DB, $obj_mPoint, $obj_mConsole, $clientId, $transactionId, $status, $comment)
    {
        $this->_iClientId = $clientId;
        $this->_iTransactionId = $transactionId;
        $this->_iStauts = $status;
        $this->_sComment = $comment;
        $this->_objDB = $_OBJ_DB;
        $this->_obj_mPoint = $obj_mPoint;
        $this->_obj_mConsole = $obj_mConsole;
        $this->_aHTTP_CONN_INFO = $aHTTP_CONN_INFO;
    }

    public function updateFraudStatus()
    {
        $xml ='';
        $aStatus = array(
            Constants::iPRE_FRAUD_CHECK_REVIEW_SUCCESS_STATE,
            Constants::iPRE_FRAUD_CHECK_REVIEW_FAIL_STATE,
            Constants::iPOST_FRAUD_CHECK_REVIEW_SUCCESS_STATE,
            Constants::iPOST_FRAUD_CHECK_REVIEW_FAIL_STATE
        );
        $obj_TxnInfo = TxnInfo::produceInfo( $this->_iTransactionId, $this->_objDB);

        if(empty($this->_iTransactionId) === false && empty($this->_sComment) === false && in_array($this->_iStauts, $aStatus) && $obj_TxnInfo->hasEitherState($this->_objDB, $aStatus) === false)
        {
            $this->_obj_mPoint->newMessage($this->_iTransactionId, $this->_iStauts, $this->_sComment);
            $xml = '<status code="200">Operation Successful</status>';
        }
        else
        {
            header("HTTP/1.1 400 Bad Request");
            $xml = '<status code="422">Invalid Operation</status>';
        }
        return $xml;
    }

    public function SSOCheck($aHCINFO)
    {
        $aClientIDs = array($this->_iClientId);
        $this->_aHTTP_CONN_INFO["mesb"] = $aHCINFO;
        $this->_aHTTP_CONN_INFO["mesb"]["path"]  = Constants::sMCONSOLE_SINGLE_SIGN_ON_PATH;
        $this->_aHTTP_CONN_INFO["mesb"]["username"] = trim($_SERVER['PHP_AUTH_USER']);
        $this->_aHTTP_CONN_INFO["mesb"]["password"] = trim($_SERVER['PHP_AUTH_PW']);
        $obj_ConnInfo = HTTPConnInfo::produceConnInfo($this->_aHTTP_CONN_INFO["mesb"]);
        $code = $this->_obj_mConsole->singleSignOn($obj_ConnInfo, $_SERVER['HTTP_X_AUTH_TOKEN'], mConsole::sPERMISSION_GET_CLIENTS, $aClientIDs, $_SERVER['HTTP_VERSION']);
        return $code;
    }

    public function getSSOValidationError($code)
    {
        $xml = '';
        switch ($code)
        {
            case (mConsole::iSERVICE_CONNECTION_TIMEOUT_ERROR):
                header("HTTP/1.1 504 Gateway Timeout");
                $xml = '<status code="'. $code .'">Single Sign-On Service is unreachable</status>';
                break;
            case (mConsole::iSERVICE_READ_TIMEOUT_ERROR):
                header("HTTP/1.1 502 Bad Gateway");
                $xml = '<status code="'. $code .'">Single Sign-On Service is unavailable</status>';
                break;
            case (mConsole::iUNAUTHORIZED_USER_ACCESS_ERROR):
                header("HTTP/1.1 401 Unauthorized");
                $xml = '<status code="'. $code .'">Unauthorized User Access</status>';
                break;
            case (mConsole::iINSUFFICIENT_USER_PERMISSIONS_ERROR):
                header("HTTP/1.1 403 Forbidden");
                $xml = '<status code="'. $code .'">Insufficient User Permissions</status>';
                break;
            case (mConsole::iINSUFFICIENT_CLIENT_LICENSE_ERROR):
                header("HTTP/1.1 402 Payment Required");
                $xml = '<status code="'. $code .'">Insufficient Client License</status>';
                break;
            default:
                header("HTTP/1.1 500 Internal Server Error");
                $xml = '<status code="'. $code .'">Internal Error</status>';
                break;
        }
        return $xml;
    }

    public function getRequestValidationError($obj_DOM)
    {
        $xml = '';
        // Error: Invalid XML Document
        if ( ($obj_DOM instanceof SimpleDOMElement) === false)
        {
            header("HTTP/1.1 415 Unsupported Media Type");
            $xml = '<status code="415">Invalid XML Document</status>';
        }
        // Error: Wrong operation
        elseif (count($obj_DOM->{'update-fraud-status'}) == 0)
        {
            header("HTTP/1.1 400 Bad Request");
            $xml = '<status code="400">Wrong operation empty request</status>';
        }
        // Error: Invalid Input
        else
        {
            header("HTTP/1.1 400 Bad Request");
            $aObj_Errs = libxml_get_errors();
            $xml = '';
            for ($i=0; $i<count($aObj_Errs); $i++)
            {
                $xml = '<status code="400">'. htmlspecialchars($aObj_Errs[$i]->message, ENT_NOQUOTES) .'</status>';
            }
        }
        return $xml;
    }

}