<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class MobilePayCallbackAPITest extends baseAPITest
{
    protected $_aMPOINT_CONN_INFO;

    public function __construct()
    {
        $this->constHTTPClient();
    }

    public function constHTTPClient()
    {
        global $aMPOINT_CONN_INFO;
        $aMPOINT_CONN_INFO['path'] = "/callback/mobilepay.php";
        $aMPOINT_CONN_INFO["contenttype"] = "text/xml";
        $this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO) );
    }

    public function testSuccessfulCallback()
    {
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
		$pspID = Constants::iMOBILEPAY_PSP;

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '1')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (113, 17, $pspID)"); //Mobilepay
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '900-55150298', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, pspid, callbackurl, amount, ip, enabled, keywordid, sessionid) VALUES (1001001, '900-55150298', 100, 113, 1100, 100, null, '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1)");

		trigger_error("mRetail expect external transaction id: 15469928");

        $this->_httpClient->connect();

		$xml  = '<root>';
		$xml .= '<callback>';
		$xml .= '<psp-config psp-id="'. Constants::iMOBILEPAY_PSP .'"></psp-config>';
		$xml .= '<transaction external-id="15469928" card-id="17">';
		$xml .= '<orderid>900-55150298</orderid>';
		$xml .= '<amount country-id="100" currency="DKK" symbol="kr." format="">10050</amount>';
		$xml .= '<status code="1000">Success</status>';
		$xml .= '</transaction>';
		$xml .= '</callback>';
		$xml .= '</root>';

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders(), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertContains("Callback handled", $sReplyBody);

        $res =  $this->queryDB("SELECT extid, stateid FROM Log.Message_Tbl m, Log.Transaction_Tbl t WHERE t.id = 1001001 AND m.txnid = t.id");
        $this->assertTrue(is_resource($res) );

        $aStates = array();
        while ($row = pg_fetch_assoc($res) )
        {
            $aStates[] = $row["stateid"];
			$this->assertEquals("15469928", $row["extid"]);
        }

        $this->assertTrue(is_int(array_search(Constants::iPAYMENT_ACCEPTED_STATE, $aStates) ) );
    }

	public function testSuccessfulCallbackAlreadyCaptured()
	{
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
		$pspID = Constants::iMOBILEPAY_PSP;

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '1')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (113, 17, $pspID)"); //Mobilepay
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '900-55152001', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, pspid, callbackurl, amount, ip, enabled, keywordid, sessionid) VALUES (1001001, '900-55152001', 100, 113, 1100, 100, null, '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1)");

		$this->_httpClient->connect();

		$xml  = '<root>';
		$xml .= '<callback>';
		$xml .= '<psp-config psp-id="'. Constants::iMOBILEPAY_PSP .'"></psp-config>';
		$xml .= '<transaction external-id="15469928" card-id="17">';
		$xml .= '<orderid>900-55152001</orderid>';
		$xml .= '<amount country-id="100" currency="DKK" symbol="kr." format="">10050</amount>';
		$xml .= '<status code="1000">Success</status>';
		$xml .= '</transaction>';
		$xml .= '</callback>';
		$xml .= '</root>';

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders(), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
		$this->assertContains("Callback handled", $sReplyBody);

		$res =  $this->queryDB("SELECT extid, stateid FROM Log.Message_Tbl m, Log.Transaction_Tbl t WHERE t.id = 1001001 AND m.txnid = t.id");
		$this->assertTrue(is_resource($res) );

		$aStates = array();
		while ($row = pg_fetch_assoc($res) )
		{
			$aStates[] = $row["stateid"];
			$this->assertEquals("15469928", $row["extid"]);
		}

		$this->assertTrue(is_int(array_search(Constants::iPAYMENT_ACCEPTED_STATE, $aStates) ) );
		$this->assertTrue(is_int(array_search(Constants::iPAYMENT_CAPTURED_STATE, $aStates) ) );
	}

	public function testSuccessfulAutoCapture()
	{
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
		$pspID = Constants::iMOBILEPAY_PSP;

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd, auto_capture) VALUES (113, 1, 100, 'Test Client', 'Tusername', 'Tpassword', true)");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '1')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (113, 17, $pspID)"); //Mobilepay
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '900-55150298', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, pspid, callbackurl, amount, ip, enabled, keywordid, auto_capture, sessionid) VALUES (1001001, '900-55150298', 100, 113, 1100, 100, null, '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, true, 1)");

		$this->_httpClient->connect();

		$xml  = '<root>';
		$xml .= '<callback>';
		$xml .= '<psp-config psp-id="'. Constants::iMOBILEPAY_PSP .'"></psp-config>';
		$xml .= '<transaction external-id="15469928" card-id="17">';
		$xml .= '<orderid>900-55150298</orderid>';
		$xml .= '<amount country-id="100" currency="DKK" symbol="kr." format="">10050</amount>';
		$xml .= '<status code="1000">Success</status>';
		$xml .= '</transaction>';
		$xml .= '</callback>';
		$xml .= '</root>';

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders(), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
		$this->assertContains("Callback handled", $sReplyBody);

		$res =  $this->queryDB("SELECT extid, stateid FROM Log.Message_Tbl m, Log.Transaction_Tbl t WHERE t.id = 1001001 AND m.txnid = t.id");
		$this->assertTrue(is_resource($res) );

		$aStates = array();
		while ($row = pg_fetch_assoc($res) )
		{
			$aStates[] = $row["stateid"];
			$this->assertEquals("15469928", $row["extid"]);
		}

		$this->assertTrue(is_int(array_search(Constants::iPAYMENT_ACCEPTED_STATE, $aStates) ) );
		//$this->assertTrue(is_int(array_search(Constants::iPAYMENT_CAPTURED_STATE, $aStates) ) );
	}

	public function testOrderUniqueByMerchant()
	{
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
		$pspID = Constants::iMOBILEPAY_PSP;

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (114, 1, 100, 'Another Client', 'Tusername', 'Tpassword')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (114, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1200, 114)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (2, 114, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, 'Merchant-1')");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (2, 114, $pspID, 'Merchant-2')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1200, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (113, 17, $pspID)"); //Mobilepay
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (114, 17, $pspID)"); //Mobilepay
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '900-55150298', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (2, 113, 1100, 208, 100, 4001, '900-55150298', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, pspid, callbackurl, amount, ip, enabled, keywordid, sessionid) VALUES (1001001, '900-55150298', 100, 113, 1100, 100, null, '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1)");
		$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, pspid, callbackurl, amount, ip, enabled, keywordid, sessionid) VALUES (1001002, '900-55150298', 100, 114, 1200, 100, null, '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 2, 2)");

		$this->_httpClient->connect();

		$xml  = '<root>';
		$xml .= '<callback>';
		$xml .= '<psp-config psp-id="'. Constants::iMOBILEPAY_PSP .'">';
		$xml .= '<name>Merchant-2</name>';
		$xml .= '</psp-config>';
		$xml .= '<transaction external-id="15469928" card-id="17">';
		$xml .= '<orderid>900-55150298</orderid>';
		$xml .= '<amount country-id="100" currency="DKK" symbol="kr." format="">5000</amount>';
		$xml .= '<status code="1000">Success</status>';
		$xml .= '</transaction>';
		$xml .= '</callback>';
		$xml .= '</root>';

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders(), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
		$this->assertContains("Callback handled", $sReplyBody);

		$res2 =  $this->queryDB("SELECT extid, stateid FROM Log.Message_Tbl m, Log.Transaction_Tbl t WHERE t.id = 1001001 AND m.txnid = t.id");
		$res1 =  $this->queryDB("SELECT extid, stateid FROM Log.Message_Tbl m, Log.Transaction_Tbl t WHERE t.id = 1001002 AND m.txnid = t.id");
		$this->assertTrue(is_resource($res1) );
		$this->assertTrue(is_resource($res2) );

		$aStates = array();
		while ($row = pg_fetch_assoc($res1) )
		{
			$aStates[] = $row["stateid"];
			$this->assertEquals("15469928", $row["extid"]);
		}

		$this->assertTrue(is_int(array_search(Constants::iPAYMENT_ACCEPTED_STATE, $aStates) ) );

		$aStates = array();
		while ($row = pg_fetch_assoc($res1) )
		{
			$aStates[] = $row["stateid"];
			$this->assertNull($row["extid"]);
		}

		$this->assertTrue(array_search(Constants::iPAYMENT_ACCEPTED_STATE, $aStates) === false);
	}

	public function testUnknownTransaction()
	{
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
		$pspID = Constants::iMOBILEPAY_PSP;

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '1')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (113, 17, $pspID)"); //Mobilepay

		$this->_httpClient->connect();

		$xml  = '<root>';
		$xml .= '<callback>';
		$xml .= '<psp-config psp-id="'. Constants::iMOBILEPAY_PSP .'"></psp-config>';
		$xml .= '<transaction external-id="15469928">';
		$xml .= '<orderid>900-55150298</orderid>';
		$xml .= '<amount country-id="100" currency="DKK" symbol="kr." format="">10050</amount>';
		$xml .= '<status code="1000">Success</status>';
		$xml .= '</transaction>';
		$xml .= '</callback>';
		$xml .= '</root>';

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders(), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->bIgnoreErrors = true;
		$this->assertEquals(404, $iStatus);
		$this->assertContains('<status code="404">Transaction not found</status>', $sReplyBody);
	}

	public function testTransactionUnknownByPSP()
	{
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
		$pspID = Constants::iMOBILEPAY_PSP;

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '1')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (113, 17, $pspID)"); //Mobilepay
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '900-55150404', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, pspid, callbackurl, amount, ip, enabled, keywordid, sessionid) VALUES (1001001, '900-55150404', 100, 113, 1100, 100, null, '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1)");

		$this->_httpClient->connect();

		$xml  = '<root>';
		$xml .= '<callback>';
		$xml .= '<psp-config psp-id="'. Constants::iMOBILEPAY_PSP .'"></psp-config>';
		$xml .= '<transaction external-id="15469928">';
		$xml .= '<orderid>900-55150404</orderid>';
		$xml .= '<amount country-id="100" currency="DKK" symbol="kr." format="">10050</amount>';
		$xml .= '<status code="1000">Success</status>';
		$xml .= '</transaction>';
		$xml .= '</callback>';
		$xml .= '</root>';

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders(), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->bIgnoreErrors = true;
		$this->assertEquals(404, $iStatus);
		$this->assertContains('<status code="404">Transaction not found</status>', $sReplyBody);
	}

	public function testTransactionInInvalidState()
	{
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
		$pspID = Constants::iMOBILEPAY_PSP;

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '1')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (113, 17, $pspID)"); //Mobilepay
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '900-55152003', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, pspid, callbackurl, amount, ip, enabled, keywordid, sessionid) VALUES (1001001, '900-55152003', 100, 113, 1100, 100, null, '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1)");

		$this->_httpClient->connect();

		$xml  = '<root>';
		$xml .= '<callback>';
		$xml .= '<psp-config psp-id="'. Constants::iMOBILEPAY_PSP .'"></psp-config>';
		$xml .= '<transaction external-id="15469928">';
		$xml .= '<orderid>900-55152003</orderid>';
		$xml .= '<amount country-id="100" currency="DKK" symbol="kr." format="">10050</amount>';
		$xml .= '<status code="1000">Success</status>';
		$xml .= '</transaction>';
		$xml .= '</callback>';
		$xml .= '</root>';

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders(), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->bIgnoreErrors = true;
		$this->assertEquals(403, $iStatus);
		$this->assertContains('<status code="403">Transaction not in a valid state, PSP state: 2003</status>', $sReplyBody);
	}

}
