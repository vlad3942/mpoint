<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class AuthorizeAPIValidationTest extends baseAPITest
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
        $aMPOINT_CONN_INFO['path'] = "/mApp/api/authorize.php";
        $aMPOINT_CONN_INFO["contenttype"] = "text/xml";
        $this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO) );
    }

	protected function getAuthDoc($client, $account, $txn=1, $amount=100, $euaPasswd='', $intAccountId=0, $clientpasswd='')
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml .= '<root>';
		$xml .= '<authorize-payment client-id="'. $client .'" account="'. $account .'">';
		$xml .= '<transaction id="'. $txn .'">';
		$xml .= '<card id="61775" type-id="2">';
		$xml .= '<amount country-id="100">'. $amount .'</amount>';
//		$xml .= '<card-number>5272342200069702</card-number>';
//		$xml .= '<expiry>03/31</expiry>';
//		$xml .= '<cryptogram type="3ds">AKh96OOsGf2HAIDEhKulAoABFA==</cryptogram>';
		$xml .= '</card>';
		$xml .= '</transaction>';
		if ($intAccountId > 0)
		{
			$secret = sha1($client. $clientpasswd);
			$xml .= '<auth-token>'. htmlspecialchars(General::genToken($intAccountId, $secret), ENT_NOQUOTES) .'</auth-token>';
		}
		else
		{
			$xml .= '<password>'. $euaPasswd. '</password>';
		}
		$xml .= '<client-info platform="iOS" version="1.00" language="da">';
		$xml .= '<mobile country-id="100" operator-id="10000">28882861</mobile>';
		$xml .= '<email>jona@oismail.com</email>';
		$xml .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
		$xml .= '</client-info>';
		$xml .= '</authorize-payment>';
		$xml .= '</root>';

		return $xml;
	}

    public function testBadRequestInvalidRequestBody()
    {
		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), '<xml></xml>');
		$sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(400, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root></root>', $sReplyBody);
    }

    public function testBadRequestDisabledClient()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, enabled) VALUES (10099, 1, 100, 'Test Client', false)");

		$xml = $this->getAuthDoc(10099, 1100);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(400, $iStatus);
		$this->assertStringContainsString('<status code="3">Client ID / Account doesn\'t match</status>', $sReplyBody);
    }

    public function testDisabledAccount()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, enabled) VALUES (10099, 1, 100, 'Test Client', true)");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, enabled) VALUES (1100, 10099, false)");

		$xml = $this->getAuthDoc(10099, 1100);

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

		$xml = $this->getAuthDoc(10099, 1100);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(404, $iStatus);
		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="404">Transaction with ID: 1 not found</status></root>', $sReplyBody);
	}

	public function testUnauthorized()
	{
		$xml = $this->getAuthDoc(1, 1);

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

		$xml = $this->getAuthDoc(10099, 1100);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Twrong'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(401, $iStatus);
		$this->assertStringContainsString('<status code="401">Username / Password doesn\'t match</status>', $sReplyBody);
	}

	public function testIINBlocked4DigitsInput4DigitRule()
	{
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, 2, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 2, '-1')");
		//As per talk with Jona and Simon 2016-07-19 it should not be possible to authorize a disabled card, since the client can ignore flags sent from initialize
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 2, 2, true, 1)");
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 2, '5019**********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
		$this->queryDB("INSERT INTO Client.IINList_Tbl (clientid, iinactionid, min, max) VALUES (10099, 1, 5019, 5020);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid, sessionid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 1, 2, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE, 208, 1,5000,208)");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_INIT_WITH_PSP_STATE .")");

		$xml = $this->getAuthDoc(10099, 1100, 1001001, 5000, 'profilePass');

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(403, $iStatus);
		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="89">Card has been blocked</status></root>', $sReplyBody);

		$res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY id ASC");
		$this->assertTrue(is_resource($res) );

		$aStates = array();
		while ($row = pg_fetch_assoc($res) )
		{
			$aStates[] = $row["stateid"];
		}

		// Assert that there is no futher txn states than
		$this->assertEquals(1, count($aStates) );
		$this->assertEquals(Constants::iPAYMENT_INIT_WITH_PSP_STATE, $aStates[0]);
	}

	public function testIINOpen4DigitsInput6DigitRange()
	{
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, 2, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 2, '-1')");
		//As per talk with Jona and Simon 2016-07-19 it should not be possible to authorize a disabled card, since the client can ignore flags sent from initialize
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 2, 2, true, 1)");
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 2, '5020**********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
		$this->queryDB("INSERT INTO Client.IINList_Tbl (clientid, iinactionid, min, max) VALUES (10099, 1, 501900, 502100);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid, sessionid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 1, 2, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE, 208, 1,5000,208)");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_INIT_WITH_PSP_STATE .")");

		$xml = $this->getAuthDoc(10099, 1100, 1001001, 5000, 'profilePass');

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="100">Payment Authorized using Stored Card</status></root>', $sReplyBody);

		$res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY id ASC");
		$this->assertTrue(is_resource($res) );

		$aStates = array();
		while ($row = pg_fetch_assoc($res) )
		{
			$aStates[] = $row["stateid"];
		}

		$this->assertEquals(6, count($aStates) );
		$this->assertEquals(Constants::iPAYMENT_INIT_WITH_PSP_STATE, $aStates[0]);
		$this->assertEquals(Constants::iPAYMENT_WITH_ACCOUNT_STATE, $aStates[1]);
		$this->assertEquals(Constants::iPAYMENT_ACCEPTED_STATE, $aStates[2]);
		$this->assertEquals(Constants::iCB_CONSTRUCTED_STATE, $aStates[3]);
		$this->assertEquals(Constants::iCB_CONNECTED_STATE, $aStates[4]);
		$this->assertEquals(Constants::iCB_ACCEPTED_STATE, $aStates[5]);
	}

	public function testIINOpen4DigitsInputNoRules()
	{
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, 2, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 2, '-1')");
		//As per talk with Jona and Simon 2016-07-19 it should not be possible to authorize a disabled card, since the client can ignore flags sent from initialize
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 2, 2, true, 1)");
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 2, '5020XXXXXXXX3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid, sessionid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 1, 2, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE, 208, 1,5000,208)");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_INIT_WITH_PSP_STATE .")");

		$xml = $this->getAuthDoc(10099, 1100, 1001001, 5000, 'profilePass');

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="100">Payment Authorized using Stored Card</status></root>', $sReplyBody);

		$res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY id ASC");
		$this->assertTrue(is_resource($res) );

		$aStates = array();
		while ($row = pg_fetch_assoc($res) )
		{
			$aStates[] = $row["stateid"];
		}

		$this->assertEquals(6, count($aStates) );
		$this->assertEquals(Constants::iPAYMENT_INIT_WITH_PSP_STATE, $aStates[0]);
		$this->assertEquals(Constants::iPAYMENT_WITH_ACCOUNT_STATE, $aStates[1]);
		$this->assertEquals(Constants::iPAYMENT_ACCEPTED_STATE, $aStates[2]);
		$this->assertEquals(Constants::iCB_CONSTRUCTED_STATE, $aStates[3]);
		$this->assertEquals(Constants::iCB_CONNECTED_STATE, $aStates[4]);
		$this->assertEquals(Constants::iCB_ACCEPTED_STATE, $aStates[5]);
	}

	public function testIINOpen6DigitsInputNoRules()
	{
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, 2, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 2, '-1')");
		//As per talk with Jona and Simon 2016-07-19 it should not be possible to authorize a disabled card, since the client can ignore flags sent from initialize
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 2, 2, true, 1)");
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 2, '502015XXXXXX3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid, sessionid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 1, 2, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE, 208, 1,5000,208)");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_INIT_WITH_PSP_STATE .")");

		$xml = $this->getAuthDoc(10099, 1100, 1001001, 5000, 'profilePass');

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="100">Payment Authorized using Stored Card</status></root>', $sReplyBody);

		$res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY id ASC");
		$this->assertTrue(is_resource($res) );

		$aStates = array();
		while ($row = pg_fetch_assoc($res) )
		{
			$aStates[] = $row["stateid"];
		}

		$this->assertEquals(6, count($aStates) );
		$this->assertEquals(Constants::iPAYMENT_INIT_WITH_PSP_STATE, $aStates[0]);
		$this->assertEquals(Constants::iPAYMENT_WITH_ACCOUNT_STATE, $aStates[1]);
		$this->assertEquals(Constants::iPAYMENT_ACCEPTED_STATE, $aStates[2]);
		$this->assertEquals(Constants::iCB_CONSTRUCTED_STATE, $aStates[3]);
		$this->assertEquals(Constants::iCB_CONNECTED_STATE, $aStates[4]);
		$this->assertEquals(Constants::iCB_ACCEPTED_STATE, $aStates[5]);
	}

	public function testIINOpen6DigitsInput4DigitRule()
	{
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, 2, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 2, '-1')");
		//As per talk with Jona and Simon 2016-07-19 it should not be possible to authorize a disabled card, since the client can ignore flags sent from initialize
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 2, 2, true, 1)");
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 2, '502014XXXXXX3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
		$this->queryDB("INSERT INTO Client.IINList_Tbl (clientid, iinactionid, min, max) VALUES (10099, 1, 5019, 5021);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid,sessionid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 1, 2, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE, 208,1,5000,208)");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_INIT_WITH_PSP_STATE .")");

		$xml = $this->getAuthDoc(10099, 1100, 1001001, 5000, 'profilePass');

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="100">Payment Authorized using Stored Card</status></root>', $sReplyBody);

		$res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY id ASC");
		$this->assertTrue(is_resource($res) );

		$aStates = array();
		while ($row = pg_fetch_assoc($res) )
		{
			$aStates[] = $row["stateid"];
		}

		$this->assertEquals(6, count($aStates) );
		$this->assertEquals(Constants::iPAYMENT_INIT_WITH_PSP_STATE, $aStates[0]);
		$this->assertEquals(Constants::iPAYMENT_WITH_ACCOUNT_STATE, $aStates[1]);
		$this->assertEquals(Constants::iPAYMENT_ACCEPTED_STATE, $aStates[2]);
		$this->assertEquals(Constants::iCB_CONSTRUCTED_STATE, $aStates[3]);
		$this->assertEquals(Constants::iCB_CONNECTED_STATE, $aStates[4]);
		$this->assertEquals(Constants::iCB_ACCEPTED_STATE, $aStates[5]);
	}

	public function testIINBlocked6DigitsInput6DigitRule()
	{
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, 2, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 2, '-1')");
		//As per talk with Jona and Simon 2016-07-19 it should not be possible to authorize a disabled card, since the client can ignore flags sent from initialize
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 2, 2, true, 1)");
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 2, '501912********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
		$this->queryDB("INSERT INTO Client.IINList_Tbl (clientid, iinactionid, min, max) VALUES (10099, 1, 501910, 501919);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid, sessionid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 1, 2, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE, 208, 1,5000,208)");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_INIT_WITH_PSP_STATE .")");

		$xml = $this->getAuthDoc(10099, 1100, 1001001, 5000, 'profilePass');

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(403, $iStatus);
		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="89">Card has been blocked</status></root>', $sReplyBody);

		$res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY id ASC");
		$this->assertTrue(is_resource($res) );

		$aStates = array();
		while ($row = pg_fetch_assoc($res) )
		{
			$aStates[] = $row["stateid"];
		}

		// Assert that there is no futher txn states than
		$this->assertEquals(1, count($aStates) );
		$this->assertEquals(Constants::iPAYMENT_INIT_WITH_PSP_STATE, $aStates[0]);
	}

	public function testIINOpen4DigitsInput6DigitRule()
	{
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, 2, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 2, '-1')");
		//As per talk with Jona and Simon 2016-07-19 it should not be possible to authorize a disabled card, since the client can ignore flags sent from initialize
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 2, 2, true, 1)");
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 2, '5019XXXXXXXX3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
		$this->queryDB("INSERT INTO Client.IINList_Tbl (clientid, iinactionid, min, max) VALUES (10099, 1, 501912, 501914);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid, sessionid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 1, 2, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE, 208, 1,5000,208)");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_INIT_WITH_PSP_STATE .")");

		$xml = $this->getAuthDoc(10099, 1100, 1001001, 5000, 'profilePass');

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="100">Payment Authorized using Stored Card</status></root>', $sReplyBody);

		$res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY id ASC");
		$this->assertTrue(is_resource($res) );

		$aStates = array();
		while ($row = pg_fetch_assoc($res) )
		{
			$aStates[] = $row["stateid"];
		}

		$this->assertEquals(6, count($aStates) );
		$this->assertEquals(Constants::iPAYMENT_INIT_WITH_PSP_STATE, $aStates[0]);
		$this->assertEquals(Constants::iPAYMENT_WITH_ACCOUNT_STATE, $aStates[1]);
		$this->assertEquals(Constants::iPAYMENT_ACCEPTED_STATE, $aStates[2]);
		$this->assertEquals(Constants::iCB_CONSTRUCTED_STATE, $aStates[3]);
		$this->assertEquals(Constants::iCB_CONNECTED_STATE, $aStates[4]);
		$this->assertEquals(Constants::iCB_ACCEPTED_STATE, $aStates[5]);
	}

	public function testIINOpen6DigitsInput6DigitRule()
	{
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, 2, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 2, '-1')");
		//As per talk with Jona and Simon 2016-07-19 it should not be possible to authorize a disabled card, since the client can ignore flags sent from initialize
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 2, 2, true, 1)");
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 2, '501916********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
		$this->queryDB("INSERT INTO Client.IINList_Tbl (clientid, iinactionid, min, max) VALUES (10099, 1, 501912, 501914);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid, sessionid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 1, 2, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE, 208, 1,5000,208)");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_INIT_WITH_PSP_STATE .")");

		$xml = $this->getAuthDoc(10099, 1100, 1001001, 5000, 'profilePass');

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="100">Payment Authorized using Stored Card</status></root>', $sReplyBody);

		$res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY id ASC");
		$this->assertTrue(is_resource($res) );

		$aStates = array();
		while ($row = pg_fetch_assoc($res) )
		{
			$aStates[] = $row["stateid"];
		}

		$this->assertEquals(6, count($aStates) );
		$this->assertEquals(Constants::iPAYMENT_INIT_WITH_PSP_STATE, $aStates[0]);
		$this->assertEquals(Constants::iPAYMENT_WITH_ACCOUNT_STATE, $aStates[1]);
		$this->assertEquals(Constants::iPAYMENT_ACCEPTED_STATE, $aStates[2]);
		$this->assertEquals(Constants::iCB_CONSTRUCTED_STATE, $aStates[3]);
		$this->assertEquals(Constants::iCB_CONNECTED_STATE, $aStates[4]);
		$this->assertEquals(Constants::iCB_ACCEPTED_STATE, $aStates[5]);
	}

   public function testInvalidTransactionAmount()
   {
        $xml = $this->getAuthDoc(10099, 1100, 1, 100.99);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(400, $iStatus);
        $this->assertStringContainsString('Element \'amount\': \'100.99\' is not a valid value of the atomic type \'xs:nonNegativeInteger\'', $sReplyBody);
    }


}
