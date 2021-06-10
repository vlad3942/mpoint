<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class UpdatePaymentStatusCronTest extends baseAPITest
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
		$aConnInfo = $aMPOINT_CONN_INFO;
		$aConnInfo['method'] = "GET";
		$aConnInfo['path'] = "/internal/update_payment_status.php";
		$aConnInfo["contenttype"] = "application/x-www-form-urlencoded";
		$this->_aMPOINT_CONN_INFO = $aConnInfo;
		$this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aConnInfo) );
	}

	public function testPaymentHasBeenCapturedMobilepay()
	{
		$pspID = Constants::iMOBILEPAY_PSP;
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
		$sCreated = date("Y-m-d H:i:s", time()-3600*10); //Now -10 hours

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name, username, passwd) VALUES (1, 10099, $pspID, '1', 'Tusername', 'Tpassword')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10099, 17, $pspID)");
        $this->queryDB("INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type,scope) VALUES ('IS_LEGACY', 'true', 10099, 'client',0)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 208, 100, 4001, '1513-2001', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, created, enabled, keywordid, fee, currencyid, sessionid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 100, $pspID, '1512', '1513-2001', '". $sCallbackURL. "', 5000, '127.0.0.1', '". $sCreated ."', TRUE, 1, 50, 208, 1,5000,208)");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders() );
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
		$this->assertStringContainsString("Updated transaction status for mPoint id's (1001001)", $sReplyBody);

		$res =  $this->queryDB("SELECT captured, stateid FROM Log.Transaction_Tbl t, Log.Message_Tbl m WHERE t.id = 1001001 AND m.txnid = t.id");
		$this->assertTrue(is_resource($res) );

		$aStates = array();
		$iCaptured = -1;
		while ($row = pg_fetch_assoc($res) )
		{
			$aStates[] = $row["stateid"];
			$iCaptured = intval($row["captured"]);
		}

		$this->assertTrue(is_int(array_search(Constants::iPAYMENT_CAPTURED_STATE, $aStates) ) );
		$this->assertEquals(5000, $iCaptured);
	}

	public function testPaymentHasBeenRefundedMobilepay()
	{
		$pspID = Constants::iMOBILEPAY_PSP;
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
		$sCreated = date("Y-m-d H:i:s", time()-3600*10); //Now -10 hours

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name, username, passwd) VALUES (1, 10099, $pspID, '1', 'Tusername', 'Tpassword')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10099, 17, $pspID)");
        $this->queryDB("INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type,scope) VALUES ('IS_LEGACY', 'true', 10099, 'client',0)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 208, 100, 4001, '1513-2003', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, created, enabled, keywordid, fee, currencyid, sessionid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 100, $pspID, '1512', '1513-2003', '". $sCallbackURL. "', 5000, '127.0.0.1', '". $sCreated ."', TRUE, 1, 50, 208, 1,5000,208)");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders() );
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
		$this->assertStringContainsString("Updated transaction status for mPoint id's (1001001)", $sReplyBody);

		$res =  $this->queryDB("SELECT refund, stateid FROM Log.Transaction_Tbl t, Log.Message_Tbl m WHERE t.id = 1001001 AND m.txnid = t.id");
		$this->assertTrue(is_resource($res) );

		$aStates = array();
		$iRefunded = -1;
		while ($row = pg_fetch_assoc($res) )
		{
			$aStates[] = $row["stateid"];
			$iRefunded = intval($row["refund"]);
		}

		$this->assertTrue(is_int(array_search(Constants::iPAYMENT_REFUNDED_STATE, $aStates) ) );
		$this->assertEquals(5000, $iRefunded);
	}

	public function testPaymentNotActiveMobilepay()
	{
		$pspID = Constants::iMOBILEPAY_PSP;
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
		$sCreated = date("Y-m-d H:i:s", time()-3600*10); //Now -10 hours

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name, username, passwd) VALUES (1, 10099, $pspID, '1', 'Tusername', 'Tpassword')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10099, 17, $pspID)");
        $this->queryDB("INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type,scope) VALUES ('IS_LEGACY', 'true', 10099, 'client',0)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 208, 100, 4001, '1513-2001', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, created, enabled, keywordid, fee, currencyid, sessionid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 100, $pspID, '1512', '1513-2001', '". $sCallbackURL. "', 5000, '127.0.0.1', '". $sCreated ."', TRUE, 1, 50, 208, 1,5000,208)");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_CANCELLED_STATE. ")");

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders() );
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
		$this->assertStringContainsString("Updated transaction status for mPoint id's ()", $sReplyBody);

		$res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY created ASC");
		$this->assertTrue(is_resource($res) );

		$aStates = array();
		while ($row = pg_fetch_assoc($res) )
		{
			$aStates[] = $row["stateid"];
		}

		$this->assertEquals(2, count($aStates) );
		$this->assertEquals(Constants::iPAYMENT_ACCEPTED_STATE, $aStates[0]);
		$this->assertEquals(Constants::iPAYMENT_CANCELLED_STATE, $aStates[1]);
	}

}
