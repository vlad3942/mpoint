<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");

class NetaxeptCallbackAPITest extends mPointBaseAPITest
{
	protected $_aMPOINT_CONN_INFO;

	public function __construct()
	{
		$this->constHTTPClient();
	}

	public function constHTTPClient()
	{
		global $aMPOINT_CONN_INFO;
		$aMPOINT_CONN_INFO['path'] = "/netaxept/accept.php?responseCode=OK&mpoint-id=1001001&transactionId=15469928";
		$aMPOINT_CONN_INFO['method'] = "GET";
		$aMPOINT_CONN_INFO["contenttype"] = "application/x-www-form-urlencoded";
		$this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
		$this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO) );
	}

	public function testSuccessfulCallback()
	{
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
		$pspID = Constants::iNETAXEPT_PSP;

		/* Setup netaxept simulator, through error file mark */
		$config = new stdClass();
		$config->CardIssuer = 'Dankort';
		$config->AmountAuthorized = 5000;
		$config->AmountCaptured = 0;
		trigger_error("NETAXEPT SIMULATOR CONFIG :: ". base64_encode(json_encode($config) ) );

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '1')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (113, 17, $pspID)");
		$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, pspid, extid, callbackurl, amount, ip, enabled, keywordid) VALUES (1001001, '900-55150298',". Constants::iPURCHASE_VIA_APP .", 113, 1100, 100, $pspID, 15469928, '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1)");

		$this->_httpClient->connect();

		$oJSON = new stdClass();
		$oJSON->TransactionId = 15469928;

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders() );
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
		$this->assertContains('<status code="2000">', $sReplyBody);

		$res =  $this->queryDB("SELECT extid, stateid FROM Log.Message_Tbl m, Log.Transaction_Tbl t WHERE t.id = 1001001 AND m.txnid = t.id");
		$this->assertTrue(is_resource($res) );

		$aStates = array();
		while ($row = pg_fetch_assoc($res) )
		{
			$aStates[] = $row["stateid"];
			$this->assertEquals("15469928", $row["extid"]);
		}

		$this->assertTrue(is_int(array_search(Constants::iPAYMENT_ACCEPTED_STATE, $aStates) ) );

		$res =  $this->queryDB("SELECT id FROM Enduser.Account_Tbl");
		$this->assertTrue(is_resource($res) );

		$this->assertEquals(0, pg_num_rows($res) );
	}

	public function testSuccessfulCallbackAndStoreCard()
	{
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
		$pspID = Constants::iNETAXEPT_PSP;

		/* Setup netaxept simulator, through error file mark */
		$config = new stdClass();
		$config->CardIssuer = 'Dankort';
		$config->AmountAuthorized = 5000;
		$config->AmountCaptured = 0;
		$config->Recurring = new stdClass();
		$config->Recurring->PanHash = md5("somehash");
		trigger_error("NETAXEPT SIMULATOR CONFIG :: ". base64_encode(json_encode($config) ) );

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '1')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (113, 17, $pspID)");
		$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, pspid, extid, callbackurl, amount, ip, enabled, keywordid, mobile, email) VALUES (1001001, '900-55150298',". Constants::iPURCHASE_VIA_APP .", 113, 1100, 100, $pspID, 15469928, '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, '29612109', 'johan@cellpointmobile.com')");

		$this->_httpClient->connect();

		$oJSON = new stdClass();
		$oJSON->TransactionId = 15469928;

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders() );
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
		$this->assertContains('<status code="2000">', $sReplyBody);

		$res =  $this->queryDB("SELECT extid, stateid, euaid FROM Log.Message_Tbl m, Log.Transaction_Tbl t WHERE t.id = 1001001 AND m.txnid = t.id");
		$this->assertTrue(is_resource($res) );

		$aStates = array();
		$euaId = -1;
		while ($txnRow = pg_fetch_assoc($res) )
		{
			$aStates[] = $txnRow["stateid"];
			$euaId = $txnRow["euaid"];
			$this->assertEquals("15469928", $txnRow["extid"]);
		}

		$this->assertTrue(is_int(array_search(Constants::iPAYMENT_ACCEPTED_STATE, $aStates) ) );

		/* Assert that an enduser account has actually been created */
		$res =  $this->queryDB("SELECT id FROM Enduser.Account_Tbl");
		$this->assertTrue(is_resource($res) );

		$this->assertEquals(1, pg_num_rows($res) );
		$userRow = pg_fetch_assoc($res);

		/* Assert that the payment transaction has been associated with the new enduser account */
		$this->assertEquals($userRow["id"], $euaId);
	}

}
