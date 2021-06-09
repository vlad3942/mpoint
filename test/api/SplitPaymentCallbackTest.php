<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package:
 * File Name: SplitPaymentCallbackTest.php
 */


require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';


class SplitPaymentCallbackTest extends baseAPITest
{
    protected $_aMPOINT_CONN_INFO;

    public function __construct()
    {
        parent::__construct();
        $this->constHTTPClient();
    }

    public function constHTTPClient()
    {
        global $aMPOINT_CONN_INFO;
        $aMPOINT_CONN_INFO['path'] = "/callback/general.php";
        $aMPOINT_CONN_INFO["contenttype"] = "text/xml";
        $this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO));
    }

    public function testSuccessfulCallbackSplitPaymentNotEnabled()
    {
        $pspID = Constants::iDSB_PSP;
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] . "://" . $this->_aMPOINT_CONN_INFO["host"] . "/_test/simulators/mticket/callback.php";

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, " . $pspID . ", '1')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10099, 8, $pspID)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid, expire) VALUES (1, 10099, 1100, 208, 100, 4001, '900-55150298', 10000, 9876543210, '', '127.0.0.1', -1, 2, (NOW() + interval '1 hour'));");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, pspid, callbackurl, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1001001, '900-55150298', 100, 10099, 1100, 100, null, '" . $sCallbackURL . "', 5000, '127.0.0.1', TRUE, 1, 1,5000)");
        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('IS_LEGACY_CALLBACK_FLOW', 'true', 10099, 'client', 0);");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001001, 10000,208," . Constants::iInitializeRequested . ",NULL,'done',10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001001, 10000,208,NULL," . Constants::iINPUT_VALID_STATE . ",'done',100,10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (102,1001001, 5000,208," . Constants::iAuthorizeRequested . ",NULL,'done',10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (103,1001001, 5000,208,NULL," . Constants::iPAYMENT_ACCEPTED_STATE . ",'inprogress',102,10099)");


        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled) VALUES (10099, " . Constants::iVOUCHER_CARD . ", $pspID, false)"); //Authorize must be possible even with disabled cardac
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, pspid, callbackurl, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1, '900-55150298', 100, 10099, 1100, 100, $pspID, '" . $sCallbackURL . "', 5000, '127.0.0.1', TRUE, 1, 1,5000)");
        $this->queryDB("INSERT INTO log.message_tbl (txnid, stateid) VALUES (1,1000)");


        $xml = $this->getCallbackDoc(1001001, '900-55150298', $pspID);
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(202, $iStatus);
        $this->assertEquals("", $sReplyBody);

        $res = $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001  ORDER BY id ASC");
        $this->assertIsResource($res);

        $aStates = [];
        while ($row = pg_fetch_assoc($res)) {
            $aStates[] = $row["stateid"];
        }

        $this->assertCount(6, $aStates);
        $this->assertTrue(is_int(array_search(2000, $aStates)));

        $res = $this->queryDB("SELECT id FROM Log.txnpassbook_tbl where transactionid= 1001001 and status= 'done' and performedopt=2000 ");
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));


        $res = $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1  ORDER BY id ASC");
        $this->assertIsResource($res);

        $aStates = [];
        while ($row = pg_fetch_assoc($res)) {
            $aStates[] = $row["stateid"];

        }

        $this->assertCount(1, $aStates);
        $this->assertTrue(is_int(array_search(1000, $aStates)));
    }

    public function getCallbackDoc($transactionId, $orderId, $pspID)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<root>';
        $xml .= '<callback>';
        $xml .= '<psp-config id="' . $pspID . '">';
        $xml .= '<name>CellpointMobileCOM</name>';
        $xml .= '</psp-config>';
        $xml .= '<transaction id="' . $transactionId . '" order-no="' . $orderId . '" external-id="-1">';
        $xml .= '<amount country-id="100" currency="DKK">5000</amount>';
        $xml .= '<card type-id="8">';
        $xml .= '<card-number>401200******6002</card-number>';
        $xml .= '<expiry>';
        $xml .= '<month>01</month>';
        $xml .= '<year>20</year>';
        $xml .= '</expiry>';
        $xml .= '</card>';
        $xml .= '</transaction>';
        $xml .= '<status code="2000">Transaction is Authorized.</status>';
        $xml .= '<approval-code>035747</approval-code>';
        $xml .= '</callback>';
        $xml .= '</root>';

        return $xml;
    }

    public function testSuccessfulCallbackVoucherIdNotSet()
    {
        $pspID = Constants::iDSB_PSP;
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] . "://" . $this->_aMPOINT_CONN_INFO["host"] . "/_test/simulators/mticket/callback.php";

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, " . $pspID . ", '1')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10099, 8, $pspID)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 208, 100, 4001, '900-55150298', 5002, 9876543210, '', '127.0.0.1', -1, 2);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, pspid, callbackurl, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1001001, '900-55150298', 100, 10099, 1100, 100, null, '" . $sCallbackURL . "', 5000, '127.0.0.1', TRUE, 1, 1,5000)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001001, 10000,208," . Constants::iInitializeRequested . ",NULL,'done',10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001001, 10000,208,NULL," . Constants::iINPUT_VALID_STATE . ",'done',100,10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (102,1001001, 5000,208," . Constants::iAuthorizeRequested . ",NULL,'done',10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (103,1001001, 5000,208,NULL," . Constants::iPAYMENT_ACCEPTED_STATE . ",'inprogress',102,10099)");


        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled) VALUES (10099, " . Constants::iVOUCHER_CARD . ", $pspID, false)"); //Authorize must be possible even with disabled cardac
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, pspid, callbackurl, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1, '900-55150298', 100, 10099, 1100, 100, $pspID, '" . $sCallbackURL . "', 2, '127.0.0.1', TRUE, 1, 1,2)");
        $this->queryDB("INSERT INTO log.message_tbl (txnid, stateid) VALUES (1,1000)");
        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('sessiontype', 2, true, 10099, 'client', 0);");

        $xml = $this->getCallbackDoc(1001001, '900-55150298', $pspID);
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(202, $iStatus);
        $this->assertEquals("", $sReplyBody);

        $res = $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001  ORDER BY id ASC");
        $this->assertIsResource($res);

        $aStates = [];
        while ($row = pg_fetch_assoc($res)) {
            $aStates[] = $row["stateid"];

        }

        $this->assertCount(2, $aStates);
        $this->assertTrue(is_int(array_search(2000, $aStates)));

        $res = $this->queryDB("SELECT id FROM Log.txnpassbook_tbl where transactionid= 1001001 and status= 'done' and performedopt=2000 ");
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));


        $res = $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1  ORDER BY id ASC");
        $this->assertIsResource($res);

        $aStates = [];
        while ($row = pg_fetch_assoc($res)) {
            $aStates[] = $row["stateid"];

        }

        $this->assertEquals(7, pg_num_rows($res));
        $this->assertTrue(is_int(array_search(1000, $aStates)));
    }

    public function testSuccessfulCardCallbackVoucherRejected()
    {
        $this->bIgnoreErrors = true;
        $pspID = Constants::iDSB_PSP;
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] . "://" . $this->_aMPOINT_CONN_INFO["host"] . "/_test/simulators/mticket/callback.php";

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, " . $pspID . ", '1')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10099, 8, $pspID)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid,expire) VALUES (1, 10099, 1100, 208, 100, 4001, '900-55150298', 10000, 9876543210, '', '127.0.0.1', -1, 2,(NOW() + interval '1 hour'));");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, pspid, callbackurl, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1001001, '900-55150298', 100, 10099, 1100, 100, null, '" . $sCallbackURL . "', 5000, '127.0.0.1', TRUE, 1, 1,5000)");
        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('IS_LEGACY_CALLBACK_FLOW', 'true', 10099, 'client', 0);");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001001, 10000,208," . Constants::iInitializeRequested . ",NULL,'done',10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001001, 10000,208,NULL," . Constants::iINPUT_VALID_STATE . ",'done',100,10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (102,1001001, 5000,208," . Constants::iAuthorizeRequested . ",NULL,'done',10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (103,1001001, 5000,208,NULL," . Constants::iPAYMENT_ACCEPTED_STATE . ",'inprogress',102,10099)");


        //$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        //$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled) VALUES (10099, " . Constants::iVOUCHER_CARD . ", $pspID, false)"); //Authorize must be possible even with disabled cardac
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, pspid, callbackurl, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1, '900-55150298', 100, 10099, 1100, 100, $pspID, '" . $sCallbackURL . "', 5000, '127.0.0.1', TRUE, 1, 1,5000)");
        $this->queryDB("INSERT INTO log.message_tbl (txnid, stateid) VALUES (1,1000)");
        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('sessiontype', 2, true, 10099, 'client', 0);");
        $this->queryDB("INSERT INTO log.additional_data_tbl(name, value, type, externalid) VALUES('voucherid', 'voucherid', 'Transaction',1)");

        $xml = $this->getCallbackDoc(1001001, '900-55150298', $pspID);
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(202, $iStatus);
        $this->assertEquals("", $sReplyBody);

        $res = $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001  ORDER BY id ASC");
        $this->assertIsResource($res);

        $aStates = [];
        while ($row = pg_fetch_assoc($res)) {
            $aStates[] = $row["stateid"];
        }

        $this->assertCount(6, $aStates);
        $this->assertTrue(is_int(array_search(2000, $aStates)));
        $this->assertTrue(is_int(array_search(4031, $aStates)));

        $res = $this->queryDB("SELECT id FROM Log.txnpassbook_tbl where transactionid= 1001001 and status= 'done' and performedopt=2000 ");
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));


        $res = $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1  ORDER BY id ASC");
        $this->assertIsResource($res);

        $aStates = [];
        while ($row = pg_fetch_assoc($res)) {
            $aStates[] = $row["stateid"];
        }

        $this->assertCount(6, $aStates);
        $this->assertTrue(is_int(array_search(2010, $aStates)));

        $res = $this->queryDB("SELECT id FROM Log.txnpassbook_tbl where transactionid= 1 and status= 'error' and performedopt=2000 ");
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));

    }

    public function testSuccessfulCallbackAccepted()
    {
        $pspID = Constants::iDSB_PSP;
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] . "://" . $this->_aMPOINT_CONN_INFO["host"] . "/_test/simulators/mticket/callback.php";

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, " . $pspID . ", '1')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10099, 8, $pspID)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid,expire) VALUES (1, 10099, 1100, 208, 100, 4001, '900-55150298', 5002, 9876543210, '', '127.0.0.1', -1, 2,(NOW() + interval '1 hour'));");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, pspid, callbackurl, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1001001, '900-55150298', 100, 10099, 1100, 100, null, '" . $sCallbackURL . "', 5000, '127.0.0.1', TRUE, 1, 1,5000)");
        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('IS_LEGACY_CALLBACK_FLOW', 'true', 10099, 'client', 0);");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001001, 10000,208," . Constants::iInitializeRequested . ",NULL,'done',10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001001, 10000,208,NULL," . Constants::iINPUT_VALID_STATE . ",'done',100,10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (102,1001001, 5000,208," . Constants::iAuthorizeRequested . ",NULL,'done',10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (103,1001001, 5000,208,NULL," . Constants::iPAYMENT_ACCEPTED_STATE . ",'inprogress',102,10099)");


        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled) VALUES (10099, " . Constants::iVOUCHER_CARD . ", $pspID, false)"); //Authorize must be possible even with disabled cardac
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, pspid, callbackurl, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1, '900-55150298', 100, 10099, 1100, 100, $pspID, '" . $sCallbackURL . "', 2, '127.0.0.1', TRUE, 1, 1,2)");
        $this->queryDB("INSERT INTO log.message_tbl (txnid, stateid) VALUES (1,1000)");
        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('sessiontype', 2, true, 10099, 'client', 0);");
        $this->queryDB("INSERT INTO log.additional_data_tbl(name, value, type, externalid) VALUES('voucherid', 'voucherid', 'Transaction',1)");

        $xml = $this->getCallbackDoc(1001001, '900-55150298', $pspID);
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(202, $iStatus);
        $this->assertEquals("", $sReplyBody);

        $res = $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001  ORDER BY id ASC");
        $this->assertIsResource($res);

        $aStates = [];
        while ($row = pg_fetch_assoc($res)) {
            $aStates[] = $row["stateid"];
        }

        $this->assertCount(6, $aStates);
        $this->assertTrue(is_int(array_search(2000, $aStates)));
        $this->assertTrue(is_int(array_search(4031, $aStates)));

        $res = $this->queryDB("SELECT id FROM Log.txnpassbook_tbl where transactionid= 1001001 and status= 'done' and performedopt=2000 ");
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));


        $res = $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1  ORDER BY id ASC");
        $this->assertIsResource($res);

        $aStates = [];
        while ($row = pg_fetch_assoc($res)) {
            $aStates[] = $row["stateid"];
        }

        $this->assertCount(12, $aStates);
        $this->assertTrue(is_int(array_search(2000, $aStates)));
        $this->assertTrue(is_int(array_search(4030, $aStates)));

        $res = $this->queryDB("SELECT id FROM Log.txnpassbook_tbl where transactionid= 1 and status= 'done' and performedopt=2000 ");
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));

    }

    public function testPostAuthFraudCheckRejectedWithRollback()
    {
        $pspID = Constants::iWIRE_CARD_PSP;
        $fraudCheckPspID = Constants::iEZY_PSP;

        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] . "://" . $this->_aMPOINT_CONN_INFO["host"] . "/_test/simulators/mticket/callback.php";

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (2, 10099, 14, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 14, '-1')");

        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (3, 10099, 60, 'EZY')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 60, '-1')");

        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (4, 10099, 36, 'mvault')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 36, '-1')");

        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid, enabled, stateid) VALUES (10099, 15, $pspID, 100,true, 1)");
        $this->queryDB("INSERT INTO client.cardaccess_tbl (clientid, cardid, pspid, countryid, enabled, stateid, psp_type) VALUES (10099, 8, $fraudCheckPspID, 100, true, 1, 10)");//psp_type = Constants::iPROCESSOR_TYPE_FRAUD_GATEWAY
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid, enabled, stateid,walletid) VALUES (10099, 8, $pspID, 100, true, 1,14)");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid, enabled, stateid) VALUES (10099, 8, $pspID, 100, true, 1)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 208, 100, 4001, '103-1418291', 5002, 9876543210, '', '127.0.0.1', -1, 2);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid,sessionid,convertedamount,convertedcurrencyid,token) VALUES (1001001, 100, 10099, 1100, 1,  $pspID, 100, '103-1418291', '" . $sCallbackURL . "', 5000, '127.0.0.1', TRUE, 208, 1,5000,208,'93736e0408d5cd3793615f6e132c89a8f32337483a74739674a5bb2a9c18f6eb91eae4960e5ff9bad1bf62e60282de3c0605ececa6a82f7d14cbe5305fd1983d')");
        $this->queryDB("INSERT INTO log.additional_data_tbl(name, value, type, externalid) VALUES('FCTxnID', '243002', 'Transaction','1001001')");
        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) VALUES('ISROLLBACK_ON_FRAUD_FAIL', 'true', 10099, 'client', 0);");
        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('IS_LEGACY_CALLBACK_FLOW', 'true', 10099, 'client', 0);");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001001, 5000,208," . Constants::iInitializeRequested . ",NULL,'done',10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001001, 5000,208,NULL," . Constants::iINPUT_VALID_STATE . ",'done',100,10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (102,1001001, 5000,208," . Constants::iAuthorizeRequested . ",NULL,'done',10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (103,1001001, 5000,208,NULL," . Constants::iPAYMENT_ACCEPTED_STATE . ",'inprogress',102,10099)");

        $pspID2 = Constants::iDSB_PSP;

        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (10, 10099, " . $pspID2 . ", '1')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID2, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled) VALUES (10099, " . Constants::iVOUCHER_CARD . ", $pspID2, false)"); //Authorize must be possible even with disabled cardac
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, pspid, callbackurl, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1, 'tst233', 100, 10099, 1100, 100, $pspID2, '" . $sCallbackURL . "', 2, '127.0.0.1', TRUE, 1, 1,2)");
        $this->queryDB("INSERT INTO log.message_tbl (txnid, stateid) VALUES (1,1000)");
        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('sessiontype', 2, true, 10099, 'client', 0);");
        $this->queryDB("INSERT INTO log.additional_data_tbl(name, value, type, externalid) VALUES('voucherid', 'voucherid', 'Transaction',1)");


        $xml = $this->getCallbackDoc(1001001, 'tst233', $pspID);
        $this->_httpClient->connect();
        $this->bIgnoreErrors = TRUE;
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(202, $iStatus);
        $res = $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY ID ASC");
        $this->assertTrue(is_resource($res));

        $aStates = [];
        while ($row = pg_fetch_assoc($res)) {
            $aStates[] = (int)$row["stateid"];
        }

        $this->assertContains(Constants::iPOST_FRAUD_CHECK_INITIATED_STATE, $aStates);
        $this->assertContains(Constants::iPOST_FRAUD_CHECK_REJECTED_STATE, $aStates);
        $this->assertContains(Constants::iPAYMENT_CANCELLED_STATE, $aStates);
        //$this->assertContains(Constants::iSESSION_FAILED, $aStates);

       /* Session Will not be closed on fraud detected based on new user story CEBU-18
        *  $res = $this->queryDB("SELECT id FROM Log.session_tbl where id= 1 and stateid= 4020");
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));*/

        $res = $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1  ORDER BY id ASC");
        $this->assertIsResource($res);

        $aStates = [];
        while ($row = pg_fetch_assoc($res)) {
            $aStates[] = $row["stateid"];

        }

        $this->assertCount(1, $aStates);
        $this->assertTrue(is_int(array_search(1000, $aStates)));

    }

    public function testPostAuthFraudCheckRejectedWithNoRollback()
    {
        $pspID = Constants::iWIRE_CARD_PSP;
        $fraudCheckPspID = Constants::iEZY_PSP;

        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] . "://" . $this->_aMPOINT_CONN_INFO["host"] . "/_test/simulators/mticket/callback.php";

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (2, 10099, 14, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 14, '-1')");

        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (3, 10099, 60, 'EZY')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 60, '-1')");

        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (4, 10099, 36, 'mvault')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 36, '-1')");

        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid, enabled, stateid) VALUES (10099, 15, $pspID, 100,true, 1)");
        $this->queryDB("INSERT INTO client.cardaccess_tbl (clientid, cardid, pspid, countryid, enabled, stateid, psp_type) VALUES (10099, 8, $fraudCheckPspID, 100, true, 1, 10)");//psp_type = Constants::iPROCESSOR_TYPE_FRAUD_GATEWAY
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid, enabled, stateid,walletid) VALUES (10099, 8, $pspID, 100, true, 1,14)");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid, enabled, stateid) VALUES (10099, 8, $pspID, 100, true, 1)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 208, 100, 4001, '103-1418291', 5002, 9876543210, '', '127.0.0.1', -1, 2);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid,sessionid,convertedamount,convertedcurrencyid,token) VALUES (1001001, 100, 10099, 1100, 1,  $pspID, 100, '103-1418291', '" . $sCallbackURL . "', 5000, '127.0.0.1', TRUE, 208, 1,5000,208,'93736e0408d5cd3793615f6e132c89a8f32337483a74739674a5bb2a9c18f6eb91eae4960e5ff9bad1bf62e60282de3c0605ececa6a82f7d14cbe5305fd1983d')");
        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('IS_LEGACY_CALLBACK_FLOW', 'true', 10099, 'client', 0);");
            $this->queryDB("INSERT INTO log.additional_data_tbl(name, value, type, externalid) VALUES('FCTxnID', '243002', 'Transaction','1001001')");
        //$this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) VALUES('ISROLLBACK_ON_FRAUD_FAIL', 'true', 10099, 'client', 0);");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001001, 5000,208," . Constants::iInitializeRequested . ",NULL,'done',10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001001, 5000,208,NULL," . Constants::iINPUT_VALID_STATE . ",'done',100,10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (102,1001001, 5000,208," . Constants::iAuthorizeRequested . ",NULL,'done',10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (103,1001001, 5000,208,NULL," . Constants::iPAYMENT_ACCEPTED_STATE . ",'inprogress',102,10099)");

        $pspID2 = Constants::iDSB_PSP;

        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (10, 10099, " . $pspID2 . ", '1')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID2, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled) VALUES (10099, " . Constants::iVOUCHER_CARD . ", $pspID2, false)"); //Authorize must be possible even with disabled cardac
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, pspid, callbackurl, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1, 'tst233', 100, 10099, 1100, 100, $pspID2, '" . $sCallbackURL . "', 2, '127.0.0.1', TRUE, 1, 1,2)");
        $this->queryDB("INSERT INTO log.message_tbl (txnid, stateid) VALUES (1,1000)");
        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('sessiontype', 2, true, 10099, 'client', 0);");
        $this->queryDB("INSERT INTO log.additional_data_tbl(name, value, type, externalid) VALUES('voucherid', 'voucherid', 'Transaction',1)");


        $xml = $this->getCallbackDoc(1001001, 'tst233', $pspID);
        $this->_httpClient->connect();
        $this->bIgnoreErrors = TRUE;
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(202, $iStatus);
        $res = $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY ID ASC");
        $this->assertTrue(is_resource($res));

        $aStates = [];
        while ($row = pg_fetch_assoc($res)) {
            $aStates[] = (int)$row["stateid"];
        }

        $this->assertContains(Constants::iPOST_FRAUD_CHECK_INITIATED_STATE, $aStates);
        $this->assertContains(Constants::iPOST_FRAUD_CHECK_REJECTED_STATE, $aStates);
        $this->assertNotContains(Constants::iPAYMENT_CANCELLED_STATE, $aStates);


  /*   Session Will not be closed on fraud detected based on new user story CEBU-18
   *       $res = $this->queryDB("SELECT id FROM Log.session_tbl where id= 1 and stateid= 4020");
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));*/

        $res = $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1  ORDER BY id ASC");
        $this->assertIsResource($res);

        $aStates = [];
        while ($row = pg_fetch_assoc($res)) {
            $aStates[] = $row["stateid"];

        }

        $this->assertCount(1, $aStates);
        $this->assertTrue(is_int(array_search(1000, $aStates)));

    }

}