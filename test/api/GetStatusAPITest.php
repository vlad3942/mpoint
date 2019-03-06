<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class GetStatusAPITest extends baseAPITest
{
	protected $_aMPOINT_CONN_INFO;

	public function __construct()
	{
		$this->constHTTPClient();
	}

	public function constHTTPClient()
	{
		global $aMPOINT_CONN_INFO;
		$aConnInfo = $aMPOINT_CONN_INFO;
		$aConnInfo['path'] = "/mApp/api/get_status.php";
		$aConnInfo["contenttype"] = "text/xml";
		$this->_aMPOINT_CONN_INFO = $aConnInfo;
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($this->_aMPOINT_CONN_INFO));
	}

    protected function getGetStatusDoc($client, $txn_id, $order_id)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<root>';
        $xml .= '<get-status client-id="'. $client .'">';
        $xml .= '<transactions>';
        $xml .= '<transaction id="'. $txn_id .'" order-no="'. $order_id .'" />';
        $xml .= '</transactions>';
        $xml .= '<client-info language="us" version="1.28" platform="iOS/9.0">';
        $xml .= '<mobile operator-id="20000" country-id="608">8767649198</mobile>';
        $xml .= '<email>gaurav.pawar@cellpointmobile.com</email>';
        $xml .= '<ip>127.0.0.1</ip>';
        $xml .= '</client-info>';
        $xml .= '</get-status>';
        $xml .= '</root>';

        return $xml;
    }


	public function testGetStatusForAcceptedTransaction()
	{
		$pspID = Constants::iWORLDPAY_PSP;
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
		$sCreated = date("Y-m-d H:i:s", time()-3600*10); //Now -10 hours

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name, username, passwd) VALUES (1, 113, $pspID, '1', 'Tusername', 'Tpassword')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (113, 17, $pspID)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '1513-2001', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, created, enabled, keywordid, fee, currencyid, sessionid) VALUES (1001001, 100, 113, 1100, 100, $pspID, '1512', '1513-2001', '". $sCallbackURL. "', 5000, '127.0.0.1', '". $sCreated ."', TRUE, 1, 50, 208, 1)");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iINPUT_VALID_STATE. ")");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_INIT_WITH_PSP_STATE. ")");


        $xml = $this->getGetStatusDoc(113, 1001001, '1513-2001');

		$this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tusername', 'Tpassword'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->bIgnoreErrors = true;
        $this->assertEquals(200, $iStatus);
        $this->assertContains('state-id="2000', $sReplyBody);

	}

	public function testGetStatusForCancelledTransaction()
	{
        $pspID = Constants::iWORLDPAY_PSP;
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
        $sCreated = date("Y-m-d H:i:s", time()-3600*10); //Now -10 hours

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name, username, passwd) VALUES (1, 113, $pspID, '1', 'Tusername', 'Tpassword')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (113, 17, $pspID)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '1513-2001', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, created, enabled, keywordid, fee, currencyid, sessionid) VALUES (1001004, 100, 113, 1100, 100, $pspID, '1512', '1513-2001', '". $sCallbackURL. "', 5000, '127.0.0.1', '". $sCreated ."', TRUE, 1, 50, 208, 1)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001004, ". Constants::iINPUT_VALID_STATE. ")");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001004, ". Constants::iPAYMENT_INIT_WITH_PSP_STATE. ")");


        $xml = $this->getGetStatusDoc(113, 1001004, '1513-2001');

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tusername', 'Tpassword'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->bIgnoreErrors = true;
        $this->assertEquals(200, $iStatus);
        $this->assertContains('state-id="2002', $sReplyBody);
	}

    public function testGetStatusForCapturedTransaction()
    {
        $pspID = Constants::iWORLDPAY_PSP;
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
        $sCreated = date("Y-m-d H:i:s", time()-3600*10); //Now -10 hours

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name, username, passwd) VALUES (1, 113, $pspID, '1', 'Tusername', 'Tpassword')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (113, 17, $pspID)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '1513-2001', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, created, enabled, keywordid, fee, currencyid, sessionid) VALUES (1001002, 100, 113, 1100, 100, $pspID, '1512', '1513-2001', '". $sCallbackURL. "', 5000, '127.0.0.1', '". $sCreated ."', TRUE, 1, 50, 208, 1)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001002, ". Constants::iINPUT_VALID_STATE. ")");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001002, ". Constants::iPAYMENT_INIT_WITH_PSP_STATE. ")");


        $xml = $this->getGetStatusDoc(113, 1001002, '1513-2001');

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tusername', 'Tpassword'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->bIgnoreErrors = true;
        $this->assertEquals(200, $iStatus);
        $this->assertContains('state-id="2001', $sReplyBody);
    }
}
