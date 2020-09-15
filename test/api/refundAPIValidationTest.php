<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class RefundAPIValidationTest extends baseAPITest
{

    protected $_aMPOINT_CONN_INFO;

    public function __construct()
    {
        $this->constHTTPClient();
    }

    public function constHTTPClient()
    {
        global $aMPOINT_CONN_INFO;
        $aMPOINT_CONN_INFO['path'] = "/buy/refund.php";
        $aMPOINT_CONN_INFO["contenttype"] = "application/x-www-form-urlencoded";
        $this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO) );
    }

    public function testTransactionNotFoundInCorrectState()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '800-1234', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, keywordid, amount, ip, enabled, currencyid, sessionid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 113, 1100, 100, 1,5000, '127.0.0.1', TRUE, 208,1,5000,208)");

		$this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders(), 'username=Tusername&password=Tpassword&clientid=113&account=1100&mpointid=1001001&amount=5000');
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(500, $iStatus);
        $this->assertEquals("msg=1001", $sReplyBody);
    }

	/*
	 *	In order to support partial refund in mPoint, this API should allow multiple refund states on one transaction
	 * 	However; this test checks that the API blocks refund if one refund state is present, since this is the current behavior
	 * 	Change the API -> Change this Test
	 */
    public function testPaymentAlreadyRefunded()
    {
		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '800-1234', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, keywordid, amount, ip, enabled, currencyid, sessionid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 113, 1100, 100, 1, 5000, '127.0.0.1', TRUE, 208, 1,5000,208)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_CAPTURED_STATE. ")");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_REFUNDED_STATE. ")");

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders(), 'username=Tusername&password=Tpassword&clientid=113&account=1100&mpointid=1001001&amount=5000');
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(500, $iStatus);
        $this->assertEquals("msg=1001", $sReplyBody);
    }


    public function testTransactionDisabled()
    {
		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '800-1234', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, keywordid, amount, ip, enabled, currencyid, sessionid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 113, 1100, 100, 1, 5000, '127.0.0.1', FALSE, 208, 1,5000,208)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");

        $this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders(), 'username=Tusername&password=Tpassword&clientid=113&account=1100&mpointid=1001001&amount=5000');
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(500, $iStatus);
        $this->assertEquals("msg=1001", $sReplyBody);
    }

    public function testPaymentRejectedState()
    {
		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '800-1234', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, keywordid, amount, ip, currencyid, sessionid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 113, 1100, 100, 1, 5000, '127.0.0.1', 208, 1,5000,208)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_REJECTED_STATE. ")");

        $this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders(), 'username=Tusername&password=Tpassword&clientid=113&account=1100&mpointid=1001001&amount=5000');
		$sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(500, $iStatus);
        $this->assertEquals("msg=1001", $sReplyBody);
    }

    public function testInvalidAmount()
    {
		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '1513-005', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, countryid, orderid, amount, ip, currencyid, sessionid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 113, 1100, 1, 100, '1513-005', 5000, '127.0.0.1', 208, 1,5000,208)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");

        //Undefined amount
        $this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders(), 'username=Tusername&password=Tpassword&clientid=113&account=1100&mpointid=1001001');
		$sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(400, $iStatus);
        $this->assertEquals("msg=51", $sReplyBody);

        $this->_httpClient->disConnect();


        $this->constHTTPClient();

        //Too small amount
        $this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders(), 'username=Tusername&password=Tpassword&clientid=113&account=1100&mpointid=1001001&amount=-1');
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(400, $iStatus);
        $this->assertEquals("msg=52", $sReplyBody);

        $this->_httpClient->disConnect();


        $this->constHTTPClient();

        //Too large amount
        $this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders(), 'username=Tusername&password=Tpassword&clientid=113&account=1100&mpointid=1001001&amount=10000');
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(400, $iStatus);
        $this->assertEquals("msg=53", $sReplyBody);

    }

    public function testBadRequestInvalidRequestBody()
    {
        $this->_httpClient->connect();
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders(), '<root></root>');
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->_httpClient->disConnect();

        $this->assertEquals(400, $iStatus);
        $this->assertEquals("msg=11", $sReplyBody);
    }

    public function testBadRequestInvalidClient()
    {
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders(), 'clientid=1');
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(400, $iStatus);
        $this->assertEquals("msg=12", $sReplyBody);
    }

    public function testBadRequestUnknownClient()
    {
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders(), 'clientid=100');
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(400, $iStatus);
        $this->assertEquals("msg=13", $sReplyBody);
    }

    public function testBadRequestDisabledClient()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, enabled) VALUES (113, 1, 100, 'Test Client', false)");

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders(), 'clientid=113');
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(400, $iStatus);
        $this->assertEquals("msg=14", $sReplyBody);
    }

    public function testBadRequestUnknownDefaultAccount()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, enabled) VALUES (113, 1, 100, 'Test Client', true)");

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders(), 'clientid=113');
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(400, $iStatus);
        $this->assertEquals("msg=23", $sReplyBody);
    }

    public function testBadRequestUnknownSpecifiedAccount()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, enabled) VALUES (113, 1, 100, 'Test Client', true)");

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders(), 'clientid=113&account=1');
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(400, $iStatus);
        $this->assertEquals("msg=13", $sReplyBody);
    }

    public function testDisabledAccount()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, enabled) VALUES (113, 1, 100, 'Test Client', true)");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, enabled) VALUES (1100, 113, false)");

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders(), 'clientid=113&account=1100');
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(400, $iStatus);
        $this->assertEquals("msg=24", $sReplyBody);
    }

}
