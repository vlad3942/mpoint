<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class SaveCardAPITest extends baseAPITest
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
        $aMPOINT_CONN_INFO['path'] = "/mApp/api/save_card.php";
        $aMPOINT_CONN_INFO["contenttype"] = "text/xml";
        $this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO));
    }

    protected function getSaveCardDoc($client, $account, $extAccountId, $passwd, $auth_token,$profileid,$email=null,$mobile=null)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<root>';
        $xml .= '<save-card client-id="' . $client . '" account="' . $account . '" txn-id="'. 1001001 .'">';
        $xml .= '<card type-id="7" psp-id="9" preferred="true" charge-type-id="2">';
        $xml .= '<name>TESTSaveCard</name>';
        $xml .= '<card-number-mask>540287******5344</card-number-mask>';
        $xml .= '<expiry-month>10</expiry-month>';
        $xml .= '<expiry-year>24</expiry-year>';
        $xml .= '<token>123456-ABCD</token>';
        $xml .= '<card-holder-name>Test CH</card-holder-name>';
        $xml .= '<address country-id="100">';
        $xml .= '<first-name>Test</first-name>';
        $xml .= '<last-name>CH</last-name>';
        $xml .= '<street>street address</street>';
        $xml .= '<postal-code>2114503</postal-code>';
        $xml .= '<city>city</city>';
        $xml .= '<state>state</state>';
        $xml .= '</address>';
        $xml .= '</card>';
        if (isset($passwd) === true)
        {
            $xml .= '<password>' . $passwd . '</password>';
        } elseif (isset($auth_token) === true)
        {
            $xml .= '<auth-token>' . $auth_token . '</auth-token>';
        }
        $xml .= '<client-info platform="iOS" version="1.00" language="da" profileid="' . $profileid . '">';
        $xml .= '<customer-ref>' . $extAccountId . '</customer-ref>';
        if (isset($mobile) === true)
        {
            $xml .= '<mobile country-id="100" operator-id="10000">'.$mobile.'</mobile>';
        } else
        {
            $xml .= '<mobile country-id="100" operator-id="10000">28882861</mobile>';
        }
        if (isset($email) === true)
        {
            $xml .= '<email>'.$email.'</email>';
        } else
        {
            $xml .= '<email>jona@oismail.com</email>';
        }
        $xml .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
        $xml .= '</client-info>';
        $xml .= '</save-card>';
        $xml .= '</root>';
        return $xml;
    }

    /**
     * Test scenario - During save card, a new enduser account is created if not present in mPoint
     * @throws ErrorException
     * @throws HTTPConnectionException
     * @throws HTTPSendException
     */
   public function testSaveCardForUnknownAccount()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.URL_Tbl (urltypeid, clientid, url) VALUES (2, 10099, 'http://mpoint.local.cellpointmobile.com/_test/simulators/auth.php')");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, amount, ip, enabled, currencyid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 100, 5000, '127.0.0.1', TRUE, 208,5000,208)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, " . Constants::iPAYMENT_ACCEPTED_STATE . ")");

        $xml = $this->getSaveCardDoc(10099, 1100, 'abcExternal', null,'successtoken','');
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertStringContainsString('Card successfully saved', $sReplyBody);

        $res = $this->queryDB("SELECT * FROM EndUser.Account_Tbl WHERE enabled = '1' and email='jona@oismail.com' and mobile='28882861'");
        $this->assertTrue(is_resource($res));
        $this->assertTrue(pg_num_rows($res) == 1);
    }

    /**
     * Test scenario - SSO with auth token is successful and card is saved for a existing enduser account.
     * @throws ErrorException
     * @throws HTTPConnectionException
     * @throws HTTPSendException
     */
    public function testSuccessfulSaveCard()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (urltypeid, clientid, url) VALUES (2, 10099, 'http://mpoint.local.cellpointmobile.com/_test/simulators/auth.php')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', null, TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, amount, ip, enabled, currencyid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 100, 5000, '127.0.0.1', TRUE, 208,5000,208)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, " . Constants::iPAYMENT_ACCEPTED_STATE . ")");

        $xml = $this->getSaveCardDoc(10099, 1100, 'abcExternal', null,'profilePass','');

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);

        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="109" card-id="1">Card successfully saved</status></root>', $sReplyBody);

        $res = $this->queryDB("SELECT * FROM EndUser.Card_Tbl WHERE accountid = 5001 and enabled = '1'");
        $this->assertTrue(is_resource($res));
        $this->assertTrue(pg_num_rows($res) == 1);
    }

    /**
     * Test scenario - SSO with auth token is unsuccessful and card is NOT saved for a existing enduser account with one card.
     * @throws ErrorException
     * @throws HTTPConnectionException
     * @throws HTTPSendException
     */
    public function testEUASSOFailureWithAuthUrl()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd, transaction_ttl) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass', 3600)");
        //Simulate error by using a actual url returning 401 or non existing url.
        $this->queryDB("INSERT INTO Client.URL_Tbl (urltypeid, clientid, url) VALUES (2, 10099, 'http://mpoint.local.cellpointmobile.com/_test/simulators/auth-error.php')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 2, '5019********3742', '/', true, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");

        $xml = $this->getSaveCardDoc(10099, 1100, 'abcExternal', null,'profilePass','');

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(403, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="31">Authentication failed</status></root>', $sReplyBody);

        $res = $this->queryDB("SELECT * FROM EndUser.Card_Tbl WHERE accountid = 5001 and enabled = '1'");
        $this->assertTrue(is_resource($res));
        $this->assertTrue(pg_num_rows($res) == 1);
    }

    /**
     * Test scenario - SSO with auth token is successful and card is saved for a new enduser account.
     * mProfile get profile and save-profile calls will fail as the endpoints are not simulated within mPoint.
     * The default flow will continue to create a new account and save the card.
     *
     * @throws ErrorException
     * @throws HTTPConnectionException
     * @throws HTTPSendException
     */
    public function testGetProfileFailsSaveAccCardSuccess()
    {
        $this->markTestIncomplete('Default value of profile id is changed from -1 to empty so that alternate flow covered in this test case will not execute');
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd, transaction_ttl) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass', 3600)");
        //Simulate error by using a actual url returning 401 or non existing url.
        $this->queryDB("INSERT INTO Client.URL_Tbl (urltypeid, clientid, url) VALUES (2, 10099, 'http://mpoint.local.cellpointmobile.com/_test/simulators/auth.php')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (urltypeid, clientid, url) VALUES (4, 10099, 'http://mpoint.local.cellpointmobile.com/_test/simulators')");
        $this->queryDB("INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type) VALUES ('ENABLE_PROFILE_ANONYMIZATION','true',10099,'client')");

        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, amount, ip, enabled, currencyid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 100, 5000, '127.0.0.1', TRUE, 208,5000,208)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, " . Constants::iPAYMENT_ACCEPTED_STATE . ")");

        $xml = $this->getSaveCardDoc(10099, 1100, 'abcExternal', null,'profilePass','');

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="109" card-id="1">Card successfully saved</status></root>', $sReplyBody);

        $res = $this->queryDB("SELECT * FROM EndUser.Card_Tbl WHERE id = 1 and enabled = '1'");
        $this->assertTrue(is_resource($res));
        $this->assertTrue(pg_num_rows($res) == 1);

        $res = $this->queryDB("SELECT profileid FROM EndUser.Account_Tbl WHERE enabled = '1' and email='jona@oismail.com' and mobile='28882861'");
        $this->assertTrue(is_resource($res));
        $this->assertTrue(pg_num_rows($res) == 1);
        $row = pg_fetch_row($res);
        $this->assertEquals(null, $row[0]);
    }

    /**
     * Test scenario - SSO with auth token is successful and card is saved along with new enduser account.
     * The profile id being present is associated with new euaid.
     *
     * @throws ErrorException
     * @throws HTTPConnectionException
     * @throws HTTPSendException
     */
    public function testSaveCardSuccessWithProfileID()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd, transaction_ttl) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass', 3600)");
        //Simulate error by using a actual url returning 401 or non existing url.
        $this->queryDB("INSERT INTO Client.URL_Tbl (urltypeid, clientid, url) VALUES (2, 10099, 'http://mpoint.local.cellpointmobile.com/_test/simulators/auth.php')");
        $this->queryDB("INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type) VALUES ('ENABLE_PROFILE_ANONYMIZATION','true',10099,'client')");

        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, amount, ip, enabled, currencyid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 100, 5000, '127.0.0.1', TRUE, 208,5000,208)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, " . Constants::iPAYMENT_ACCEPTED_STATE . ")");

        $xml = $this->getSaveCardDoc(10099, 1100, 'abcExternal', null,'profilePass','12345');//,'testemail@test.com',123456677);
        //printf($xml);
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="109" card-id="1">Card successfully saved</status></root>', $sReplyBody);

        $res = $this->queryDB("SELECT * FROM EndUser.Card_Tbl WHERE id = 1 and enabled = '1'");
        $this->assertTrue(is_resource($res));
        $this->assertTrue(pg_num_rows($res) == 1);

        $res = $this->queryDB("SELECT * FROM EndUser.Account_Tbl WHERE profileid='12345' and enabled = '1'");
        $this->assertTrue(is_resource($res));
        $this->assertTrue(pg_num_rows($res) == 1);
       /* $row = pg_fetch_row($res);
        $this->assertEquals(12345, $row[0]);*/

    }

    /**
     * Test scenario - During save card with password, a new enduser account is created if not present in mPoint
     * @throws ErrorException
     * @throws HTTPConnectionException
     * @throws HTTPSendException
     */
   public function testSaveCardForUnknownAccountPwdFlow()
   {
       $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
       $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
       $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
       $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, amount, ip, enabled, currencyid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 100, 5000, '127.0.0.1', TRUE, 208,5000,208)");
       $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, " . Constants::iPAYMENT_ACCEPTED_STATE . ")");

       $xml = $this->getSaveCardDoc(10099, 1100, 'abcExternal', '123456','','');
       $this->_httpClient->connect();

       $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
       $sReplyBody = $this->_httpClient->getReplyBody();

       $this->assertEquals(200, $iStatus);
       $this->assertStringContainsString('Card successfully saved', $sReplyBody);

       $res = $this->queryDB("SELECT * FROM EndUser.Account_Tbl WHERE enabled = '1' and email='jona@oismail.com' and mobile='28882861'");
       $this->assertTrue(is_resource($res));
       $this->assertTrue(pg_num_rows($res) == 1);
   }

   /**
     * Test scenario - SSO with password is successful and card is saved for a existing enduser account.
     * @throws ErrorException
     * @throws HTTPConnectionException
     * @throws HTTPSendException
     */
    public function testSuccessfulSaveCardPwdFlow()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', '123456', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, amount, ip, enabled, currencyid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 100, 5000, '127.0.0.1', TRUE, 208,5000,208)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, " . Constants::iPAYMENT_ACCEPTED_STATE . ")");

        $xml = $this->getSaveCardDoc(10099, 1100, 'abcExternal', '123456','','');

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);

        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="109" card-id="1">Card successfully saved</status></root>', $sReplyBody);

        $res = $this->queryDB("SELECT * FROM EndUser.Card_Tbl WHERE accountid = 5001 and enabled = '1'");
        $this->assertTrue(is_resource($res));
        $this->assertTrue(pg_num_rows($res) == 1);
    }

    /**
     * Test scenario - SSO with password is unsuccessful and card is NOT saved for a existing enduser account with one card.
     * @throws ErrorException
     * @throws HTTPConnectionException
     * @throws HTTPSendException
     */
    public function testEUASSOFailureWithPwd()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd, transaction_ttl) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass', 3600)");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', '1234567', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 2, '5019********3742', '/', true, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");

        $xml = $this->getSaveCardDoc(10099, 1100, 'abcExternal', '123456','','');

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(403, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="31">Authentication failed</status></root>', $sReplyBody);

        $res = $this->queryDB("SELECT * FROM EndUser.Card_Tbl WHERE accountid = 5001 and enabled = '1'");
        $this->assertTrue(is_resource($res));
        $this->assertTrue(pg_num_rows($res) == 1);
    }

}