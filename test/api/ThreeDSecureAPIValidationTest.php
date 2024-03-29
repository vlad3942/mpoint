<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class ThreeDSecureAPIValidationTest extends baseAPITest
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
        $aMPOINT_CONN_INFO['path'] = "/mApp/api/3dsecure.php";
        $aMPOINT_CONN_INFO["contenttype"] = "text/xml";
        $this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO) );
    }

	protected function getRequestDoc($client, $account, $txn=1001001, $contentType='text/html', $url='http://cellpointmobile.com')
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml .= '<root>';
		$xml .= '<request-3dsecure client-id="'. $client .'" account="'. $account .'">';
		$xml .= '<transaction id="'. $txn .'">800-1234</transaction>';
		$xml .= '<challenge content-type="'. $contentType .'" url="'. $url .'">';
		$xml .= ' &lt;html&gt;&lt;body&gt;Lorem Ipsum&lt;/body&gt;&lt;/html&gt;';
		$xml .= '</challenge>';
		$xml .= '<client-info platform="iOS" version="1.00" language="da">';
		$xml .= '<mobile country-id="100" operator-id="10000">28882861</mobile>';
		$xml .= '<email>jona@oismail.com</email>';
		$xml .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
		$xml .= '</client-info>';
		$xml .= '</request-3dsecure>';
		$xml .= '</root>';

		return $xml;
	}


    public function testBadRequestInvalidRequestBody()
    {
		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, enabled, username, passwd) VALUES (10099, 1, 100, 'Test Client', true, 'Tuser', 'Tpass')");

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), '<xml></xml>');
		$sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(400, $iStatus);
        $this->assertStringContainsString('<root><status code="400">Element \'xml\'', $sReplyBody);
    }

    public function testBadRequestDisabledClient()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, enabled, username, passwd) VALUES (10099, 1, 100, 'Test Client', false, 'Tuser', 'Tpass')");

		$xml = $this->getRequestDoc(10099, 1100);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(400, $iStatus);
		$this->assertStringContainsString('<status code="3">Client ID / Account doesn\'t match</status>', $sReplyBody);
    }

    public function testDisabledAccount()
    {
		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, enabled, username, passwd) VALUES (10099, 1, 100, 'Test Client', true, 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, enabled) VALUES (1100, 10099, false)");

		$xml = $this->getRequestDoc(10099, 1100);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(400, $iStatus);
		$this->assertStringContainsString('<status code="14">Client ID / Account doesn\'t match</status>', $sReplyBody);
	}

    public function testUndefinedTransaction()
    {
		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");

		$xml = $this->getRequestDoc(10099, 1100);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(400, $iStatus);
		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="22">Transaction and Order ID doesn\'t match. mPoint ID: 1001001 Order ID: 800-1234</status></root>', $sReplyBody);
	}

	public function testTransactionInWrongStateAuthorized()
	{
		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
		$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid,convertedamount) VALUES (1001001, 100, 10099, 1100, 100, 2, '1512', '800-1234', '', 5000, '127.0.0.1', TRUE, 1,5000)");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iINPUT_VALID_STATE .")");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE .")");

		$xml = $this->getRequestDoc(10099, 1100);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(400, $iStatus);
		$this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?><root><status code="40">Transaction not in right state. mPoint ID: 1001001 Client ID: 10099</status></root>', $sReplyBody);
	}

	public function testTransactionInWrongStateRefunded()
	{
		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
		$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid,convertedamount) VALUES (1001001, 100, 10099, 1100, 100, 2, '1512', '800-1234', '', 5000, '127.0.0.1', TRUE, 1,5000)");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iINPUT_VALID_STATE .")");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE .")");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_CAPTURED_STATE .")");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_REFUNDED_STATE .")");

		$xml = $this->getRequestDoc(10099, 1100);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(400, $iStatus);
		$this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?><root><status code="37">Transaction not in right state. mPoint ID: 1001001 Client ID: 10099</status></root>', $sReplyBody);
	}

	public function testUnauthorized()
	{
		$xml = $this->getRequestDoc(1, 1);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders(), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(401, $iStatus);
		$this->assertStringContainsString('<status code="401">Authorization required</status>', $sReplyBody);
	}

	public function testWrongUsernamePassword()
	{
		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");

		$xml = $this->getRequestDoc(10099, 1100);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Twrong'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(401, $iStatus);
		$this->assertStringContainsString('<status code="401">Username / Password doesn\'t match</status>', $sReplyBody);
	}

	public function testWrongContentType()
	{
		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
		$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid,convertedamount) VALUES (1001001, 100, 10099, 1100, 100, 2, '1512', '800-1234', '', 5000, '127.0.0.1', TRUE, 1,5000)");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iINPUT_VALID_STATE .")");

		$xml = $this->getRequestDoc(10099, 1100, '1001001', '1/2');

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(400, $iStatus);
		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="42">Challenge invalid. Content-Type: 1/2 URL: http://cellpointmobile.com</status></root>', $sReplyBody);
	}

	public function testWrongURL()
	{
		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
		$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid) VALUES (1001001, 100, 10099, 1100, 100, 2, '1512', '800-1234', '', 5000, '127.0.0.1', TRUE, 1)");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iINPUT_VALID_STATE .")");

		$xml = $this->getRequestDoc(10099, 1100, '1001001', 'text/html', 'http://');

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(400, $iStatus);
		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="43">Challenge invalid. Content-Type: text/html URL: http://</status></root>', $sReplyBody);
	}

	public function testNotConfiguredForClient()
	{
		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
		$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid) VALUES (1001001, 100, 10099, 1100, 100, 2, '1512', '800-1234', '', 5000, '127.0.0.1', TRUE, 1)");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iINPUT_VALID_STATE .")");

		$xml = $this->getRequestDoc(10099, 1100);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(405, $iStatus);
		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="51">Mobile Optimized 3D secure not configured for client: 10099</status></root>', $sReplyBody);
	}
}
