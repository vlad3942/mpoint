<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:paymentsession.php
 */

final class PaymentSession
{
    private $_id;

    protected static $instance;

    private $_obj_ClientConfig;

    private $_obj_CountryConfig;

    private $_obj_CurrencyConfig;

    private $_amount;

    private $_orderId;

    private $_externalId;

    private $_sessionTypeId;

    private $_obj_Db;

    private $_iClientId;

    private $_iAccountId;

    private $_iCountryId;

    private $_iCurrencyId;

    private $_iStateId = 4001;

    private $_sMobile;

    private $_sEmail;

    private $_sIp;

    private $_sDeviceId;

    private $_pendingAmount;

    private $_expire;

    protected function __construct()
    {
        $args = func_get_args();
        $aArgs = $args[0];
        $this->_obj_Db = $aArgs[0];
        switch (count($aArgs)) {
            case 2:
                $this->getSession($aArgs[1]);
                break;
            case 9:
                $this->createSession($aArgs[1], $aArgs[2], $aArgs[3], $aArgs[4], $aArgs[5], $aArgs[6], $aArgs[7], $aArgs[8], null, null, null);
                break;
            case 10:
                $this->createSession($aArgs[1], $aArgs[2], $aArgs[3], $aArgs[4], $aArgs[5], $aArgs[6], $aArgs[7], $aArgs[8], $aArgs[9], null, null);
                break;
            case 11:
                $this->createSession($aArgs[1], $aArgs[2], $aArgs[3], $aArgs[4], $aArgs[5], $aArgs[6], $aArgs[7], $aArgs[8], $aArgs[9], $aArgs[10], null);
                break;
            case 12:
                $this->createSession($aArgs[1], $aArgs[2], $aArgs[3], $aArgs[4], $aArgs[5], $aArgs[6], $aArgs[7], $aArgs[8], $aArgs[9], $aArgs[10], $aArgs[11]);
                break;
            case 13:
                $this->createSession($aArgs[1], $aArgs[2], $aArgs[3], $aArgs[4], $aArgs[5], $aArgs[6], $aArgs[7], $aArgs[8], $aArgs[9], $aArgs[10], $aArgs[11], $aArgs[12]);
                break;
        }
    }

