<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class GenerateHmacSecurityHashAPITest extends baseAPITest
{
	protected $_aMPOINT_CONN_INFO;

	public function __construct()
	{
        parent::__construct();
        $this->bIgnoreErrors = true;
		$this->constHTTPClient();
	}

	public function constHTTPClient()
	{
		global $aMPOINT_CONN_INFO;
		$aMPOINT_CONN_INFO['path'] = "/mApp/api/generate_hmac_security_hash.php";
		$aMPOINT_CONN_INFO["contenttype"] = "text/xml";
		$this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($this->_aMPOINT_CONN_INFO));
	}

    protected function getDoc($clientid)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<root>';
        $xml .= '<generate-hmac-security-hash>';
        $xml .= '<transactions>';
		$xml .= '<transaction>';		
		$xml .= '<hmac-type>FX</hmac-type>';
		$xml .= '<unique-reference>101</unique-reference>';
		$xml .= '<client-id>'.$clientid.'</client-id>';
		$xml .= '<order-no>CY973</order-no>';
		$xml .= '<amount>200</amount>';
		$xml .= '<nonce>123456</nonce>';
		$xml .= '<country-id>200</country-id>';
		$xml .= '<sale-amount>200</sale-amount>';
		$xml .= '<sale-currency>200</sale-currency>';
		$xml .= '<client-info>';
        $xml .= '<mobile>9898989898</mobile>';
        $xml .= '<mobile-country>640</mobile-country>';
        $xml .= '<email>jona@oismail.com</email>';
        $xml .= '<device-id></device-id>';
        $xml .= '</client-info>';        
        $xml .= '</transaction>';        
        $xml .= '</transactions>';        
        $xml .= '</generate-hmac-security-hash>';
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
		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), '<xl</xl>');
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(415, $iStatus);
		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="415">Invalid XML Document</status></root>', $sReplyBody);
	}


    // public function testGetTransactionStatusSuccess()
    // {
    //     $pspID = Constants::iWIRE_CARD_PSP;

    //     $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
	// 	$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
	// 	$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
	// 	$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
	// 	$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
	// 	$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
	// 	$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 2, $pspID, true, 1)");
	// 	$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
	// 	$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
	// 	$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '501910******3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
    //     $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
    //     $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, operatorid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid,sessionid,convertedamount,convertedcurrencyid,expiry,created) VALUES (1001001, 100, 10099, 1100, 1,  $pspID, 5001, 10000, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE, 208, 1,5000,208,'12/21','2021-01-18 13:09:28')");
    //     $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_INIT_WITH_PSP_STATE .")");
    //     $this->queryDB("INSERT INTO Log.Address_Tbl (first_name,last_name ,street, street2, city, state, country, zip, reference_id, reference_type, mobile_country_id, mobile, email) VALUES ('test','test', 'test', 'test', 'test', 'test', 'test', '411023', '1001001', 'transaction','200','test@test.com','8888888888')");

    //     $xml = $this->getGetTransactionStatusDoc(1001001,10099);

	// 	$this->_httpClient->connect();

	// 	$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
    //     $sReplyBody = $this->_httpClient->getReplyBody();

    //     $this->assertEquals(200, $iStatus);

	/*  	$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><transaction id="1001001" mpoint-id="1001001" order-no="103-1418291" accoutid="1100" clientid="10099" language="gb"  card-id="0" psp-id="18" payment-method-id="1"   session-id="1" session-type="" extid="" approval-code="" walletid="-1"><amount country-id="100" currency="208" symbol="Kr." format="{PRICE} {CURRENCY}" pending = "5000"  currency-code = "DKK" decimals = "2" conversationRate = "1">5000</amount><card-expiry>12/21</card-expiry><card-name>System Record</card-name><psp-name>Wire Card</psp-name><accept-url></accept-url><cancel-url></cancel-url><css-url></css-url><logo-url></logo-url><google-analytics-id></google-analytics-id><form-method></form-method><createdDate>2021-01-18</createdDate><createdTime>13:09:28</createdTime><status><status-message id = "1009" position = "1">Payment Initialized with Payment Service Provider</status-message></status><sign>257e89dffd3e6ff7db2fed0182ee54ef</sign><client-info language="gb" platform=""><mobile operator-id="10000" country-id="100"></mobile><email></email><customer-ref></customer-ref><device-id></device-id></client-info><address><first-name>test</first-name><last-name>test</last-name><street>test</street><street2>test</street2><postal-code>411023</postal-code><city>test</city><state>test</state><country><name></name><code>0</code><alpha2code></alpha2code><alpha3code></alpha3code></country><mobile idc="1">test@test.com</mobile><email>8888888888</email></address></transaction><payment_status>Pending</payment_status><stored-card><card-id>61775</card-id><card-mask>501910******3742</card-mask><card-expiry>06/24</card-expiry><card-type>2</card-type></stored-card></root>', $sReplyBody);*/
    // }

}
