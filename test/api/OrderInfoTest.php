<?php
/**
 * Created by VS code
 * User: Chaitenya Yadav
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package:
 * File Name:HomeTest.php
 */

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once sAPI_CLASS_PATH . 'simpledom.php';


class OrderInfoTest extends baseAPITest
{

    private $_OBJ_DB;

    public function setUp():void
    {
        parent::setUp(TRUE);
        $this->bIgnoreErrors = true;
        $this->_OBJ_DB = RDB::produceDatabase($this->mPointDBInfo);
    }

   public function testProduceConfigurationsFromOrderID()
    {
        $iTxnID = 1001001;
        $iClientID = 10099;
        $iAccountID = 1100;
        $iCurrencyID = 840;
        $iAmount = 5000;
        $sOrderId = 'CY360';
        
        $data = array();

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES ($iClientID, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES ($iClientID, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES ($iAccountID, $iClientID)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, $iClientID, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, $iClientID, 18, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES ($iAccountID, 18, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES ($iClientID, 2, 18, true, 1)");
        $this->queryDB("INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid, enabled) VALUES ($iClientID,100,$iCurrencyID, true)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES ($iClientID, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 18, '5019********3742', '06/24', TRUE, $iClientID, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, $iClientID, $iAccountID, 208, 100, 4001, '103-1418291', $iAmount, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid) VALUES ($iTxnID, 100, $iClientID, $iAccountID, 1,  18, 5001, 100, '". $sOrderId. "', 'test.com', $iAmount, '127.0.0.1', TRUE,10)");
        $this->queryDB("INSERT INTO log.order_tbl (id, txnid, countryid, amount, productsku, productname, productdescription, productimageurl, points, reward, quantity, enabled, orderref, fees) VALUES (23940, $iTxnID, 100, $iAmount, 'product-ticket', 'ONE WAY', 'ONE WAY', '', 0, 0, 1, true, '". $sOrderId."', 0)");
        $this->queryDB("INSERT INTO log.flight_tbl (id, service_class, departure_airport, arrival_airport, airline_code, order_id, arrival_date, departure_date, flight_number, tag, trip_count, service_level, departure_countryid, arrival_countryid, time_zone) VALUES (36174, 'A', 'MNL', 'DVO', '5J', 23940, '2021-06-09 06:30:00.000', '2021-06-09 04:30:00.000', '961', '1', '1', '3', 100, 100, '+08:00')");
        $this->queryDB("INSERT INTO log.passenger_tbl (id, first_name, last_name, type, order_id, title, email, mobile, country_id, amount) VALUES (41798, 'fname', 'lname', '', 23940, 'Mr', 'demo@cellpointmobile.com', '639123123123', '100', 0)");
             
        

        $obj_ClientConfig = ClientConfig::produceConfig($this->_OBJ_DB, (int) $iClientID, (int) $iAccountID);

        $obj_CurrencyConfig = CurrencyConfig::produceConfig($this->_OBJ_DB, (integer) $iCurrencyID);
        
        $data['typeid']= 1 ;
        $data['amount']= $iAmount ;
        $data['converted-amount']= $iAmount ;
        $data['currency-config']= $obj_CurrencyConfig ;
        $data['converted-currency-config']= $obj_CurrencyConfig ;
        $data['conversion-rate']= 1 ;
        $data['orderid']= $sOrderId;

        $this->_OBJ_TXNINFO = TxnInfo::produceInfo((int)$iTxnID,$this->_OBJ_DB, $obj_ClientConfig, $data);
        $aObj_OrderInfoConfigs = OrderInfo::produceConfigurationsFromOrderID($this->_OBJ_DB, $this->_OBJ_TXNINFO);
        $obj_OrderInfo = $aObj_OrderInfoConfigs[0];
        
        $this->assertIsObject($obj_OrderInfo);
        $this->assertObjectHasAttribute('_iID', $obj_OrderInfo);
    }

    public function tearDown():void
    {
        $this->_OBJ_DB->disConnect();
        parent::tearDown();
    }

}
