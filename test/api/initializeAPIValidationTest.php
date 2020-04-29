<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class InitializeAPIValidationTest extends baseAPITest
{

    protected $_aMPOINT_CONN_INFO;

    public function __construct()
    {
        $this->constHTTPClient();
    }

    public function constHTTPClient()
    {
        global $aMPOINT_CONN_INFO;
        $aMPOINT_CONN_INFO['path'] = "/mApp/api/initialize.php";
        $aMPOINT_CONN_INFO["contenttype"] = "text/xml";
        $this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO) );
    }

	protected function getInitDoc($client, $account, $currecyid = null, $token=null, $amount = 200,$hmac=null,$email=null)
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml .= '<root>';
		$xml .= '<initialize-payment client-id="'. $client .'" account="'. $account .'">';
		$xml .= '<transaction order-no="1234abc">';
		$xml .= '<amount country-id="100"';
		if(isset($currecyid) === true)
		    $xml .= ' currency-id="'.$currecyid.'"';
		$xml .= '>'.$amount.'</amount>';
		$xml .= '<callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url>';
		if(isset($hmac)=== true) $xml .= '<hmac>'.$hmac.'</hmac>';
		$xml .= '</transaction>';
		if(isset($token) === true)
        {
		    $xml .= '<auth-token>'.$token.'</auth-token>';
        }
		$xml .= '<client-info platform="iOS" version="5.1.1" language="da">';
		$xml .= '<mobile country-id="100" operator-id="10000">288828610</mobile>';
		if(isset($email) === true) {
			$xml .= '<email>'.$email.'</email>';
		} else {
			$xml .= '<email>jona@oismail.com</email>';			
		}
		$xml .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
		$xml .= '</client-info>';
		$xml .= '</initialize-payment>';
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

	public function testUnsupportedMediaType()
	{
		// Ignore errors in the app_error log file
		$this->bIgnoreErrors = true;

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), '<xl</xl>');
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(415, $iStatus);
		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="415">Invalid XML Document</status></root>', $sReplyBody);
	}

    public function testBadRequestDisabledClient()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, enabled) VALUES (113, 1, 100, 'Test Client', false)");

		$xml = $this->getInitDoc(113, 1100);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(400, $iStatus);
		$this->assertContains('<status code="3">Client ID / Account doesn\'t match</status>', $sReplyBody);
    }

    public function testDisabledAccount()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, enabled) VALUES (113, 1, 100, 'Test Client', true)");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, enabled) VALUES (1100, 113, false)");

		$xml = $this->getInitDoc(113, 1100);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(400, $iStatus);
		$this->assertContains('<status code="14">Client ID / Account doesn\'t match</status>', $sReplyBody);
	}

	public function testUnauthorized()
	{
		$xml = $this->getInitDoc(1, 1);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders(), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(401, $iStatus);
		$this->assertContains('<status code="401">Authorization required</status>', $sReplyBody);
	}

	public function testWrongUsernamePassword()
	{
		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");

		$xml = $this->getInitDoc(113, 1100);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Twrong'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(401, $iStatus);
		$this->assertContains('<status code="401">Username / Password doesn\'t match</status>', $sReplyBody);
	}

	public function testEmptyCardConfiguration()
	{
		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");

		$xml = $this->getInitDoc(113, 1100);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
		$this->assertContains('<?xml version="1.0" encoding="UTF-8"?><root><client-config id="113" account="1100" store-card="0" max-stored-cards="-1" auto-capture="false" enable-cvv="true" mode="0"><name>Test Client</name><callback-url></callback-url><accept-url></accept-url><cancel-url></cancel-url><app-url></app-url><css-url></css-url><logo-url></logo-url><base-image-url></base-image-url><additional-config></additional-config><accounts><account id= "1100" markup= "" /></accounts></client-config><transaction id="1" order-no="1234abc" type-id="0" eua-id="-1" language="da" auto-capture="false" mode="0"><amount country-id="100" currency-id="208" currency="DKK" decimals="2" symbol="" format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><mobile country-id="100" operator-id="10000">288828610</mobile><email>jona@oismail.com</email><callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url><accept-url/><cancel-url/></transaction><session id=\'1\' type=\'1\' total-amount=\'200\'><amount country-id="100" currency-id="208" currency="DKK" symbol="" format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount></session><cards></cards><wallets></wallets></root>', $sReplyBody);
	}


    public function testEmptyCardConfigurationWithCurrency()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
        $this->queryDB("INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid, enabled) VALUES (113,100,840, true)");
        $xml = $this->getInitDoc(113, 1100,840);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(200, $iStatus);
        $this->assertContains('<?xml version="1.0" encoding="UTF-8"?><root><client-config id="113" account="1100" store-card="0" max-stored-cards="-1" auto-capture="false" enable-cvv="true" mode="0"><name>Test Client</name><callback-url></callback-url><accept-url></accept-url><cancel-url></cancel-url><app-url></app-url><css-url></css-url><logo-url></logo-url><base-image-url></base-image-url><additional-config></additional-config><accounts><account id= "1100" markup= "" /></accounts></client-config><transaction id="1" order-no="1234abc" type-id="0" eua-id="-1" language="da" auto-capture="false" mode="0"><amount country-id="100" currency-id="840" currency="USD" decimals="2" symbol="$" format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><mobile country-id="100" operator-id="10000">288828610</mobile><email>jona@oismail.com</email><callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url><accept-url/><cancel-url/></transaction><session id=\'1\' type=\'1\' total-amount=\'200\'><amount country-id="100" currency-id="840" currency="USD" symbol="$" format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount></session><cards></cards><wallets></wallets></root>', $sReplyBody);
    }


	public function testSoftDisabledCardType()
	{
		$pspID = 2;

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (113, 2, $pspID, true, 2)");
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '5019********3742', '06/24', TRUE, 113, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
		$this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 2)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 113, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 113, 1100, 1,  $pspID, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE,10,5000)");

		$xml = $this->getInitDoc(113, 1100);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
		$this->assertContains('<cards><card id="2" type-id="2" psp-id="2" min-length="16" max-length="16" cvc-length="3" state-id="2" payment-type="1" preferred="false" enabled="true" processor-type="1" installment="0" cvcmandatory="false" dcc="false"><name>Dankort</name><prefixes><prefix><min>5019</min><max>5019</max></prefix><prefix><min>4571</min><max>4571</max></prefix></prefixes>Dankort</card></cards>', $sReplyBody);
	}

	public function testHardDisabledCardType()
	{
		$pspID = 2;

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (113, 2, $pspID, false, 2)");
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '5019********3742', '06/24', TRUE, 113, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
		$this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 2)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (11, 113, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 113, 1100, 1,  $pspID, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE,11,5000)");

		$xml = $this->getInitDoc(113, 1100);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
		$this->assertContains('<cards></cards>', $sReplyBody);
	}

    public function testEmptyCurrencyId()
    {
        $pspID = 2;

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (113, 2, $pspID, false, 2)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '5019********3742', '06/24', TRUE, 113, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 2)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (11, 113, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 113, 1100, 1,  $pspID, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE,11,5000)");

        $xml = $this->getInitDoc(113, 1100, null);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(200, $iStatus);
        $this->assertContains('<?xml version="1.0" encoding="UTF-8"?><root><client-config id="113" account="1100" store-card="0" max-stored-cards="-1" auto-capture="false" enable-cvv="true" mode="0"><name>Test Client</name><callback-url></callback-url><accept-url></accept-url><cancel-url></cancel-url><app-url></app-url><css-url></css-url><logo-url></logo-url><base-image-url></base-image-url><additional-config></additional-config><accounts><account id= "1100" markup= "" /></accounts></client-config><transaction id="1" order-no="1234abc" type-id="0" eua-id="-1" language="da" auto-capture="false" mode="0"><amount country-id="100" currency-id="208" currency="DKK" decimals="2" symbol="" format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><mobile country-id="100" operator-id="10000">288828610</mobile><email>jona@oismail.com</email><callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url><accept-url/><cancel-url/></transaction><session id=\'1\' type=\'1\' total-amount=\'200\'><amount country-id="100" currency-id="208" currency="DKK" symbol="" format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount></session><cards></cards><wallets></wallets></root>', $sReplyBody);
    }

    public function testEuaIdPasswordFlow()
	{
		$pspID = 2;

		$authenticateURL = $sCallbackURL = $this->_aMPOINT_CONN_INFO['protocol'] . '://' . $this->_aMPOINT_CONN_INFO['host']. '/_test/simulators/login.php';

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (2, 113, 1, 'CPM Wallet')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 1, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (113, 2, $pspID, true, 2)");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (113, 11, 1, true, 2)");
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled, email) VALUES (5001, 100, 'abcExternal', '288828610', TRUE, 'profilePass', TRUE, 'jona@oismail.com')");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '5019********3742', '06/24', TRUE, 113, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
		$this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 2)");
		$this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 11)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 113, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 113, 1100, 1,  $pspID, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE,10,5000)");

		$xml = $this->getInitDoc(113, 1100, 208);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();
		$this->assertEquals(200, $iStatus);
		$this->assertContains('eua-id="5001"', $sReplyBody);
		$this->assertNotContains('<stored-cards><card id="61775" type-id="2" psp-id="2" preferred="true" state-id="2" charge-type-id="0" cvc-length="3" expired="false"><card-number-mask>5019 **** **** 3742 </card-number-mask><expiry>06/24</expiry></card></stored-cards>', $sReplyBody);
	}

    public function testSSOFailForStoredCard()
	{
		$pspID = 2;
		$this->bIgnoreErrors = true; //User Warning Expected

		$authenticateURL = $sCallbackURL = $this->_aMPOINT_CONN_INFO['protocol'] . '://' . $this->_aMPOINT_CONN_INFO['host']. '/_test/simulators/login.php';

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 2, '".$authenticateURL."')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (2, 113, 1, 'CPM Wallet')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 1, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (113, 2, $pspID, true, 2)");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (113, 11, 1, true, 2)");
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled, email) VALUES (5001, 100, 'abcExternal', '288828610', TRUE, 'profilePass', TRUE, 'jona@oismail.com')");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '5019********3742', '06/24', TRUE, 113, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
		$this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 2)");
		$this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 11)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 113, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 113, 1100, 1,  $pspID, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE,10,5000)");

		$xml = $this->getInitDoc(113, 1100, 208, 'fail');

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();
		$this->assertEquals(200, $iStatus);
		$this->assertContains('eua-id="-1"', $sReplyBody);
		$this->assertNotContains('<stored-cards><card id="61775" type-id="2" psp-id="2" preferred="true" state-id="2" charge-type-id="0" cvc-length="3" expired="false"><card-number-mask>5019 **** **** 3742 </card-number-mask><expiry>06/24</expiry></card></stored-cards>', $sReplyBody);
	}

	public function testSSOTimeoutForStoredCard()
	{
		$pspID = 2;
        $this->bIgnoreErrors = true; //User Warning Expected
		$authenticateURL = $sCallbackURL = $this->_aMPOINT_CONN_INFO['protocol'] . '://' . $this->_aMPOINT_CONN_INFO['host']. '/_test/simulators/login.php';

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 2, '".$authenticateURL."')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (2, 113, 1, 'CPM Wallet')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 1, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (113, 2, $pspID, true, 2)");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (113, 11, 1, true, 2)");
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled, email) VALUES (5001, 100, 'abcExternal', '288828610', TRUE, 'profilePass', TRUE, 'jona@oismail.com')");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '5019********3742', '06/24', TRUE, 113, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
		$this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 2)");
		$this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 11)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 113, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 113, 1100, 1,  $pspID, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE,10,5000)");

		$xml = $this->getInitDoc(113, 1100, 208, 'timeout');

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();
		$this->assertEquals(200, $iStatus);
		$this->assertContains('eua-id="-1"', $sReplyBody);
		$this->assertNotContains('<stored-cards><card id="61775" type-id="2" psp-id="2" preferred="true" state-id="2" charge-type-id="0" cvc-length="3" expired="false"><card-number-mask>5019 **** **** 3742 </card-number-mask><expiry>06/24</expiry></card></stored-cards>', $sReplyBody);
	}

	public function testSSOSuccessForStoredCard()
	{
		$pspID = 2;
        $this->bIgnoreErrors = true; //User Warning Expected
		$authenticateURL = $sCallbackURL = $this->_aMPOINT_CONN_INFO['protocol'] . '://' . $this->_aMPOINT_CONN_INFO['host']. '/_test/simulators/login.php';

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 2, '".$authenticateURL."')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (2, 113, 1, 'CPM Wallet')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 1, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (113, 2, $pspID, true, 2)");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (113, 11, 1, true, 2)");
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled, email) VALUES (5001, 100, 'abcExternal', '288828610', TRUE, 'profilePass', TRUE, 'jona@oismail.com')");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '5019********3742', '06/24', TRUE, 113, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
		$this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 2)");
		$this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 11)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 113, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 113, 1100, 1,  $pspID, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE,10,5000)");

		$xml = $this->getInitDoc(113, 1100, 208, 'success');

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();
		$this->assertEquals(200, $iStatus);
		$this->assertContains('eua-id="5001"', $sReplyBody);
		$this->assertContains('<stored-cards><card id="61775" type-id="2" psp-id="2" preferred="true" state-id="2" charge-type-id="0" cvc-length="3" expired="false"><card-number-mask>5019 **** **** 3742 </card-number-mask><expiry>06/24</expiry></card></stored-cards>', $sReplyBody);
	}

	public function testSSOMissingAuthToken()
	{
		$pspID = 2;
        $this->bIgnoreErrors = true; //User Warning Expected
		$authenticateURL = $sCallbackURL = $this->_aMPOINT_CONN_INFO['protocol'] . '://' . $this->_aMPOINT_CONN_INFO['host']. '/_test/simulators/login.php';

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 2, '".$authenticateURL."')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (2, 113, 1, 'CPM Wallet')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 1, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (113, 2, $pspID, true, 2)");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (113, 11, 1, true, 2)");
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled, email) VALUES (5001, 100, 'abcExternal', '288828610', TRUE, 'profilePass', TRUE, 'jona@oismail.com')");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '5019********3742', '06/24', TRUE, 113, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
		$this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 2)");
		$this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 11)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 113, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 113, 1100, 1,  $pspID, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE,10,5000)");

		$xml = $this->getInitDoc(113, 1100, 208);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();
		$this->assertEquals(200, $iStatus);
		$this->assertContains('eua-id="-1"', $sReplyBody);
		$this->assertNotContains('<stored-cards><card id="61775" type-id="2" psp-id="2" preferred="true" state-id="2" charge-type-id="0" cvc-length="3" expired="false"><card-number-mask>5019 **** **** 3742 </card-number-mask><expiry>06/24</expiry></card></stored-cards>', $sReplyBody);
	}

    public function testInvalidTransactionAmount()
    {
        $xml = $this->getInitDoc(113, 1100, 208, NULL, 100.99);

       $this->_httpClient->connect();

       $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
       $sReplyBody = $this->_httpClient->getReplyBody();

       $this->assertEquals(400, $iStatus);
       $this->assertContains('<?xml version="1.0" encoding="UTF-8"?><root><status code="400">Element \'amount\': \'100.99\' is not a valid value of the atomic type \'xs:nonNegativeInteger\'.</status></root>', $sReplyBody);
    }

    public function testAttemptNumber()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");

		$xml = $this->getInitDoc(113, 1100);

		$this->_httpClient->connect();
		// First Attempt
		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
		$this->assertContains('<?xml version="1.0" encoding="UTF-8"?><root><client-config id="113" account="1100" store-card="0" max-stored-cards="-1" auto-capture="false" enable-cvv="true" mode="0"><name>Test Client</name><callback-url></callback-url><accept-url></accept-url><cancel-url></cancel-url><app-url></app-url><css-url></css-url><logo-url></logo-url><base-image-url></base-image-url><additional-config></additional-config><accounts><account id= "1100" markup= "" /></accounts></client-config><transaction id="1" order-no="1234abc" type-id="0" eua-id="-1" language="da" auto-capture="false" mode="0"><amount country-id="100" currency-id="208" currency="DKK" decimals="2" symbol="" format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><mobile country-id="100" operator-id="10000">288828610</mobile><email>jona@oismail.com</email><callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url><accept-url/><cancel-url/></transaction><session id=\'1\' type=\'1\' total-amount=\'200\'><amount country-id="100" currency-id="208" currency="DKK" symbol="" format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount></session><cards></cards><wallets></wallets></root>', $sReplyBody);
		$res =  $this->queryDB('SELECT attempt from Log.Transaction_Tbl WHERE id = 1');
		$this->assertTrue(is_resource($res) );

		$attempt = 0;
		while ($row = pg_fetch_assoc($res) )
		{
			$attempt = (int)$row["attempt"];
		}
		$this->assertEquals(1, $attempt);


		// Second Attempt
        $this->constHTTPClient();
        $this->_httpClient->connect();
		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
		$this->assertContains('<?xml version="1.0" encoding="UTF-8"?><root><client-config id="113" account="1100" store-card="0" max-stored-cards="-1" auto-capture="false" enable-cvv="true" mode="0"><name>Test Client</name><callback-url></callback-url><accept-url></accept-url><cancel-url></cancel-url><app-url></app-url><css-url></css-url><logo-url></logo-url><base-image-url></base-image-url><additional-config></additional-config><accounts><account id= "1100" markup= "" /></accounts></client-config><transaction id="2" order-no="1234abc" type-id="0" eua-id="-1" language="da" auto-capture="false" mode="0"><amount country-id="100" currency-id="208" currency="DKK" decimals="2" symbol="" format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><mobile country-id="100" operator-id="10000">288828610</mobile><email>jona@oismail.com</email><callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url><accept-url/><cancel-url/></transaction><session id=\'2\' type=\'1\' total-amount=\'200\'><amount country-id="100" currency-id="208" currency="DKK" symbol="" format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount></session><cards></cards><wallets></wallets></root>', $sReplyBody);
		$res =  $this->queryDB('SELECT attempt from Log.Transaction_Tbl WHERE id = 2');
		$this->assertTrue(is_resource($res) );

		$attempt = 0;
		while ($row = pg_fetch_assoc($res) )
		{
			$attempt = (int)$row["attempt"];
		}
		$this->assertEquals(2, $attempt);
    }


    public function testStaticRouteLevelConfiguration()
	{
		$pspID = 2;

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (113, 2, $pspID, true, 2)");
		$this->queryDB("INSERT INTO client.staticroutelevelconfiguration (cardaccessid, cvcmandatory) VALUES (1, true);");
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '5019********3742', '06/24', TRUE, 113, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
		$this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 2)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 113, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 113, 1100, 1,  $pspID, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE,10,5000)");

		$xml = $this->getInitDoc(113, 1100);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
		$this->assertContains('<cards><card id="2" type-id="2" psp-id="2" min-length="16" max-length="16" cvc-length="3" state-id="2" payment-type="1" preferred="false" enabled="true" processor-type="1" installment="0" cvcmandatory="true" dcc="false"><name>Dankort</name><prefixes><prefix><min>5019</min><max>5019</max></prefix><prefix><min>4571</min><max>4571</max></prefix></prefixes>Dankort</card></cards>', $sReplyBody);
	}

	public function testCardNodes()
	{
		$pspID = 2;
		
		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");

		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid, psp_type) VALUES (113, 2, $pspID, true, 1, 1)");

		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (3, 113, 30, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 30, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid, psp_type) VALUES (113, 32, 30, true, 1, 4)");
        $this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 32)");

		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (4, 113, 51, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 51, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid, psp_type) VALUES (113, 73, 51, true, 1, 7)");
		$this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 73)");

		$this->queryDB("INSERT INTO client.staticroutelevelconfiguration (cardaccessid, cvcmandatory) VALUES (1, true);");
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '5019********3742', '06/24', TRUE, 113, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
		$this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 2)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 113, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid) VALUES (1001001, 100, 113, 1100, 1,  $pspID, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE,10)");

		$xml = $this->getInitDoc(113, 1100);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();
		$this->assertEquals(200, $iStatus);
		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><client-config id="113" account="1100" store-card="0" max-stored-cards="-1" auto-capture="false" enable-cvv="true" mode="0"><name>Test Client</name><callback-url></callback-url><accept-url></accept-url><cancel-url></cancel-url><app-url></app-url><css-url></css-url><logo-url></logo-url><base-image-url></base-image-url><additional-config></additional-config><accounts><account id= "1100" markup= "" /></accounts></client-config><transaction id="1" order-no="1234abc" type-id="0" eua-id="-1" language="da" auto-capture="false" mode="0"><amount country-id="100" currency-id="208" currency="DKK" decimals="2" symbol="" format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><mobile country-id="100" operator-id="10000">288828610</mobile><email>jona@oismail.com</email><callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url><accept-url/><cancel-url/></transaction><session id=\'1\' type=\'1\' total-amount=\'200\'><amount country-id="100" currency-id="208" currency="DKK" symbol="" format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount></session><cards><card id="2" type-id="2" psp-id="2" min-length="16" max-length="16" cvc-length="3" state-id="1" payment-type="1" preferred="false" enabled="true" processor-type="1" installment="0" cvcmandatory="true" dcc="false"><name>Dankort</name><prefixes><prefix><min>5019</min><max>5019</max></prefix><prefix><min>4571</min><max>4571</max></prefix></prefixes>Dankort</card><card id="32" type-id="32" psp-id="30" min-length="" max-length="" cvc-length="" state-id="1" payment-type="4" preferred="false" enabled="true" processor-type="4" installment="0" cvcmandatory="false" dcc="false"><name>Alipay</name><prefixes/>Alipay</card><card id="73" type-id="73" psp-id="51" min-length="" max-length="" cvc-length="" state-id="1" payment-type="7" preferred="false" enabled="true" processor-type="7" installment="0" cvcmandatory="false" dcc="false"><name>FPX</name><prefixes/><active-payment-menthods/>FPX</card></cards><wallets></wallets></root>', $sReplyBody);

		$this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('isnewcardconfig', 'true', 113, 'client', 0);");

		$this->constHTTPClient();
		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();
		$this->assertEquals(200, $iStatus);
		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><client-config id="113" account="1100" store-card="0" max-stored-cards="-1" auto-capture="false" enable-cvv="true" mode="0"><name>Test Client</name><callback-url></callback-url><accept-url></accept-url><cancel-url></cancel-url><app-url></app-url><css-url></css-url><logo-url></logo-url><base-image-url></base-image-url><additional-config></additional-config><accounts><account id= "1100" markup= "" /></accounts></client-config><transaction id="2" order-no="1234abc" type-id="0" eua-id="-1" language="da" auto-capture="false" mode="0"><amount country-id="100" currency-id="208" currency="DKK" decimals="2" symbol="" format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><mobile country-id="100" operator-id="10000">288828610</mobile><email>jona@oismail.com</email><callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url><accept-url/><cancel-url/></transaction><session id=\'2\' type=\'1\' total-amount=\'200\'><amount country-id="100" currency-id="208" currency="DKK" symbol="" format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount></session><cards><card id="2" type-id="2" psp-id="2" min-length="16" max-length="16" cvc-length="3" state-id="1" payment-type="1" preferred="false" enabled="true" processor-type="1" installment="0" cvcmandatory="true" dcc="false"><name>Dankort</name><prefixes><prefix><min>5019</min><max>5019</max></prefix><prefix><min>4571</min><max>4571</max></prefix></prefixes>Dankort</card></cards><wallets></wallets><apms><card id="32" type-id="32" psp-id="30" min-length="" max-length="" cvc-length="" state-id="1" payment-type="4" preferred="false" enabled="true" processor-type="4" installment="0" cvcmandatory="false" dcc="false"><name>Alipay</name><prefixes/>Alipay</card></apms><gateways><card id="73" type-id="73" psp-id="51" min-length="" max-length="" cvc-length="" state-id="1" payment-type="7" preferred="false" enabled="true" processor-type="7" installment="0" cvcmandatory="false" dcc="false"><name>FPX</name><prefixes/><active-payment-menthods/>FPX</card></gateways></root>', $sReplyBody);
	}

	public function testInvalidEmailAddress()
    {
		$xml = $this->getInitDoc(113, 1100, 208, NULL, 100, NULL, "invalid email@test.com");
		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(400, $iStatus);
		$this->assertContains('<?xml version="1.0" encoding="UTF-8"?><root><status code="400">Element \'email\': [facet \'pattern\'] The value \'invalid email@test.com\' is not accepted by the pattern \'(\w+([-+.\']\w+)*){1,64}@([a-zA-Z0-9]+([-.]\w+)*\.\w+([-.]\w+)*[a-zA-Z0-9]){1,255}\'.</status>', $sReplyBody);
    }
}
