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
        }
    }

    private function createSession(ClientConfig $clientConfig, CountryConfig $countryConfig, CurrencyConfig $currencyConfig, $amount, $orderid, $sessiontypeid, $mobile, $email, $externalId, $deviceid, $ipaddress)
    {

        $this->_obj_ClientConfig = $clientConfig;
        $this->_obj_CountryConfig = $countryConfig;
        $this->_obj_CurrencyConfig = $currencyConfig;
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

        $sql = "INSERT INTO Log" . sSCHEMA_POSTFIX . ".session_tbl 
                    (clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, sessiontypeid, externalid) 
                VALUES 
                    ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12) RETURNING id;";

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
                $externalId
            );

            $result = $this->_obj_Db->execute($res, $aParams);

            if ($result === false) {
                throw new Exception("Fail to create session", E_USER_ERROR);
            } else {
                $RS = $this->_obj_Db->fetchName($result);
                $this->_id = $RS["ID"];
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

    public function updateState($stateId =null)
    {
        if($stateId == null && $this->getPendingAmount() == 0){
            $stateId = Constants::iSESSION_COMPLETED;
        }
        if($stateId != null)
        {
            $this->_iStateId = intval($stateId);
            $sql = "UPDATE log" . sSCHEMA_POSTFIX . ".session_tbl SET stateid = ".$stateId." WHERE id = " . $this->_id;
            $this->_obj_Db->query($sql);
        }
    }

    public function getSessionType()
    {
        return $this->_sessionTypeId;
    }

    public function getPendingAmount()
    {
        $sql="SELECT coalesce(sum(amount),0) as amount  
              FROM log.transaction_tbl txn 
                INNER JOIN log.message_tbl msg ON txn.id = msg.txnid 
              WHERE sessionid = ". $this->_id. " 
                AND msg.stateid in (2000,2001,2007,2008)";
        //return $this->_pendingAmount;
        $res = $this->_obj_Db->query($sql);
        $RS = $this->_obj_Db->fetchName($res);
        $amount = intval($RS['AMOUNT']);
        return $this->_amount - $amount;
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
        if(($this->_obj_CurrencyConfig instanceof CurrencyConfig) == false) {
            $this->_obj_CurrencyConfig = CurrencyConfig::produceConfig($this->_obj_Db, $this->_iCurrencyId);
        }
        return $this->_obj_CurrencyConfig;
    }

    public function toXML(){
        $xml = "<session id='".$this->getId()."' type='".$this->getSessionType()."'>";
        $xml .= '<amount country-id="'. $this->getCountryConfig()->getID() .'" currency-id="'. $this->getCurrencyConfig()->getID() .'" currency="'.$this->getCurrencyConfig()->getCode() .'" symbol="'. $this->getCountryConfig()->getSymbol() .'" format="'. $this->getCountryConfig()->getPriceFormat() .'" alpha2code="'. $this->getCountryConfig()->getAlpha2code() .'" alpha3code="'. $this->getCountryConfig()->getAlpha3code() .'" code="'. $this->getCountryConfig()->getNumericCode() .'">'. $this->getPendingAmount() .'</amount>';
        $xml .= "</session>";
        return $xml;
}

}