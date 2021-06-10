<?php
/**
 * User: SAGAR BADAVE
 */

require_once __DIR__ . '/AuthorizeAPITest.php';

class DCCAuthorizeAPITest extends AuthorizeAPITest
{
    public function testSuccessfulAuthorize()
    {
        $pspID = Constants::iWIRE_CARD_PSP;
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd,salt) VALUES (10018, 1, 100, 'Test Client', 'Tuser', 'Tpass','23lkhfgjh24qsdfkjh')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10018, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10018, 'app')");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10018, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10018, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid,dccenabled) VALUES (10018, 8, $pspID,100,true)");
        $this->queryDB("INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type,scope) VALUES ('IS_LEGACY', 'true', 10018, 'client',0)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (50011, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10018, 50011)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 50011, 8, $pspID, '501910******3742', '06/24', TRUE, 10018, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid, enabled) VALUES (10018,100,840, true)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10018, 1100, 208, 100, 4001, '1513-005', 5000, 29612109, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,currencyid,euaid,convertedamount,convertedcurrencyid) VALUES (1001012, 100, 10018, 1100, 100, $pspID, '1512', '1234abc', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,840,50011,5000,840)");
        $this->queryDB("INSERT INTO log.txnpassbook_tbl(id, transactionid, amount, currencyid, requestedopt, performedopt, status,clientid) VALUES(102291, 1001012, 5000, 840, 5014, NULL, 'done', 10018)");
        $this->queryDB("INSERT INTO log.txnpassbook_tbl(transactionid, amount, currencyid,  performedopt, status, extref, extrefidentifier, clientid) VALUES ( 1001012, 5000, 840,  1001, 'done', '102291', 'log.txnpassbook_tbl', 10018)");

        $aDccParams = array(
            "12345",
            "4","840","5000"
        );
        $xml = $this->getAuthDoc(10018, 1100, 1001012,20000, 'profilePass', 0,null,208,'4874b565db376dffc0801d91e2bdb5b9d2a3f610917753204cd15a30357e42984cabceb6055495a6f95a7f761270dc9e9c9a04fd45d2a4992f36f3f8799a77f5',8,$aDccParams);
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="2000">Payment authorized</status></root>', $sReplyBody);
        $res =  $this->queryDB("SELECT externalid FROM log.externalreference_tbl where txnid=1001012 and type=".Constants::iForeignExchange);
        $this->assertTrue(is_resource($res) );
        $res =  $this->queryDB("SELECT convertedcurrencyid FROM Log.Transaction_Tbl where id=1001012 and convertedcurrencyid = 208 and currencyid=840 and convertedamount=20000");
        $this->assertTrue(is_resource($res) );
        $res =  $this->queryDB("SELECT * FROM Log.txnpassbook_tbl where transactionid=1001012 and performedopt = 2000");
        $this->assertTrue(is_resource($res) && pg_num_rows($res) == 1);
        $res =  $this->queryDB("SELECT id FROM Log.Session_Tbl where id=1 and sessiontypeid=1");
        $this->assertIsResource($res);
    }

    public function testSuccessfulSplitAuthorize()
    {
        $pspID = Constants::iWIRE_CARD_PSP;
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd,salt) VALUES (10018, 1, 100, 'Test Client', 'Tuser', 'Tpass','23lkhfgjh24qsdfkjh')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10018, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10018, 'app')");
        $this->queryDB("INSERT INTO Client.AdditionalProperty_tbl (key, value,type , externalid) VALUES ('sessiontype', '2', 'client','10018')");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10018, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10018, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid,dccenabled) VALUES (10018, 8, $pspID,100,true)");
        $this->queryDB("INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type,scope) VALUES ('IS_LEGACY', 'true', 10018, 'client',0)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (50011, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10018, 50011)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 50011, 8, $pspID, '501910******3742', '06/24', TRUE, 10018, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid, enabled) VALUES (10018,100,840, true)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10018, 1100, 208, 100, 4001, '1513-005', 5000, 29612109, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,currencyid,euaid,convertedamount,convertedcurrencyid) VALUES (1001012, 100, 10018, 1100, 100, $pspID, '1512', '1234abc', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,840,50011,5000,840)");
        $this->queryDB("INSERT INTO log.txnpassbook_tbl(id, transactionid, amount, currencyid, requestedopt, performedopt, status,clientid) VALUES(102291, 1001012, 5000, 840, 5014, NULL, 'done', 10018)");
        $this->queryDB("INSERT INTO log.txnpassbook_tbl(transactionid, amount, currencyid,  performedopt, status, extref, extrefidentifier, clientid) VALUES ( 1001012, 5000, 840,  1001, 'done', '102291', 'log.txnpassbook_tbl', 10018)");

        $aDccParams = array(
            "12345",
            "4","840","2500"
        );
        $xml = $this->getAuthDoc(10018, 1100, 1001012,10000, 'profilePass', 0,null,208,'f969214287fd1f0555c0643684ccbe173cd9831400586481babf7c843c87e555f35165a63b5e69a10e6445e983fbc42f032159fe8a48699eca125d08aefab9c0',8,$aDccParams);
        $this->_httpClient->connect();
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="2000">Payment authorized</status></root>', $sReplyBody);
        $res =  $this->queryDB("SELECT externalid FROM log.externalreference_tbl where txnid=1001012 and type=".Constants::iForeignExchange);
        $this->assertTrue(is_resource($res) );
        $res =  $this->queryDB("SELECT convertedcurrencyid FROM Log.Transaction_Tbl where id=1001012 and convertedcurrencyid = 208 and currencyid=840 and convertedamount=10000");
        $this->assertTrue(is_resource($res) );
        $res =  $this->queryDB("SELECT * FROM Log.txnpassbook_tbl where transactionid=1001012 and performedopt = 2000");
        $this->assertTrue(is_resource($res) && pg_num_rows($res) == 1);
        $res =  $this->queryDB("SELECT id FROM Log.Session_Tbl where id=1 and sessiontypeid=2");
        $this->assertTrue(is_resource($res) );
    }

    public function testInvalidSplitAuthorizeAmount()
    {
        $pspID = Constants::iWIRE_CARD_PSP;
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd,salt) VALUES (10018, 1, 100, 'Test Client', 'Tuser', 'Tpass','23lkhfgjh24qsdfkjh')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10018, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10018, 'app')");
        $this->queryDB("INSERT INTO Client.AdditionalProperty_tbl (key, value,type , externalid) VALUES ('sessiontype', '2', 'client','10018')");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10018, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10018, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid,dccenabled) VALUES (10018, 8, $pspID,100,true)");
        $this->queryDB("INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type,scope) VALUES ('IS_LEGACY', 'true', 10018, 'client',0)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (50011, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10018, 50011)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 50011, 8, $pspID, '501910******3742', '06/24', TRUE, 10018, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid, enabled) VALUES (10018,100,840, true)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10018, 1100, 208, 100, 4001, '1513-005', 5000, 29612109, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,currencyid,euaid,convertedamount,convertedcurrencyid) VALUES (1001012, 100, 10018, 1100, 100, $pspID, '1512', '1234abc', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,840,50011,5000,840)");
        $this->queryDB("INSERT INTO log.txnpassbook_tbl(id, transactionid, amount, currencyid, requestedopt, performedopt, status,clientid) VALUES(102291, 1001012, 5000, 840, 5014, NULL, 'done', 10018)");
        $this->queryDB("INSERT INTO log.txnpassbook_tbl(transactionid, amount, currencyid,  performedopt, status, extref, extrefidentifier, clientid) VALUES ( 1001012, 5000, 840,  1001, 'done', '102291', 'log.txnpassbook_tbl', 10018)");

        $aDccParams = array(
            "12345",
            "4","840","7500"
        );
        $xml = $this->getAuthDoc(10018, 1100, 1001012,30000, 'profilePass', 0,null,208,'0ab2d5ea5a19a456ad19358ce7640472c567d302ce717b0c00c68c60c00cec31b75dcc257892351515367e2107f4dec5b9fdf97ef3a4acac2892c18b1501fad9',8,$aDccParams);
        $this->_httpClient->connect();
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(400, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="53">Amount is more than pending amount: 30000</status></root>', $sReplyBody);
    }
}