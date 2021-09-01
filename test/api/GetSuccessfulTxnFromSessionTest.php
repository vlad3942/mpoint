<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class GetSuccessfulTxnFromSessionTest extends baseAPITest
{
	protected $_aMPOINT_CONN_INFO;

	public function __construct()
	{
        parent::__construct();
        //$this->bIgnoreErrors = true;

	}

	public function constHTTPClient($path)
	{
		global $aMPOINT_CONN_INFO;
		$aMPOINT_CONN_INFO['path'] = $path;
		$aMPOINT_CONN_INFO["contenttype"] = "application/x-www-form-urlencoded";
        $aMPOINT_CONN_INFO["method"] = 'GET';
		$this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($this->_aMPOINT_CONN_INFO));
	}

    public function testInvalidRequestMissingClientId()
    {
        $this->constHTTPClient("/mApp/api/get_successful_txn_from_session.php?sessionid=1");
		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'));
		$sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(400, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="400">Client id required</status></root>', $sReplyBody);
    }

    public function testInvalidRequestMissingSessionId()
    {
        $this->constHTTPClient("/mApp/api/get_successful_txn_from_session.php");
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'));
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(400, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="400">Session id required</status></root>', $sReplyBody);
    }

    public function testGetTransactionStatusSuccess()
    {
        $pspID = Constants::iWIRE_CARD_PSP;
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd,salt) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass','23lkhfgjh24qsdfkjh')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10099, 'app')");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type,scope) VALUES ('IS_LEGACY', 'true', 10099, 'client',0)");

        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 208, 100, 4001, '1513-005', 5000, 29612109, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,currencyid,convertedamount,convertedcurrencyid, created) VALUES (1001001, 100, 10099, 1100, 100, $pspID, '1512', '1234abc', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,840,5000,840, '2021-09-01 11:43:38.793849')");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");

        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,currencyid,convertedamount,convertedcurrencyid, created) VALUES (1001002, 100, 10099, 1100, 100, $pspID, '1512', '1234abc', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,840,5000,840, '2021-09-01 11:43:38.793849')");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001002, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");

        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,currencyid,convertedamount,convertedcurrencyid, created) VALUES (1001003, 100, 10099, 1100, 100, $pspID, '1512', '1234abc', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,840,5000,840, '2021-09-01 11:43:38.793849')");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001003, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001003, ". Constants::iPOST_FRAUD_CHECK_REJECTED_STATE. ")");

        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,currencyid,convertedamount,convertedcurrencyid, created) VALUES (1001004, 100, 10099, 1100, 100, $pspID, '1512', '1234abc', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,840,5000,840, '2021-09-01 11:43:38.793849')");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001004, ". Constants::iPAYMENT_REFUNDED_STATE. ")");

        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,currencyid,convertedamount,convertedcurrencyid, created) VALUES (1001005, 100, 10099, 1100, 100, $pspID, '1512', '1234abc', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,840,5000,840, '2021-09-01 11:43:38.793849')");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001005, ". Constants::iPAYMENT_INIT_WITH_PSP_STATE. ")");

        $this->constHTTPClient("/mApp/api/get_successful_txn_from_session.php?sessionid=1&clientid=10099");

        $this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'));
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(200, $iStatus);

	 	$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><session><client_id>10099</client_id><account_id>1100</account_id><session_id>1</session_id><sale_amount><value>5000</value><currency_id>208</currency_id><decimals>2</decimals><alpha3code>DKK</alpha3code></sale_amount><status><code>4001</code><message>SessionCreated</message></status><transactions><transaction><id>1001001</id><order_id>1234abc</order_id><fee>0</fee><hmac>c86718a06b5cd8bc3a3a531847563c25b5fa4842620999608d10041c5fafb3db26b1a1d1e9aa388a8aa4b5b83f69f7f3f0a9660068f0dd9be0cf9fcbd0baa8fe</hmac><product_type>100</product_type><payment_method>CD</payment_method><payment_type>1</payment_type><date_time>2021-09-01T11:43:38+00:00</date_time><amount><value>5000</value><currency_id>840</currency_id><decimals>2</decimals><alpha3code>USD</alpha3code><conversion_rate>1</conversion_rate></amount><status><code>2000</code><message>PaymentauthorizedbyPSP</message></status><psp><id>18</id><name>WireCard</name><external_id>1512</external_id></psp><card><id>-1</id><card_name>SystemRecord</card_name></card><customer_info><language>gb</language></customer_info><pos>100</pos><ip_address>127.0.0.1</ip_address><route_config_id>-1</route_config_id><installment>0</installment></transaction><transaction><id>1001002</id><order_id>1234abc</order_id><fee>0</fee><hmac>c86718a06b5cd8bc3a3a531847563c25b5fa4842620999608d10041c5fafb3db26b1a1d1e9aa388a8aa4b5b83f69f7f3f0a9660068f0dd9be0cf9fcbd0baa8fe</hmac><product_type>100</product_type><payment_method>CD</payment_method><payment_type>1</payment_type><date_time>2021-09-01T11:43:38+00:00</date_time><amount><value>5000</value><currency_id>840</currency_id><decimals>2</decimals><alpha3code>USD</alpha3code><conversion_rate>1</conversion_rate></amount><status><code>2000</code><message>PaymentauthorizedbyPSP</message></status><psp><id>18</id><name>WireCard</name><external_id>1512</external_id></psp><card><id>-1</id><card_name>SystemRecord</card_name></card><customer_info><language>gb</language></customer_info><pos>100</pos><ip_address>127.0.0.1</ip_address><route_config_id>-1</route_config_id><installment>0</installment></transaction></transactions><callback_url>:///_test/simulators/mticket/callback.php</callback_url><session_type>1</session_type></session></root>', $sReplyBody);
    }
}
