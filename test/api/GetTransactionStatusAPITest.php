<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class GetTransactionStatusAPITest extends baseAPITest
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
		$aMPOINT_CONN_INFO['path'] = "/mApp/api/get_transaction_status.php";
		$aMPOINT_CONN_INFO["contenttype"] = "text/xml";
		$this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($this->_aMPOINT_CONN_INFO));
	}

    protected function getGetTransactionStatusDoc($txn_id,$clientid,$mode=0)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<root>';
        $xml .= '<get-transaction-status>';
        $xml .= '<client-id>'.$clientid.'</client-id>';
        $xml .= '<transactions>';
        $xml .= '<transaction-id ';
        if($mode>0){ $xml .= 'mode= "'.$mode.'"'; }
        $xml .= '>'.$txn_id.'</transaction-id>';
        $xml .= '</transactions>';        
        $xml .= '</get-transaction-status>';
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
		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), '<xl</xl>');
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(415, $iStatus);
		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="415">Invalid XML Document</status></root>', $sReplyBody);
	}

    public function testUnauthorized()
	{
        $xml = $this->getGetTransactionStatusDoc(1001001);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders(), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(401, $iStatus);
		$this->assertStringContainsString('<status code="401">Authorization required</status>', $sReplyBody);
	}

    public function testMissingTxnId()
    {
        $xml = $this->getGetTransactionStatusDoc("");

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(400, $iStatus);
		$this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?><root><status code="400">Element \'transaction-id\': \'\' is not a valid value of the atomic type', $sReplyBody);
    }
    
    public function testInvalidTxnId()
    {
        $xml = $this->getGetTransactionStatusDoc('70063s82');
		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(400, $iStatus);
		$this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?><root><status code="400">Element \'transaction-id\': \'70063s82\' is not a valid value of the atomic type', $sReplyBody);
    }


    public function testGetTransactionStatusSuccess()
    {
        $pspID = Constants::iWIRE_CARD_PSP;

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 2)");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (113, 2, $pspID, true, 1)");
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '501910******3742', '06/24', TRUE, 113, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, operatorid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid,sessionid,convertedamount,convetredcurrencyid,expiry) VALUES (1001001, 100, 113, 1100, 1,  $pspID, 5001, 10000, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE, 208, 1,5000,208,'12/21')");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_INIT_WITH_PSP_STATE .")");
        $this->queryDB("INSERT INTO Log.Address_Tbl (first_name,last_name ,street, street2, city, state, country, zip, reference_id, reference_type) VALUES ('test','test', 'test', 'test', 'test', 'test', 'test', '411023', '1001001', 'transaction')");

        $xml = $this->getGetTransactionStatusDoc(1001001,113);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
	 	$this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?><root><transaction id="1001001" mpoint-id="1001001" order-no="103-1418291" accoutid="1100" clientid="113" language="gb"  card-id="0" psp-id="18" payment-method-id="1"   session-id="1" session-type="" extid="" approval-code="" walletid="0"><amount country-id="100" currency="208" symbol="" format="{PRICE} {CURRENCY}" pending = "5000"  currency-code = "DKK" decimals = "2" conversationRate = "1">5000</amount><card-expiry>12/21</card-expiry><card-name>System Record</card-name><psp-name>WireCard</psp-name><accept-url></accept-url><cancel-url></cancel-url><css-url></css-url><logo-url></logo-url><google-analytics-id></google-analytics-id><form-method></form-method><status><status-message id = "1009" position = "1">Payment Initialized with Payment Service Provider</status-message></status><sign>7fc36114dfb232e45f1d1fd91fb16d33</sign><client-info language="gb" platform=""><mobile operator-id="10000" country-id="100"></mobile><email></email><customer-ref></customer-ref><device-id></device-id></client-info><address><first-name>test</first-name><last-name>test</last-name><street>test</street><street2>test</street2><postal-code>411023</postal-code><city>test</city><state>test</state><country></country></address></transaction><stored-card><card-id>61775</card-id><card-mask>501910******3742</card-mask><card-expiry>06/24</card-expiry><card-type>2</card-type></stored-card></root>', $sReplyBody);
    }

    public function testGetTransactionStatusSuccessWithDCC()
    {
        $pspID = Constants::iWIRE_CARD_PSP;

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 2)");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (113, 2, $pspID, true, 1)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '501910******3742', '06/24', TRUE, 113, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, operatorid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid,sessionid,convertedamount,convetredcurrencyid,conversionrate,expiry) VALUES (1001001, 100, 113, 1100, 1,  $pspID, 5001, 10000, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE, 208, 1,10000,840,2,'12/21')");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_INIT_WITH_PSP_STATE .")");
        $this->queryDB("INSERT INTO Log.Address_Tbl (first_name,last_name ,street, street2, city, state, country, zip, reference_id, reference_type) VALUES ('test','test', 'test', 'test', 'test', 'test', 'test', '411023', '1001001', 'transaction')");

        $xml = $this->getGetTransactionStatusDoc(1001001,113,1);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><transaction id="1001001" mpoint-id="1001001" order-no="103-1418291" accoutid="1100" clientid="113" language="gb"  card-id="0" psp-id="18" payment-method-id="1"   session-id="1" session-type="" extid="" approval-code="" walletid="0"><amount country-id="100" currency="840" symbol="$" format="{PRICE} {CURRENCY}" pending = "5000"  currency-code = "USD" decimals = "2" conversationRate = "2">10000</amount><initialize_amount country-id="1001001" currency="208" symbol="" format="{PRICE} {CURRENCY}" pending = "5000"  currency-code = "DKK" decimals = "2">5000</initialize_amount><card-expiry>12/21</card-expiry><card-name>System Record</card-name><psp-name>WireCard</psp-name><accept-url></accept-url><cancel-url></cancel-url><css-url></css-url><logo-url></logo-url><google-analytics-id></google-analytics-id><form-method></form-method><status></status><sign>b735893e420b89d4956acb7e8423c18d</sign><client-info language="gb" platform=""><mobile operator-id="10000" country-id="100"></mobile><email></email><customer-ref></customer-ref><device-id></device-id></client-info><address><first-name>test</first-name><last-name>test</last-name><street>test</street><street2>test</street2><postal-code>411023</postal-code><city>test</city><state>test</state><country></country></address></transaction><stored-card><card-id>61775</card-id><card-mask>501910******3742</card-mask><card-expiry>06/24</card-expiry><card-type>2</card-type></stored-card></root>', $sReplyBody);
    }

}
