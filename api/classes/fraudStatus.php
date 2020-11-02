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

    public function SSOCheck()
    {
        $aClientIDs = array($this->_iClientId);
        $this->_aHTTP_CONN_INFO["mconsole"]["path"] = $this->_aHTTP_CONN_INFO["mconsole"]["paths"]['single-sign-on'];
        $this->_aHTTP_CONN_INFO["mconsole"]["username"] = trim($_SERVER['PHP_AUTH_USER']);
        $this->_aHTTP_CONN_INFO["mconsole"]["password"] = trim($_SERVER['PHP_AUTH_PW']);
        $obj_ConnInfo = HTTPConnInfo::produceConnInfo($this->_aHTTP_CONN_INFO["mconsole"]);
        $code = $this->_obj_mConsole->singleSignOn($obj_ConnInfo, $_SERVER['HTTP_X_AUTH_TOKEN'], mConsole::sPERMISSION_GET_CLIENTS, $aClientIDs, $_SERVER['HTTP_VERSION']);
        return $code;
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