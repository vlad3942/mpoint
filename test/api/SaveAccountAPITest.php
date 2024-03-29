<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class SaveAccountAPITest extends baseAPITest
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
        $aMPOINT_CONN_INFO['path'] = "/mApp/api/save_account.php";
        $aMPOINT_CONN_INFO["contenttype"] = "text/xml";
        $this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO));
    }

    protected function getSaveAccDoc($client, $account, $extAccountId, $auth_token, $passwd,$card_name,$ssn)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<root>';
        $xml .= '<save-account client-id="' . $client . '" account="' . $account . '">';
        if (isset($passwd) === true)
        {
            $xml .= '<password>' . $passwd . '</password>';
            $xml .= '<confirm-password>' . $passwd . '</confirm-password>';
        }
        if (isset($ssn) === true)
        {
            $xml .= '<social-security-number>' . $ssn . '</social-security-number>';
        }
        $xml .= '<full-name>Test User</full-name>';
        if (isset($card_name) === true)
        {
            $xml .= '<card type-id="2">' . $card_name . '</card>';
        }
        if (isset($auth_token) === true)
        {
            $xml .= '<auth-token>'.$auth_token.'</auth-token>';
        }
        $xml .= '<client-info platform="iOS" version="1.00" language="da">';
        $xml .= '<customer-ref>' . $extAccountId . '</customer-ref>';
        $xml .= '<mobile country-id="100" operator-id="10000">28882861</mobile>';
        $xml .= '<email>tes@test.com</email>';
        $xml .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
        $xml .= '</client-info>';
        $xml .= '</save-account>';
        $xml .= '</root>';
        return $xml;
    }


    public function testSuccessfulSaveAccount()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (urltypeid, clientid, url) VALUES (2, 10099, 'http://mpoint.local.cellpointmobile.com/_test/simulators/auth.php')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, amount, ip, enabled, currencyid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 100, 5000, '127.0.0.1', TRUE, 208,5000,208)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, " . Constants::iPAYMENT_ACCEPTED_STATE . ")");

        $xml = $this->getSaveAccDoc(10099, 1100, 'abcExternal', 'profilePass', 'testvalidsaveAcc', 'test',null);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);

        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertStringContainsString('Account information successfully saved', $sReplyBody);

        $res = $this->queryDB("SELECT * FROM EndUser.Account_Tbl WHERE email='tes@test.com' and mobile='28882861' and countryid=100 and enabled=true");
        $this->assertTrue(is_resource($res));
        $this->assertTrue(pg_num_rows($res) == 1);
    }

    public function testEUASSOFailureWithAuthUrl()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd, transaction_ttl) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass', 3600)");
        //Simulate error by using a actual url returning 401 or non existing url.
        $this->queryDB("INSERT INTO Client.URL_Tbl (urltypeid, clientid, url) VALUES (2, 10099, 'http://mpoint.local.cellpointmobile.com/_test/simulators/auth-error.php')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");

        $xml = $this->getSaveAccDoc(10099, 1100, 'abcExternal', 'profilePass', null, 'test',null);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(403, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="38">Invalid Auth Token: profilePass</status></root>', $sReplyBody);

        $res = $this->queryDB("SELECT * FROM EndUser.Account_Tbl WHERE email='tes@test.com' and mobile='28882861' and countryid=100");
        $this->assertTrue(is_resource($res));
        $this->assertTrue(pg_num_rows($res) == 0);
    }

    public function testSuccessfulSaveAccountAfterTwoTxn()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (urltypeid, clientid, url) VALUES (2, 10099, 'http://mpoint.local.cellpointmobile.com/_test/simulators/auth.php')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, email, mobile, enabled) VALUES (1110, 100, 'tes@test.com', '28882861', false)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, email, mobile, enabled) VALUES (1111, 100,'tes@test.com', '28882861', false)");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, amount, ip, enabled, currencyid,euaid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 100, 5000, '127.0.0.1', TRUE, 208,1110,5000,208)");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, amount, ip, enabled, currencyid,euaid,convertedamount,convertedcurrencyid) VALUES (1001002, 100, 10099, 1100, 100, 5000, '127.0.0.1', TRUE, 208,1111,5000,208)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, " . Constants::iPAYMENT_ACCEPTED_STATE . ")");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001002, " . Constants::iPAYMENT_ACCEPTED_STATE . ")");

        $xml = $this->getSaveAccDoc(10099, 1100, 'abcExternal', 'profilePass', 'testvalidsaveAcc', 'test',null);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);

        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertStringContainsString('Account information successfully saved', $sReplyBody);

        $res = $this->queryDB("SELECT * FROM EndUser.Account_Tbl WHERE email='tes@test.com' and mobile='28882861' and countryid=100 and enabled=true");
        $this->assertTrue(is_resource($res));
        $this->assertTrue(pg_num_rows($res) == 1);
        $res = $this->queryDB("SELECT * FROM EndUser.Account_Tbl WHERE email='tes@test.com' and mobile='28882861' and countryid=100 and enabled=false");
        $this->assertTrue(is_resource($res));
        $this->assertTrue(pg_num_rows($res) == 2);
    }

    public function testSuccessfulSaveAccountNoTxn()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (urltypeid, clientid, url) VALUES (2, 10099, 'http://mpoint.local.cellpointmobile.com/_test/simulators/auth.php')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");

        $xml = $this->getSaveAccDoc(10099, 1100, 'abcExternal', 'profilePass', 'testvalidsaveAcc', 'test',null);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);

        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertStringContainsString('Account information successfully saved', $sReplyBody);

        $res = $this->queryDB("SELECT * FROM EndUser.Account_Tbl WHERE email='tes@test.com' and mobile='28882861' and countryid=100 and enabled=true");
        $this->assertTrue(is_resource($res));
        $this->assertTrue(pg_num_rows($res) == 1);
        $res = $this->queryDB("SELECT * FROM EndUser.Account_Tbl WHERE email='tes@test.com' and mobile='28882861' and countryid=100 and enabled=false");
        $this->assertTrue(is_resource($res));
        $this->assertTrue(pg_num_rows($res) == 0);
    }


    public function testSuccessfulUpdateAccountPwd()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (urltypeid, clientid, url) VALUES (2, 10099, 'http://mpoint.local.cellpointmobile.com/_test/simulators/auth.php')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, email, mobile, enabled) VALUES (1111, 100,'tes@test.com', '28882861', true)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 1111)");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, amount, ip, enabled, currencyid,euaid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 100, 5000, '127.0.0.1', TRUE, 208,1111,5000,208)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, " . Constants::iPAYMENT_ACCEPTED_STATE . ")");

        $xml = $this->getSaveAccDoc(10099, 1100, 'abcExternal', 'authToken', 'testvalidsaveAcc', 'test',null);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);

        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertStringContainsString('Account information successfully saved', $sReplyBody);

        $res = $this->queryDB("SELECT * FROM EndUser.Account_Tbl WHERE email='tes@test.com' and mobile='28882861' and countryid=100 and enabled=true");
        $this->assertTrue(is_resource($res));
        $this->assertTrue(pg_num_rows($res) == 1);
        $res = $this->queryDB("SELECT * FROM EndUser.Account_Tbl WHERE email='tes@test.com' and mobile='28882861' and countryid=100 and enabled=false");
        $this->assertTrue(is_resource($res));
        $this->assertTrue(pg_num_rows($res) == 0);
    }

    public function testSuccessfulSaveAccountPwdFlow()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, amount, ip, enabled, currencyid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 100, 5000, '127.0.0.1', TRUE, 208,5000,208)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, " . Constants::iPAYMENT_ACCEPTED_STATE . ")");

        $xml = $this->getSaveAccDoc(10099, 1100, 'abcExternal', null, 'testvalidsaveAcc', 'test',null);
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);

        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertStringContainsString('Account information successfully saved', $sReplyBody);

        $res = $this->queryDB("SELECT * FROM EndUser.Account_Tbl WHERE email='tes@test.com' and mobile='28882861' and countryid=100 and enabled=true");
        $this->assertTrue(is_resource($res));
        $this->assertTrue(pg_num_rows($res) == 1);
    }
}