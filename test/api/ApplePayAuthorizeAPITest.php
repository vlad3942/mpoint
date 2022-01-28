<?php
/**
 * User: jot
 * Date: 24-03-15
 * Time: 19:46
 */

require_once __DIR__ . '/AuthorizeAPITest.php';

class ApplePayAuthorizeAPITest extends AuthorizeAPITest
{
    public function testSuccessfulAuthorize()
    {
        $pspID = Constants::iWIRE_CARD_PSP;

        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (2, 10099, 14, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 14, '-1')");
        //As per talk with Jona and Simon 2016-07-19 it should not be possible to authorize a disabled card, since the client can ignore flags sent from initialize
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 15, $pspID, true, 1)");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid,walletid) VALUES (10099, 8, $pspID, true, 1,14)");
        $this->queryDB("INSERT INTO client.services_tbl (clientid, legacy_flow_enabled) VALUES(10099, true);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 840, 200, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid,sessionid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 1,  $pspID, 200, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE, 840, 1,5000,840)");

        $xml = $this->getAuthDoc(10099, 1100, 1001001, 5000, 'profilePass');

        $this->_httpClient->connect();


        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(200, $iStatus);
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?><root><status code="2000">Payment authorized</status>', $sReplyBody);

        $res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY ID ASC");
        $this->assertTrue(is_resource($res) );

        $aStates = array();
        while ($row = pg_fetch_assoc($res) )
        {
            $aStates[] = $row["stateid"];
        }

        $this->assertEquals(0, count($aStates) );

    }


    public function testSuccessfulAuthorizeWithTokenization()
    {
        $pspID = Constants::iWIRE_CARD_PSP;
        $tokenizationPSPID  = Constants::iUATP_CARD_ACCOUNT;

        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (2, 10099, $tokenizationPSPID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (3, 10099, 14, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $tokenizationPSPID, '-1')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 14, '-1')");
        //As per talk with Jona and Simon 2016-07-19 it should not be possible to authorize a disabled card, since the client can ignore flags sent from initialize
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid, enabled, stateid) VALUES (10099, 15, $pspID, 200, true, 1)");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid, enabled, stateid,walletid) VALUES (10099, 8, $pspID, 200, true, 1,14)");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid, enabled, stateid, psp_type) VALUES (10099, 15, $tokenizationPSPID, 200, true, 1, 8)");
        $this->queryDB("INSERT INTO client.services_tbl (clientid, legacy_flow_enabled) VALUES(10099, true);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 840, 200, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid,sessionid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 1,  $pspID, 200, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE, 840, 1,5000,840)");

        $xml = $this->getAuthDoc(10099, 1100, 1001001, 5000, 'profilePass');

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(200, $iStatus);
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?><root><status code="2000">Payment authorized</status><card><card-number>165400418292788</card-number><expiry-month>03</expiry-month><expiry-year>2020</expiry-year></card></root>', $sReplyBody);

        $res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY ID ASC");
        $this->assertTrue(is_resource($res) );

        $aStates = array();
        while ($row = pg_fetch_assoc($res) )
        {
            $aStates[] = $row["stateid"];
        }

        $this->assertEquals(1, count($aStates) );

        $s = 0;
        $this->assertEquals(Constants::iPAYMENT_TOKENIZATION_COMPLETE_STATE, $aStates[$s++]);

    }

    public function getAuthDoc($client, $account, $txn=1, $amount=100, $euaPasswd='', $intAccountId=0, $clientpasswd='', $currecyid = null)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<root>';
        $xml .= '<authorize-payment client-id="'. $client .'" account="'. $account .'">';
        $xml .= '<transaction type-id="1009" id="'. $txn .'">';
        $xml .= '<card type-id="15">';
        $xml .= '<amount country-id="200"';
        if(isset($currecyid) === true)
            $xml .= ' currency-id="'.$currecyid.'"';
        $xml .= '>'. $amount .'</amount>';
        $xml .= '<token>6125070622746068102</token>';
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
}