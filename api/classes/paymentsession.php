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

    private string $_created;

    private $_aSessionAdditionalData;

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
            $this->_expire = date("Y-m-d H:i:s.u", time() + (30 * 60));
        }
        $this->_obj_CurrencyConfig = $currencyConfig;
        $currencyConfigId = $this->_obj_CurrencyConfig->getId();
        if(empty($currencyConfigId) === true || ($this->_obj_CurrencyConfig instanceof CurrencyConfig) == false) {
            $this->_obj_CurrencyConfig = CurrencyConfig::produceConfig($this->_obj_Db, $this->_iCurrencyId);
        }
        try {
            $sql = "INSERT INTO Log" . sSCHEMA_POSTFIX . ".session_tbl 
                    (clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, sessiontypeid, externalid, expire) 
                VALUES 
                    ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13) RETURNING id;";

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
            $res = $this->_obj_Db->executeQuery($sql, $aParams);
            
            if ($res === false) {
                throw new Exception("Fail to create session", E_USER_ERROR);
            }

            $result = $this->_obj_Db->fetchName($res);
            $this->_id = $result["ID"];

        } catch (Exception $e) {
            trigger_error ( "Failed to create a new session" . $e->getMessage(), E_USER_ERROR );
        }
    }

    private function getSession($sessionid)
    {
        $sql = "SELECT * FROM log" . sSCHEMA_POSTFIX . ".session_tbl WHERE id = " . (int)$sessionid;

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
            $this->_created = $RS["CREATED"];

            if(($this->_obj_CurrencyConfig instanceof CurrencyConfig) == false) {
                $this->_obj_CurrencyConfig = CurrencyConfig::produceConfig($this->_obj_Db, $this->_iCurrencyId);
            }
            $this->_aSessionAdditionalData = self::_produceSessionAdditionalData($this->_obj_Db, $this->_id, $this->_created);
            /* $RS["MOBILE"];
             $RS["DEVICEID"];
             $RS["IPADDRESS"];*/
        }

    }

    public static function Get()
    {
     //   if (empty(self::$instance) === true) {
            self::$instance = new PaymentSession(func_get_args());
      //  }

        return self::$instance;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function updateState(int $stateId = null)
    {
        if ($stateId == null)
        {
            $iPendingAmt = $this->getPendingAmount();
            if ($iPendingAmt == 0)
            {
                $paymentAcceptStates = array(Constants::iPAYMENT_ACCEPTED_STATE, Constants::iPAYMENT_CAPTURED_STATE, Constants::iPAYMENT_WITH_VOUCHER_STATE);
                if ($this->getTransactionStatesWithAncillary($paymentAcceptStates , $paymentAcceptStates ) == true) { $stateId = Constants::iSESSION_COMPLETED; }
                elseif ($this->getTransactionStatesWithAncillary($paymentAcceptStates , array(Constants::iPAYMENT_REJECTED_STATE)) == true) { $stateId = Constants::iSESSION_PARTIALLY_COMPLETED; }
                elseif ($this->getTransactionStatesWithAncillary($paymentAcceptStates ) == true) { $stateId = Constants::iSESSION_COMPLETED; }
            }
            elseif ($iPendingAmt != 0 && $this->getExpireTime() < date("Y-m-d H:i:s.u", time())) { $stateId = Constants::iSESSION_EXPIRED; }
            elseif ($iPendingAmt != 0)
            {
                if ($iPendingAmt != $this->getAmount() && $this->getTransactionStates(Constants::iPAYMENT_ACCEPTED_STATE) == true) {
                    $stateId = Constants::iSESSION_PARTIALLY_COMPLETED;
                }

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

            if ($this->isValidStateForLogging($stateId)) {
                $this->_iStateId = $stateId;
                $sql = "UPDATE log" . sSCHEMA_POSTFIX . ".session_tbl SET stateid = ".$stateId." WHERE id = " . $this->_id;
                $RS1 = $this->_obj_Db->query($sql);
                if (is_resource($RS1) === true)
                {
                    if($stateId === Constants::iSESSION_EXPIRED || $stateId === Constants::iSESSION_FAILED || $stateId === Constants::iSESSION_FAILED_MAXIMUM_ATTEMPTS)
                    {
                        if($this->getSessionType() > 1) {
                            $isManualRefund = General::xml2bool($this->getClientConfig()->getAdditionalProperties(Constants::iInternalProperty, "IS_MANUAL_REFUND"));
                            global $_OBJ_TXT;
                            $obj_general = new General($this->_obj_Db, $_OBJ_TXT);
                            $obj_general->changeSplitSessionStatus($this->getClientConfig()->getID(), $this->getId(), 'Failed', $isManualRefund);
                        }
                    }
                    elseif($stateId === Constants::iSESSION_COMPLETED)
                    {
                        global $_OBJ_TXT;
                        $obj_general = new General($this->_obj_Db, $_OBJ_TXT);
                        $obj_general->changeSplitSessionStatus($this->getClientConfig()->getID(), $this->getId(), 'Completed');
                    }
                    return 1;
                }
            }
        }
        return 0;
    }

    public function checkSessionCompletion() {
        $result = FALSE;
        $query = "SELECT COUNT(T.ID) FROM  LOG" . sSCHEMA_POSTFIX . ".TRANSACTION_TBL T
                  INNER JOIN LOG" . sSCHEMA_POSTFIX . ".MESSAGE_TBL M
                  ON (T.ID=M.TXnID AND M.STATEID in (".Constants::iSESSION_COMPLETED.",".Constants::iSESSION_EXPIRED.",".Constants::iSESSION_FAILED." ) AND SESSIONID=".$this->_id.")";
        $RS = $this->_obj_Db->getName($query);
        if(is_array($RS) === true) {
            if($RS["COUNT"] > 0) {
                $result = TRUE;
            }
        }
        return $result;
    }

    public function getSessionType()
    {
        return $this->_sessionTypeId;
    }

    public function getPendingAmount()
    {
        try
        {
            $amount = 0;
            if (empty($this->_id) === false)
            {
                $sql = "SELECT * FROM (SELECT  txn.id, txn.amount,msg.stateid ,rank() over(partition by msg.txnid order by msg.id desc) as rn
              FROM log" . sSCHEMA_POSTFIX . ".transaction_tbl txn 
                INNER JOIN log" . sSCHEMA_POSTFIX . ".message_tbl msg ON txn.id = msg.txnid 
              WHERE sessionid = " . $this->_id . " 
                AND txn.id not in (SELECT Sdt.Transaction_Id
                                    FROM Log.Split_Details_Tbl AS Sdt
                                             INNER JOIN Log.Split_Session_Tbl Sst ON Sst.Id = Sdt.Split_Session_Id
                                    WHERE Sessionid = " . $this->_id . "
                                      AND Sst.Status = 'failed')
                AND msg.stateid in (".Constants::iPAYMENT_PENDING_STATE.",".Constants::iPAYMENT_ACCEPTED_STATE.",".Constants::iPOST_FRAUD_CHECK_REJECTED_STATE.",".Constants::iPAYMENT_REFUNDED_STATE.",".Constants::iPAYMENT_CANCELLED_STATE.")) s where s.rn =1 and s.stateid not in (".Constants::iPOST_FRAUD_CHECK_REJECTED_STATE.",".Constants::iPAYMENT_REFUNDED_STATE.",".Constants::iPAYMENT_CANCELLED_STATE.")
                ";

                $res = $this->_obj_Db->query($sql);
                while ($RS = $this->_obj_Db->fetchName($res)) {
                    $amount += (int)$RS['AMOUNT'];
                }
            }
            return $this->_amount  - $amount;
        }
        catch (Exception $e){
            trigger_error ( "Session - ." . $e->getMessage(), E_USER_ERROR );
        }
    }

    public function updateTransaction($txnId)
    {
        $sql = "UPDATE log" . sSCHEMA_POSTFIX . ".transaction_tbl SET sessionid = " . $this->_id . " WHERE id = " . (int)$txnId ." and SESSIONID ISNULL";
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
        $xml .= '<amount country-id="'. $this->getCountryConfig()->getID() .'" currency-id="'. $this->getCurrencyConfig()->getID() .'" currency="'.$this->getCurrencyConfig()->getCode() .'" symbol="'. $this->getCurrencyConfig()->getSymbol() .'" format="'. $this->getCountryConfig()->getPriceFormat() .'" alpha2code="'. $this->getCountryConfig()->getAlpha2code() .'" alpha3code="'. $this->getCountryConfig()->getAlpha3code() .'" code="'. $this->getCountryConfig()->getNumericCode() .'">'. $this->getPendingAmount() .'</amount>';
        $xml .= '<status>'. $this->getStateId() .'</status>';
        $xml .= "</session>";
        return $xml;
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
            //DateTime check is added to improve the performance of query
            //Session is created immediately after transaction is created
            $primaryProdBtwnCondition = " BETWEEN ". Constants::iPrimaryProdTypeBase ." AND ". Constants::iPrimaryProdTypeBase." + 99";
            $sql = "SELECT COUNT(txn.id) AS CNT FROM log" . sSCHEMA_POSTFIX . ".message_tbl msg
                    INNER JOIN log" . sSCHEMA_POSTFIX . ".transaction_tbl txn ON txn.id = msg.txnid
                    WHERE sessionid = " . $this->_id . " and txn.created >= ('". $this->_created ."'::date -  INTERVAL '30 min')
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

    public function getTransactions()
    {
        $aTransaction = [];
        try
        {
            $sql = "SELECT * FROM ( SELECT txn.id,msg.stateid,rank() over(partition by msg.txnid order by msg.id desc) as rn FROM log" . sSCHEMA_POSTFIX . ".Transaction_tbl txn
                    INNER JOIN log" . sSCHEMA_POSTFIX . ".message_tbl msg ON txn.id = msg.txnid  
                    WHERE sessionid = " . $this->getId() ." AND txn.created >= ('" . $this->_created . "'::date -  INTERVAL '30 min')) s where s.rn = 1 and s.stateid != ".Constants::iTRANSACTION_CREATED;
            $aRS = $this->_obj_Db->getAllNames($sql);
            if (is_array($aRS) === true)
            {
                foreach ($aRS as $rs)
                {
                    array_push($aTransaction, $rs['ID']);
                }
            }
        }
        catch (mPointException $mPointException)
        {
            trigger_error ( "Get Transactions From Session error - ." . $mPointException->getMessage(), E_USER_ERROR );
        }
        return $aTransaction;
    }

    /**
     * @param string $whereClause
     *
     * @return array
     */
    public function getFilteredTransaction(string $whereClause = ''): array
    {
        $aTransaction = [];
        $sql ='';
        try {
            //DateTime check is added to improve the performance of query
            //Session is created immediately after transaction is created
            $sql = "SELECT transaction_tbl.id FROM log" . sSCHEMA_POSTFIX . ".message_tbl message_tbl
            INNER JOIN log" . sSCHEMA_POSTFIX . ".transaction_tbl transaction_tbl ON transaction_tbl.id = message_tbl.txnid
            WHERE sessionid = " . $this->_id . " and transaction_tbl.created >= ('" . $this->_created . "'::date -  INTERVAL '30 min')
            AND " . $whereClause;
            $aRS = $this->_obj_Db->getAllNames($sql);
            if (is_array($aRS) === TRUE) {
                foreach ($aRS as $rs) {
                    array_push($aTransaction, $rs['ID']);
                }
            }
        }
        catch (Exception $e) {
            trigger_error("Session Get Filtered Transaction query {$sql} failed - " . $e->getMessage() , E_USER_WARNING);
        }
        return $aTransaction;
    }

    private function isValidStateForLogging($sessionState) : bool
    {
        $currentState = $this->_iStateId;
        switch ($sessionState) {
            case 4020 :
            case 4021 :
            case 4030 :
            case 4010 :
                if (in_array($currentState, [4030, 4010, 4021, 4020]))
                    return false;
                break;
            case 4031 :
                if ($currentState != 4001)
                    return false;
                break;
            default :
                return false;
        }
        return true;
    }

    /*
     * Returns the Session's Additional data
     * if param is sent returns value of property
     *
     * @param string    key
     * @return 	string
     * */
    public function getSessionAdditionalData($key = "")
    {
        try
        {
            if (empty($key) === true)
            {
                if (is_array($this->_aSessionAdditionalData) && count($this->_aSessionAdditionalData) > 0)
                {
                    return $this->_aSessionAdditionalData;
                }
                return null;
            }
            if (is_array($this->_aSessionAdditionalData) && $this->_aSessionAdditionalData != null && array_key_exists($key, $this->_aSessionAdditionalData) === true)
            {
                return $this->_aSessionAdditionalData[$key];
            }
        }
        catch (Exception $e)
        {

        }
        return null;
    }

    /**
     * Function to insert new records in the Additional Data table that are send as part of the session
     *
     * @param 	Array $sessionAdditionalData	Data object with the Additional Data details
     *
     */
    public function setSessionAdditionalDetails(RDB $obj_DB, $sessionAdditionalData, $ExternalID)
    {
        $additional_id = "";
        if( is_array($sessionAdditionalData) === true )
        {
            foreach ($sessionAdditionalData as $aAdditionalDataObj)
            {
                $name = $aAdditionalDataObj["name"];
                $value = $aAdditionalDataObj["value"];
                if($name === null || empty($name) === true || $value === null || empty($value) === true)
                {
                    return $additional_id;
                }
                $sql = "INSERT INTO log".sSCHEMA_POSTFIX.".additional_data_tbl(name, value, type, externalid)
								VALUES('". $aAdditionalDataObj["name"] ."', '". $aAdditionalDataObj["value"] ."', '". $aAdditionalDataObj["type"] ."','". $ExternalID ."') RETURNING id";
                // Error: Unable to insert a new Additional Data record in the Additional Data Table
                if (is_resource($res = $obj_DB->query($sql) ) === false)
                {
                    throw new mPointException("Unable to insert new record for Additional Data: ". $RS["ID"], 1002);
                }
                else
                {
                    $RS = pg_fetch_assoc($res);
                    $additional_id = $RS["id"];
                    $this->_aSessionAdditionalData[$name] = $value;
                }
            }
            return $additional_id;
        }
    }

    public static function  _produceSessionAdditionalData($_OBJ_DB, $txnId, $sessionCreatedTimestamp)
    {
        $additionalData = [];

        $sqlA = "SELECT name, value FROM log" . sSCHEMA_POSTFIX . ".additional_data_tbl WHERE type='Session' and created >= to_timestamp('" . $sessionCreatedTimestamp  . "', 'YYYY-MM-DD HH24-MI-SS.US') and externalid=" . $txnId;

        $rsa = $_OBJ_DB->getAllNames ( $sqlA );
        if (empty($rsa) === false )
        {
            foreach ($rsa as $rs)
            {
                $additionalData[$rs["NAME"] ] = $rs ["VALUE"];
            }
        }
        return $additionalData;
    }

    function updateSessionTypeId($amount)
    {
        if ($amount < $this->_amount)
        {
            $sql = "UPDATE log" . sSCHEMA_POSTFIX . ".Session_tbl SET sessiontypeid = 2 where id = ".$this->_id . " and sessiontypeid = 1";
            $this->_obj_Db->query($sql);
        }else if($amount == $this->_amount){
            $sql = "UPDATE log" . sSCHEMA_POSTFIX . ".Session_tbl SET sessiontypeid = 1 where id = ".$this->_id . " and sessiontypeid = 2";
            $this->_obj_Db->query($sql);
        }
    }

}