<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class DelCardAPIValidationTest extends baseAPITest
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
        $aMPOINT_CONN_INFO['path'] = "/mApp/api/del_card.php";
        $aMPOINT_CONN_INFO["contenttype"] = "text/xml";
        $this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO));
    }

    protected function getDelCardDoc($client, $account, $cardid, $extAccountId, $passwd, $intAccountId = null, $clientpasswd = null)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<root>';
        $xml .= '<delete-card client-id="' . $client . '" account="' . $account . '">';
        $xml .= '<card>' . $cardid . '</card>';
        if (isset($intAccountId) === true) {
            $secret = sha1($client . $clientpasswd);
            $xml .= '<auth-token>' . htmlspecialchars(General::genToken($intAccountId, $secret), ENT_NOQUOTES) . '</auth-token>';
        } else {
            $xml .= '<password>' . $passwd . '</password>';
        }
        $xml .= '<client-info platform="iOS" version="1.00" language="da">';
        $xml .= '<customer-ref>' . $extAccountId . '</customer-ref>';
        $xml .= '<mobile country-id="100" operator-id="10000">28882861</mobile>';
        $xml .= '<email>jona@oismail.com</email>';
        $xml .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
        $xml .= '</client-info>';
        $xml .= '</delete-card>';
        $xml .= '</root>';

        return $xml;
    }

    public function testUnknownCard()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, amount, ip, enabled, currencyid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 100, 5000, '127.0.0.1', TRUE,208,5000,208)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");

        $xml = $this->getDelCardDoc(10099, 1100, 5002, 'abcExternal', 'profilePass');

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(400, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="43" >Card not found.</status></root>', $sReplyBody);
    }

    public function testSuccessfulDeleteCard()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (urltypeid, clientid, url) VALUES (2, 10099, 'http://mpoint.local.cellpointmobile.com/_test/simulators/auth.php')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 2, '5019********3742', '/', true, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, amount, ip, enabled, currencyid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 100, 5000, '127.0.0.1', TRUE, 208,5000,208)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, " . Constants::iPAYMENT_ACCEPTED_STATE . ")");

        $xml = $this->getDelCardDoc(10099, 1100, 61775, 'abcExternal', 'profilePass', 5001, 'Tpass');

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);

        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="100" eua-id="5001">Card successfully deleted</status><token>1767989 ### CELLPOINT ### 100 ### DKK</token></root>', $sReplyBody);

        $res = $this->queryDB("SELECT * FROM EndUser.Card_Tbl WHERE id = 61775 and enabled = '1'");
        $this->assertTrue(is_resource($res));
        $this->assertTrue(pg_num_rows($res) == 0);
    }

    public function testNotAllowedOngoingTransaction()
    {
        $authTime = date('c', time() - 1800); //-30 minutes

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd, transaction_ttl) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass', 3600)");
        $this->queryDB("INSERT INTO Client.URL_Tbl (urltypeid, clientid, url) VALUES (2, 10099, 'http://mpoint.local.cellpointmobile.com/_test/simulators/auth.php')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 2, '5019********3742', '/', true, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, euaid, countryid, amount, ip, created, enabled, currencyid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 5001, 100, 5000, '127.0.0.1', '" . $authTime . "', TRUE, 208,5000,208)");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, euaid, countryid, amount, ip, created, enabled, currencyid,convertedamount,convertedcurrencyid) VALUES (1001002, 100, 10099, 1100, 5001, 100, 5000, '127.0.0.1', '" . $authTime . "', TRUE, 208,5000,208)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, " . Constants::iPAYMENT_ACCEPTED_STATE . ")");
        $xml = $this->getDelCardDoc(10099, 1100, 61775, 'abcExternal', 'profilePass', 5001, 'Tpass');

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(403, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="51">Cannot delete card with ongoing transactions</status></root>', $sReplyBody);

        $res = $this->queryDB("SELECT * FROM EndUser.Card_Tbl WHERE id = 61775 and enabled = '1'");
        $this->assertTrue(is_resource($res));
        $this->assertTrue(pg_num_rows($res) == 1);
    }

    public function testDifferentEUAOngoingTransaction()
    {
        $authTime = date('c', time() - 1800); //-30 minutes

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd, transaction_ttl) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass', 3600)");
        $this->queryDB("INSERT INTO Client.URL_Tbl (urltypeid, clientid, url) VALUES (2, 10099, 'http://mpoint.local.cellpointmobile.com/_test/simulators/auth.php')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, passwd, enabled) VALUES (5002, 100, 'abcExternal2', '30206162', 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5002)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 2, '5019********3742', '/', true, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61776, 5002, 2, 2, '5020********3742', '/', true, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        //$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, euaid, countryid, amount, ip, created, enabled) VALUES (1001001, 100, 10099, 1100, 5001, 100, 5000, '127.0.0.1', '". $authTime. "', TRUE)");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, euaid, countryid, amount, ip, created, enabled, currencyid,convertedamount,convertedcurrencyid) VALUES (1001002, 100, 10099, 1100, 5002, 100, 5000, '127.0.0.1', '" . $authTime . "', TRUE, 208,5000,208)");
        //$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001002, " . Constants::iPAYMENT_ACCEPTED_STATE . ")");
        $xml = $this->getDelCardDoc(10099, 1100, 61775, 'abcExternal', 'profilePass', 5001, 'Tpass');

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="100" eua-id="5001">Card successfully deleted</status><token>1767989 ### CELLPOINT ### 100 ### DKK</token></root>', $sReplyBody);

        $res = $this->queryDB("SELECT * FROM EndUser.Card_Tbl WHERE id = 61775 and enabled = '1'");
        $this->assertTrue(is_resource($res));
        $this->assertTrue(pg_num_rows($res) == 0);

        $res = $this->queryDB("SELECT * FROM EndUser.Card_Tbl WHERE id = 61776 and enabled = '1'");
        $this->assertTrue(is_resource($res));
        $this->assertTrue(pg_num_rows($res) == 1);
    }

    public function testExpiredOngoingTransaction()
    {
        $authTime = date('c', time() - 3660); //-61 minutes

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd, transaction_ttl) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass', 3600)");
        $this->queryDB("INSERT INTO Client.URL_Tbl (urltypeid, clientid, url) VALUES (2, 10099, 'http://mpoint.local.cellpointmobile.com/_test/simulators/auth.php')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 2, '5019********3742', '/', true, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, amount, ip, created, enabled, currencyid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 100, 5000, '127.0.0.1', '" . $authTime . "', TRUE, 208,5000,208)");
        $this->queryDB("INSERT INTO EndUser.Transaction_Tbl (accountid, txnid, typeid) VALUES (5001, 1001001, 40)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, " . Constants::iPAYMENT_ACCEPTED_STATE . ")");
        $xml = $this->getDelCardDoc(10099, 1100, 61775, 'abcExternal', 'profilePass', 5001, 'Tpass');

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="100" eua-id="5001">Card successfully deleted</status><token>1767989 ### CELLPOINT ### 100 ### DKK</token></root>', $sReplyBody);

        $res = $this->queryDB("SELECT * FROM EndUser.Card_Tbl WHERE id = 61775 and enabled = '1'");
        $this->assertTrue(is_resource($res));
        $this->assertTrue(pg_num_rows($res) == 0);
    }

    public function testCompletedOngoingTransaction()
    {
        $authTime = date('c', time() - 1800); //-61 minutes

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd, transaction_ttl) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass', 3600)");
        // $this->queryDB("INSERT INTO Client.Url_Tbl (clientid, urltypeid, url) VALUES (10099, 2, '. $this->_authUrl .' )");

        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 2, '5019********3742', '/', true, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, amount, ip, created, enabled, currencyid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 100, 5000, '127.0.0.1', '" . $authTime . "', TRUE, 208,5000,208)");
        $this->queryDB("INSERT INTO EndUser.Transaction_Tbl (accountid, txnid, typeid) VALUES (5001, 1001001, 40)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, " . Constants::iPAYMENT_ACCEPTED_STATE . ")");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, " . Constants::iPAYMENT_CAPTURED_STATE . ")");
        $this->queryDB("INSERT INTO Client.URL_Tbl (urltypeid, clientid, url) VALUES (2, 10099, 'http://mpoint.local.cellpointmobile.com/_test/simulators/auth.php')");
        $xml = $this->getDelCardDoc(10099, 1100, 61775, 'abcExternal', 'profilePass', 5001, 'Tpass');

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="100" eua-id="5001">Card successfully deleted</status><token>1767989 ### CELLPOINT ### 100 ### DKK</token></root>', $sReplyBody);

        $res = $this->queryDB("SELECT * FROM EndUser.Card_Tbl WHERE id = 61775 and enabled = '1'");
        $this->assertTrue(is_resource($res));
        $this->assertTrue(pg_num_rows($res) == 0);
    }

    public function testRefundedOngoingTransaction()
    {
        $authTime = date('c', time() - 1800); //-61 minutes

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd, transaction_ttl) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass', 3600)");
        $this->queryDB("INSERT INTO Client.URL_Tbl (urltypeid, clientid, url) VALUES (2, 10099, 'http://mpoint.local.cellpointmobile.com/_test/simulators/auth.php')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 2, '5019********3742', '/', true, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, amount, ip, created, enabled, currencyid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 100, 5000, '127.0.0.1', '" . $authTime . "', TRUE, 208,5000,208)");
        $this->queryDB("INSERT INTO EndUser.Transaction_Tbl (accountid, txnid, typeid) VALUES (5001, 1001001, 40)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, " . Constants::iPAYMENT_ACCEPTED_STATE . ")");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, " . Constants::iPAYMENT_CAPTURED_STATE . ")");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, " . Constants::iPAYMENT_REFUNDED_STATE . ")");
        $xml = $this->getDelCardDoc(10099, 1100, 61775, 'abcExternal', 'profilePass', 5001, 'Tpass');

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="100" eua-id="5001">Card successfully deleted</status><token>1767989 ### CELLPOINT ### 100 ### DKK</token></root>', $sReplyBody);

        $res = $this->queryDB("SELECT * FROM EndUser.Card_Tbl WHERE id = 61775 and enabled = '1'");
        $this->assertTrue(is_resource($res));
        $this->assertTrue(pg_num_rows($res) == 0);
    }

    public function testTTLUnset()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (urltypeid, clientid, url) VALUES (2, 10099, 'http://mpoint.local.cellpointmobile.com/_test/simulators/auth.php')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 2, '5019********3742', '/', true, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, amount, ip, enabled, currencyid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 100, 5000, '127.0.0.1', TRUE, 208,5000,208)");
        $this->queryDB("INSERT INTO EndUser.Transaction_Tbl (accountid, txnid, typeid) VALUES (5001, 1001001, 40)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, " . Constants::iPAYMENT_ACCEPTED_STATE . ")");

        $xml = $this->getDelCardDoc(10099, 1100, 61775, 'abcExternal', 'profilePass', 5001, 'Tpass');

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="100" eua-id="5001">Card successfully deleted</status><token>1767989 ### CELLPOINT ### 100 ### DKK</token></root>', $sReplyBody);

        $res = $this->queryDB("SELECT * FROM EndUser.Card_Tbl WHERE id = 61775 and enabled = '1'");
        $this->assertTrue(is_resource($res));
        $this->assertTrue(pg_num_rows($res) == 0);
    }

    public function testRejectedBeforeAuthorize()
    {
        $authTime = date('c', time() - 1800); //-30 minutes

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd, transaction_ttl) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass', 3600)");
        $this->queryDB("INSERT INTO Client.URL_Tbl (urltypeid, clientid, url) VALUES (2, 10099, 'http://mpoint.local.cellpointmobile.com/_test/simulators/auth.php')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 2, '5019********3742', '/', true, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, euaid, countryid, amount, ip, created, enabled, currencyid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 5001, 100, 5000, '127.0.0.1', '" . $authTime . "', TRUE, 208,5000,208)");
        $this->queryDB("INSERT INTO EndUser.Transaction_Tbl (accountid, txnid, typeid) VALUES (5001, 1001001, 40)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, " . Constants::iPAYMENT_REJECTED_STATE . ")");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, " . Constants::iPAYMENT_ACCEPTED_STATE . ")");
        $xml = $this->getDelCardDoc(10099, 1100, 61775, 'abcExternal', 'profilePass', 5001, 'Tpass');

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        //$this->assertEquals(403, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="51">Cannot delete card with ongoing transactions</status></root>', $sReplyBody);

        $res = $this->queryDB("SELECT * FROM EndUser.Card_Tbl WHERE id = 61775 and enabled = '1'");
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
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 2, '5019********3742', '/', true, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");

        $xml = $this->getDelCardDoc(10099, 1100, 61775, 'abcExternal', 'profilePass', 5001, 'Tpass');

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(403, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="31">Authentication failed</status></root>', $sReplyBody);

        $res = $this->queryDB("SELECT * FROM EndUser.Card_Tbl WHERE id = 61775 and enabled = '1'");
        $this->assertTrue(is_resource($res));
        $this->assertTrue(pg_num_rows($res) == 1);
        $this->bIgnoreErrors=true; //warning or error is expected.
    }

}