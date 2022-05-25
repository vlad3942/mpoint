<?php
require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class InitializeAPIValidationTest extends baseAPITest
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
        $aMPOINT_CONN_INFO['path'] = "/mApp/api/initialize.php";
        $aMPOINT_CONN_INFO["contenttype"] = "text/xml";
        $this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO) );
    }

    protected function getInitDoc($client, $account, $currecyid = null, $token=null, $amount = 200, $hmac=null, $email=null, $customerref=null, $mobile=null, $profileid=null, $sso_preference=null, $version="2.0",$fxservicetypeid=0,$countryid=100, $orderXml='',$sessionid=0)
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml .= '<root>';
		$xml .= '<initialize-payment client-id="'. $client .'" account="'. $account .'">';
		$xml .= '<transaction order-no="1234abc"';
		if($sessionid > 0){
            $xml .= ' session-id="'.$sessionid.'"';
        }
        $xml .= '>';
		$xml .= '<amount country-id="'.$countryid.'"';
		if(isset($currecyid) === true)
		    $xml .= ' currency-id="'.$currecyid.'"';
		$xml .= '>'.$amount.'</amount>';
		$xml .= '<callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url>';
        if(!empty($orderXml))
            $xml .= $orderXml;
		if(isset($hmac)=== true) $xml .= '<hmac>'.$hmac.'</hmac>';
        if($fxservicetypeid > 0)
            $xml .= '<foreign-exchange-info><service-type-id>'.$fxservicetypeid.'</service-type-id></foreign-exchange-info>';
        $xml .= '</transaction>';
		if(isset($token) === true)
        {
		    $xml .= '<auth-token>'.$token.'</auth-token>';
        }

        if(isset($sso_preference) === true && ($sso_preference === 'STRICT'))
        {	
        	if(isset($profileid) === true) {
				$xml .= '<client-info platform="iOS" sdk-version="'.$version.'" version="'.$version.'" language="da" profileid= "'.$profileid.'">';
			} else {
				$xml .= '<client-info platform="iOS" sdk-version="'.$version.'" version="'.$version.'" language="da" >';
			}
        	

        	if(isset($mobile) === true) {
				$xml .= '<mobile country-id="100" operator-id="10000">'.$mobile.'</mobile>';
			} 
			if(isset($email) === true) {
				$xml .= '<email>'.$email.'</email>';
			} 
			if(isset($customerref) === true) {
				$xml .= '<customer-ref>'.$email.'</customer-ref>';
			} 
        } 
        else {
			$xml .= '<client-info platform="iOS" sdk-version="'.$version.'" version="'.$version.'" language="da">';
			$xml .= '<mobile country-id="100" operator-id="10000">288828610</mobile>';
			if(isset($email) === true) {
				$xml .= '<email>'.$email.'</email>';
			} else {
				$xml .= '<email>jona@oismail.com</email>';			
			}
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

	public function failed_testUnsupportedMediaType()
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
        $this->bIgnoreErrors = true;
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, enabled) VALUES (10099, 1, 100, 'Test Client', false)");

		$xml = $this->getInitDoc(10099, 1100);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(400, $iStatus);
		$this->assertStringContainsString('<status code="3">Client ID / Account doesn\'t match</status>', $sReplyBody);
    }

    public function testDisabledAccount()
    {
        $this->bIgnoreErrors = true;
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, enabled) VALUES (10099, 1, 100, 'Test Client', true)");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, enabled) VALUES (1100, 10099, false)");

		$xml = $this->getInitDoc(10099, 1100);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(400, $iStatus);
		$this->assertStringContainsString('<status code="14">Client ID / Account doesn\'t match</status>', $sReplyBody);
	}

	public function testUnauthorized()
	{
		$xml = $this->getInitDoc(1, 1);

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

		$xml = $this->getInitDoc(10099, 1100);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Twrong'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(401, $iStatus);
		$this->assertStringContainsString('<status code="401">Username / Password doesn\'t match</status>', $sReplyBody);
	}

	public function testEmptyCardConfiguration()
	{
		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");

		$xml = $this->getInitDoc(10099, 1100);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?><root><client-config id="10099" account="1100" store-card="0" max-stored-cards="-1" auto-capture="false" enable-cvv="true" mode="0"><name>Test Client</name><callback-url></callback-url><accept-url></accept-url><cancel-url></cancel-url><app-url></app-url><css-url></css-url><logo-url></logo-url><base-image-url></base-image-url><additional-config></additional-config><accounts><account id= "1100" markup= "" /></accounts></client-config><transaction id="1" order-no="1234abc" type-id="1" eua-id="-1" language="da" auto-capture="false" mode="0"><amount country-id="100" currency-id="208" currency="DKK" decimals="2" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><mobile country-id="100" operator-id="10000">288828610</mobile><email>jona@oismail.com</email><callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url><accept-url/><cancel-url/></transaction><session id=\'1\' type=\'1\' total-amount=\'200\'><amount country-id="100" currency-id="208" currency="DKK" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><status>4001</status></session><cards></cards><wallets></wallets><apms></apms><aggregators></aggregators><offline></offline><vouchers></vouchers></root>', $sReplyBody);
    }


    public function testEmptyCardConfigurationWithCurrency()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
        $this->queryDB("INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid, enabled) VALUES (10099,100,840, true)");
        $xml = $this->getInitDoc(10099, 1100,840);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(200, $iStatus);
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?><root><client-config id="10099" account="1100" store-card="0" max-stored-cards="-1" auto-capture="false" enable-cvv="true" mode="0"><name>Test Client</name><callback-url></callback-url><accept-url></accept-url><cancel-url></cancel-url><app-url></app-url><css-url></css-url><logo-url></logo-url><base-image-url></base-image-url><additional-config></additional-config><accounts><account id= "1100" markup= "" /></accounts></client-config><transaction id="1" order-no="1234abc" type-id="1" eua-id="-1" language="da" auto-capture="false" mode="0"><amount country-id="100" currency-id="840" currency="USD" decimals="2" symbol="$" format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><mobile country-id="100" operator-id="10000">288828610</mobile><email>jona@oismail.com</email><callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url><accept-url/><cancel-url/></transaction><session id=\'1\' type=\'1\' total-amount=\'200\'><amount country-id="100" currency-id="840" currency="USD" symbol="$" format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><status>4001</status></session><cards></cards><wallets></wallets><apms></apms><aggregators></aggregators><offline></offline><vouchers></vouchers></root>', $sReplyBody);
    }


	public function testSoftDisabledCardType()
	{
		$pspID = 2;

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 2, $pspID, true, 2)");
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, 1,  $pspID, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE,10,5000)");

		$xml = $this->getInitDoc(10099, 1100);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
		$this->assertStringContainsString('<cards><card id="2" type-id="2" psp-id="2" min-length="16" max-length="16" cvc-length="3" state-id="2" payment-type="1" preferred="false" enabled="true" processor-type="1" installment="0" cvcmandatory="false" dcc="false" presentment-currency="false"><name>Dankort</name><prefixes><prefix><min>5019</min><max>5019</max></prefix><prefix><min>4571</min><max>4571</max></prefix></prefixes>Dankort</card></cards>', $sReplyBody);
	}

	public function testHardDisabledCardType()
	{
		$pspID = 2;

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 2, $pspID, false, 2)");
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (11, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, 1,  $pspID, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE,11,5000)");

		$xml = $this->getInitDoc(10099, 1100);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
		$this->assertStringContainsString('<cards></cards>', $sReplyBody);
	}

    public function testEmptyCurrencyId()
    {
        $pspID = 2;

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 2, $pspID, false, 2)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (11, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, 1,  $pspID, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE,11,5000)");

        $xml = $this->getInitDoc(10099, 1100, null);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(200, $iStatus);
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?><root><client-config id="10099" account="1100" store-card="0" max-stored-cards="-1" auto-capture="false" enable-cvv="true" mode="0"><name>Test Client</name><callback-url></callback-url><accept-url></accept-url><cancel-url></cancel-url><app-url></app-url><css-url></css-url><logo-url></logo-url><base-image-url></base-image-url><additional-config></additional-config><accounts><account id= "1100" markup= "" /></accounts></client-config><transaction id="1" order-no="1234abc" type-id="1" eua-id="-1" language="da" auto-capture="false" mode="0"><amount country-id="100" currency-id="208" currency="DKK" decimals="2" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><mobile country-id="100" operator-id="10000">288828610</mobile><email>jona@oismail.com</email><callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url><accept-url/><cancel-url/></transaction><session id=\'1\' type=\'1\' total-amount=\'200\'><amount country-id="100" currency-id="208" currency="DKK" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><status>4001</status></session><cards></cards><wallets></wallets><apms></apms><aggregators></aggregators><offline></offline><vouchers></vouchers></root>', $sReplyBody);
    }

    public function testEuaIdPasswordFlow()
	{
		$pspID = 2;

		$authenticateURL = $sCallbackURL = $this->_aMPOINT_CONN_INFO['protocol'] . '://' . $this->_aMPOINT_CONN_INFO['host']. '/_test/simulators/login.php';

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (2, 10099, 1, 'CPM Wallet')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 1, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 2, $pspID, true, 2)");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 11, 1, true, 2)");
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled, email) VALUES (5001, 100, 'abcExternal', '288828610', TRUE, 'profilePass', TRUE, 'jona@oismail.com')");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, 1,  $pspID, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE,10,5000)");

		$xml = $this->getInitDoc(10099, 1100, 208);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();
		$this->assertEquals(200, $iStatus);
		$this->assertStringContainsString('eua-id="5001"', $sReplyBody);
		$this->assertStringNotContainsString('<stored-cards><card id="61775" type-id="2" psp-id="2" preferred="true" state-id="2" charge-type-id="0" cvc-length="3" expired="false"><card-number-mask>5019 **** **** 3742 </card-number-mask><expiry>06/24</expiry></card></stored-cards>', $sReplyBody);
	}

    public function testSSOFailForStoredCard()
	{
		$pspID = 2;
		$this->bIgnoreErrors = true; //User Warning Expected

		$authenticateURL = $sCallbackURL = $this->_aMPOINT_CONN_INFO['protocol'] . '://' . $this->_aMPOINT_CONN_INFO['host']. '/_test/simulators/login.php';

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 2, '".$authenticateURL."')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (2, 10099, 1, 'CPM Wallet')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 1, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 2, $pspID, true, 2)");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 11, 1, true, 2)");
        $this->queryDB("INSERT INTO client.services_tbl (clientid, legacy_flow_enabled) VALUES(10099, true);");
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled, email) VALUES (5001, 100, 'abcExternal', '288828610', TRUE, 'profilePass', TRUE, 'jona@oismail.com')");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, 1,  $pspID, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE,10,5000)");

		$xml = $this->getInitDoc(10099, 1100, 208, 'fail');

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();
		$this->assertEquals(200, $iStatus);
		$this->assertStringContainsString('eua-id="-1"', $sReplyBody);
		$this->assertStringNotContainsString('<stored-cards><card id="61775" type-id="2" psp-id="2" preferred="true" state-id="2" charge-type-id="0" cvc-length="3" expired="false"><card-number-mask>5019 **** **** 3742 </card-number-mask><expiry>06/24</expiry></card></stored-cards>', $sReplyBody);
	}

	public function testSSOTimeoutForStoredCard()
	{
		$pspID = 2;
        $this->bIgnoreErrors = true; //User Warning Expected
		$authenticateURL = $sCallbackURL = $this->_aMPOINT_CONN_INFO['protocol'] . '://' . $this->_aMPOINT_CONN_INFO['host']. '/_test/simulators/login.php';

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 2, '".$authenticateURL."')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (2, 10099, 1, 'CPM Wallet')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 1, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 2, $pspID, true, 2)");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 11, 1, true, 2)");
        $this->queryDB("INSERT INTO client.services_tbl (clientid, legacy_flow_enabled) VALUES(10099, true);");
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled, email) VALUES (5001, 100, 'abcExternal', '288828610', TRUE, 'profilePass', TRUE, 'jona@oismail.com')");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, 1,  $pspID, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE,10,5000)");

		$xml = $this->getInitDoc(10099, 1100, 208, 'timeout');

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();
		$this->assertEquals(200, $iStatus);
		$this->assertStringContainsString('eua-id="-1"', $sReplyBody);
		$this->assertStringNotContainsString('<stored-cards><card id="61775" type-id="2" psp-id="2" preferred="true" state-id="2" charge-type-id="0" cvc-length="3" expired="false"><card-number-mask>5019 **** **** 3742 </card-number-mask><expiry>06/24</expiry></card></stored-cards>', $sReplyBody);
	}

	public function testSSOSuccessForStoredCard()
	{
		$pspID = 2;
        $this->bIgnoreErrors = true; //User Warning Expected
		$authenticateURL = $sCallbackURL = $this->_aMPOINT_CONN_INFO['protocol'] . '://' . $this->_aMPOINT_CONN_INFO['host']. '/_test/simulators/login.php';

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 2, '".$authenticateURL."')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (2, 10099, 1, 'CPM Wallet')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 1, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid,countryid) VALUES (10099, 2, $pspID, true, 2,100)");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid,countryid) VALUES (10099, 11, 1, true, 2,100)");
		$this->queryDB("INSERT INTO client.staticroutelevelconfiguration (cardaccessid, cvcmandatory) VALUES (1, true);");
		$this->queryDB("INSERT INTO client.staticroutelevelconfiguration (cardaccessid, cvcmandatory) VALUES (2, true);");
        $this->queryDB("INSERT INTO client.services_tbl (clientid, legacy_flow_enabled) VALUES(10099, true);");
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled, email) VALUES (5001, 100, 'abcExternal', '288828610', TRUE, 'profilePass', TRUE, 'jona@oismail.com')");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, 1,  $pspID, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE,10,5000)");

		$xml = $this->getInitDoc(10099, 1100, 208, 'success');

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();
		$this->assertEquals(200, $iStatus);
		$this->assertStringContainsString('eua-id="5001"', $sReplyBody);
        $this->assertStringContainsString('<stored-cards><card id="61775" type-id="2" psp-id="2" preferred="true" state-id="2" charge-type-id="0" cvc-length="3" expired="false" cvcmandatory="true" dcc="false" presentment-currency="false"><card-number-mask>5019 **** **** 3742 </card-number-mask><expiry>06/24</expiry></card></stored-cards>', $sReplyBody);
    }

	public function testSSOMissingAuthToken()
	{
		$pspID = 2;
        $this->bIgnoreErrors = true; //User Warning Expected
		$authenticateURL = $sCallbackURL = $this->_aMPOINT_CONN_INFO['protocol'] . '://' . $this->_aMPOINT_CONN_INFO['host']. '/_test/simulators/login.php';

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 2, '".$authenticateURL."')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (2, 10099, 1, 'CPM Wallet')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 1, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 2, $pspID, true, 2)");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 11, 1, true, 2)");
        $this->queryDB("INSERT INTO client.services_tbl (clientid, legacy_flow_enabled) VALUES(10099, true);");
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled, email) VALUES (5001, 100, 'abcExternal', '288828610', TRUE, 'profilePass', TRUE, 'jona@oismail.com')");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, 1,  $pspID, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE,10,5000)");

		$xml = $this->getInitDoc(10099, 1100, 208);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();
		$this->assertEquals(200, $iStatus);
		$this->assertStringContainsString('eua-id="-1"', $sReplyBody);
        $this->assertStringNotContainsString('<stored-cards><card id="61775" type-id="2" psp-id="2" preferred="true" state-id="2" charge-type-id="0" cvc-length="3" expired="false"><card-number-mask>5019 **** **** 3742 </card-number-mask><expiry>06/24</expiry></card></stored-cards>', $sReplyBody);
    }

    public function testInvalidTransactionAmount()
    {
        $xml = $this->getInitDoc(10099, 1100, 208, NULL, 100.99);

       $this->_httpClient->connect();

       $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
       $sReplyBody = $this->_httpClient->getReplyBody();

       $this->assertEquals(400, $iStatus);
       $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?><root><status code="400">Element \'amount\': \'100.99\' is not a valid value of the atomic type \'xs:nonNegativeInteger\'.</status></root>', $sReplyBody);
    }

    public function testAttemptNumber()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");

		$xml = $this->getInitDoc(10099, 1100);

		$this->_httpClient->connect();
		// First Attempt
		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?><root><client-config id="10099" account="1100" store-card="0" max-stored-cards="-1" auto-capture="false" enable-cvv="true" mode="0"><name>Test Client</name><callback-url></callback-url><accept-url></accept-url><cancel-url></cancel-url><app-url></app-url><css-url></css-url><logo-url></logo-url><base-image-url></base-image-url><additional-config></additional-config><accounts><account id= "1100" markup= "" /></accounts></client-config><transaction id="1" order-no="1234abc" type-id="1" eua-id="-1" language="da" auto-capture="false" mode="0"><amount country-id="100" currency-id="208" currency="DKK" decimals="2" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><mobile country-id="100" operator-id="10000">288828610</mobile><email>jona@oismail.com</email><callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url><accept-url/><cancel-url/></transaction><session id=\'1\' type=\'1\' total-amount=\'200\'><amount country-id="100" currency-id="208" currency="DKK" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><status>4001</status></session><cards></cards><wallets></wallets><apms></apms><aggregators></aggregators><offline></offline><vouchers></vouchers></root>', $sReplyBody);
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
		$this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?><root><client-config id="10099" account="1100" store-card="0" max-stored-cards="-1" auto-capture="false" enable-cvv="true" mode="0"><name>Test Client</name><callback-url></callback-url><accept-url></accept-url><cancel-url></cancel-url><app-url></app-url><css-url></css-url><logo-url></logo-url><base-image-url></base-image-url><additional-config></additional-config><accounts><account id= "1100" markup= "" /></accounts></client-config><transaction id="2" order-no="1234abc" type-id="1" eua-id="-1" language="da" auto-capture="false" mode="0"><amount country-id="100" currency-id="208" currency="DKK" decimals="2" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><mobile country-id="100" operator-id="10000">288828610</mobile><email>jona@oismail.com</email><callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url><accept-url/><cancel-url/></transaction><session id=\'2\' type=\'1\' total-amount=\'200\'><amount country-id="100" currency-id="208" currency="DKK" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><status>4001</status></session><cards></cards><wallets></wallets><apms></apms><aggregators></aggregators><offline></offline><vouchers></vouchers></root>', $sReplyBody);
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
        $this->queryDB("DELETE FROM CLIENT.STATICROUTELEVELCONFIGURATION");
		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (id, clientid, cardid, pspid, enabled, stateid) VALUES (3,10099, 2, $pspID, true, 2)");
		$this->queryDB("INSERT INTO Client.staticroutelevelconfiguration (cardaccessid, cvcmandatory) VALUES (3, true);");
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, 1,  $pspID, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE,10,5000)");

		$xml = $this->getInitDoc(10099, 1100);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
		$this->assertStringContainsString('<cards><card id="2" type-id="2" psp-id="2" min-length="16" max-length="16" cvc-length="3" state-id="2" payment-type="1" preferred="false" enabled="true" processor-type="1" installment="0" cvcmandatory="true" dcc="false" presentment-currency="false"><name>Dankort</name><prefixes><prefix><min>5019</min><max>5019</max></prefix><prefix><min>4571</min><max>4571</max></prefix></prefixes>Dankort</card></cards><wallets></wallets><apms></apms><aggregators></aggregators><offline></offline><vouchers></vouchers></root>', $sReplyBody);
	}

	public function testCardNodes()
	{
		$pspID = 2;
        $this->queryDB("DELETE FROM CLIENT.STATICROUTELEVELCONFIGURATION");
		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");

		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid, psp_type) VALUES (10099, 2, $pspID, true, 1, 1)");

		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (3, 10099, 30, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 30, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid, psp_type) VALUES (10099, 32, 30, true, 1, 4)");

		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (4, 10099, 51, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 51, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid, psp_type) VALUES ( 10099, 73, 51, true, 1, 7)");

		//$this->queryDB("INSERT INTO client.staticroutelevelconfiguration (cardaccessid, cvcmandatory) VALUES (2, true);");
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid) VALUES (1001001, 100, 10099, 1100, 1,  $pspID, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE,10)");

		$xml = $this->getInitDoc(10099, 1100);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();
		$this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><client-config id="10099" account="1100" store-card="0" max-stored-cards="-1" auto-capture="false" enable-cvv="true" mode="0"><name>Test Client</name><callback-url></callback-url><accept-url></accept-url><cancel-url></cancel-url><app-url></app-url><css-url></css-url><logo-url></logo-url><base-image-url></base-image-url><additional-config></additional-config><accounts><account id= "1100" markup= "" /></accounts></client-config><transaction id="1" order-no="1234abc" type-id="1" eua-id="-1" language="da" auto-capture="false" mode="0"><amount country-id="100" currency-id="208" currency="DKK" decimals="2" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><mobile country-id="100" operator-id="10000">288828610</mobile><email>jona@oismail.com</email><callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url><accept-url/><cancel-url/></transaction><session id=\'1\' type=\'1\' total-amount=\'200\'><amount country-id="100" currency-id="208" currency="DKK" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><status>4001</status></session><cards><card id="2" type-id="2" psp-id="2" min-length="16" max-length="16" cvc-length="3" state-id="1" payment-type="1" preferred="false" enabled="true" processor-type="1" installment="0" cvcmandatory="false" dcc="false" presentment-currency="false"><name>Dankort</name><prefixes><prefix><min>5019</min><max>5019</max></prefix><prefix><min>4571</min><max>4571</max></prefix></prefixes>Dankort</card></cards><wallets></wallets><apms></apms><aggregators></aggregators><offline></offline><vouchers></vouchers></root>', $sReplyBody);

        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('isnewcardconfig', 'true', 10099, 'client', 0);");

		$this->constHTTPClient();
		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();
		$this->assertEquals(200, $iStatus);
		$this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?><root><client-config id="10099" account="1100" store-card="0" max-stored-cards="-1" auto-capture="false" enable-cvv="true" mode="0"><name>Test Client</name><callback-url></callback-url><accept-url></accept-url><cancel-url></cancel-url><app-url></app-url><css-url></css-url><logo-url></logo-url><base-image-url></base-image-url><additional-config></additional-config><accounts><account id= "1100" markup= "" /></accounts></client-config><transaction id="2" order-no="1234abc" type-id="1" eua-id="-1" language="da" auto-capture="false" mode="0"><amount country-id="100" currency-id="208" currency="DKK" decimals="2" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><mobile country-id="100" operator-id="10000">288828610</mobile><email>jona@oismail.com</email><callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url><accept-url/><cancel-url/></transaction><session id=\'2\' type=\'1\' total-amount=\'200\'><amount country-id="100" currency-id="208" currency="DKK" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><status>4001</status></session><cards><card id="2" type-id="2" psp-id="2" min-length="16" max-length="16" cvc-length="3" state-id="1" payment-type="1" preferred="false" enabled="true" processor-type="1" installment="0" cvcmandatory="false" dcc="false" presentment-currency="false"><name>Dankort</name><prefixes><prefix><min>5019</min><max>5019</max></prefix><prefix><min>4571</min><max>4571</max></prefix></prefixes>Dankort</card></cards><wallets></wallets><apms></apms><aggregators></aggregators><offline></offline><vouchers></vouchers></root>', $sReplyBody);
	}

	public function testInvalidEmailAddress()
    {
		$xml = $this->getInitDoc(10099, 1100, 208, NULL, 100, NULL, "invalid email@test.com");
		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(400, $iStatus);
		$this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?><root><status code="400">Element \'email\': [facet \'pattern\'] The value \'invalid email@test.com\' is not accepted by the pattern \'((_[a-zA-Z0-9]|[a-zA-Z0-9])+(_|([-+._\'][a-zA-Z0-9]+)*)){1,64}@([a-zA-Z0-9]+([-.]\w+)*\.\w+([-.]\w+)*[a-zA-Z0-9]){1,255}\'.</status>', $sReplyBody);
    }

    public function testInvalidEmailAddressWithRepetitiveUnderscoreInTheEnd()
    {
        $xml = $this->getInitDoc(10099, 1100, 208, NULL, 100, NULL, "email__@test.com");
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(400, $iStatus);
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?><root><status code="400">Element \'email\': [facet \'pattern\'] The value \'email__@test.com\' is not accepted by the pattern \'((_[a-zA-Z0-9]|[a-zA-Z0-9])+(_|([-+._\'][a-zA-Z0-9]+)*)){1,64}@([a-zA-Z0-9]+([-.]\w+)*\.\w+([-.]\w+)*[a-zA-Z0-9]){1,255}\'.</status>', $sReplyBody);
    }
    public function testInvalidEmailAddressWithQuoteInTheEnd()
    {
        $xml = $this->getInitDoc(10099, 1100, 208, NULL, 100, NULL, "demo'@test.com");
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(400, $iStatus);
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?><root><status code="400">Element \'email\': [facet \'pattern\'] The value \'demo\'@test.com\' is not accepted by the pattern \'((_[a-zA-Z0-9]|[a-zA-Z0-9])+(_|([-+._\'][a-zA-Z0-9]+)*)){1,64}@([a-zA-Z0-9]+([-.]\w+)*\.\w+([-.]\w+)*[a-zA-Z0-9]){1,255}\'.</status>', $sReplyBody);
    }

    public function testInvalidEmailAddressWithPlusInTheEnd()
    {
        $xml = $this->getInitDoc(10099, 1100, 208, NULL, 100, NULL, "demo+@test.com");
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(400, $iStatus);
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?><root><status code="400">Element \'email\': [facet \'pattern\'] The value \'demo+@test.com\' is not accepted by the pattern \'((_[a-zA-Z0-9]|[a-zA-Z0-9])+(_|([-+._\'][a-zA-Z0-9]+)*)){1,64}@([a-zA-Z0-9]+([-.]\w+)*\.\w+([-.]\w+)*[a-zA-Z0-9]){1,255}\'.</status>', $sReplyBody);
    }

    public function testInvalidEmailAddressWithHyphenInTheEnd()
    {
        $xml = $this->getInitDoc(10099, 1100, 208, NULL, 100, NULL, "demo-@test.com");
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(400, $iStatus);
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?><root><status code="400">Element \'email\': [facet \'pattern\'] The value \'demo-@test.com\' is not accepted by the pattern \'((_[a-zA-Z0-9]|[a-zA-Z0-9])+(_|([-+._\'][a-zA-Z0-9]+)*)){1,64}@([a-zA-Z0-9]+([-.]\w+)*\.\w+([-.]\w+)*[a-zA-Z0-9]){1,255}\'.</status>', $sReplyBody);
    }

    public function testInvalidEmailAddressWithDotInTheEnd()
    {
        $xml = $this->getInitDoc(10099, 1100, 208, NULL, 100, NULL, "demo.@test.com");
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(400, $iStatus);
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?><root><status code="400">Element \'email\': [facet \'pattern\'] The value \'demo.@test.com\' is not accepted by the pattern \'((_[a-zA-Z0-9]|[a-zA-Z0-9])+(_|([-+._\'][a-zA-Z0-9]+)*)){1,64}@([a-zA-Z0-9]+([-.]\w+)*\.\w+([-.]\w+)*[a-zA-Z0-9]){1,255}\'.</status>', $sReplyBody);
    }

    public function testValidEmailAddressWithUnderscoreInTheEnd()
    {
        $pspID = Constants::iWIRE_CARD_PSP;
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 2, $pspID, true, 1)");

        $xml = $this->getInitDoc(10099, 1100, 208,null,200,null,"DUMMY_@cellpointmobile.com","DUMMY_@cellpointmobile.com","9766367227",null,null,"2.0","11");
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><client-config id="10099" account="1100" store-card="0" max-stored-cards="-1" auto-capture="false" enable-cvv="true" mode="0"><name>Test Client</name><callback-url></callback-url><accept-url></accept-url><cancel-url></cancel-url><app-url></app-url><css-url></css-url><logo-url></logo-url><base-image-url></base-image-url><additional-config></additional-config><accounts><account id= "1100" markup= "" /></accounts></client-config><transaction id="1" order-no="1234abc" type-id="1" eua-id="-1" language="da" auto-capture="false" mode="0"><amount country-id="100" currency-id="208" currency="DKK" decimals="2" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><mobile country-id="100" operator-id="10000">288828610</mobile><email>DUMMY_@cellpointmobile.com</email><callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url><accept-url/><cancel-url/></transaction><session id=\'1\' type=\'1\' total-amount=\'200\'><amount country-id="100" currency-id="208" currency="DKK" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><status>4001</status></session><cards><card id="2" type-id="2" psp-id="18" min-length="16" max-length="16" cvc-length="3" state-id="1" payment-type="1" preferred="false" enabled="true" processor-type="1" installment="0" cvcmandatory="false" dcc="false" presentment-currency="false"><name>Dankort</name><prefixes><prefix><min>5019</min><max>5019</max></prefix><prefix><min>4571</min><max>4571</max></prefix></prefixes>Dankort</card></cards><wallets></wallets><apms></apms><aggregators></aggregators><offline></offline><vouchers></vouchers></root>', $sReplyBody);
    }

    public function testValidEmailAddressWithPlus()
    {
        $pspID = Constants::iWIRE_CARD_PSP;
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 2, $pspID, true, 1)");

        $xml = $this->getInitDoc(10099, 1100, 208,null,200,null,"DUMMY+test+2@cellpointmobile.com","DUMMY+test+2@cellpointmobile.com","9766367227",null,null,"2.0","11");
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><client-config id="10099" account="1100" store-card="0" max-stored-cards="-1" auto-capture="false" enable-cvv="true" mode="0"><name>Test Client</name><callback-url></callback-url><accept-url></accept-url><cancel-url></cancel-url><app-url></app-url><css-url></css-url><logo-url></logo-url><base-image-url></base-image-url><additional-config></additional-config><accounts><account id= "1100" markup= "" /></accounts></client-config><transaction id="1" order-no="1234abc" type-id="1" eua-id="-1" language="da" auto-capture="false" mode="0"><amount country-id="100" currency-id="208" currency="DKK" decimals="2" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><mobile country-id="100" operator-id="10000">288828610</mobile><email>DUMMY+test+2@cellpointmobile.com</email><callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url><accept-url/><cancel-url/></transaction><session id=\'1\' type=\'1\' total-amount=\'200\'><amount country-id="100" currency-id="208" currency="DKK" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><status>4001</status></session><cards><card id="2" type-id="2" psp-id="18" min-length="16" max-length="16" cvc-length="3" state-id="1" payment-type="1" preferred="false" enabled="true" processor-type="1" installment="0" cvcmandatory="false" dcc="false" presentment-currency="false"><name>Dankort</name><prefixes><prefix><min>5019</min><max>5019</max></prefix><prefix><min>4571</min><max>4571</max></prefix></prefixes>Dankort</card></cards><wallets></wallets><apms></apms><aggregators></aggregators><offline></offline><vouchers></vouchers></root>', $sReplyBody);
    }

    public function testValidEmailAddressWithHyphen()
    {
        $pspID = Constants::iWIRE_CARD_PSP;
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 2, $pspID, true, 1)");

        $xml = $this->getInitDoc(10099, 1100, 208,null,200,null,"DUMMY-test-1@cellpointmobile.com","DUMMY-test-1@cellpointmobile.com","9766367227",null,null,"2.0","11");
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><client-config id="10099" account="1100" store-card="0" max-stored-cards="-1" auto-capture="false" enable-cvv="true" mode="0"><name>Test Client</name><callback-url></callback-url><accept-url></accept-url><cancel-url></cancel-url><app-url></app-url><css-url></css-url><logo-url></logo-url><base-image-url></base-image-url><additional-config></additional-config><accounts><account id= "1100" markup= "" /></accounts></client-config><transaction id="1" order-no="1234abc" type-id="1" eua-id="-1" language="da" auto-capture="false" mode="0"><amount country-id="100" currency-id="208" currency="DKK" decimals="2" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><mobile country-id="100" operator-id="10000">288828610</mobile><email>DUMMY-test-1@cellpointmobile.com</email><callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url><accept-url/><cancel-url/></transaction><session id=\'1\' type=\'1\' total-amount=\'200\'><amount country-id="100" currency-id="208" currency="DKK" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><status>4001</status></session><cards><card id="2" type-id="2" psp-id="18" min-length="16" max-length="16" cvc-length="3" state-id="1" payment-type="1" preferred="false" enabled="true" processor-type="1" installment="0" cvcmandatory="false" dcc="false" presentment-currency="false"><name>Dankort</name><prefixes><prefix><min>5019</min><max>5019</max></prefix><prefix><min>4571</min><max>4571</max></prefix></prefixes>Dankort</card></cards><wallets></wallets><apms></apms><aggregators></aggregators><offline></offline><vouchers></vouchers></root>', $sReplyBody);
    }

    public function testValidEmailAddressWithDot()
    {
        $pspID = Constants::iWIRE_CARD_PSP;
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 2, $pspID, true, 1)");

        $xml = $this->getInitDoc(10099, 1100, 208,null,200,null,"DUMMY.test.demo@cellpointmobile.com","DUMMY.test.demo@cellpointmobile.com","9766367227",null,null,"2.0","11");
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><client-config id="10099" account="1100" store-card="0" max-stored-cards="-1" auto-capture="false" enable-cvv="true" mode="0"><name>Test Client</name><callback-url></callback-url><accept-url></accept-url><cancel-url></cancel-url><app-url></app-url><css-url></css-url><logo-url></logo-url><base-image-url></base-image-url><additional-config></additional-config><accounts><account id= "1100" markup= "" /></accounts></client-config><transaction id="1" order-no="1234abc" type-id="1" eua-id="-1" language="da" auto-capture="false" mode="0"><amount country-id="100" currency-id="208" currency="DKK" decimals="2" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><mobile country-id="100" operator-id="10000">288828610</mobile><email>DUMMY.test.demo@cellpointmobile.com</email><callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url><accept-url/><cancel-url/></transaction><session id=\'1\' type=\'1\' total-amount=\'200\'><amount country-id="100" currency-id="208" currency="DKK" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><status>4001</status></session><cards><card id="2" type-id="2" psp-id="18" min-length="16" max-length="16" cvc-length="3" state-id="1" payment-type="1" preferred="false" enabled="true" processor-type="1" installment="0" cvcmandatory="false" dcc="false" presentment-currency="false"><name>Dankort</name><prefixes><prefix><min>5019</min><max>5019</max></prefix><prefix><min>4571</min><max>4571</max></prefix></prefixes>Dankort</card></cards><wallets></wallets><apms></apms><aggregators></aggregators><offline></offline><vouchers></vouchers></root>', $sReplyBody);
    }

    public function testValidEmailAddressWithSingleQuote()
    {
        $pspID = Constants::iWIRE_CARD_PSP;
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 2, $pspID, true, 1)");

        $xml = $this->getInitDoc(10099, 1100, 208,null,200,null,"DUMMY's@cellpointmobile.com","DUMMY's@cellpointmobile.com","9766367227",null,null,"2.0","11");
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><client-config id="10099" account="1100" store-card="0" max-stored-cards="-1" auto-capture="false" enable-cvv="true" mode="0"><name>Test Client</name><callback-url></callback-url><accept-url></accept-url><cancel-url></cancel-url><app-url></app-url><css-url></css-url><logo-url></logo-url><base-image-url></base-image-url><additional-config></additional-config><accounts><account id= "1100" markup= "" /></accounts></client-config><transaction id="1" order-no="1234abc" type-id="1" eua-id="-1" language="da" auto-capture="false" mode="0"><amount country-id="100" currency-id="208" currency="DKK" decimals="2" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><mobile country-id="100" operator-id="10000">288828610</mobile><email>DUMMY\'s@cellpointmobile.com</email><callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url><accept-url/><cancel-url/></transaction><session id=\'1\' type=\'1\' total-amount=\'200\'><amount country-id="100" currency-id="208" currency="DKK" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><status>4001</status></session><cards><card id="2" type-id="2" psp-id="18" min-length="16" max-length="16" cvc-length="3" state-id="1" payment-type="1" preferred="false" enabled="true" processor-type="1" installment="0" cvcmandatory="false" dcc="false" presentment-currency="false"><name>Dankort</name><prefixes><prefix><min>5019</min><max>5019</max></prefix><prefix><min>4571</min><max>4571</max></prefix></prefixes>Dankort</card></cards><wallets></wallets><apms></apms><aggregators></aggregators><offline></offline><vouchers></vouchers></root>', $sReplyBody);
    }

    public function testValidEmailAddressStartWithUnderscore()
    {
        $pspID = Constants::iWIRE_CARD_PSP;
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 2, $pspID, true, 1)");

        $xml = $this->getInitDoc(10099, 1100, 208,null,200,null,"_DUMMY@cellpointmobile.com","_DUMMY@cellpointmobile.com","9766367227",null,null,"2.0","11");
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><client-config id="10099" account="1100" store-card="0" max-stored-cards="-1" auto-capture="false" enable-cvv="true" mode="0"><name>Test Client</name><callback-url></callback-url><accept-url></accept-url><cancel-url></cancel-url><app-url></app-url><css-url></css-url><logo-url></logo-url><base-image-url></base-image-url><additional-config></additional-config><accounts><account id= "1100" markup= "" /></accounts></client-config><transaction id="1" order-no="1234abc" type-id="1" eua-id="-1" language="da" auto-capture="false" mode="0"><amount country-id="100" currency-id="208" currency="DKK" decimals="2" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><mobile country-id="100" operator-id="10000">288828610</mobile><email>_DUMMY@cellpointmobile.com</email><callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url><accept-url/><cancel-url/></transaction><session id=\'1\' type=\'1\' total-amount=\'200\'><amount country-id="100" currency-id="208" currency="DKK" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><status>4001</status></session><cards><card id="2" type-id="2" psp-id="18" min-length="16" max-length="16" cvc-length="3" state-id="1" payment-type="1" preferred="false" enabled="true" processor-type="1" installment="0" cvcmandatory="false" dcc="false" presentment-currency="false"><name>Dankort</name><prefixes><prefix><min>5019</min><max>5019</max></prefix><prefix><min>4571</min><max>4571</max></prefix></prefixes>Dankort</card></cards><wallets></wallets><apms></apms><aggregators></aggregators><offline></offline><vouchers></vouchers></root>', $sReplyBody);
    }

    public function testValidEmailAddressWithAllowedSpecialChar()
    {
        $pspID = Constants::iWIRE_CARD_PSP;
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 2, $pspID, true, 1)");

        $xml = $this->getInitDoc(10099, 1100, 208,null,200,null,"_Dr.emai'l+test_@cellpointmobile.com","_Dr.emai'l+test_@cellpointmobile.com","9766367227",null,null,"2.0","11");
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><client-config id="10099" account="1100" store-card="0" max-stored-cards="-1" auto-capture="false" enable-cvv="true" mode="0"><name>Test Client</name><callback-url></callback-url><accept-url></accept-url><cancel-url></cancel-url><app-url></app-url><css-url></css-url><logo-url></logo-url><base-image-url></base-image-url><additional-config></additional-config><accounts><account id= "1100" markup= "" /></accounts></client-config><transaction id="1" order-no="1234abc" type-id="1" eua-id="-1" language="da" auto-capture="false" mode="0"><amount country-id="100" currency-id="208" currency="DKK" decimals="2" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><mobile country-id="100" operator-id="10000">288828610</mobile><email>_Dr.emai\'l+test_@cellpointmobile.com</email><callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url><accept-url/><cancel-url/></transaction><session id=\'1\' type=\'1\' total-amount=\'200\'><amount country-id="100" currency-id="208" currency="DKK" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><status>4001</status></session><cards><card id="2" type-id="2" psp-id="18" min-length="16" max-length="16" cvc-length="3" state-id="1" payment-type="1" preferred="false" enabled="true" processor-type="1" installment="0" cvcmandatory="false" dcc="false" presentment-currency="false"><name>Dankort</name><prefixes><prefix><min>5019</min><max>5019</max></prefix><prefix><min>4571</min><max>4571</max></prefix></prefixes>Dankort</card></cards><wallets></wallets><apms></apms><aggregators></aggregators><offline></offline><vouchers></vouchers></root>', $sReplyBody);
    }

    public function testValidEmailAddressGeneric()
    {
        $pspID = Constants::iWIRE_CARD_PSP;
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 2, $pspID, true, 1)");

        $xml = $this->getInitDoc(10099, 1100, 208,null,200,null,"DEMO@cellpointmobile.com","DEMO@cellpointmobile.com","9766367227",null,null,"2.0","11");
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><client-config id="10099" account="1100" store-card="0" max-stored-cards="-1" auto-capture="false" enable-cvv="true" mode="0"><name>Test Client</name><callback-url></callback-url><accept-url></accept-url><cancel-url></cancel-url><app-url></app-url><css-url></css-url><logo-url></logo-url><base-image-url></base-image-url><additional-config></additional-config><accounts><account id= "1100" markup= "" /></accounts></client-config><transaction id="1" order-no="1234abc" type-id="1" eua-id="-1" language="da" auto-capture="false" mode="0"><amount country-id="100" currency-id="208" currency="DKK" decimals="2" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><mobile country-id="100" operator-id="10000">288828610</mobile><email>DEMO@cellpointmobile.com</email><callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url><accept-url/><cancel-url/></transaction><session id=\'1\' type=\'1\' total-amount=\'200\'><amount country-id="100" currency-id="208" currency="DKK" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><status>4001</status></session><cards><card id="2" type-id="2" psp-id="18" min-length="16" max-length="16" cvc-length="3" state-id="1" payment-type="1" preferred="false" enabled="true" processor-type="1" installment="0" cvcmandatory="false" dcc="false" presentment-currency="false"><name>Dankort</name><prefixes><prefix><min>5019</min><max>5019</max></prefix><prefix><min>4571</min><max>4571</max></prefix></prefixes>Dankort</card></cards><wallets></wallets><apms></apms><aggregators></aggregators><offline></offline><vouchers></vouchers></root>', $sReplyBody);
    }

    public function testInvalidFXServiceTypeID()
    {
        $pspID = Constants::iWIRE_CARD_PSP;
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 2, $pspID, true, 1)");

        $xml = $this->getInitDoc(10099, 1100, 208,null,200,null,"abhinav.shaha@cellpointmobile.com","abhinav.shaha@cellpointmobile.com","9766367227",null,null,"2.0","13");
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(400, $iStatus);
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?><root><status code="57">Invalid service type id :13</status>', $sReplyBody);
    }

    public function testStoredFXServiceTypeID()
    {
        $pspID = Constants::iWIRE_CARD_PSP;

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 2, $pspID, true, 1)");

        $xml = $this->getInitDoc(10099, 1100, 208,null,200,null,"abhinav.shaha@cellpointmobile.com","abhinav.shaha@cellpointmobile.com","9766367227",null,null,"2.0","11");
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><client-config id="10099" account="1100" store-card="0" max-stored-cards="-1" auto-capture="false" enable-cvv="true" mode="0"><name>Test Client</name><callback-url></callback-url><accept-url></accept-url><cancel-url></cancel-url><app-url></app-url><css-url></css-url><logo-url></logo-url><base-image-url></base-image-url><additional-config></additional-config><accounts><account id= "1100" markup= "" /></accounts></client-config><transaction id="1" order-no="1234abc" type-id="1" eua-id="-1" language="da" auto-capture="false" mode="0"><amount country-id="100" currency-id="208" currency="DKK" decimals="2" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><mobile country-id="100" operator-id="10000">288828610</mobile><email>abhinav.shaha@cellpointmobile.com</email><callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url><accept-url/><cancel-url/></transaction><session id=\'1\' type=\'1\' total-amount=\'200\'><amount country-id="100" currency-id="208" currency="DKK" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><status>4001</status></session><cards><card id="2" type-id="2" psp-id="18" min-length="16" max-length="16" cvc-length="3" state-id="1" payment-type="1" preferred="false" enabled="true" processor-type="1" installment="0" cvcmandatory="false" dcc="false" presentment-currency="false"><name>Dankort</name><prefixes><prefix><min>5019</min><max>5019</max></prefix><prefix><min>4571</min><max>4571</max></prefix></prefixes>Dankort</card></cards><wallets></wallets><apms></apms><aggregators></aggregators><offline></offline><vouchers></vouchers></root>', $sReplyBody);

        $res =  $this->queryDB('SELECT fxservicetypeid from Log.Transaction_Tbl WHERE id = 1');
        $this->assertTrue(is_resource($res) );

        $fxservicetypeid = 0;
        while ($row = pg_fetch_assoc($res) )
        {
            $fxservicetypeid = (int)$row["fxservicetypeid"];
        }
        $this->assertEquals(11, $fxservicetypeid);
    }

    public function testSplitPaymentCombinations()
	{
		$pspID = 2;
        $this->queryDB("DELETE FROM CLIENT.STATICROUTELEVELCONFIGURATION");
		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");

		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid, psp_type) VALUES (10099, 2, $pspID, true, 1, 1)");

		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (3, 10099, 30, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 30, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid, psp_type) VALUES (10099, 32, 30, true, 1, 4)");

		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (4, 10099, 51, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 51, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid, psp_type) VALUES (10099, 73, 51, true, 1, 7)");

		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");

        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('isnewcardconfig', 'true', 10099, 'client', 0);");
        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('sessiontype', 2, true, 10099, 'client', 0);");

        //split payment combinations
        $this->queryDB("INSERT INTO client.split_configuration_tbl (id,client_id, name, is_one_step_auth, enabled) VALUES (1,10099, 'Card+Card', false, true);");
        $this->queryDB("INSERT INTO client.split_combination_tbl (id,split_config_id, payment_type, sequence_no) VALUES (1, 1, 1, 1);");
        $this->queryDB("INSERT INTO client.split_combination_tbl (id,split_config_id, payment_type, sequence_no) VALUES (2, 1, 1, 2);");


        $xml = $this->getInitDoc(10099, 1100);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();
		$this->assertEquals(200, $iStatus);
		$this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?><root><client-config id="10099" account="1100" store-card="0" max-stored-cards="-1" auto-capture="false" enable-cvv="true" mode="0"><name>Test Client</name><callback-url></callback-url><accept-url></accept-url><cancel-url></cancel-url><app-url></app-url><css-url></css-url><logo-url></logo-url><base-image-url></base-image-url><additional-config></additional-config><accounts><account id= "1100" markup= "" /></accounts></client-config><split_payment><configuration><applicable_combinations><combination><payment_type><id>1</id><sequence>1</sequence></payment_type><payment_type><id>1</id><sequence>2</sequence></payment_type><is_one_step_authorization>false</is_one_step_authorization></combination></applicable_combinations></configuration></split_payment><transaction id="1" order-no="1234abc" type-id="1" eua-id="-1" language="da" auto-capture="false" mode="0"><amount country-id="100" currency-id="208" currency="DKK" decimals="2" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><mobile country-id="100" operator-id="10000">288828610</mobile><email>jona@oismail.com</email><callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url><accept-url/><cancel-url/></transaction><session id=\'1\' type=\'1\' total-amount=\'200\'><amount country-id="100" currency-id="208" currency="DKK" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><status>4001</status></session><cards><card id="2" type-id="2" psp-id="2" min-length="16" max-length="16" cvc-length="3" state-id="1" payment-type="1" preferred="false" enabled="true" processor-type="1" installment="0" cvcmandatory="false" dcc="false" presentment-currency="false"><name>Dankort</name><prefixes><prefix><min>5019</min><max>5019</max></prefix><prefix><min>4571</min><max>4571</max></prefix></prefixes>Dankort</card></cards><wallets></wallets><apms></apms><aggregators></aggregators><offline></offline><vouchers></vouchers></root>', $sReplyBody);
	}

	public function testVoucherNodes()
	{
		$pspID = 71;
        $this->queryDB("DELETE FROM CLIENT.STATICROUTELEVELCONFIGURATION");
		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");

		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid, psp_type) VALUES (10099, 26, $pspID, true, 1, 11)");

		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
		$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid) VALUES (1001001, 100, 10099, 1100, 1,  $pspID, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE,10)");
		$this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('isnewcardconfig', 'true', 10099, 'client', 0);");

		$xml = $this->getInitDoc(10099, 1100);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();
		$this->assertEquals(200, $iStatus);
		$this->assertStringContainsString('<vouchers><card id="26" type-id="26" psp-id="71" min-length="-1" max-length="-1" cvc-length="-1" state-id="1" payment-type="2" preferred="false" enabled="true" processor-type="11" installment="0" cvcmandatory="false" dcc="false" presentment-currency="false"><name>TravelFund</name><prefixes><prefix><min>0</min><max>0</max></prefix></prefixes>TravelFund</card></vouchers>', $sReplyBody);
	}


	public function testVoucherNodesWithFetchBalance()
	{
		$authenticateURL = $this->_aMPOINT_CONN_INFO['protocol'] . '://' . $this->_aMPOINT_CONN_INFO['host']. '/_test/simulators/mprofile/ciam/get-customer-profile.php';
		$pspID = 71;
        $this->queryDB("DELETE FROM CLIENT.STATICROUTELEVELCONFIGURATION");
		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 2, '".$authenticateURL."')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");

		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid, psp_type) VALUES (10099, 26, $pspID, true, 1, 11)");

		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (2, 10099, 2, '4216311')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 2, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid, psp_type) VALUES (10099, 2, 2, true, 1, 1)");
        $this->queryDB("INSERT INTO client.services_tbl (clientid, legacy_flow_enabled) VALUES(10099, true);");

		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
		$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid) VALUES (1001001, 100, 10099, 1100, 1,  $pspID, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE,10)");
		$this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('isnewcardconfig', 'true', 10099, 'client', 0);");

		$this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('SSO_PREFERENCE', 'STRICT', 10099, 'client', 0)");
		$this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('autoFetchBalance', 'true', 10099, 'client', 0)");
		$this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('fetchBalanceUserType', '{\"1\":2}', 10099, 'client', 0)");
		$this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('fetchBalancePaymentMethods', '{\"1\":26}', 10099, 'client', 0)");


		$xml = $this->getInitDoc(10099, 1100, null, 'success', 200,null,'jona@oismail.com',null,'288828610','member','STRICT','2.0',0,100,'');

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
		$this->assertStringContainsString('<vouchers><card id="26" type-id="26" psp-id="71" min-length="-1" max-length="-1" cvc-length="-1" state-id="1" payment-type="2" preferred="false" enabled="true" processor-type="11" installment="0" cvcmandatory="false" dcc="false" presentment-currency="false"><name>TravelFund</name><fetch-balance>true</fetch-balance><prefixes><prefix><min>0</min><max>0</max></prefix></prefixes>TravelFund</card></vouchers>', $sReplyBody);

		// Assertion for FOP which done has fetch-balance node.
		$this->assertStringContainsString('<card id="2" type-id="2" psp-id="2" min-length="16" max-length="16" cvc-length="3" state-id="1" payment-type="1" preferred="false" enabled="true" processor-type="1" installment="0" cvcmandatory="false" dcc="false" presentment-currency="false"><name>Dankort</name><prefixes><prefix><min>5019</min><max>5019</max></prefix><prefix><min>4571</min><max>4571</max></prefix></prefixes>Dankort</card>', $sReplyBody);
	}

    public function testAirlineDataWithEmailHavingUnderscoreBefore()
    {

        $pspID = Constants::iWIRE_CARD_PSP;
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10078, 1, 640, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10078, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (100780, 10078)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10078, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10078, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100780, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10078, 2, $pspID, true, 1)");

        $orderXml = '<orders> <line-item> <product order-ref="abc123" sku="product-ticket"> <type>100</type> <name>ONE WAY</name> <description>MNL-CEB</description> <airline-data> <profiles> <profile> <seq>2</seq> <title>Mr</title> <first-name>dan</first-name> <last-name>dan</last-name> <type>ADT</type> <contact-info> <email>DAN_@DAN.com</email> <mobile country-id="640">9187231231</mobile> </contact-info> <additional-data> <param name="loyality_id">345rtyu</param> </additional-data> </profile> </profiles> <billing-summary> <fare-detail> <fare> <profile-seq>2</profile-seq> <description>adult</description> <currency>PHP</currency> <amount>60</amount> <product-code>ABF</product-code> <product-category>FARE</product-category> <product-item>Base fare for adult</product-item> </fare> </fare-detail> <add-ons> <add-on> <profile-seq>2</profile-seq> <trip-tag>2</trip-tag> <trip-seq>2</trip-seq> <description>adult</description> <currency>PHP</currency> <amount>60</amount> <product-code>ABF</product-code> <product-category>FARE</product-category> <product-item>Base fare for adult</product-item> </add-on> </add-ons> </billing-summary> <trips> <trip tag="1" seq="1"> <origin external-id="MNL" country-id="640" time-zone="+08:00" terminal="1">Ninoy Aquino International Airport</origin> <destination external-id="CEB" country-id="640" time-zone="+08:00" terminal="2">Mactan Cebu International Airport</destination> <departure-time>2021-03-07T19:35:00Z</departure-time> <arrival-time>2021-03-07T21:05:00Z</arrival-time> <booking-class>Z</booking-class> <service-level>Economy</service-level> <transportation code="5J" number="1"> <carriers> <carrier code="5J" type-id="Aircraft Boeing-737-9"> <number>563</number> </carrier> </carriers> </transportation> <additional-data> <param name="fare_basis">we543s3</param> </additional-data> </trip> </trips> </airline-data> </product> <amount>125056</amount> <quantity>1</quantity> <additional-data> <param name="deviceFingerPrint">hVdMGC9x3eJsGssbGZFB9d4Q7hdP</param> </additional-data> </line-item> </orders>';

        $xml = $this->getInitDoc(10078, 100780, 608,null,100000,null,"DAN_@DAN.com","DAN_@DAN.com","9766367227",null,null,"2.0","0", 640, $orderXml);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertStringContainsString('<orders><line-item><product order-ref="abc123" sku="product-ticket"><type>100</type><name>ONE WAY</name><description>MNL-CEB</description><airline-data><profiles><profile><seq>2</seq><title>Mr</title><first-name>dan</first-name><last-name>dan</last-name><type>ADT</type><contact-info><email>DAN_@DAN.com</email><mobile country-id="640">9187231231</mobile></contact-info><additional-data><param name="loyality_id">345rtyu</param></additional-data></profile></profiles><billing-summary><fare-detail><fare><profile-seq>2</profile-seq><description>adult</description><currency>PHP</currency><amount>60</amount><product-code>ABF</product-code><product-category>FARE</product-category><product-item>Base fare for adult</product-item></fare></fare-detail><add-ons><add-on><profile-seq>2</profile-seq><trip-tag>2</trip-tag><trip-seq>2</trip-seq><description>adult</description><currency>PHP</currency><amount>60</amount><product-code>ABF</product-code><product-category>FARE</product-category><product-item>Base fare for adult</product-item></add-on></add-ons></billing-summary><trips><trip tag="1" seq="1"><origin external-id="MNL" country-id="640" time-zone="+08:00" terminal="1">Ninoy Aquino International Airport</origin><destination external-id="CEB" country-id="640" time-zone="+08:00" terminal="2">Mactan Cebu International Airport</destination><departure-time>2021-03-07T19:35:00Z</departure-time><arrival-time>2021-03-07T21:05:00Z</arrival-time><booking-class>Z</booking-class><service-level>Economy</service-level><transportation code="5J" number="1"><carriers><carrier code="5J" type-id="Aircraft Boeing-737-9"><number>563</number></carrier></carriers></transportation><additional-data><param name="fare_basis">we543s3</param></additional-data></trip></trips></airline-data></product><amount>125056</amount><quantity>1</quantity><additional-data><param name="deviceFingerPrint">hVdMGC9x3eJsGssbGZFB9d4Q7hdP</param></additional-data></line-item></orders>', $sReplyBody);

        //Check passenger_tbl entry
        $res =  $this->queryDB("SELECT seq from Log.Order_Tbl ot join Log.passenger_tbl pt on ot.id = pt.order_id WHERE ot.orderref='abc123'");

        $this->assertTrue(is_resource($res) );

        while ($row = pg_fetch_assoc($res) )
        {
            $seq = (int)$row["seq"];
        }
        $this->assertEquals(2, $seq);

        //Check billing_summary_tbl entry
        $res =  $this->queryDB("SELECT profile_seq, trip_tag, trip_seq, product_code, product_category, product_item from Log.Order_Tbl ot join Log.billing_summary_tbl bst on ot.id = bst.order_id WHERE ot.orderref='abc123' and bst.bill_type='Fare'");

        $this->assertTrue(is_resource($res) );

        while ($row = pg_fetch_assoc($res) )
        {
            $profileSeq = (int) $row['profile_seq'];
            $tripTag = (int) $row['trip_tag'];
            $tripSeq = (int) $row['trip_seq'];
            $productCode = $row["product_code"];
            $productCat = $row['product_category'];
            $productItem = $row['product_item'];

        }
        $this->assertEquals(2, $profileSeq);
        $this->assertEquals(0, $tripTag);
        $this->assertEquals(0, $tripSeq);
        $this->assertEquals('ABF', $productCode);
        $this->assertEquals('FARE', $productCat);
        $this->assertEquals('Base fare for adult', $productItem);

        //Check billing_summary_tbl entry
        $res =  $this->queryDB("SELECT profile_seq, trip_tag, trip_seq, product_code, product_category, product_item from Log.Order_Tbl ot join Log.billing_summary_tbl bst on ot.id = bst.order_id WHERE ot.orderref='abc123' and bst.bill_type='Add-on'");

        $this->assertTrue(is_resource($res) );

        while ($row = pg_fetch_assoc($res) )
        {
            $profileSeq = (int) $row['profile_seq'];
            $tripTag = (int) $row['trip_tag'];
            $tripSeq = (int) $row['trip_seq'];
            $productCode = $row["product_code"];
            $productCat = $row['product_category'];
            $productItem = $row['product_item'];
        }
        $this->assertEquals(2, $profileSeq);
        $this->assertEquals(2, $tripTag);
        $this->assertEquals(2, $tripSeq);
        $this->assertEquals('ABF', $productCode);
        $this->assertEquals('FARE', $productCat);
        $this->assertEquals('Base fare for adult', $productItem);

        //Check flight_tbl entry
        $res =  $this->queryDB("SELECT op_flight_number, arrival_timezone, mkt_airline_code, departure_city, arrival_city, aircraft_type, arrival_terminal, departure_terminal from Log.Order_Tbl ot join Log.flight_tbl ft on ot.id = ft.order_id WHERE ot.orderref='abc123'");

        $this->assertTrue(is_resource($res) );

        while ($row = pg_fetch_assoc($res) )
        {
            $opFlightNumber = $row["op_flight_number"];
            $arrivalTz = $row["arrival_timezone"];
            $mktAirlineCode = $row["mkt_airline_code"];
            $deptCity = $row["departure_city"];
            $arrCity = $row["arrival_city"];
            $aircraftType = $row["aircraft_type"];
            $arrivalTerminal = $row["arrival_terminal"];
            $deptTerminal = $row["departure_terminal"];

        }
        $this->assertEquals('1', $opFlightNumber);
        $this->assertEquals('+08:00', $arrivalTz);
        $this->assertEquals('5J', $mktAirlineCode);
        $this->assertEquals('Ninoy Aquino International Airport', $deptCity);
        $this->assertEquals('Mactan Cebu International Airport', $arrCity);
        $this->assertEquals('Aircraft Boeing-737-9', $aircraftType);
        $this->assertEquals('2', $arrivalTerminal);
        $this->assertEquals('1', $deptTerminal);

        //Check order_tbl entry
        $res =  $this->queryDB("SELECT orderref, type from Log.Order_Tbl ot WHERE ot.orderref='abc123'");

        $this->assertTrue(is_resource($res) );

        while ($row = pg_fetch_assoc($res) )
        {
            $orderref = $row["orderref"];
            $type = $row["type"];
        }
        $this->assertEquals('abc123', $orderref);
        $this->assertEquals(100, $type);

    }

    public function testAirlineDataWithEmailHavingDot()
    {
        $pspID = Constants::iWIRE_CARD_PSP;
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10078, 1, 640, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10078, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (100780, 10078)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10078, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10078, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100780, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10078, 2, $pspID, true, 1)");

        $orderXml = '<orders> <line-item> <product order-ref="abc123" sku="product-ticket"> <type>100</type> <name>ONE WAY</name> <description>MNL-CEB</description> <airline-data> <profiles> <profile> <seq>2</seq> <title>Mr</title> <first-name>dan</first-name> <last-name>dan</last-name> <type>ADT</type> <contact-info> <email>DAN.TEST@DAN.com</email> <mobile country-id="640">9187231231</mobile> </contact-info> <additional-data> <param name="loyality_id">345rtyu</param> </additional-data> </profile> </profiles> <billing-summary> <fare-detail> <fare> <profile-seq>2</profile-seq> <description>adult</description> <currency>PHP</currency> <amount>60</amount> <product-code>ABF</product-code> <product-category>FARE</product-category> <product-item>Base fare for adult</product-item> </fare> </fare-detail> <add-ons> <add-on> <profile-seq>2</profile-seq> <trip-tag>2</trip-tag> <trip-seq>2</trip-seq> <description>adult</description> <currency>PHP</currency> <amount>60</amount> <product-code>ABF</product-code> <product-category>FARE</product-category> <product-item>Base fare for adult</product-item> </add-on> </add-ons> </billing-summary> <trips> <trip tag="1" seq="1"> <origin external-id="MNL" country-id="640" time-zone="+08:00" terminal="1">Ninoy Aquino International Airport</origin> <destination external-id="CEB" country-id="640" time-zone="+08:00" terminal="2">Mactan Cebu International Airport</destination> <departure-time>2021-03-07T19:35:00Z</departure-time> <arrival-time>2021-03-07T21:05:00Z</arrival-time> <booking-class>Z</booking-class> <service-level>Economy</service-level> <transportation code="5J" number="1"> <carriers> <carrier code="5J" type-id="Aircraft Boeing-737-9"> <number>563</number> </carrier> </carriers> </transportation> <additional-data> <param name="fare_basis">we543s3</param> </additional-data> </trip> </trips> </airline-data> </product> <amount>125056</amount> <quantity>1</quantity> <additional-data> <param name="deviceFingerPrint">hVdMGC9x3eJsGssbGZFB9d4Q7hdP</param> </additional-data> </line-item> </orders>';

        $xml = $this->getInitDoc(10078, 100780, 608,null,100000,null,"DAN.TEST@DAN.com","DAN.TEST@DAN.com","9766367227",null,null,"2.0","0", 640, $orderXml);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertStringContainsString('<orders><line-item><product order-ref="abc123" sku="product-ticket"><type>100</type><name>ONE WAY</name><description>MNL-CEB</description><airline-data><profiles><profile><seq>2</seq><title>Mr</title><first-name>dan</first-name><last-name>dan</last-name><type>ADT</type><contact-info><email>DAN.TEST@DAN.com</email><mobile country-id="640">9187231231</mobile></contact-info><additional-data><param name="loyality_id">345rtyu</param></additional-data></profile></profiles><billing-summary><fare-detail><fare><profile-seq>2</profile-seq><description>adult</description><currency>PHP</currency><amount>60</amount><product-code>ABF</product-code><product-category>FARE</product-category><product-item>Base fare for adult</product-item></fare></fare-detail><add-ons><add-on><profile-seq>2</profile-seq><trip-tag>2</trip-tag><trip-seq>2</trip-seq><description>adult</description><currency>PHP</currency><amount>60</amount><product-code>ABF</product-code><product-category>FARE</product-category><product-item>Base fare for adult</product-item></add-on></add-ons></billing-summary><trips><trip tag="1" seq="1"><origin external-id="MNL" country-id="640" time-zone="+08:00" terminal="1">Ninoy Aquino International Airport</origin><destination external-id="CEB" country-id="640" time-zone="+08:00" terminal="2">Mactan Cebu International Airport</destination><departure-time>2021-03-07T19:35:00Z</departure-time><arrival-time>2021-03-07T21:05:00Z</arrival-time><booking-class>Z</booking-class><service-level>Economy</service-level><transportation code="5J" number="1"><carriers><carrier code="5J" type-id="Aircraft Boeing-737-9"><number>563</number></carrier></carriers></transportation><additional-data><param name="fare_basis">we543s3</param></additional-data></trip></trips></airline-data></product><amount>125056</amount><quantity>1</quantity><additional-data><param name="deviceFingerPrint">hVdMGC9x3eJsGssbGZFB9d4Q7hdP</param></additional-data></line-item></orders>', $sReplyBody);

        //Check passenger_tbl entry
        $res =  $this->queryDB("SELECT seq from Log.Order_Tbl ot join Log.passenger_tbl pt on ot.id = pt.order_id WHERE ot.orderref='abc123'");

        $this->assertTrue(is_resource($res) );

        while ($row = pg_fetch_assoc($res) )
        {
            $seq = (int)$row["seq"];
        }
        $this->assertEquals(2, $seq);

        //Check billing_summary_tbl entry
        $res =  $this->queryDB("SELECT profile_seq, trip_tag, trip_seq, product_code, product_category, product_item from Log.Order_Tbl ot join Log.billing_summary_tbl bst on ot.id = bst.order_id WHERE ot.orderref='abc123' and bst.bill_type='Fare'");

        $this->assertTrue(is_resource($res) );

        while ($row = pg_fetch_assoc($res) )
        {
            $profileSeq = (int) $row['profile_seq'];
            $tripTag = (int) $row['trip_tag'];
            $tripSeq = (int) $row['trip_seq'];
            $productCode = $row["product_code"];
            $productCat = $row['product_category'];
            $productItem = $row['product_item'];

        }
        $this->assertEquals(2, $profileSeq);
        $this->assertEquals(0, $tripTag);
        $this->assertEquals(0, $tripSeq);
        $this->assertEquals('ABF', $productCode);
        $this->assertEquals('FARE', $productCat);
        $this->assertEquals('Base fare for adult', $productItem);

        //Check billing_summary_tbl entry
        $res =  $this->queryDB("SELECT profile_seq, trip_tag, trip_seq, product_code, product_category, product_item from Log.Order_Tbl ot join Log.billing_summary_tbl bst on ot.id = bst.order_id WHERE ot.orderref='abc123' and bst.bill_type='Add-on'");

        $this->assertTrue(is_resource($res) );

        while ($row = pg_fetch_assoc($res) )
        {
            $profileSeq = (int) $row['profile_seq'];
            $tripTag = (int) $row['trip_tag'];
            $tripSeq = (int) $row['trip_seq'];
            $productCode = $row["product_code"];
            $productCat = $row['product_category'];
            $productItem = $row['product_item'];
        }
        $this->assertEquals(2, $profileSeq);
        $this->assertEquals(2, $tripTag);
        $this->assertEquals(2, $tripSeq);
        $this->assertEquals('ABF', $productCode);
        $this->assertEquals('FARE', $productCat);
        $this->assertEquals('Base fare for adult', $productItem);

        //Check flight_tbl entry
        $res =  $this->queryDB("SELECT op_flight_number, arrival_timezone, mkt_airline_code, departure_city, arrival_city, aircraft_type, arrival_terminal, departure_terminal from Log.Order_Tbl ot join Log.flight_tbl ft on ot.id = ft.order_id WHERE ot.orderref='abc123'");

        $this->assertTrue(is_resource($res) );

        while ($row = pg_fetch_assoc($res) )
        {
            $opFlightNumber = $row["op_flight_number"];
            $arrivalTz = $row["arrival_timezone"];
            $mktAirlineCode = $row["mkt_airline_code"];
            $deptCity = $row["departure_city"];
            $arrCity = $row["arrival_city"];
            $aircraftType = $row["aircraft_type"];
            $arrivalTerminal = $row["arrival_terminal"];
            $deptTerminal = $row["departure_terminal"];

        }
        $this->assertEquals('1', $opFlightNumber);
        $this->assertEquals('+08:00', $arrivalTz);
        $this->assertEquals('5J', $mktAirlineCode);
        $this->assertEquals('Ninoy Aquino International Airport', $deptCity);
        $this->assertEquals('Mactan Cebu International Airport', $arrCity);
        $this->assertEquals('Aircraft Boeing-737-9', $aircraftType);
        $this->assertEquals('2', $arrivalTerminal);
        $this->assertEquals('1', $deptTerminal);

        //Check order_tbl entry
        $res =  $this->queryDB("SELECT orderref, type from Log.Order_Tbl ot WHERE ot.orderref='abc123'");

        $this->assertTrue(is_resource($res) );

        while ($row = pg_fetch_assoc($res) )
        {
            $orderref = $row["orderref"];
            $type = $row["type"];
        }
        $this->assertEquals('abc123', $orderref);
        $this->assertEquals(100, $type);

    }

    public function testAirlineData()
    {

        /**   AIR LINE DATA XML
        <?xml version="1.0" encoding="UTF-8"?>
        <root>
        <initialize-payment account="100770" client-id="10077">
        <transaction order-no="TESTABHINAVV1" type-id="1">
        <amount country-id="640">100000</amount>
        <hmac></hmac>
        <orders>
        <line-item>
        <product sku="product-ticket">
        <name>ONE WAY</name>
        <description>MNL-CEB</description>
        <airline-data>
        <profiles>
        <profile>
        <seq>2</seq>
        <title>Mr</title>
        <first-name>dan</first-name>
        <last-name>dan</last-name>
        <type>ADT</type>
        <contact-info>
        <email>dan@dan.com</email>
        <mobile country-id="640">9187231231</mobile>
        </contact-info>
        <additional-data>
        <param name="loyality_id">345rtyu</param>
        </additional-data>
        </profile>
        </profiles>
        <billing-summary>
        <fare-detail>
        <fare>
        <type>1</type>
        <description>adult</description>
        <currency>PHP</currency>
        <amount>60</amount>
        <product-code>ABF</product-code>
        <product-category>FARE</product-category>
        <product-item>Base fare for adult</product-item>
        </fare>
        </fare-detail>
        <add-ons>
        <add-on>
        <profile-seq>1</profile-seq>
        <trip-tag>2</trip-tag>
        <trip-seq>2</trip-seq>
        <description>adult</description>
        <currency>PHP</currency>
        <amount>60</amount>
        <product-code>ABF</product-code>
        <product-category>FARE</product-category>
        <product-item>Base fare for adult</product-item>
        </add-on>
        </add-ons>
        </billing-summary>
        <trips>
        <trip tag="1" seq="1">
        <origin external-id="MNL" country-id="640" time-zone="+08:00" terminal="1">Ninoy Aquino International Airport</origin>
        <destination external-id="CEB" country-id="640" time-zone="+08:00" terminal="2">Mactan Cebu International Airport</destination>
        <departure-time>2021-03-07T19:35:00Z</departure-time>
        <arrival-time>2021-03-07T21:05:00Z</arrival-time>
        <booking-class>Z</booking-class>
        <service-level>Economy</service-level>
        <transportation code="5J" number="1">
        <carriers>
        <carrier code="5J" type-id="Aircraft Boeing-737-9">
        <number>563</number>
        </carrier>
        </carriers>
        </transportation>
        <additional-data>
        <param name="fare_basis">we543s3</param>
        </additional-data>
        </trip>
        </trips>
        </airline-data>
        </product>
        <amount>125056</amount>
        <quantity>1</quantity>
        <additional-data>
        <param name="deviceFingerPrint">hVdMGC9x3eJsGssbGZFB9d4Q7hdP</param>
        </additional-data>
        </line-item>
        </orders>
        </transaction>
        <client-info language="en" sdk-version="2.0.0" version="2.0.0" platform="HTML5">
        <mobile operator-id="64000" country-id="640">9766367227</mobile>
        <email>abhinav.shaha@cellpointmobile.com</email>
        <customer-ref>abhinav.shaha@cellpointmobile.com</customer-ref>
        </client-info>
        </initialize-payment>
        </root>*/

        $pspID = Constants::iWIRE_CARD_PSP;
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10078, 1, 640, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10078, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (100780, 10078)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10078, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10078, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100780, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10078, 2, $pspID, true, 1)");

        $orderXml = '<orders> <line-item> <product order-ref="abc123" sku="product-ticket"> <type>100</type> <name>ONE WAY</name> <description>MNL-CEB</description> <airline-data> <profiles> <profile> <seq>2</seq> <title>Mr</title> <first-name>dan</first-name> <last-name>dan</last-name> <type>ADT</type> <contact-info> <email>dan@dan.com</email> <mobile country-id="640">9187231231</mobile> </contact-info> <additional-data> <param name="loyality_id">345rtyu</param> </additional-data> </profile> </profiles> <billing-summary> <fare-detail> <fare> <profile-seq>2</profile-seq> <description>adult</description> <currency>PHP</currency> <amount>60</amount> <product-code>ABF</product-code> <product-category>FARE</product-category> <product-item>Base fare for adult</product-item> </fare> </fare-detail> <add-ons> <add-on> <profile-seq>2</profile-seq> <trip-tag>2</trip-tag> <trip-seq>2</trip-seq> <description>adult</description> <currency>PHP</currency> <amount>60</amount> <product-code>ABF</product-code> <product-category>FARE</product-category> <product-item>Base fare for adult</product-item> </add-on> </add-ons> </billing-summary> <trips> <trip tag="1" seq="1"> <origin external-id="MNL" country-id="640" time-zone="+08:00" terminal="1">Ninoy Aquino International Airport</origin> <destination external-id="CEB" country-id="640" time-zone="+08:00" terminal="2">Mactan Cebu International Airport</destination> <departure-time>2021-03-07T19:35:00Z</departure-time> <arrival-time>2021-03-07T21:05:00Z</arrival-time> <booking-class>Z</booking-class> <service-level>Economy</service-level> <transportation code="5J" number="1"> <carriers> <carrier code="5J" type-id="Aircraft Boeing-737-9"> <number>563</number> </carrier> </carriers> </transportation> <additional-data> <param name="fare_basis">we543s3</param> </additional-data> </trip> </trips> </airline-data> </product> <amount>125056</amount> <quantity>1</quantity> <additional-data> <param name="deviceFingerPrint">hVdMGC9x3eJsGssbGZFB9d4Q7hdP</param> </additional-data> </line-item> </orders>';

        $xml = $this->getInitDoc(10078, 100780, 608,null,100000,null,"abhinav.shaha@cellpointmobile.com","abhinav.shaha@cellpointmobile.com","9766367227",null,null,"2.0","0", 640, $orderXml);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertStringContainsString('<orders><line-item><product order-ref="abc123" sku="product-ticket"><type>100</type><name>ONE WAY</name><description>MNL-CEB</description><airline-data><profiles><profile><seq>2</seq><title>Mr</title><first-name>dan</first-name><last-name>dan</last-name><type>ADT</type><contact-info><email>dan@dan.com</email><mobile country-id="640">9187231231</mobile></contact-info><additional-data><param name="loyality_id">345rtyu</param></additional-data></profile></profiles><billing-summary><fare-detail><fare><profile-seq>2</profile-seq><description>adult</description><currency>PHP</currency><amount>60</amount><product-code>ABF</product-code><product-category>FARE</product-category><product-item>Base fare for adult</product-item></fare></fare-detail><add-ons><add-on><profile-seq>2</profile-seq><trip-tag>2</trip-tag><trip-seq>2</trip-seq><description>adult</description><currency>PHP</currency><amount>60</amount><product-code>ABF</product-code><product-category>FARE</product-category><product-item>Base fare for adult</product-item></add-on></add-ons></billing-summary><trips><trip tag="1" seq="1"><origin external-id="MNL" country-id="640" time-zone="+08:00" terminal="1">Ninoy Aquino International Airport</origin><destination external-id="CEB" country-id="640" time-zone="+08:00" terminal="2">Mactan Cebu International Airport</destination><departure-time>2021-03-07T19:35:00Z</departure-time><arrival-time>2021-03-07T21:05:00Z</arrival-time><booking-class>Z</booking-class><service-level>Economy</service-level><transportation code="5J" number="1"><carriers><carrier code="5J" type-id="Aircraft Boeing-737-9"><number>563</number></carrier></carriers></transportation><additional-data><param name="fare_basis">we543s3</param></additional-data></trip></trips></airline-data></product><amount>125056</amount><quantity>1</quantity><additional-data><param name="deviceFingerPrint">hVdMGC9x3eJsGssbGZFB9d4Q7hdP</param></additional-data></line-item></orders>', $sReplyBody);

        //Check passenger_tbl entry
        $res =  $this->queryDB("SELECT seq from Log.Order_Tbl ot join Log.passenger_tbl pt on ot.id = pt.order_id WHERE ot.orderref='abc123'");

        $this->assertTrue(is_resource($res) );

        while ($row = pg_fetch_assoc($res) )
        {
            $seq = (int)$row["seq"];
        }
        $this->assertEquals(2, $seq);

        //Check billing_summary_tbl entry
        $res =  $this->queryDB("SELECT profile_seq, trip_tag, trip_seq, product_code, product_category, product_item from Log.Order_Tbl ot join Log.billing_summary_tbl bst on ot.id = bst.order_id WHERE ot.orderref='abc123' and bst.bill_type='Fare'");

        $this->assertTrue(is_resource($res) );

        while ($row = pg_fetch_assoc($res) )
        {
            $profileSeq = (int) $row['profile_seq'];
            $tripTag = (int) $row['trip_tag'];
            $tripSeq = (int) $row['trip_seq'];
            $productCode = $row["product_code"];
            $productCat = $row['product_category'];
            $productItem = $row['product_item'];

        }
        $this->assertEquals(2, $profileSeq);
        $this->assertEquals(0, $tripTag);
        $this->assertEquals(0, $tripSeq);
        $this->assertEquals('ABF', $productCode);
        $this->assertEquals('FARE', $productCat);
        $this->assertEquals('Base fare for adult', $productItem);

        //Check billing_summary_tbl entry
        $res =  $this->queryDB("SELECT profile_seq, trip_tag, trip_seq, product_code, product_category, product_item from Log.Order_Tbl ot join Log.billing_summary_tbl bst on ot.id = bst.order_id WHERE ot.orderref='abc123' and bst.bill_type='Add-on'");

        $this->assertTrue(is_resource($res) );

        while ($row = pg_fetch_assoc($res) )
        {
            $profileSeq = (int) $row['profile_seq'];
            $tripTag = (int) $row['trip_tag'];
            $tripSeq = (int) $row['trip_seq'];
            $productCode = $row["product_code"];
            $productCat = $row['product_category'];
            $productItem = $row['product_item'];
        }
        $this->assertEquals(2, $profileSeq);
        $this->assertEquals(2, $tripTag);
        $this->assertEquals(2, $tripSeq);
        $this->assertEquals('ABF', $productCode);
        $this->assertEquals('FARE', $productCat);
        $this->assertEquals('Base fare for adult', $productItem);

        //Check flight_tbl entry
        $res =  $this->queryDB("SELECT op_flight_number, arrival_timezone, mkt_airline_code, departure_city, arrival_city, aircraft_type, arrival_terminal, departure_terminal from Log.Order_Tbl ot join Log.flight_tbl ft on ot.id = ft.order_id WHERE ot.orderref='abc123'");

        $this->assertTrue(is_resource($res) );

        while ($row = pg_fetch_assoc($res) )
        {
            $opFlightNumber = $row["op_flight_number"];
            $arrivalTz = $row["arrival_timezone"];
            $mktAirlineCode = $row["mkt_airline_code"];
            $deptCity = $row["departure_city"];
            $arrCity = $row["arrival_city"];
            $aircraftType = $row["aircraft_type"];
            $arrivalTerminal = $row["arrival_terminal"];
            $deptTerminal = $row["departure_terminal"];

        }
        $this->assertEquals('1', $opFlightNumber);
        $this->assertEquals('+08:00', $arrivalTz);
        $this->assertEquals('5J', $mktAirlineCode);
        $this->assertEquals('Ninoy Aquino International Airport', $deptCity);
        $this->assertEquals('Mactan Cebu International Airport', $arrCity);
        $this->assertEquals('Aircraft Boeing-737-9', $aircraftType);
        $this->assertEquals('2', $arrivalTerminal);
        $this->assertEquals('1', $deptTerminal);

        //Check order_tbl entry
        $res =  $this->queryDB("SELECT orderref, type from Log.Order_Tbl ot WHERE ot.orderref='abc123'");

        $this->assertTrue(is_resource($res) );

        while ($row = pg_fetch_assoc($res) )
        {
            $orderref = $row["orderref"];
            $type = $row["type"];
        }
        $this->assertEquals('abc123', $orderref);
        $this->assertEquals(100, $type);

    }

    public function testAirlineDataWithoutBillingSummary()
    {

        $pspID = Constants::iWIRE_CARD_PSP;
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10078, 1, 640, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10078, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (100780, 10078)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10078, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10078, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (100780, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10078, 2, $pspID, true, 1)");

        $orderXml = '<orders> <line-item> <product sku="product-ticket"><type>100</type> <name>ONE WAY</name> <description>MNL-CEB</description> <airline-data> <profiles> <profile> <seq>2</seq> <title>Mr</title> <first-name>dan</first-name> <last-name>dan</last-name> <type>ADT</type> <contact-info> <email>dan@dan.com</email> <mobile country-id="640">9187231231</mobile> </contact-info> <additional-data> <param name="loyality_id">345rtyu</param> </additional-data> </profile> </profiles> <trips> <trip tag="1" seq="1"> <origin external-id="MNL" country-id="640" time-zone="+08:00" terminal="1">Ninoy Aquino International Airport</origin> <destination external-id="CEB" country-id="640" time-zone="+08:00" terminal="2">Mactan Cebu International Airport</destination> <departure-time>2021-03-07T19:35:00Z</departure-time> <arrival-time>2021-03-07T21:05:00Z</arrival-time> <booking-class>Z</booking-class> <service-level>Economy</service-level> <transportation code="5J" number="1"> <carriers> <carrier code="5J" type-id="Aircraft Boeing-737-9"> <number>563</number> </carrier> </carriers> </transportation> <additional-data> <param name="fare_basis">we543s3</param> </additional-data> </trip> </trips> </airline-data> </product> <amount>125056</amount> <quantity>1</quantity> <additional-data> <param name="deviceFingerPrint">hVdMGC9x3eJsGssbGZFB9d4Q7hdP</param> </additional-data> </line-item> </orders>';

        $xml = $this->getInitDoc(10078, 100780, 608,null,100000,null,"abhinav.shaha@cellpointmobile.com","abhinav.shaha@cellpointmobile.com","9766367227",null,null,"2.0","0", 640, $orderXml);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertStringContainsString('<orders><line-item><product sku="product-ticket"><type>100</type><name>ONE WAY</name><description>MNL-CEB</description><airline-data><profiles><profile><seq>2</seq><title>Mr</title><first-name>dan</first-name><last-name>dan</last-name><type>ADT</type><contact-info><email>dan@dan.com</email><mobile country-id="640">9187231231</mobile></contact-info><additional-data><param name="loyality_id">345rtyu</param></additional-data></profile></profiles><trips><trip tag="1" seq="1"><origin external-id="MNL" country-id="640" time-zone="+08:00" terminal="1">Ninoy Aquino International Airport</origin><destination external-id="CEB" country-id="640" time-zone="+08:00" terminal="2">Mactan Cebu International Airport</destination><departure-time>2021-03-07T19:35:00Z</departure-time><arrival-time>2021-03-07T21:05:00Z</arrival-time><booking-class>Z</booking-class><service-level>Economy</service-level><transportation code="5J" number="1"><carriers><carrier code="5J" type-id="Aircraft Boeing-737-9"><number>563</number></carrier></carriers></transportation><additional-data><param name="fare_basis">we543s3</param></additional-data></trip></trips></airline-data></product><amount>125056</amount><quantity>1</quantity><additional-data><param name="deviceFingerPrint">hVdMGC9x3eJsGssbGZFB9d4Q7hdP</param></additional-data></line-item></orders>', $sReplyBody);

        //Check passenger_tbl entry
        $res =  $this->queryDB("SELECT seq from Log.Order_Tbl ot join Log.passenger_tbl pt on ot.id = pt.order_id WHERE ot.orderref='1234abc'");

        $this->assertTrue(is_resource($res) );

        while ($row = pg_fetch_assoc($res) )
        {
            $seq = (int)$row["seq"];
        }
        $this->assertEquals(2, $seq);

        //Check flight_tbl entry
        $res =  $this->queryDB("SELECT op_flight_number, arrival_timezone, mkt_airline_code, departure_city, arrival_city, aircraft_type, arrival_terminal, departure_terminal from Log.Order_Tbl ot join Log.flight_tbl ft on ot.id = ft.order_id WHERE ot.orderref='1234abc'");

        $this->assertTrue(is_resource($res) );

        while ($row = pg_fetch_assoc($res) )
        {
            $opFlightNumber = $row["op_flight_number"];
            $arrivalTz = $row["arrival_timezone"];
            $mktAirlineCode = $row["mkt_airline_code"];
            $deptCity = $row["departure_city"];
            $arrCity = $row["arrival_city"];
            $aircraftType = $row["aircraft_type"];
            $arrivalTerminal = $row["arrival_terminal"];
            $deptTerminal = $row["departure_terminal"];

        }
        $this->assertEquals('1', $opFlightNumber);
        $this->assertEquals('+08:00', $arrivalTz);
        $this->assertEquals('5J', $mktAirlineCode);
        $this->assertEquals('Ninoy Aquino International Airport', $deptCity);
        $this->assertEquals('Mactan Cebu International Airport', $arrCity);
        $this->assertEquals('Aircraft Boeing-737-9', $aircraftType);
        $this->assertEquals('2', $arrivalTerminal);
        $this->assertEquals('1', $deptTerminal);

    }

    public function testActiveSplitNode()
    {
        $pspID = 2;
        $this->queryDB("DELETE FROM CLIENT.STATICROUTELEVELCONFIGURATION");
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");

        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid, psp_type) VALUES (10099, 2, $pspID, true, 1, 1)");

        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");

        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('isnewcardconfig', 'true', 10099, 'client', 0);");
        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('sessiontype', 2, true, 10099, 'client', 0);");

        //split payment combinations
        $this->queryDB("INSERT INTO client.split_configuration_tbl (id,client_id, name, is_one_step_auth, enabled) VALUES (1,10099, 'Card+Card', false, true);");
        $this->queryDB("INSERT INTO client.split_combination_tbl (id,split_config_id, payment_type, sequence_no) VALUES (1, 1, 1, 1);");
        $this->queryDB("INSERT INTO client.split_combination_tbl (id,split_config_id, payment_type, sequence_no) VALUES (2, 1, 1, 2);");

        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, cardid,pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid) VALUES (1001001, 100, 10099, 1100, 1, 2, $pspID, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE,10)");

        $this->queryDB("INSERT INTO log.split_session_tbl(id,sessionid,status) VALUES (1, 10,'Active');");
        $this->queryDB("INSERT INTO log.split_details_tbl(id,split_session_id, transaction_id, sequence_no,payment_status) VALUES (1, 1, 1001001, 1,'Success');");

        $xml = $this->getInitDoc(10099, 1100, null, 'success', 200,null,'jona@oismail.com',null,'288828610','member','STRICT','2.0',0,100,'',10);
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertStringContainsString('<split_payment><active_split><current_split_sequence>2</current_split_sequence><transactions><transaction><payment_type>1</payment_type><id>1001001</id><sequence>1</sequence></transaction></transactions></active_split><configuration><applicable_combinations><combination><payment_type><id>1</id><sequence>1</sequence></payment_type><payment_type><id>1</id><sequence>2</sequence></payment_type><is_one_step_authorization>false</is_one_step_authorization></combination></applicable_combinations></configuration></split_payment>', $sReplyBody);
    }

    public function testMCPEnabledDCCEnabled()
    {
        $pspID = Constants::iWIRE_CARD_PSP;

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid, dccenabled) VALUES (10099, 2, $pspID, true, 1, true)");

        $xml = $this->getInitDoc(10099, 1100, 208,null,200,null,"abhinav.shaha@cellpointmobile.com","abhinav.shaha@cellpointmobile.com","9766367227",null,null,"2.0","31");
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><client-config id="10099" account="1100" store-card="0" max-stored-cards="-1" auto-capture="false" enable-cvv="true" mode="0"><name>Test Client</name><callback-url></callback-url><accept-url></accept-url><cancel-url></cancel-url><app-url></app-url><css-url></css-url><logo-url></logo-url><base-image-url></base-image-url><additional-config></additional-config><accounts><account id= "1100" markup= "" /></accounts></client-config><transaction id="1" order-no="1234abc" type-id="1" eua-id="-1" language="da" auto-capture="false" mode="0"><amount country-id="100" currency-id="208" currency="DKK" decimals="2" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><mobile country-id="100" operator-id="10000">288828610</mobile><email>abhinav.shaha@cellpointmobile.com</email><callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url><accept-url/><cancel-url/></transaction><session id=\'1\' type=\'1\' total-amount=\'200\'><amount country-id="100" currency-id="208" currency="DKK" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><status>4001</status></session><cards><card id="2" type-id="2" psp-id="18" min-length="16" max-length="16" cvc-length="3" state-id="1" payment-type="1" preferred="false" enabled="true" processor-type="1" installment="0" cvcmandatory="false" dcc="false" presentment-currency="false"><name>Dankort</name><prefixes><prefix><min>5019</min><max>5019</max></prefix><prefix><min>4571</min><max>4571</max></prefix></prefixes>Dankort</card></cards><wallets></wallets><apms></apms><aggregators></aggregators><offline></offline><vouchers></vouchers></root>', $sReplyBody);

        $res =  $this->queryDB('SELECT fxservicetypeid from Log.Transaction_Tbl WHERE id = 1');
        $this->assertTrue(is_resource($res) );

        $fxservicetypeid = 0;
        while ($row = pg_fetch_assoc($res) )
        {
            $fxservicetypeid = (int)$row["fxservicetypeid"];
        }
        $this->assertEquals(31, $fxservicetypeid);
    }

    public function testMCPDisabledDCCEnabled()
    {
        $pspID = Constants::iWIRE_CARD_PSP;

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid, dccenabled) VALUES (10099, 2, $pspID, true, 1, true)");

        $xml = $this->getInitDoc(10099, 1100, 208,null,200,null,"abhinav.shaha@cellpointmobile.com","abhinav.shaha@cellpointmobile.com","9766367227",null,null,"2.0","11");
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><client-config id="10099" account="1100" store-card="0" max-stored-cards="-1" auto-capture="false" enable-cvv="true" mode="0"><name>Test Client</name><callback-url></callback-url><accept-url></accept-url><cancel-url></cancel-url><app-url></app-url><css-url></css-url><logo-url></logo-url><base-image-url></base-image-url><additional-config></additional-config><accounts><account id= "1100" markup= "" /></accounts></client-config><transaction id="1" order-no="1234abc" type-id="1" eua-id="-1" language="da" auto-capture="false" mode="0"><amount country-id="100" currency-id="208" currency="DKK" decimals="2" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><mobile country-id="100" operator-id="10000">288828610</mobile><email>abhinav.shaha@cellpointmobile.com</email><callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url><accept-url/><cancel-url/></transaction><session id=\'1\' type=\'1\' total-amount=\'200\'><amount country-id="100" currency-id="208" currency="DKK" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><status>4001</status></session><cards><card id="2" type-id="2" psp-id="18" min-length="16" max-length="16" cvc-length="3" state-id="1" payment-type="1" preferred="false" enabled="true" processor-type="1" installment="0" cvcmandatory="false" dcc="true" presentment-currency="false"><name>Dankort</name><prefixes><prefix><min>5019</min><max>5019</max></prefix><prefix><min>4571</min><max>4571</max></prefix></prefixes>Dankort</card></cards><wallets></wallets><apms></apms><aggregators></aggregators><offline></offline><vouchers></vouchers></root>', $sReplyBody);

        $res =  $this->queryDB('SELECT fxservicetypeid from Log.Transaction_Tbl WHERE id = 1');
        $this->assertTrue(is_resource($res) );

        $fxservicetypeid = 0;
        while ($row = pg_fetch_assoc($res) )
        {
            $fxservicetypeid = (int)$row["fxservicetypeid"];
        }
        $this->assertEquals(11, $fxservicetypeid);
    }

    public function testMCPEnabledDCCDisabled()
    {
        $pspID = Constants::iWIRE_CARD_PSP;

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid, dccenabled) VALUES (10099, 2, $pspID, true, 1, false)");

        $xml = $this->getInitDoc(10099, 1100, 208,null,200,null,"abhinav.shaha@cellpointmobile.com","abhinav.shaha@cellpointmobile.com","9766367227",null,null,"2.0","31");
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><client-config id="10099" account="1100" store-card="0" max-stored-cards="-1" auto-capture="false" enable-cvv="true" mode="0"><name>Test Client</name><callback-url></callback-url><accept-url></accept-url><cancel-url></cancel-url><app-url></app-url><css-url></css-url><logo-url></logo-url><base-image-url></base-image-url><additional-config></additional-config><accounts><account id= "1100" markup= "" /></accounts></client-config><transaction id="1" order-no="1234abc" type-id="1" eua-id="-1" language="da" auto-capture="false" mode="0"><amount country-id="100" currency-id="208" currency="DKK" decimals="2" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><mobile country-id="100" operator-id="10000">288828610</mobile><email>abhinav.shaha@cellpointmobile.com</email><callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url><accept-url/><cancel-url/></transaction><session id=\'1\' type=\'1\' total-amount=\'200\'><amount country-id="100" currency-id="208" currency="DKK" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><status>4001</status></session><cards><card id="2" type-id="2" psp-id="18" min-length="16" max-length="16" cvc-length="3" state-id="1" payment-type="1" preferred="false" enabled="true" processor-type="1" installment="0" cvcmandatory="false" dcc="false" presentment-currency="false"><name>Dankort</name><prefixes><prefix><min>5019</min><max>5019</max></prefix><prefix><min>4571</min><max>4571</max></prefix></prefixes>Dankort</card></cards><wallets></wallets><apms></apms><aggregators></aggregators><offline></offline><vouchers></vouchers></root>', $sReplyBody);

        $res =  $this->queryDB('SELECT fxservicetypeid from Log.Transaction_Tbl WHERE id = 1');
        $this->assertTrue(is_resource($res) );

        $fxservicetypeid = 0;
        while ($row = pg_fetch_assoc($res) )
        {
            $fxservicetypeid = (int)$row["fxservicetypeid"];
        }
        $this->assertEquals(31, $fxservicetypeid);
    }

    public function testMCPDisabledDCCDisabled()
    {
        $pspID = Constants::iWIRE_CARD_PSP;

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid, dccenabled) VALUES (10099, 2, $pspID, true, 1, false)");

        $xml = $this->getInitDoc(10099, 1100, 208,null,200,null,"abhinav.shaha@cellpointmobile.com","abhinav.shaha@cellpointmobile.com","9766367227",null,null,"2.0");
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><client-config id="10099" account="1100" store-card="0" max-stored-cards="-1" auto-capture="false" enable-cvv="true" mode="0"><name>Test Client</name><callback-url></callback-url><accept-url></accept-url><cancel-url></cancel-url><app-url></app-url><css-url></css-url><logo-url></logo-url><base-image-url></base-image-url><additional-config></additional-config><accounts><account id= "1100" markup= "" /></accounts></client-config><transaction id="1" order-no="1234abc" type-id="1" eua-id="-1" language="da" auto-capture="false" mode="0"><amount country-id="100" currency-id="208" currency="DKK" decimals="2" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><mobile country-id="100" operator-id="10000">288828610</mobile><email>abhinav.shaha@cellpointmobile.com</email><callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url><accept-url/><cancel-url/></transaction><session id=\'1\' type=\'1\' total-amount=\'200\'><amount country-id="100" currency-id="208" currency="DKK" symbol="Kr." format="{PRICE} {CURRENCY}" alpha2code="DK" alpha3code="DNK" code="208">200</amount><status>4001</status></session><cards><card id="2" type-id="2" psp-id="18" min-length="16" max-length="16" cvc-length="3" state-id="1" payment-type="1" preferred="false" enabled="true" processor-type="1" installment="0" cvcmandatory="false" dcc="false" presentment-currency="false"><name>Dankort</name><prefixes><prefix><min>5019</min><max>5019</max></prefix><prefix><min>4571</min><max>4571</max></prefix></prefixes>Dankort</card></cards><wallets></wallets><apms></apms><aggregators></aggregators><offline></offline><vouchers></vouchers></root>', $sReplyBody);
    }
}
