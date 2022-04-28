<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class PayAPIValidationTest extends baseAPITest
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
        $aMPOINT_CONN_INFO['path'] = "/mApp/api/pay.php";
        $aMPOINT_CONN_INFO["contenttype"] = "text/xml";
        $this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO) );
    }

	protected function getPayDoc($client, $account, $txn=1, $amount = 200, $authToken = null,$fxservicetypeid=0, $additionalData=null)
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml .= '<root>';
		$xml .= '<pay client-id="'. $client .'" account="'. $account .'">';
		$xml .= '<transaction id="'. $txn .'" store-card="false">';
		$xml .= '<card type-id="7">';
		$xml .= '<amount country-id="100">'.$amount.'</amount>';
		$xml .= '</card>';
        if($fxservicetypeid > 0)
            $xml .= '<foreign-exchange-info><id>1</id><service-type-id>'.$fxservicetypeid.'</service-type-id></foreign-exchange-info>';
        if (!is_null($additionalData)) {
            $xml .= '<additional-data>';
            foreach ($additionalData as $data) {
                $xml .= '<param name="' .$data['name']. '">' .$data['value']. '</param>';
            }
            $xml .= '</additional-data>';
        }
        $xml .= '</transaction>';
		if($authToken !== null){
			$xml .= '<auth-token>'.$authToken.'</auth-token>';
		}
		$xml .= '<client-info platform="iOS" version="1.00" language="da">';
		$xml .= '<mobile country-id="100" operator-id="10000">28882861</mobile>';
		$xml .= '<email>jona@oismail.com</email>';
		$xml .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
		$xml .= '</client-info>';
		$xml .= '</pay>';
		$xml .= '</root>';

		return $xml;
	}

    public function testInvalidTransactionState()
    {
		$pspID = 2;

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10099, 'app')");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		//As per talk with Jona and Simon 2016-07-19 it should not be possible to authorize a disabled card, since the client can ignore flags sent from initialize
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 7, $pspID, true, 1)");
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 7, $pspID, '501910******3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, auto_capture, enabled, currencyid, sessionid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 1,  $pspID, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', 1, TRUE,208, 1,5000,208)");

		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_CAPTURED_STATE. ")");

		$xml = $this->getPayDoc(10099, 1100, 1001001);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(400, $iStatus); //TODO: Correct when CMP-236 is solved
        //$this->assertEquals("msg=173", $sReplyBody); //TODO: Correct when CMP-236 is solved
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
        $this->bIgnoreErrors = true; // In case of failure mPoint will throw the exception
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, enabled) VALUES (10099, 1, 100, 'Test Client', false)");

		$xml = $this->getPayDoc(10099, 1100);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(400, $iStatus);
		$this->assertStringContainsString('<status code="3">Client ID / Account doesn\'t match</status>', $sReplyBody);
    }

    public function testDisabledAccount()
    {
        $this->bIgnoreErrors = true; // In case of failure mPoint will throw the exception
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, enabled) VALUES (10099, 1, 100, 'Test Client', true)");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, enabled) VALUES (1100, 10099, false)");

		$xml = $this->getPayDoc(10099, 1100);
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

		$xml = $this->getPayDoc(10099, 1100);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->bIgnoreErrors = true; //TODO: Remove once CMP-235 is implemented
		$this->assertEquals(200, $iStatus); //TODO: Change once CMP-235 is implemented
		$this->assertEquals('', $sReplyBody); //TODO: Change once CMP-235 is implemented
	}

	public function testUnauthorized()
	{
		$xml = $this->getPayDoc(1, 1);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders(), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(401, $iStatus);
		$this->assertStringContainsString('<status code="401">Authorization required</status>', $sReplyBody);
	}

	public function testWrongUsernamePassword()
	{
        $pspID = 2;

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");

        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid,  countryid, orderid, callbackurl, amount, ip, auto_capture, enabled, currencyid, sessionid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 1,  $pspID, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', 1, TRUE,208, 1,5000,208)");


		$xml = $this->getPayDoc(10099, 1100,1001001);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Twrong'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(401, $iStatus);
		$this->assertStringContainsString('<status code="401">Username / Password doesn\'t match</status>', $sReplyBody);
	}

