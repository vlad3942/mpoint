<?php
/**
 * User: jot
 * Date: 24-03-15
 * Time: 19:46
 */

require_once __DIR__ . '/AuthorizeAPITest.php';

class AuthorizeAPIAlternateRoutingTest extends AuthorizeAPITest
{
    /**
     * Prepare RQ for Authorisation
     *
     * @param        $client    Client Id
     * @param        $account   Account
     * @param int    $txn       Transaction
     * @param int    $amount
     * @param string $euaPasswd
     * @param int    $intAccountId
     * @param string $clientpasswd
     * @param null   $currecyid
     *
     * @return string
     */
    public function getAuthDoc($client, $account, $txn=1, $amount=100, $euaPasswd='', $intAccountId=0, $clientpasswd='', $currecyid = null)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<root>';
        $xml .= '<authorize-payment client-id="'. $client .'" account="'. $account .'">';
        $xml .= '<transaction type-id="10091" id="'. $txn .'">';
        $xml .= '<card type-id="8">';
        $xml .= '<amount country-id="100"';
        if(isset($currecyid) === true)
            $xml .= ' currency-id="'.$currecyid.'"';
        $xml .= '>'. $amount .'</amount>';
        $xml .= '<card-holder-name>CellPointMobie</card-holder-name>
				<card-number>4112344112344113</card-number>
				<expiry>11/24</expiry>
				<cvc>411</cvc>
				<address country-id="200">
					<full-name>TestCellPointMobile Test</full-name>
					<street>Karve Road Pune</street>
					<postal-code>416010</postal-code>
					<city>Pune</city>
					<state>maharashtra</state>
				</address>';
        $xml .= '</card>';
        $xml .= '</transaction>';
        $xml .= '<client-info platform="iOS" version="1.00" language="da">';
        $xml .= '<mobile country-id="100" operator-id="10000">28882861</mobile>';
        $xml .= '<email>jona@oismail.com</email>';
        $xml .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
        $xml .= '</client-info>';
        $xml .= '</authorize-payment>';
        $xml .= '</root>';