    private function createSession(ClientConfig $clientConfig, CountryConfig $countryConfig, CurrencyConfig $currencyConfig, $amount, $orderid, $sessiontypeid, $mobile, $email, $externalId, $deviceid, $ipaddress, $expire=null)
    {

        $this->_obj_ClientConfig = $clientConfig;
        $this->_obj_CountryConfig = $countryConfig;

        $this->_orderId = $orderid;
        $this->_amount = $amount;
        $this->_externalId = $externalId;
        $this->_sessionTypeId = $sessiontypeid;
        $this->_iAccountId = $clientConfig->getAccountConfig()->getID();
        $this->_iClientId = $clientConfig->getID();
        $this->_iCurrencyId = $currencyConfig->getID();
        if (isset($this->_iCurrencyId) === false || $this->_iCurrencyId == 0)
            $this->_iCurrencyId = $countryConfig->getCurrencyConfig()->getID();
        $this->_iCountryId = $countryConfig->getID();

        $this->_sDeviceId = $deviceid;
        $this->_sEmail = $email;
        $this->_sIp = $ipaddress;
        $this->_sMobile = $mobile;
        if ($expire != null) {
            $this->_expire = $expire;
        } else {
            $this->_expire = date("Y-m-d H:i:s.u", time() + (15 * 60));
        }
        $this->_obj_CurrencyConfig = $currencyConfig;
        if(empty($this->_obj_CurrencyConfig->getId() === true) || ($this->_obj_CurrencyConfig instanceof CurrencyConfig) == false) {
            $this->_obj_CurrencyConfig = CurrencyConfig::produceConfig($this->_obj_Db, $this->_iCurrencyId);
        }
        // New session will not be generated, if the session is partially complete(4031) for same order id. 
        if ($this->updateSessionDataFromOrderId() != true) {
            try {
                $sql = "INSERT INTO Log" . sSCHEMA_POSTFIX . ".session_tbl 
                        (clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, sessiontypeid, externalid, expire) 
                    VALUES 
                        ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13) RETURNING id;";

                $res = $this->_obj_Db->prepare($sql);
                if (is_resource($res) === true) {
                    $aParams = array(
                        $this->_iClientId,
                        $this->_iAccountId,
                        $this->_iCurrencyId,
                        $this->_iCountryId,
                        '4001',
                        $orderid,
                        $amount,
                        $this->_sMobile,
                        $this->_sDeviceId,
                        $this->_sIp,
                        $sessiontypeid,
                        $externalId,
                        $this->_expire
                    );

                    $result = $this->_obj_Db->execute($res, $aParams);

                    if ($result === false) {
                        throw new Exception("Fail to create session", E_USER_ERROR);
                    } else {
                        $RS = $this->_obj_Db->fetchName($result);
                        $this->_id = $RS["ID"];
                    }
                }
            } catch (Exception $e) {
                trigger_error ( "Failed to create a new session" . $e->getMessage(), E_USER_ERROR );
            }
        }

    }

    private function getSession($sessionid)
    {
        $sql = "SELECT * FROM log" . sSCHEMA_POSTFIX . ".session_tbl WHERE id = " . intval($sessionid);

        $RS = $this->_obj_Db->getName($sql);
        if (is_array($RS) === true) {

            $this->_orderId = $RS["ORDERID"];
            $this->_amount = $RS["AMOUNT"];
            $this->_externalId = $RS["EXTERNALID"];
            $this->_sessionTypeId = $RS["SESSIONTYPEID"];
            $this->_iAccountId = $RS["ACCOUNTID"];
            $this->_iClientId = $RS["CLIENTID"];
            $this->_iCurrencyId = $RS["CURRENCYID"];
            $this->_iCountryId = $RS["COUNTRYID"];
            $this->_iStateId = $RS["STATEID"];
            $this->_id=$RS["ID"];
            $this->_expire = $RS["EXPIRE"];
            /* $RS["MOBILE"];
             $RS["DEVICEID"];
             $RS["IPADDRESS"];*/
        }

    }

    public static function Get()
    {
        if ((self::$instance instanceof PaymentSession) === false) {
            //self::$instance = call_user_func("__construct", func_get_args());
            self::$instance = new PaymentSession(func_get_args());
        }

        return self::$instance;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function updateState($stateId = null)
    {
        if ($stateId == null) {
            if ($this->getPendingAmount() == 0) {

                $paymentAcceptStates = array(Constants::iPAYMENT_ACCEPTED_STATE, Constants::iPAYMENT_CAPTURED_STATE, Constants::iPAYMENT_WITH_VOUCHER_STATE,Constants::iPAYMENT_WITH_ACCOUNT_STATE);

                if ($this->getTransactionStatesWithAncillary($paymentAcceptStates , $paymentAcceptStates ) == true) {
                    $stateId = Constants::iSESSION_COMPLETED;
                } elseif ($this->getTransactionStatesWithAncillary($paymentAcceptStates , array(Constants::iPAYMENT_REJECTED_STATE)) == true) {
                    $stateId = Constants::iSESSION_PARTIALLY_COMPLETED;
                } elseif ($this->getTransactionStatesWithAncillary($paymentAcceptStates ) == true) {
                    $stateId = Constants::iSESSION_COMPLETED;
                }
            } elseif ($this->getPendingAmount() != 0) {
                if ($this->getTransactionStates(Constants::iPAYMENT_ACCEPTED_STATE) == true) {
                    $stateId = Constants::iSESSION_PARTIALLY_COMPLETED;
                } elseif ($this->getTransactionStates(Constants::iPAYMENT_REJECTED_STATE) == true) {
                    $stateId = Constants::iSESSION_FAILED;
                }
            } elseif ($this->getPendingAmount() != 0 && $this->getExpireTime() < date("Y-m-d H:i:s.u", time())) {
                $stateId = Constants::iSESSION_EXPIRED;
            }
        }
        if($stateId != null)
        {
            $checkState = "SELECT count(id) FROM log" . sSCHEMA_POSTFIX . ".session_tbl WHERE  stateid = ".$stateId." and id =". $this->_id;

            $RS = $this->_obj_Db->getName($checkState);
            if (is_array($RS) === true)
            {
                if($RS["COUNT"] != 0)
                {
                    return 2;
                }
            }

            $this->_iStateId = intval($stateId);
            $sql = "UPDATE log" . sSCHEMA_POSTFIX . ".session_tbl SET stateid = ".$stateId." WHERE id = " . $this->_id;
            $RS1 = $this->_obj_Db->query($sql);
            if (is_resource($RS1) === true)
            {
                return 1;
            }
        }
        return 0;
    }

    public function getSessionType()
    {
        return $this->_sessionTypeId;
    }

    public function getPendingAmount()
    {
        try {
            $amount = 0;
            if (empty($this->_id) === false) {
                $sql = "SELECT  DISTINCT txn.id,  txn.amount 
              FROM log" . sSCHEMA_POSTFIX . ".transaction_tbl txn 
                INNER JOIN log" . sSCHEMA_POSTFIX . ".message_tbl msg ON txn.id = msg.txnid 
              WHERE sessionid = " . $this->_id . " 
                AND msg.stateid in (2000,2001,2007,2010,2011)
                GROUP BY txn.id,msg.stateid";
                //return $this->_pendingAmount;
                $res = $this->_obj_Db->query($sql);
                while ($RS = $this->_obj_Db->fetchName($res)) {
                    $amount = ($amount + intval($RS['AMOUNT']));
                }
            }
            return $this->_amount - $amount;
        }
        catch (Exception $e){
            trigger_error ( "Session - ." . $e->getMessage(), E_USER_ERROR );
        }
    }

    public function updateTransaction($txnId)
    {
        $sql = "UPDATE log" . sSCHEMA_POSTFIX . ".transaction_tbl SET sessionid = " . $this->_id . " WHERE id = " . intval($txnId);
        $this->_obj_Db->query($sql);
    }

    public function getClientConfig(){
        if(($this->_obj_ClientConfig instanceof ClientConfig) == false) {
            $this->_obj_ClientConfig = ClientConfig::produceConfig($this->_obj_Db, $this->_iClientId, $this->_iAccountId);
        }
        return $this->_obj_ClientConfig;
    }

    public function getCountryConfig(){
        if(($this->_obj_CountryConfig instanceof CountryConfig) == false) {
            $this->_obj_CountryConfig = CountryConfig::produceConfig($this->_obj_Db, $this->_iCountryId);
        }
        return $this->_obj_CountryConfig;
    }

    public function getCurrencyConfig(){
        return $this->_obj_CurrencyConfig;
    }

    public function toXML(){
        $xml = "<session id='".$this->getId()."' type='".$this->getSessionType()."' total-amount='".$this->_amount."'>";
        $xml .= '<amount country-id="'. $this->getCountryConfig()->getID() .'" currency-id="'. $this->getCurrencyConfig()->getID() .'" currency="'.$this->getCurrencyConfig()->getCode() .'" symbol="'. $this->getCountryConfig()->getSymbol() .'" format="'. $this->getCountryConfig()->getPriceFormat() .'" alpha2code="'. $this->getCountryConfig()->getAlpha2code() .'" alpha3code="'. $this->getCountryConfig()->getAlpha3code() .'" code="'. $this->getCountryConfig()->getNumericCode() .'">'. $this->getPendingAmount() .'</amount>';
        $xml .= "</session>";
        return $xml;
    }

    private function updateSessionDataFromOrderId()
    {
        $status = false;
        try {
            $sql = "SELECT id, amount, stateid FROM log" . sSCHEMA_POSTFIX . ".session_tbl
                    WHERE orderid = '" . $this->_orderId . "'
                    AND stateid = '" . Constants::iSESSION_PARTIALLY_COMPLETED . "'
                    ORDER BY id DESC LIMIT 1";

            $RS = $this->_obj_Db->getName($sql);
            if (is_array($RS) === true) {
                $this->_id = $RS["ID"];
                $status = true;
            }
        } catch (Exception $e) {
            trigger_error ( "Update Session Data - ." . $e->getMessage(), E_USER_ERROR );
        }
        return $status;
    }

    public function getAmount()
    {
        return $this->_amount;
    }

    public function getStateId()
    {
        return $this->_iStateId;
    }

    public function getExpireTime()
    {
        return $this->_expire;
    }


    public function getTransactionStates($stateId)
    {
        $status = false;
        try {
            $primaryProdBtwnCondition = " BETWEEN ". Constants::iPrimaryProdTypeBase ." AND ". Constants::iPrimaryProdTypeBase." + 99";
            $sql = "SELECT COUNT(txn.id) AS CNT FROM log" . sSCHEMA_POSTFIX . ".message_tbl msg
                    INNER JOIN log" . sSCHEMA_POSTFIX . ".transaction_tbl txn ON txn.id = msg.txnid
                    WHERE sessionid = " . $this->_id . "
                    AND (msg.stateid = ".$stateId." AND txn.productType".$primaryProdBtwnCondition.") LIMIT 1";
            $RS = $this->_obj_Db->getName($sql);
            if (is_array($RS) === true) {
                if($RS["CNT"] > 0) {
                    $status = true;
                }
            }
        }
        catch (Exception $e){
            trigger_error ( "Session Get Transaction States- ." . $e->getMessage(), E_USER_ERROR );
        }
        return $status;
    }

    public function getTransactionStatesWithAncillary($ticketState, $ancillaryState = null)
    {
        $status = false;
        try {
            $primaryProdBtwnCondition = " BETWEEN ". Constants::iPrimaryProdTypeBase ." AND ". Constants::iPrimaryProdTypeBase." + 99";
            $ancillaryProdBtwnCondition = " BETWEEN ". Constants::iAncillaryProdTypeBase ." AND ". Constants::iAncillaryProdTypeBase." + 99";
            if ($ancillaryState != null) {
                $sql = "SELECT COUNT( DISTINCT txn.id) AS CNT FROM log" . sSCHEMA_POSTFIX . ".message_tbl msg
                    INNER JOIN log" . sSCHEMA_POSTFIX . ".transaction_tbl txn ON txn.id = msg.txnid
                    WHERE sessionid = " . $this->_id . "
                    AND (msg.stateid in ('" . implode("', '", $ticketState). "') AND txn.productType".$primaryProdBtwnCondition.")
                    AND (msg.stateid in ('" . implode("', '", $ancillaryState ) . "') AND txn.productType".$ancillaryProdBtwnCondition.") LIMIT 1";
            } else {
                $sql = "SELECT COUNT(txn.id) AS CNT FROM log" . sSCHEMA_POSTFIX . ".message_tbl msg
                    INNER JOIN log" . sSCHEMA_POSTFIX . ".transaction_tbl txn ON txn.id = msg.txnid
                    WHERE sessionid = " . $this->_id . "
                    AND (msg.stateid in ('" . implode("', '", $ticketState)  . "') AND txn.productType".$primaryProdBtwnCondition.") LIMIT 1";
            }
            $RS = $this->_obj_Db->getName($sql);
            if (is_array($RS) === true) {
                if($RS["CNT"] != 0) {
                    $status = true;
                }
            }
        }
        catch (Exception $e){
            trigger_error ( "Session Get Transaction States- ." . $e->getMessage(), E_USER_ERROR );
        }
        return $status;
    }

    public function getSessionCallbackData()
    {
        $data = '';
        try {
            $sql = "SELECT msg.data FROM log" . sSCHEMA_POSTFIX . ".message_tbl msg
                    INNER JOIN log" . sSCHEMA_POSTFIX . ".transaction_tbl txn ON txn.id = msg.txnid
                    WHERE sessionid = " . $this->_id . "
                    AND msg.stateid IN (".Constants::iSESSION_PARTIALLY_COMPLETED.", ".Constants::iSESSION_FAILED.") ORDER BY msg.id DESC  LIMIT 1";
            $RS = $this->_obj_Db->getName($sql);
            if (is_array($RS) === true) {
                $data = strchr($RS['DATA'],"transaction-data");
            }
        }
        catch (Exception $e){
            trigger_error ( "Session CallBack- ." . $e->getMessage(), E_USER_ERROR );
        }
        return $data;
    }
}