/* 	public function testInvalidMerchantConfiguration()
	{
		$pspID = 2;
		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 7, $pspID, true, 1)");
		$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, countryid, amount, ip, enabled) VALUES (1001001, 100, 10099, 1100, 1, 100, 5000, '127.0.0.1', TRUE)");

		$xml = $this->getPayDoc(10099, 1100, 1001001);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(400, $iStatus);
		$this->assertStringContainsString('<status code="90">Unable to find configuration for Payment Service Provider and card', $sReplyBody);
	} */

	public function testDisabledCard()
	{
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

		$pspID = 2;

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		//As per talk with Jona and Simon 2016-07-19 it should not be possible to authorize a disabled card, since the client can ignore flags sent from initialize
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 7, $pspID, true, 4)");
        $this->queryDB("INSERT INTO client.services_tbl (clientid, legacy_flow_enabled) VALUES(10099, true);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (2, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, countryid, orderid, callbackurl, amount, ip, auto_capture, enabled, currencyid,sessionid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 1,  $pspID, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', 1, TRUE, 208,2,5000,208)");

		$xml = $this->getPayDoc(10099, 1100, 1001001);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(400, $iStatus);
		$this->assertStringContainsString('<status code="24">The selected payment card is not available', $sReplyBody);
	}

	public function testDisabledCardWithOneActiveCard()
	{
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";


		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10099, 'app')");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, 2, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 2, '-1')");
		//As per talk with Jona and Simon 2016-07-19 it should not be possible to authorize a disabled card, since the client can ignore flags sent from initialize
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 7, 2, true, 4)");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid, countryid) VALUES (10099, 7, 2, true, 1, 100)");
        $this->queryDB("INSERT INTO client.services_tbl (clientid, legacy_flow_enabled) VALUES(10099, true);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (3, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, countryid, orderid, callbackurl, amount, ip, auto_capture, enabled, currencyid, sessionid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 1,  2, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', 1, TRUE, 208,3,5000,208)");


		$xml = $this->getPayDoc(10099, 1100, 1001001);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
	}

    public function testInvalidTransactionAmount()
    {
        $xml = $this->getPayDoc(10099, 1100, 1, 100.99);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(400, $iStatus);
        $this->assertStringContainsString('Element \'amount\': \'100.99\' is not a valid value of the atomic type \'xs:nonNegativeInteger\'', $sReplyBody);

    }

    public function testInvalidFXServiceTypeID()
    {
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";


        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10099, 'app')");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, 2, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 2, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 7, 2, true, 4)");
        $this->queryDB("INSERT INTO client.services_tbl (clientid, legacy_flow_enabled) VALUES(10099, true);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (3, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, countryid, orderid, callbackurl, amount, ip, auto_capture, enabled, currencyid, sessionid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 1,  2, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', 1, TRUE, 208,3,5000,208)");

        $xml = $this->getPayDoc(10099, 1100, 1001001,200,null,13);
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(400, $iStatus);
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?><root><status code="57">Invalid service type id :13</status>', $sReplyBody);
    }

    public function testStoredFXServiceTypeID()
    {
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10099, 'app')");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, 2, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 2, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 7, 2, true, 4)");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid, countryid) VALUES (10099, 7, 2, true, 1, 100)");
        $this->queryDB("INSERT INTO client.services_tbl (clientid, legacy_flow_enabled) VALUES(10099, true);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (3, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, countryid, orderid, callbackurl, amount, ip, auto_capture, enabled, currencyid, sessionid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 1,  2, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', 1, TRUE, 208,3,5000,208)");

        $xml = $this->getPayDoc(10099, 1100, 1001001,200,null,11);
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><psp-info id="2" merchant-account="4216310"  type="1"><url content-type="application/x-www-form-urlencoded" method="post">https://payment.architrade.com/shoppages//auth.pml</url><card-number>cardno</card-number><expiry-month>expmon</expiry-month><expiry-year>expyear</expiry-year><cvc>cvc</cvc><hidden-fields><merchant/><callbackurl/><amount/><currency/><orderid/><fullreply>true</fullreply><paytype/><lang/><language/><cardid/><mpointid/><euaid/><clientid/><accountid/><markup>app</markup></hidden-fields><store-card>preauth</store-card><message language="gb"></message></psp-info><status code="1009">Payment Initialize with PSP</status></root>', $sReplyBody);

        $res =  $this->queryDB('SELECT fxservicetypeid from Log.Transaction_Tbl WHERE id = 1001001');
        $this->assertTrue(is_resource($res) );

        $fxservicetypeid = 0;
        while ($row = pg_fetch_assoc($res) )
        {
            $fxservicetypeid = (int)$row["fxservicetypeid"];
        }
        $this->assertEquals(11, $fxservicetypeid);

        $res =  $this->queryDB("SELECT id FROM Log.Session_Tbl where id=1 and sessiontypeid=1");
        $this->assertTrue(is_resource($res) );

    }

    /**
     * Check if foreign exchange service type id is already passed in a init transaction
     * If passed then if try to send some other id in pay it should not update
     */
    public function testAlreadyStoredFXServiceTypeID()
    {
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10099, 'app')");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, 2, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 2, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 7, 2, true, 4)");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid, countryid) VALUES (10099, 7, 2, true, 1, 100)");
        $this->queryDB("INSERT INTO client.services_tbl (clientid, legacy_flow_enabled) VALUES(10099, true);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (3, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, countryid, orderid, callbackurl, amount, ip, auto_capture, enabled, currencyid, sessionid,convertedamount,convertedcurrencyid,fxservicetypeid) VALUES (1001001, 100, 10099, 1100, 1,  2, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', 1, TRUE, 208,3,5000,208,11)");

        $xml = $this->getPayDoc(10099, 1100, 1001001,200,null,12);
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><psp-info id="2" merchant-account="4216310"  type="1"><url content-type="application/x-www-form-urlencoded" method="post">https://payment.architrade.com/shoppages//auth.pml</url><card-number>cardno</card-number><expiry-month>expmon</expiry-month><expiry-year>expyear</expiry-year><cvc>cvc</cvc><hidden-fields><merchant/><callbackurl/><amount/><currency/><orderid/><fullreply>true</fullreply><paytype/><lang/><language/><cardid/><mpointid/><euaid/><clientid/><accountid/><markup>app</markup></hidden-fields><store-card>preauth</store-card><message language="gb"></message></psp-info><status code="1009">Payment Initialize with PSP</status></root>', $sReplyBody);

        $res =  $this->queryDB('SELECT fxservicetypeid from Log.Transaction_Tbl WHERE id = 1001001');
        $this->assertTrue(is_resource($res) );

        $fxservicetypeid = 0;
        while ($row = pg_fetch_assoc($res) )
        {
            $fxservicetypeid = (int)$row["fxservicetypeid"];
        }
        $this->assertEquals(12, $fxservicetypeid);
    }

    public function testSuccessfulPayWithAdiitionalData()
    {
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10099, 'app')");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, 2, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 2, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 7, 2, true, 4)");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid, countryid) VALUES (10099, 7, 2, true, 1, 100)");
        $this->queryDB("INSERT INTO client.services_tbl (clientid, legacy_flow_enabled) VALUES(10099, true);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (3, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, countryid, orderid, callbackurl, amount, ip, auto_capture, enabled, currencyid, sessionid,convertedamount,convertedcurrencyid,fxservicetypeid) VALUES (1001001, 100, 10099, 1100, 1,  2, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', 1, TRUE, 208,3,5000,208,11)");


        $additionalData[] = ['name' => 'testKey', 'value' => 'testValue'];
        $xml = $this->getPayDoc(10099, 1100, 1001001,200,null,12, $additionalData);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(200, $iStatus);

        $res =  $this->queryDB("SELECT value FROM Log.additional_data_tbl where externalid= 1001001 and name= 'testKey'");
        $this->assertTrue(is_resource($res));

        $row = pg_fetch_assoc($res);
        $this->assertEquals('testValue', $row['value']);
    }

}