        return $xml;
    }

    public function testSuccessfulFlowAuthorize()
    {
        $pspID = Constants::i2C2P_ALC_PSP;
        // $pspID = Constants::iWIRE_CARD_PSP;

        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");

        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");

        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (2, 10099, 13, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 13, '-1')");

        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 16, $pspID, true, 1)");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 8, $pspID, true, 1)");

        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 200, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 16, $pspID, '501910******3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");

        # Transaction Related Entry
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 840, 200, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid,sessionid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 1,  $pspID, 5001, 200, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE, 840, 1,5000,840)");

        # RQ Document
        $xml = $this->getAuthDoc(10099, 1100, 1001001, 5000, 'profilePass');

        $this->_httpClient->connect();
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(303, $iStatus);
        $this->assertStringContainsString('<status code="2005">3d verification required</status>', $sReplyBody);

        // States check
        $SQL_MessageTbl =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY ID ASC");
        $this->assertTrue(is_resource($SQL_MessageTbl));

        $aStates = array();
        while ($row = pg_fetch_assoc($SQL_MessageTbl) )
        {
            $aStates[] = $row["stateid"];
        }
        $this->assertGreaterThan(0, count($aStates) );
    }

    // Non Legacy flow Test case
    public function testSuccessfulNonLegacyFlowAuthorize()
    {
        $pspID = Constants::i2C2P_ALC_PSP;
        // $pspID = Constants::iWIRE_CARD_PSP;

        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");

        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");

        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (2, 10099, 13, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 13, '-1')");

        //As per talk with Jona and Simon 2016-07-19 it should not be possible to authorize a disabled card, since the client can ignore flags sent from initialize
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 16, $pspID, true, 1)");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 8, $pspID, true, 1)");

        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 200, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 16, $pspID, '501910******3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");

        # Set Is Legacy Code
        $this->queryDB("INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type,scope) VALUES ('IS_LEGACY', 'false', 10099, 'client',2)");

        ## Route Related SQL
        $this->queryDB("INSERT INTO client.route_tbl(id, clientid, providerid) VALUES (10001, 10099, $pspID)");

        $this->queryDB("INSERT INTO client.routeconfig_tbl( id, routeid, name, capturetype, mid, username, password, enabled) VALUES (18, 10001, '2c2p-alc_Master_VISA_USD', 2, 'CebuPacific_USD', 'CELLPM', 'HC1XBPV0O4WLKZMG', 'true')");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (18)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (18)");

        $this->queryDB("INSERT INTO client.routeconfig_tbl( id, routeid, name, capturetype, mid, username, password, enabled) VALUES (17, 10001, '2c2p-alc_Master_VISA_USD', 2, 'CebuPacific_USD', 'CELLPM', 'HC1XBPV0O4WLKZMG', 'true')");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (17)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (17)");

        $this->queryDB("INSERT INTO client.routeconfig_tbl( id, routeid, name, capturetype, mid, username, password, enabled) VALUES (1127, 10001, 'First data', 2, 'First Data MID', 'Username', 'Password', 'true')");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (1127)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (1127)");

        # Transaction Related Entry
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 840, 200, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid,sessionid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 1,  $pspID, 5001, 200, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE, 840, 1,5000,840)");

        # RQ Document
        $xml = $this->getAuthDoc(10099, 1100, 1001001, 5000, 'profilePass');

        $this->_httpClient->connect();
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(303, $iStatus);
        $this->assertStringContainsString('<status code="2005">3d verification required</status>', $sReplyBody);

        // Check Route config ID
        $SQL_TxnTbl =  $this->queryDB("SELECT Txn.routeconfigid FROM Log.Transaction_Tbl Txn WHERE Txn.id = 1001001");
        $this->assertTrue(is_resource($SQL_TxnTbl));
        $res_TxnTbl = pg_fetch_all($SQL_TxnTbl);
        $this->assertEquals(18, $res_TxnTbl[0]['routeconfigid'],'Route Config Id not matched' );

        // States check
        $SQL_MessageTbl =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY ID ASC");
        $this->assertTrue(is_resource($SQL_MessageTbl));

        $aStates = array();
        while ($row = pg_fetch_assoc($SQL_MessageTbl) )
        {
            $aStates[] = $row["stateid"];
        }
        $this->assertGreaterThan(0, count($aStates) );
    }

    // Non Legacy flow Test case
    public function testAuthorizeFlowWithAlternateRouting()
    {
        $pspID_2C2P_ALC = Constants::i2C2P_ALC_PSP;
        $pspID_FirstData_PSP = Constants::iFirstData_PSP;

        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");

        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");

        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID_2C2P_ALC, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (2, 10099, $pspID_FirstData_PSP, '4216310')");

        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID_2C2P_ALC, '-1')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID_FirstData_PSP, '-1')");

        //As per talk with Jona and Simon 2016-07-19 it should not be possible to authorize a disabled card, since the client can ignore flags sent from initialize
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 8, $pspID_2C2P_ALC, true, 1)");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 8, $pspID_FirstData_PSP, true, 1)");

        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 200, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 16, $pspID_2C2P_ALC, '501910******3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");

        # Set Is Legacy Code
        $this->queryDB("INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type,scope) VALUES ('IS_LEGACY', 'false', 10099, 'client',2)");

        # Allow alternate route for client
        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type) VALUES ('PAYMENT_RETRY_WITH_ALTERNATE_ROUTE', 'true', true, 10099, 'client')");

        # Route Related SQL
        $this->queryDB("INSERT INTO client.route_tbl(id, clientid, providerid) VALUES (10001, 10099, $pspID_2C2P_ALC)");
        $this->queryDB("INSERT INTO client.route_tbl(id, clientid, providerid) VALUES (10002, 10099, $pspID_FirstData_PSP)");

        $this->queryDB("INSERT INTO client.routeconfig_tbl( id, routeid, name, capturetype, mid, username, password, enabled) VALUES (17, 10001, '2c2p-alc_Master_VISA', 2, 'CebuPacific', 'CELLPM', 'HC1XBPV0O4WLKZMG', 'true')");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (17)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (17)");

        $this->queryDB("INSERT INTO client.routeconfig_tbl( id, routeid, name, capturetype, mid, username, password, enabled) VALUES (18, 10002, 'Firstdata', 2, 'first-data', 'user', 'password', 'true')");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (18)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (18)");

        # Transaction Related Entry
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 840, 200, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, euaid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid,sessionid,convertedamount,convertedcurrencyid) VALUES (1001002, 100, 10099, 1100, 1, 5001, 200, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE, 840, 1,5000,840)");

        # RQ Document
        $xml = $this->getAuthDoc(10099, 1100, 1001002, 5000, 'profilePass');

        $this->_httpClient->connect();
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        echo "{status Code :: \n\n";
        print_r($iStatus);
        echo "\n}\n";

        // Check Transaction Against Session
        $SQL_TxnPaymentRouteTbl =  $this->queryDB("SELECT Txn.* FROM log.paymentroute_tbl Pr WHERE Pr.sessionid = 1");
        $this->assertTrue(is_resource($SQL_TxnPaymentRouteTbl));
        $res_TxnPaymentRouteTbl = pg_fetch_all($SQL_TxnPaymentRouteTbl);

        echo "{res_TxnPaymentRouteTbl \n\n";
        print_r($res_TxnPaymentRouteTbl);
        echo "\n}\n";

        $this->assertEquals(303, $iStatus);
        $this->assertStringContainsString('<status code="2005">3d verification required</status>', $sReplyBody);

        // Check Route config ID
        $SQL_TxnTbl =  $this->queryDB("SELECT Txn.* FROM Log.Transaction_Tbl Txn WHERE Txn.id = 1001002");
        $this->assertTrue(is_resource($SQL_TxnTbl));
        $res_TxnTbl = pg_fetch_all($SQL_TxnTbl);

        // Assertion for expected Route config ID
        $this->assertEquals(17, $res_TxnTbl[0]['routeconfigid'],'Route Config Id not matched' );

        // Check Transaction Against Session
        $SQL_TxnSessionTbl =  $this->queryDB("SELECT Txn.* FROM Log.Transaction_Tbl Txn WHERE Txn.sessionid = 1");
        $this->assertTrue(is_resource($SQL_TxnSessionTbl));
        $res_TxnSessionTbl = pg_fetch_all($SQL_TxnSessionTbl);
        $this->assertEquals(2, count($res_TxnSessionTbl) );

        // States check
        $SQL_MessageTbl =  $this->queryDB("select * from log.message_tbl where txnid in (select id from log.transaction_tbl where sessionid = 1) ORDER BY ID ASC");
        $this->assertTrue(is_resource($SQL_MessageTbl));
        $aStates = array();
        while ($row = pg_fetch_assoc($SQL_MessageTbl) )
        {
            $aStates[$row["stateid"]] = $row["txnid"];
        }

        $this->assertEquals(3, count($aStates) );
        $this->assertArrayHasKey(7010, $aStates );
        $this->assertArrayHasKey(2010303, $aStates );
    }
}