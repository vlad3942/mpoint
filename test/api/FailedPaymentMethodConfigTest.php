<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anna Lagad
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package:
 * File Name:RoutingServiceTest.php
 */

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once sAPI_CLASS_PATH . 'simpledom.php';
require_once __DIR__ . '/../../api/classes/failed_payment_method_config.php';

class FailedPaymentMethodConfigTest extends baseAPITest
{

    private $_OBJ_DB;
    protected $_aHTTP_CONN_INFO;

    public function setUp()
    {
        parent::setUp(TRUE);
        global $aHTTP_CONN_INFO;
        $this->bIgnoreErrors = true;
        $this->_aHTTP_CONN_INFO = $aHTTP_CONN_INFO;
        $this->_OBJ_DB = RDB::produceDatabase($this->mPointDBInfo);
    }


    public function testSuccessGetFailedPaymentMethods()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 18, '5019********3742', '06/24', TRUE, 113, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 2)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '1513-2001', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, cardid, extid, orderid, callbackurl, amount, ip, created, enabled, keywordid, fee, currencyid, sessionid,convertedamount,convetredcurrencyid) VALUES (1001004, 100, 113, 1100, 100, 18, 8, '1512', '1513-2001', '', 5000, '127.0.0.1', '2020-06-12 10:30:19', TRUE, 1, 50, 208, 1,5000,208)");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, cardid, extid, orderid, callbackurl, amount, ip, created, enabled, keywordid, fee, currencyid, sessionid,convertedamount,convetredcurrencyid) VALUES (1001005, 100, 113, 1100, 100, 17, 7, '1512', '1513-2001', '', 5000, '127.0.0.1', '2020-06-12 10:31:00', TRUE, 1, 50, 208, 1,5000,208)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001004, ". Constants::iINPUT_VALID_STATE. ")");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001004, ". Constants::iPAYMENT_INIT_WITH_PSP_STATE. ")");
        $sessionId = 1;
        $obj_FailedPaymentMethods = FailedPaymentMethodConfig::produceFailedTxnInfoFromSession($this->_OBJ_DB, $sessionId);
        $xml = '';
        if(count($obj_FailedPaymentMethods) > 0 )
        {
            $xml .= '<failed_payment_methods>';
            foreach ($obj_FailedPaymentMethods as $obj_FailedPaymentMethod)
            {
                if (($obj_FailedPaymentMethod instanceof FailedPaymentMethodConfig) === TRUE)
                {
                    $xml .= $obj_FailedPaymentMethod->toAttributeLessXML();
                }
            }
            $xml .= '<failed_payment_methods>';
        }
        $this->assertEquals(1, count($obj_FailedPaymentMethod));
        $this->assertContains('<failed_payment_methods><failed_payment_method><session_id>1</session_id><transaction_id>1001004</transaction_id><card_id>8</card_id><psp_id>18</psp_id><payment_state>1009</payment_state><card_category>1</card_category><psp_category>1</psp_category></failed_payment_method><failed_payment_methods>', $xml);
    }

    public function testGetFailedPaymentMethodsNegetiveScenario()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 18, '5019********3742', '06/24', TRUE, 113, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 2)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4030, '1513-2001', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, cardid, extid, orderid, callbackurl, amount, ip, created, enabled, keywordid, fee, currencyid, sessionid,convertedamount,convetredcurrencyid) VALUES (1001004, 100, 113, 1100, 100, 18, 8, '1512', '1513-2001', '', 5000, '127.0.0.1', '2020-06-12 10:30:19', TRUE, 1, 50, 208, 1,5000,208)");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, cardid, extid, orderid, callbackurl, amount, ip, created, enabled, keywordid, fee, currencyid, sessionid,convertedamount,convetredcurrencyid) VALUES (1001005, 100, 113, 1100, 100, 17, 7, '1512', '1513-2001', '', 5000, '127.0.0.1', '2020-06-12 10:31:00', TRUE, 1, 50, 208, 1,5000,208)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001004, ". Constants::iINPUT_VALID_STATE. ")");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001004, ". Constants::iPAYMENT_INIT_WITH_PSP_STATE. ")");
        $sessionId = 1;
        $obj_FailedPaymentMethods = FailedPaymentMethodConfig::produceFailedTxnInfoFromSession($this->_OBJ_DB, $sessionId);
        $xml = '';
        if(count($obj_FailedPaymentMethods) > 0 )
        {
            $xml .= '<failed_payment_methods>';
            foreach ($obj_FailedPaymentMethods as $obj_FailedPaymentMethod)
            {
                if (($obj_FailedPaymentMethod instanceof FailedPaymentMethodConfig) === TRUE)
                {
                    $xml .= $obj_FailedPaymentMethod->toAttributeLessXML();
                }
            }
            $xml .= '<failed_payment_methods>';
        }
        $this->assertEquals(0, count($obj_FailedPaymentMethod));
        $this->assertEmpty($obj_FailedPaymentMethods);
    }

    public function testGetFailedPaymentMethodsInvalidSession()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 18, '5019********3742', '06/24', TRUE, 113, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 2)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '1513-2001', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, cardid, extid, orderid, callbackurl, amount, ip, created, enabled, keywordid, fee, currencyid, sessionid,convertedamount,convetredcurrencyid) VALUES (1001004, 100, 113, 1100, 100, 18, 8, '1512', '1513-2001', '', 5000, '127.0.0.1', '2020-06-12 10:30:19', TRUE, 1, 50, 208, 1,5000,208)");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, cardid, extid, orderid, callbackurl, amount, ip, created, enabled, keywordid, fee, currencyid, sessionid,convertedamount,convetredcurrencyid) VALUES (1001005, 100, 113, 1100, 100, 17, 7, '1512', '1513-2001', '', 5000, '127.0.0.1', '2020-06-12 10:31:00', TRUE, 1, 50, 208, 1,5000,208)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001004, ". Constants::iINPUT_VALID_STATE. ")");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001004, ". Constants::iPAYMENT_INIT_WITH_PSP_STATE. ")");
        $sessionId = 10;
        $obj_FailedPaymentMethods = FailedPaymentMethodConfig::produceFailedTxnInfoFromSession($this->_OBJ_DB, $sessionId);
        $xml = '';
        if(count($obj_FailedPaymentMethods) > 0 )
        {
            $xml .= '<failed_payment_methods>';
            foreach ($obj_FailedPaymentMethods as $obj_FailedPaymentMethod)
            {
                if (($obj_FailedPaymentMethod instanceof FailedPaymentMethodConfig) === TRUE)
                {
                    $xml .= $obj_FailedPaymentMethod->toAttributeLessXML();
                }
            }
            $xml .= '<failed_payment_methods>';
        }
        $this->assertEquals(0, count($obj_FailedPaymentMethod));
        $this->assertEmpty($obj_FailedPaymentMethods);
    }




    public function tearDown()
    {
        $this->_OBJ_DB->disConnect();
        parent::tearDown();
    }


}
