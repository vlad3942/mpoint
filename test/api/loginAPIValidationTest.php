<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class LoginAPIValidationTest extends mPointBaseAPITest
{

    protected $_aMPOINT_CONN_INFO;

    public function __construct()
    {
        $this->constHTTPClient();
    }

    public function constHTTPClient()
    {
        global $aMPOINT_CONN_INFO;
        $aMPOINT_CONN_INFO['path'] = "/mApp/api/login.php";
        $aMPOINT_CONN_INFO["contenttype"] = "text/xml";
        $this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO) );
    }

	protected function getLoginDoc($client, $extAccountId, $passwd, $intAccountId=null, $clientpasswd=null)
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml .= '<root>';
		$xml .= '<login client-id="'. $client .'">';
		if (isset($intAccountId) === true)
		{
			$secret = sha1($client. $clientpasswd);
			$xml .= '<auth-token>'. htmlspecialchars(General::genToken($intAccountId, $secret), ENT_NOQUOTES) .'</auth-token>';
			$xml .= '<auth-url>'. $this->_aMPOINT_CONN_INFO["protocol"] .'://'. $this->_aMPOINT_CONN_INFO["host"] .'/login/sys/auth.php</auth-url>';
		}
		else
		{
			$xml .= '<password>'. $passwd .'</password>';
		}
		$xml .= '<client-info platform="iOS" version="1.00" language="da">';
		$xml .= '<customer-ref>'. $extAccountId. '</customer-ref>';
		$xml .= '<mobile country-id="100" operator-id="10000">28882861</mobile>';
		$xml .= '<email>jona@oismail.com</email>';
		$xml .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
		$xml .= '</client-info>';
		$xml .= '</login>';
		$xml .= '</root>';

		return $xml;
	}

	public function testSuccessfulLogin()
	{
		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 2, '5019********3742', '/', true, 113, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
		$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, amount, ip, enabled) VALUES (1001001, 100, 113, 1100, 100, 5000, '127.0.0.1', TRUE)");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");

		$xml = $this->getLoginDoc(113, 'abcExternal', 'profilePass');

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
		$this->assertContains('<?xml version="1.0" encoding="UTF-8"?><root><account id="5001" country-id="100"><first-name></first-name><last-name></last-name><mobile country-id="100" verified="true">29612109</mobile><email></email><password mask="***********">profilePass</password><balance country-id="100" currency="DKK" symbol="" format="{PRICE} {CURRENCY}">0</balance><funds>0,00 </funds><points country-id="0" currency="points" symbol="points" format="{PRICE} {CURRENCY}">0</points><clients><client id="113" store-card="0">Test Client</client></clients>', $sReplyBody);
	}

	public function testUnSuccessfulLogin()
	{
		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 2, '5019********3742', '/', true, 113, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
		$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, amount, ip, enabled) VALUES (1001001, 100, 113, 1100, 100, 5000, '127.0.0.1', TRUE)");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");

		$xml = $this->getLoginDoc(113, 'abcExternal', 'WrongprofilePass');

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(403, $iStatus);
		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="31" /></root>', $sReplyBody);

		$res =  $this->queryDB("SELECT attempts, enabled FROM EndUser.Account_Tbl WHERE id = 5001");
		$this->assertTrue(is_resource($res) && pg_num_rows($res) == 1);
		$row = pg_fetch_assoc($res);
		$this->assertTrue($row["attempts"] == 1);
		$this->assertEquals('t', $row["enabled"]);


		$this->constHTTPClient();
		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(403, $iStatus);
		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="32" /></root>', $sReplyBody);

		$res =  $this->queryDB("SELECT attempts, enabled FROM EndUser.Account_Tbl WHERE id = 5001");
		$this->assertTrue(is_resource($res) && pg_num_rows($res) == 1);
		$row = pg_fetch_assoc($res);
		$this->assertTrue($row["attempts"] == 2);
		$this->assertEquals('t', $row["enabled"]);


		$this->constHTTPClient();
		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(403, $iStatus);
		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="33" /></root>', $sReplyBody);

		$res =  $this->queryDB("SELECT attempts, enabled FROM EndUser.Account_Tbl WHERE id = 5001");
		$this->assertTrue(is_resource($res) && pg_num_rows($res) == 1);
		$row = pg_fetch_assoc($res);
		$this->assertTrue($row["attempts"] == 3);
		$this->assertEquals('f', $row["enabled"]);
	}

	public function testAttemptsReset()
	{
		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 2, '5019********3742', '/', true, 113, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
		$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, amount, ip, enabled) VALUES (1001001, 100, 113, 1100, 100, 5000, '127.0.0.1', TRUE)");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");

		$xml = $this->getLoginDoc(113, 'abcExternal', 'WrongprofilePass');

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(403, $iStatus);
		$this->assertContains('<?xml version="1.0" encoding="UTF-8"?><root><status code="31" /></root>', $sReplyBody);

		$res =  $this->queryDB("SELECT attempts FROM EndUser.Account_Tbl WHERE id = 5001");
		$this->assertTrue(is_resource($res) && pg_num_rows($res) == 1);
		$row = pg_fetch_assoc($res);
		$this->assertTrue($row["attempts"] == 1);

		// Now we will test that the number of login attempts gets a reset on the user profile
		$xml = $this->getLoginDoc(113, 'abcExternal', 'profilePass');

		$this->constHTTPClient();
		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
		$this->assertContains('<?xml version="1.0" encoding="UTF-8"?><root><account id="5001" country-id="100"><first-name></first-name><last-name></last-name><mobile country-id="100" verified="true">29612109</mobile><email></email><password mask="***********">profilePass</password><balance country-id="100" currency="DKK" symbol="" format="{PRICE} {CURRENCY}">0</balance><funds>0,00 </funds><points country-id="0" currency="points" symbol="points" format="{PRICE} {CURRENCY}">0</points><clients><client id="113" store-card="0">Test Client</client></clients>', $sReplyBody);

		$res =  $this->queryDB("SELECT attempts FROM EndUser.Account_Tbl WHERE id = 5001");
		$this->assertTrue(is_resource($res) && pg_num_rows($res) == 1);
		$row = pg_fetch_assoc($res);
		$this->assertTrue($row["attempts"] == 0);
	}

	public function testOngoingTransactionSignal()
	{
		$authTime = date('c', time() - 1800); //-30 minutes

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd, transaction_ttl) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass', 3600)");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 2, '5019********3742', '/', true, 113, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
		$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, euaid, countryid, amount, ip, enabled) VALUES (1001001, 100, 113, 1100, 5001, 100, 5000, '127.0.0.1', TRUE)");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid, created) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ", '". $authTime ."')");

		$xml = $this->getLoginDoc(113, 'abcExternal', 'WrongprofilePass');

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(403, $iStatus);
		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="31" /></root>', $sReplyBody);

		$res =  $this->queryDB("SELECT attempts FROM EndUser.Account_Tbl WHERE id = 5001");
		$this->assertTrue(is_resource($res) && pg_num_rows($res) == 1);
		$row = pg_fetch_assoc($res);
		$this->assertTrue($row["attempts"] == 1);


		$this->constHTTPClient();
		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(403, $iStatus);
		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="34" /></root>', $sReplyBody);

		$res =  $this->queryDB("SELECT attempts FROM EndUser.Account_Tbl WHERE id = 5001");
		$this->assertTrue(is_resource($res) && pg_num_rows($res) == 1);
		$row = pg_fetch_assoc($res);
		$this->assertTrue($row["attempts"] == 2);
	}

	public function testExpiredOngoingTransaction()
	{
		$authTime = date('c', time() - 3660); //-61 minutes

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd, transaction_ttl) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass', 3600)");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 2, '5019********3742', '/', true, 113, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
		$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, euaid, countryid, amount, ip, enabled) VALUES (1001001, 100, 113, 1100, 5001, 100, 5000, '127.0.0.1', TRUE)");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid, created) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ", '". $authTime ."')");

		$xml = $this->getLoginDoc(113, 'abcExternal', 'WrongprofilePass');

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(403, $iStatus);
		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="31" /></root>', $sReplyBody);

		$res =  $this->queryDB("SELECT attempts FROM EndUser.Account_Tbl WHERE id = 5001");
		$this->assertTrue(is_resource($res) && pg_num_rows($res) == 1);
		$row = pg_fetch_assoc($res);
		$this->assertTrue($row["attempts"] == 1);


		$this->constHTTPClient();
		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(403, $iStatus);
		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="32" /></root>', $sReplyBody);

		$res =  $this->queryDB("SELECT attempts FROM EndUser.Account_Tbl WHERE id = 5001");
		$this->assertTrue(is_resource($res) && pg_num_rows($res) == 1);
		$row = pg_fetch_assoc($res);
		$this->assertTrue($row["attempts"] == 2);
	}

	public function testCompletedOngoingTransaction()
	{
		$authTime = date('c', time() - 1800); //-30 minutes

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd, transaction_ttl) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass', 3600)");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 2, '5019********3742', '/', true, 113, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
		$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, euaid, countryid, amount, ip, enabled) VALUES (1001001, 100, 113, 1100, 5001, 100, 5000, '127.0.0.1', TRUE)");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid, created) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ", '". $authTime ."')");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_CAPTURED_STATE. ")");

		$xml = $this->getLoginDoc(113, 'abcExternal', 'WrongprofilePass');

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(403, $iStatus);
		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="31" /></root>', $sReplyBody);

		$res =  $this->queryDB("SELECT attempts FROM EndUser.Account_Tbl WHERE id = 5001");
		$this->assertTrue(is_resource($res) && pg_num_rows($res) == 1);
		$row = pg_fetch_assoc($res);
		$this->assertTrue($row["attempts"] == 1);


		$this->constHTTPClient();
		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(403, $iStatus);
		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="32" /></root>', $sReplyBody);

		$res =  $this->queryDB("SELECT attempts FROM EndUser.Account_Tbl WHERE id = 5001");
		$this->assertTrue(is_resource($res) && pg_num_rows($res) == 1);
		$row = pg_fetch_assoc($res);
		$this->assertTrue($row["attempts"] == 2);
	}

}
