<?php
/**
 * User: jot
 * Date: 24-03-15
 * Time: 19:46
 */

require_once __DIR__. '/refundAPITest.php';

class NetAxeptRefundAPITest extends RefundAPITest
{
	const iCLIENTID = 110;
	const iACCOUNTID = 1100;
	
	const sMESB_USERNAME = "Taxa4x35";
	const sMESB_PASSWORD = "DEMOisNO_2";
	
	const sMERCHANT_ACCOUNT = "12002878";
	const sPSP_USERNAME = "12002878";
	const sPSP_PASSWORD = "N_k7)2K";
	
	const sORDER_NUMBER = "1513-005";
	const iAMOUNT = 5000;
	
	private $_iTransactionID;
	
	private function _popuplate($pspid, $ma, $un, $pw, $cardid, $txnid)
	{
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
		
		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (". self::iCLIENTID .", 1, 100, 'Test Client', '". self::sMESB_USERNAME ."', '". self::sMESB_PASSWORD ."')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (". self::iCLIENTID .", 4, 'http://taxa.mesb.test.cellpointmobile.com:10080')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (". self::iACCOUNTID .", ". self::iCLIENTID .", 'app')");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, ". self::iCLIENTID .", 'CPM', true)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name, username, passwd) VALUES (1, ". self::iCLIENTID .", ". $pspid .", '". $ma ."', '". $un ."', '". $pw ."')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (". self::iACCOUNTID .", ". $pspid .", '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (". self::iCLIENTID .", ". $cardid .", ". $pspid .")");
		$this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT C.id * -1 AS pricepointid, ". $cardid ." FROM System.Country_Tbl C, System.Card_Tbl Card WHERE C.id = 100 GROUP BY pricepointid;");
		$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, orderid, callbackurl, amount, ip, enabled, keywordid) VALUES (". $txnid .", 100, ". self::iCLIENTID .", ". self::iACCOUNTID .", 100, ". $pspid .", '". self::sORDER_NUMBER ."', '". $sCallbackURL. "', ". self::iAMOUNT .", '127.0.0.1', TRUE, 1)");
	}

	private function _getPayDoc($client, $account, $txn=1, $card=7, $store=false)
	{
		$sStore = $store ? 'true' : 'false';
	
		$xml = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml .= '<root>';
		$xml .= '<pay client-id="'. $client .'" account="'. $account .'">';
		$xml .= '<transaction id="'. $txn .'" store-card="'. $sStore .'">';
		$xml .= '<card type-id="'. $card .'">';
		$xml .= '<amount country-id="100">5000</amount>';
		$xml .= '</card>';
		$xml .= '</transaction>';
		$xml .= '<client-info platform="iOS" version="1.00" language="da">';
		$xml .= '<mobile country-id="100" operator-id="10000">28882861</mobile>';
		$xml .= '<email>jona@oismail.com</email>';
		$xml .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
		$xml .= '</client-info>';
		$xml .= '</pay>';
		$xml .= '</root>';
	
		return $xml;
	}
	private function _getAuthBody(SimpleXMLElement $obj_Pay, $cardno, $expmonth, $expyear, $cvc)
	{
		if ($expmonth < 10) { $expmonth = "0". intval($expmonth); }
		$body = "";
		$body .= $obj_Pay->{'card-number'} ."=". $cardno;
		$body .= "&". $obj_Pay->{'expiry-month'} ."=". $expmonth . $expyear;
		$body .= "&". $obj_Pay->{'cvc'} ."=". $cvc;
		foreach ($obj_Pay->{'hidden-fields'}->children() as $obj_Field)
		{
			$body .= "&". $obj_Field->getName() ."=". $obj_Field;
		}
		
		return $body;
	}
    protected function successfulPayTest($pspid, $ma, $un, $pw, $cardid, $sc=false)
    {
    	$this->_iTransactionID = time();
    	$this->_popuplate($pspid, $ma, $un, $pw, $cardid, $this->_iTransactionID);
    	
    	$aHTTP_CONN_INFO = $this->_aMPOINT_CONN_INFO;
    	$aHTTP_CONN_INFO["path"] = "/mApp/api/pay.php";
    	$obj_Client = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO) );
    	$xml = $this->_getPayDoc(self::iCLIENTID, self::iACCOUNTID, $this->_iTransactionID, $cardid, $sc);
    	$obj_Client->connect();
    	$iStatus = $obj_Client->send($this->constHTTPHeaders(self::sMESB_USERNAME, self::sMESB_PASSWORD), $xml);
    	$obj_Client->disconnect();
    	$sReplyBody = $obj_Client->getReplyBody();
    	
    	$this->assertEquals(200, $iStatus, $sReplyBody);
    	$this->assertContains('<?xml version="1.0" encoding="UTF-8"?><root><psp-info id="'. $pspid. '" merchant-account="'. $ma .'">', $sReplyBody);
    	$this->assertContains('<url content-type="application/x-www-form-urlencoded" method="post">', $sReplyBody);
    	$this->assertContains('epayment.nets.eu/Terminal/default.aspx</url', $sReplyBody);
    	$this->assertContains('<card-number>pan</card-number>', $sReplyBody);
    	$this->assertContains('<card-number>pan</card-number>', $sReplyBody);
    	$this->assertContains('<expiry-month>expiryDate</expiry-month>', $sReplyBody);
    	$this->assertContains('<cvc>securityCode</cvc>', $sReplyBody);
    	$this->assertContains('<merchantId>'. self::sMERCHANT_ACCOUNT .'</merchantId>', $sReplyBody);
    	$this->assertContains('<transactionId>'. $this->_iTransactionID .'</transactionId>', $sReplyBody);
    	
    	$res = $this->queryDB("SELECT id FROM Enduser.Account_Tbl");
    	$this->assertTrue(is_resource($res) );
    	
    	$this->assertEquals(intval($sc), pg_num_rows($res) );
    	
    	return $sReplyBody;
	}
	
	private function _sendCardDetails(SimpleXMLElement $obj_PSPInfo, $cardno, $expmonth, $expyear, $cvc)
	{
		$body = $this->_getAuthBody($obj_PSPInfo, $cardno, $expmonth, $expyear, $cvc);
		$aURL = parse_url(trim($obj_PSPInfo->{'url'}) );
		
		$aHTTP_CONN_INFO = $this->_aMPOINT_CONN_INFO;
		$aHTTP_CONN_INFO["protocol"] = $aURL["scheme"];
		$aHTTP_CONN_INFO["host"] = $aURL["host"];
		if ($aHTTP_CONN_INFO["protocol"] == "https") { $aHTTP_CONN_INFO["port"] = 443; }
		$aHTTP_CONN_INFO["path"] = $aURL["path"];
		$aHTTP_CONN_INFO["contenttype"] = trim($obj_PSPInfo->{'url'}['content-type']);
		
		$obj_Client = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO) );
		$obj_Client->connect();
		$iStatus = $obj_Client->send($this->constHTTPHeaders(), $body);
		$obj_Client->disconnect();
		
		return $obj_Client;
	}
	private function _parseHeaders($header)
	{
		$a = explode("\r\n", $header);
		$aHeaders = array();
		for ($i=0; $i<count($a); $i++)
		{
			$pos = strpos($a[$i], ":");
			$aHeaders[strtolower(trim(substr($a[$i], 0, $pos) ) )] = trim(substr($a[$i], $pos+1) );
		}
		
		return $aHeaders;
	}
	private function _handleRedirect($url)
	{
		$aURL = parse_url($url);
		$obj_Client = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo("http://". $aURL["host"] .":10080". $aURL["path"] ."?". $aURL["query"]) );
		$obj_Client->connect();
		$iStatus = $obj_Client->send($this->constHTTPHeaders() );
		$obj_Client->disconnect();
		
		return $obj_Client;
	}
	protected function successfulAuthorizationTest(SimpleXMLElement $obj_Pay, $cardno, $expmonth, $expyear, $cvc, $state=Constants::iPAYMENT_ACCEPTED_STATE)
	{
		$obj_Client = $this->_sendCardDetails($obj_Pay->{'psp-info'}, $cardno, $expmonth, $expyear, $cvc);
		$this->assertEquals(302, $obj_Client->getReturnCode(), $obj_Client->getReplyHeader() );
		
		$aHeaders = $this->_parseHeaders($obj_Client->getReplyHeader() );
		$this->assertTrue(array_key_exists("location", $aHeaders), "Location Header not found");
		
		$obj_Client = $this->_handleRedirect($aHeaders["location"]);
		$this->assertEquals(200, $obj_Client->getReturnCode(), $obj_Client->getReplyBody() );
		$this->assertContains('<status code="'. $state .'">', $obj_Client->getReplyBody() );
		
		$this->_assertTransactionState($state);
	}
	protected function captureTest($httpcode, $statuscode, $state)
	{
		$body = "clientid=". self::iCLIENTID ."&account=". self::iACCOUNTID ."&mpointid=". $this->_iTransactionID ."&orderid=". self::sORDER_NUMBER ."&amount=". self::iAMOUNT;
		
		$aHTTP_CONN_INFO = $this->_aMPOINT_CONN_INFO;
		$aHTTP_CONN_INFO["path"] = "/buy/capture.php";
		
		$obj_Client = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO) );
		$obj_Client->connect();
		$iStatus = $obj_Client->send($this->constHTTPHeaders(self::sMESB_USERNAME, self::sMESB_PASSWORD), $body);
		$obj_Client->disconnect();
		
		$this->assertEquals($httpcode, $iStatus, $obj_Client->getReplyBody() );
		$this->assertEquals("msg=". $statuscode, $obj_Client->getReplyBody() );
	}
	protected function refundTest($httpcode, $statuscode, $state)
	{
		$body = "username=". self::sMESB_USERNAME ."&password=". self::sMESB_PASSWORD ."&clientid=". self::iCLIENTID ."&account=". self::iACCOUNTID ."&mpointid=". $this->_iTransactionID ."&orderid=". self::sORDER_NUMBER ."&amount=". self::iAMOUNT;
		$this->_httpClient->connect();
		$iStatus = $this->_httpClient->send($this->constHTTPHeaders(self::sMESB_USERNAME, self::sMESB_PASSWORD), $body);
		$this->_httpClient->disconnect();
		
		$this->assertEquals($httpcode, $iStatus, $this->_httpClient->getReplyBody() );
		$this->assertEquals("msg=". $statuscode, $this->_httpClient->getReplyBody() );
	}
	private function _assertTransactionState($state, $num=1)
	{
		$res = $this->queryDB("SELECT id FROM Log.Message_Tbl WHERE txnid = ". $this->_iTransactionID ." AND stateid = ". $state);
		$this->assertTrue(is_resource($res) );
		$this->assertEquals($num, pg_num_rows($res), "State: ". $state ." found: ". pg_num_rows($res) ." time(s) for transaction");
	}
	
	public function testSuccessfulRefundWithDankort()
	{
	    $this->markTestIncomplete("Skipped as the test case tests an old integration which is no longer supported by NetAxept. To remedy this, the new NetAxept integration from branch: release/v2.02 needs to be merged in");
/*		$obj_Pay = simplexml_load_string($this->successfulPayTest(Constants::iNETAXEPT_PSP, self::sMERCHANT_ACCOUNT, self::sPSP_USERNAME, self::sPSP_PASSWORD, Constants::iDANKORT_CARD) );
		$this->successfulAuthorizationTest($obj_Pay, "5019994001300153", 05, 21, 603);
		$this->captureTest(200, 1000, Constants::iPAYMENT_CAPTURED_STATE);
		$this->_assertTransactionState(Constants::iPAYMENT_CAPTURED_STATE);
		
		$this->refundTest(200, 1000, Constants::iPAYMENT_REFUNDED_STATE);
		$this->_assertTransactionState(Constants::iPAYMENT_REFUNDED_STATE);*/
        $this->assertTrue(true);

	}
	public function testSuccessfulRefundWithVISA()
	{
	    $this->markTestIncomplete("Skipped as the test case tests an old integration which is no longer supported by NetAxept. To remedy this, the new NetAxept integration from branch: release/v2.02 needs to be merged in");
		/*$obj_Pay = simplexml_load_string($this->successfulPayTest(Constants::iNETAXEPT_PSP, self::sMERCHANT_ACCOUNT, self::sPSP_USERNAME, self::sPSP_PASSWORD, Constants::iVISA_CARD) );
		$this->successfulAuthorizationTest($obj_Pay, "4925000000000004", 05, 21, 603);
		$this->captureTest(200, 1000, Constants::iPAYMENT_CAPTURED_STATE);
		$this->_assertTransactionState(Constants::iPAYMENT_CAPTURED_STATE);
	
		$this->refundTest(200, 1000, Constants::iPAYMENT_REFUNDED_STATE);
		$this->_assertTransactionState(Constants::iPAYMENT_REFUNDED_STATE);*/
        $this->assertTrue(true);

	}
	public function testDeclinedRefundWithAmericanExpress()
	{
	    $this->markTestIncomplete("Skipped as the test case tests an old integration which is no longer supported by NetAxept. To remedy this, the new NetAxept integration from branch: release/v2.02 needs to be merged in");
		/*$obj_Pay = simplexml_load_string($this->successfulPayTest(Constants::iNETAXEPT_PSP, self::sMERCHANT_ACCOUNT, self::sPSP_USERNAME, self::sPSP_PASSWORD, Constants::iAMEX_CARD) );
		$this->successfulAuthorizationTest($obj_Pay, "375700000000002", 05, 21, 603);
		
		$res = $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid, data) VALUES (". $this->_iTransactionID .", ". Constants::iPAYMENT_CAPTURED_STATE .", 'Faked')");
		$this->refundTest(502, 999, Constants::iPAYMENT_REFUNDED_STATE);
		$this->_assertTransactionState(Constants::iPAYMENT_REFUNDED_STATE, 0);
		
		$this->bIgnoreErrors = true;*/
        $this->assertTrue(true);
	}
	public function testSuccessfulCancelWithMasterCard()
	{
	    $this->markTestIncomplete("Skipped as the test case tests an old integration which is no longer supported by NetAxept. To remedy this, the new NetAxept integration from branch: release/v2.02 needs to be merged in");
		/*$obj_Pay = simplexml_load_string($this->successfulPayTest(Constants::iNETAXEPT_PSP, self::sMERCHANT_ACCOUNT, self::sPSP_USERNAME, self::sPSP_PASSWORD, Constants::iMASTERCARD) );
		$this->successfulAuthorizationTest($obj_Pay, "5413000000000000", 05, 21, 603);
		$this->refundTest(200, 1001, Constants::iPAYMENT_CANCELLED_STATE);
		$this->_assertTransactionState(Constants::iPAYMENT_CANCELLED_STATE);*/
        $this->assertTrue(true);
	}
	public function testSuccessfulCancelWithDeclinedCaptureUsingVISA()
	{
	    $this->markTestIncomplete("Skipped as the test case tests an old integration which is no longer supported by NetAxept. To remedy this, the new NetAxept integration from branch: release/v2.02 needs to be merged in");
		/*$obj_Pay = simplexml_load_string($this->successfulPayTest(Constants::iNETAXEPT_PSP, self::sMERCHANT_ACCOUNT, self::sPSP_USERNAME, self::sPSP_PASSWORD, Constants::iVISA_CARD) );
		$this->successfulAuthorizationTest($obj_Pay, "4925000000000079", 05, 21, 603);
		
		$this->captureTest(502, 999, Constants::iPAYMENT_CAPTURED_STATE);
		$this->_assertTransactionState(Constants::iPAYMENT_CAPTURED_STATE, 0);
		$this->_assertTransactionState(Constants::iPAYMENT_DECLINED_STATE);
		
		$this->refundTest(200, 1001, Constants::iPAYMENT_CANCELLED_STATE);
		$this->_assertTransactionState(Constants::iPAYMENT_CANCELLED_STATE);
	
		$this->bIgnoreErrors = true;*/
        $this->assertTrue(true);
	}
	public function testFailedCancelWithMaestro()
	{
	    $this->markTestIncomplete("Skipped as the test case tests an old integration which is no longer supported by NetAxept. To remedy this, the new NetAxept integration from branch: release/v2.02 needs to be merged in");
		/*$obj_Pay = simplexml_load_string($this->successfulPayTest(Constants::iNETAXEPT_PSP, self::sMERCHANT_ACCOUNT, self::sPSP_USERNAME, self::sPSP_PASSWORD, Constants::iMAESTRO_CARD) );
		$this->successfulAuthorizationTest($obj_Pay, "6761638084569584", 05, 21, 603);

		$this->captureTest(200, 1000, Constants::iPAYMENT_CAPTURED_STATE);
		$res = $this->queryDB("DELETE FROM Log.Message_Tbl WHERE txnid = ". $this->_iTransactionID ." AND stateid = ". Constants::iPAYMENT_CAPTURED_STATE);
		$this->_assertTransactionState(Constants::iPAYMENT_CAPTURED_STATE, 0);
		$this->refundTest(502, 999, Constants::iPAYMENT_CANCELLED_STATE);
		$this->_assertTransactionState(Constants::iPAYMENT_CANCELLED_STATE, 0);

		$this->bIgnoreErrors = true;*/
        $this->assertTrue(true);
	}
}