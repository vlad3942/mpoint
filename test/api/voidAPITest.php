<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

abstract class VoidAPITest extends baseAPITest
{
    protected $_aMPOINT_CONN_INFO;

    public function __construct()
    {
        $this->constHTTPClient();
    }

    public function constHTTPClient()
    {
        global $aMPOINT_CONN_INFO;
        $aMPOINT_CONN_INFO['path'] = "/mApp/api/void.php";
        $aMPOINT_CONN_INFO["contenttype"] = "text/xml";
        $this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO) );
    }

    protected function getAuthDoc($client, $account, $txnid, $amount, $orderno, $orderref)
    {
		$xml = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml .= '<root>';
		$xml .= '<void client-id="'. $client .'" account="'. $account .'">';
		$xml .= '<transaction id="'. $txnid .'" order-no="'.$orderno.'" order-ref="'.$orderref.'">';
		$xml .= '<amount country-id="100">'.$amount .'</amount>';
		$xml .= '</transaction>';
		$xml .= '</void>';
		$xml .= '</root>';

		return $xml;
    }

    protected function testSuccessfulRefund($pspID)
    {
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Admin.User_Tbl (id, countryid, firstname, lastname, email, username, passwd) VALUES (1, 100, 'Test', 'TestTest', 'test@cellpointmobile.com', 'Tusername', 'Tpassword')");
		$this->queryDB("INSERT INTO Admin.Access_Tbl (id, clientid, userid) VALUES (1, 113, 1)");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name, username, passwd) VALUES (1, 113, $pspID, '1', 'Tusername', 'Tpassword')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (113, 17, $pspID)");
		$this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '1513-2001', 5000, 29612109, '', '127.0.0.1', -1, 1);");
		$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1001001,". Constants::iPURCHASE_VIA_APP .", 113, 1100, 100, $pspID, '1515', '1513-2001', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,5000)");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_CAPTURED_STATE. ")");

		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001001, 5000,208,". Constants::iInitializeRequested. ",NULL,'done',113)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001001, 5000,208,NULL,". Constants::iINPUT_VALID_STATE. ",'done',100,113)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (102,1001001, 5000,208,". Constants::iAuthorizeRequested. ",NULL,'done',113)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (103,1001001, 5000,208,NULL,". Constants::iPAYMENT_ACCEPTED_STATE. ",'done',102,113)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (104,1001001, 5000,208,". Constants::iCaptureRequested. ",NULL,'done',113)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (105,1001001, 5000,208,NULL,". Constants::iPAYMENT_CAPTURED_STATE. ",'done',104,113)");

		$xml = $this->getAuthDoc(113, 1100, 1001001, 5000, '1513-2001', 123456);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><transactions client-id="113"><transaction id="1001001" order-no="1513-2001" order-ref="123456"><status code="1000"></status></transaction></transactions></root>',$sReplyBody);

		//log.message table should contain refund state
		$res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001");
		$this->assertTrue(is_resource($res) );

		$aStates = array();
		while ($row = pg_fetch_assoc($res) )
		{
			$aStates[] = $row["stateid"];
		}
		$this->assertTrue(is_int(array_search(Constants::iPAYMENT_REFUNDED_STATE, $aStates) ) );

		//Check refund amount got updated in log.transaction_tbl
		$res =  $this->queryDB("SELECT refund FROM Log.transaction_Tbl WHERE id = 1001001");
		$this->assertTrue(is_resource($res) );

		$aRefundAmount = array();
		$rStates = array();
		while ($row = pg_fetch_assoc($res) )
		{
			$aRefundAmount[] = $row["refund"];
		}
		$this->assertEquals(5000, $aRefundAmount[0]);

		//log.txnpassbook_tbl should have refund state status as "done"
		$refundStateStatus = $this->queryDB("SELECT status FROM Log.Txnpassbook_Tbl WHERE transactionid = 1001001 and performedopt = 2003");
		$this->assertTrue(is_resource($refundStateStatus));
		while ($row = pg_fetch_assoc($refundStateStatus))
		{
			$rStates[] = $row["status"];
		}
		$this->assertEquals(Constants::sPassbookStatusDone, $rStates[0]);
    }

    protected function testSuccessfulCancelTriggeredByVoid($pspID)
    {
    	$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
    	
    	$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
    	$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
    	$this->queryDB("INSERT INTO Admin.User_Tbl (id, countryid, firstname, lastname, email, username, passwd) VALUES (1, 100, 'Test', 'TestTest', 'test@cellpointmobile.com', 'Tusername', 'Tpassword')");
    	$this->queryDB("INSERT INTO Admin.Access_Tbl (id, clientid, userid) VALUES (1, 113, 1)");
    	$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
    	$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
    	$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name, username, passwd) VALUES (1, 113, $pspID, '1', 'Tusername', 'Tpassword')");
    	$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
    	$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (113, 17, $pspID)");
    	$this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '1513-2001', 5000, 29612109, '', '127.0.0.1', -1, 1);");
    	$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1001001,". Constants::iPURCHASE_VIA_APP .", 113, 1100, 100, $pspID, '1515', '1513-2001', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,5000)");
    	$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");
    	$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_CAPTURED_STATE. ")");
    	
    	$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001001, 5000,208,". Constants::iInitializeRequested. ",NULL,'done',113)");
    	$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001001, 5000,208,NULL,". Constants::iINPUT_VALID_STATE. ",'done',100,113)");
    	$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (102,1001001, 5000,208,". Constants::iAuthorizeRequested. ",NULL,'done',113)");
    	$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (103,1001001, 5000,208,NULL,". Constants::iPAYMENT_ACCEPTED_STATE. ",'done',102,113)");
    	
    	$xml = $this->getAuthDoc(113, 1100, 1001001, 5000, '1513-2001', 123456);
    	
    	$this->_httpClient->connect();
    	
    	$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
    	$sReplyBody = $this->_httpClient->getReplyBody();

    	$this->assertEquals(200, $iStatus);
    	$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><transactions client-id="113"><transaction id="1001001" order-no="1513-2001" order-ref="123456"><status code="1000"></status></transaction></transactions></root>',$sReplyBody);

    	//log.message table should contain cancel state
    	$res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001");
    	$this->assertTrue(is_resource($res));
    	$aStates = array();
    	$cStates = array();
    	while ($row = pg_fetch_assoc($res))
    	{
    		$aStates[] = $row["stateid"];
    	}
    	$this->assertTrue(is_int(array_search(Constants::iPAYMENT_CANCELLED_STATE, $aStates) ) );

    	//log.txnpassbook_tbl should have cancel state status as "done"
    	$cancelStateStatus = $this->queryDB("SELECT status FROM Log.Txnpassbook_Tbl WHERE transactionid = 1001001 and performedopt = 2002");
    	$this->assertTrue(is_resource($cancelStateStatus));
    	while ($row = pg_fetch_assoc($cancelStateStatus))
    	{
    		$cStates[] = $row["status"];
    	}
    	$this->assertEquals(Constants::sPassbookStatusDone, $cStates[0]);
    }
}
