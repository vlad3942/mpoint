<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class CallbackAPITest extends baseAPITest
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
        $aMPOINT_CONN_INFO['path'] = "/callback/general.php";
        $aMPOINT_CONN_INFO["contenttype"] = "text/xml";
        $this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO) );
    }

    private function getTransStatus($iTransStatus)
    {
        switch ($iTransStatus)
        {
            case Constants::iPAYMENT_ACCEPTED_STATE:
                $status = '<status code="2000">Transaction is Authorized.</status>';
                break;
            case Constants::iPAYMENT_CAPTURED_STATE:
                $status = '<status code="2001">Transaction is Captured.</status>';
                break;
            case Constants::iPAYMENT_CANCELLED_STATE:
                $status = '<status code="2002">Transaction is Cancelled.</status>';
                break;
            case Constants::iPAYMENT_REFUNDED_STATE:
                $status = '<status code="2003">Transaction is Refunded.</status>';
                break;
            default:
                $status = '<status code="999">Unknown Error.</status>';
        }
        return $status;
    }

    public function getCallbackDoc($transactionId, $orderId, $pspID, $iTransStatus, $bSendToken = true)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<root>';
        $xml .= '<callback>';
        $xml .= '<psp-config id="'.$pspID.'">';
        $xml .= '<name>CellpointMobileCOM</name>';
        $xml .= '</psp-config>';
        $xml .= '<transaction id="'.$transactionId.'" order-no="'.$orderId.'" external-id="-1">';
        $xml .= '<amount country-id="100" currency="DKK">5000</amount>';
        $xml .= '<card type-id="8">';
        $xml .= '<card-number>401200******6002</card-number>';
        if($bSendToken == true) {
            $xml .= '<token>4819253888096002</token>';
        }
        $xml .= '<expiry>';
        $xml .= '<month>01</month>';
        $xml .= '<year>20</year>';
        $xml .= '</expiry>';
        $xml .= '</card>';
        $xml .= '</transaction>';
        $xml .= $this->getTransStatus($iTransStatus);
        $xml .= '<approval-code>035747</approval-code>';
        $xml .= '</callback>';
        $xml .= '</root>';

        return $xml;
    }


    public function successfulCallbackAccepted($pspID, $iTransStatus)
    {
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, ".$pspID.", '1')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (113, 8, $pspID)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '900-55150298', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, pspid, callbackurl, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1001001, '900-55150298', 100, 113, 1100, 100, null, '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,5000)");
        $this->queryDB("INSERT INTO log.message_tbl (txnid, stateid) VALUES (1001001, $iTransStatus)");
        $this->queryDB("INSERT INTO log.message_tbl (txnid, stateid) VALUES (1001001, 1991)");
        $this->queryDB("INSERT INTO log.message_tbl (txnid, stateid) VALUES (1001001, 1992)");
        $this->queryDB("INSERT INTO log.message_tbl (txnid, stateid) VALUES (1001001, 1990)");

        $xml = $this->getCallbackDoc(1001001, '900-55150298', $pspID, $iTransStatus);
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(202, $iStatus);
        $this->assertEquals("", $sReplyBody);

        $res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001");
        $this->assertTrue(is_resource($res) );

        $aStates = array();
        while ($row = pg_fetch_assoc($res) )
        {
            $aStates[] = $row["stateid"];
        }

        $this->assertEquals(4, count($aStates));
        $this->assertTrue(is_int(array_search($iTransStatus, $aStates) ) );
    }

    public function successfulAutoCapture($pspID, $iTransStatus)
    {
        $this->bIgnoreErrors = true;
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, ".$pspID.", '1')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, capture_type) VALUES (113, 8, $pspID, 3)");

        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");

        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '900-55150298', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, pspid, callbackurl, amount, ip, enabled, keywordid, sessionid, auto_capture,convertedamount) VALUES (1001001, '900-55150298', 100, 113, 1100, 100, null, '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1, 3,5000)");

        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001001, 5000,208,". Constants::iInitializeRequested. ",NULL,'done',113)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001001, 5000,208,NULL,". Constants::iINPUT_VALID_STATE. ",'done',100,113)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (102,1001001, 5000,208,". Constants::iAuthorizeRequested. ",NULL,'done',113)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (103,1001001, 5000,208,NULL,". Constants::iPAYMENT_ACCEPTED_STATE. ",'inprogress',102,113)");

        $xml = $this->getCallbackDoc(1001001, '900-55150298', $pspID, $iTransStatus, false);
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(202, $iStatus);
        $this->assertEquals("", $sReplyBody);

        $res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001");
        $this->assertTrue(is_resource($res) );

        $aStates = array();
        $cStates = array();
        $retries = 0;
        while ($retries++ <= 5)
        {
            $res = $this->queryDB("SELECT t.extid, t.pspid, t.amount, m.stateid FROM Log.Transaction_Tbl t, Log.Message_Tbl m WHERE m.txnid = t.id AND t.id = 1001001 ORDER BY m.id ASC");
            $this->assertTrue(is_resource($res) );
            $aStates = array();
            $trow = null;
            while ($row = pg_fetch_assoc($res) )
            {
                $trow = $row;
                $aStates[] = $row["stateid"];
            }
            if (count($aStates) >= 9) { break; }
            usleep(200000);// As callback happens asynchroniously, sleep a bit here in order to wait for transaction to complete in other thread
        }

        $this->assertTrue(is_int(array_search(Constants::iPAYMENT_ACCEPTED_STATE, $aStates) ) );
        $this->assertTrue(is_int(array_search(Constants::iPAYMENT_CAPTURED_STATE, $aStates) ) );

        $captureStateStatus = $this->queryDB("SELECT status FROM Log.Txnpassbook_Tbl WHERE transactionid = 1001001 and performedopt = 2000");
        $this->assertTrue(is_resource($captureStateStatus));
        while ($row = pg_fetch_assoc($captureStateStatus))
        {
			$cStates[] = $row["status"];
        }
        $this->assertEquals(Constants::sPassbookStatusDone, $cStates[0]);
    }

    public function successfulNoAutoCapture($pspID, $iTransStatus)
    {
        $this->bIgnoreErrors=true;
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, ".$pspID.", '1')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, capture_type) VALUES (113, 8, $pspID, 1)");

        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");

        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '900-55150298', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, pspid, callbackurl, amount, ip, enabled, keywordid, sessionid, auto_capture,convertedamount) VALUES (1001001, '900-55150298', 100, 113, 1100, 100, null, '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1, 0,5000)");

        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001001, 5000,208,". Constants::iInitializeRequested. ",NULL,'done',113)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001001, 5000,208,NULL,". Constants::iINPUT_VALID_STATE. ",'done',100,113)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (102,1001001, 5000,208,". Constants::iAuthorizeRequested. ",NULL,'done',113)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (103,1001001, 5000,208,NULL,". Constants::iPAYMENT_ACCEPTED_STATE. ",'inprogress',102,113)");

        $xml = $this->getCallbackDoc(1001001, '900-55150298', $pspID, $iTransStatus, false);
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(202, $iStatus);
        $this->assertEquals("", $sReplyBody);

        $res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001");
        $this->assertTrue(is_resource($res) );

        $aStates = array();
        $retries = 0;
        while ($retries++ <= 5)
        {
            $res = $this->queryDB("SELECT t.extid, t.pspid, t.amount, m.stateid FROM Log.Transaction_Tbl t, Log.Message_Tbl m WHERE m.txnid = t.id AND t.id = 1001001 ORDER BY m.id ASC");
            $this->assertTrue(is_resource($res) );
            $aStates = array();
            $trow = null;
            while ($row = pg_fetch_assoc($res) )
            {
                $trow = $row;
                $aStates[] = $row["stateid"];
            }
            if (count($aStates) >= 4) { break; }
            usleep(200000);// As callback happens asynchroniously, sleep a bit here in order to wait for transaction to complete in other thread
        }

        $this->assertTrue(is_int(array_search(Constants::iPAYMENT_ACCEPTED_STATE, $aStates)));
        $this->assertFalse(is_int(array_search(Constants::iPAYMENT_CAPTURED_STATE, $aStates)));
    }

    public function successfulOneStepAuthorizationAutoCapture($pspID, $iTransStatus)
    {
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, ".$pspID.", '1')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, capture_type) VALUES (113, 8, $pspID, 2)");

		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', 'profilePass', TRUE)");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");

		$this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '900-55150298', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
		$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, pspid, callbackurl, amount, ip, enabled, keywordid, sessionid, auto_capture,convertedamount) VALUES (1001001, '900-55150298', 1, 113, 1100, 100, null, '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1, 2,5000)");

		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001001, 5000,208,". Constants::iInitializeRequested. ",NULL,'done',113)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001001, 5000,208,NULL,". Constants::iINPUT_VALID_STATE. ",'done',100,113)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (102,1001001, 5000,208,". Constants::iAuthorizeRequested. ",NULL,'done',113)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (103,1001001, 5000,208,NULL,". Constants::iPAYMENT_ACCEPTED_STATE. ",'inprogress',102,113)");

		$xml = $this->getCallbackDoc(1001001, '900-55150298', $pspID, $iTransStatus, false);
		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(202, $iStatus);
		$this->assertEquals("", $sReplyBody);

		$aStates = array();
		$cStates = array();

		$res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001");
		$this->assertTrue(is_resource($res) );

		while ($row = pg_fetch_assoc($res) )
		{
			$aStates[] = $row["stateid"];
		}

		$retries = 0;
		while ($retries++ <= 5)
		{
			$res = $this->queryDB("SELECT t.extid, t.pspid, t.amount, m.stateid FROM Log.Transaction_Tbl t, Log.Message_Tbl m WHERE m.txnid = t.id AND t.id = 1001001 ORDER BY m.id ASC");
			$this->assertTrue(is_resource($res) );
			$aStates = array();
			$trow = null;
			while ($row = pg_fetch_assoc($res) )
			{
				$trow = $row;
				$aStates[] = $row["stateid"];
			}
			if (count($aStates) >= 9) { break; }
			usleep(200000);// As callback happens asynchroniously, sleep a bit here in order to wait for transaction to complete in other thread
		}

		$this->assertTrue(is_int(array_search(Constants::iPAYMENT_ACCEPTED_STATE, $aStates) ) );
		$this->assertFalse(is_int(array_search(Constants::iPAYMENT_CAPTURED_STATE, $aStates) ) );

		$captureStateStatus = $this->queryDB("SELECT status FROM Log.Txnpassbook_Tbl WHERE transactionid = 1001001 and performedopt = 2000");
		$this->assertTrue(is_resource($captureStateStatus));
		while ($row = pg_fetch_assoc($captureStateStatus))
		{
			$cStates[] = $row["status"];
		}
		$this->assertEquals(Constants::sPassbookStatusDone, $cStates[0]);
    }

    public function testCallbackAttempt($pspID, $iTransStatus)
    {
        $this->bIgnoreErrors = true;
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/invalidURL.php";
        $this->queryDB("INSERT INTO CLIENT.CLIENT_TBL (ID, FLOWID, COUNTRYID, NAME, USERNAME, PASSWD) VALUES (113, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
        $this->queryDB("INSERT INTO CLIENT.URL_TBL (CLIENTID, URLTYPEID, URL) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO CLIENT.ACCOUNT_TBL (ID, CLIENTID) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO CLIENT.KEYWORD_TBL (ID, CLIENTID, NAME, STANDARD) VALUES (1, 113, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, " . $pspID . ", '1')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (113, 8, $pspID)");
        $this->queryDB("INSERT INTO LOG.SESSION_TBL (ID, CLIENTID, ACCOUNTID, CURRENCYID, COUNTRYID, STATEID, ORDERID, AMOUNT, MOBILE, DEVICEID, IPADDRESS, EXTERNALID, SESSIONTYPEID) VALUES (1, 113, 1100, 208, 100, 4001, '900-55150298', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, pspid, callbackurl, amount, ip, enabled, keywordid, sessionid, producttype) VALUES (1001001, '900-55150298', 100, 113, 1100, 100, null, '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1, 100)");
        //$this->queryDB("INSERT INTO log.message_tbl (txnid, stateid) VALUES (1001001, $iTransStatus)");
        //$this->queryDB("INSERT INTO log.message_tbl (txnid, stateid) VALUES (1001001, 1991)");
        //$this->queryDB("INSERT INTO log.message_tbl (txnid, stateid) VALUES (1001001, 1992)");
        //$this->queryDB("INSERT INTO log.message_tbl (txnid, stateid) VALUES (1001001, 1990)");

        $this->queryDB("INSERT INTO LOG.TXNPASSBOOK_TBL (ID,TRANSACTIONID,AMOUNT,CURRENCYID,REQUESTEDOPT,PERFORMEDOPT,STATUS,CLIENTID) VALUES (100,1001001, 5000,208," . Constants::iInitializeRequested . ",NULL,'done',113)");
        $this->queryDB("INSERT INTO LOG.TXNPASSBOOK_TBL (ID,TRANSACTIONID,AMOUNT,CURRENCYID,REQUESTEDOPT,PERFORMEDOPT,STATUS,EXTREF,CLIENTID) VALUES (101,1001001, 5000,208,NULL," . Constants::iINPUT_VALID_STATE . ",'done',100,113)");
        $this->queryDB("INSERT INTO LOG.TXNPASSBOOK_TBL (ID,TRANSACTIONID,AMOUNT,CURRENCYID,REQUESTEDOPT,PERFORMEDOPT,STATUS,CLIENTID) VALUES (102,1001001, 5000,208," . Constants::iAuthorizeRequested . ",NULL,'done',113)");
        $this->queryDB("INSERT INTO LOG.TXNPASSBOOK_TBL (ID,TRANSACTIONID,AMOUNT,CURRENCYID,REQUESTEDOPT,PERFORMEDOPT,STATUS,EXTREF,CLIENTID) VALUES (103,1001001, 5000,208,NULL," . Constants::iPAYMENT_ACCEPTED_STATE . ",'inprogress',102,113)");

        /*if ($iTransStatus === Constants::iPAYMENT_CAPTURED_STATE) {
            $this->queryDB("INSERT INTO LOG.TXNPASSBOOK_TBL (ID,TRANSACTIONID,AMOUNT,CURRENCYID,REQUESTEDOPT,PERFORMEDOPT,STATUS,CLIENTID) VALUES (104,1001001, 5000,208," . Constants::iCaptureRequested . ",NULL,'done',113)");
            $this->queryDB("INSERT INTO LOG.TXNPASSBOOK_TBL (ID,TRANSACTIONID,AMOUNT,CURRENCYID,REQUESTEDOPT,PERFORMEDOPT,STATUS,EXTREF,CLIENTID) VALUES (105,1001001, 5000,208,NULL," . Constants::iPAYMENT_CAPTURED_STATE . ",'inprogress',104,113)");
        } elseif ($iTransStatus === Constants::iPAYMENT_CAPTURED_STATE) {
            $this->queryDB("INSERT INTO LOG.TXNPASSBOOK_TBL (ID,TRANSACTIONID,AMOUNT,CURRENCYID,REQUESTEDOPT,PERFORMEDOPT,STATUS,CLIENTID) VALUES (104,1001001, 5000,208," . Constants::iCancelRequested . ",NULL,'done',113)");
            $this->queryDB("INSERT INTO LOG.TXNPASSBOOK_TBL (ID,TRANSACTIONID,AMOUNT,CURRENCYID,REQUESTEDOPT,PERFORMEDOPT,STATUS,EXTREF,CLIENTID) VALUES (105,1001001, 5000,208,NULL," . Constants::iPAYMENT_CANCELLED_STATE . ",'inprogress',104,113)");
        } elseif ($iTransStatus === Constants::iPAYMENT_CAPTURED_STATE) {
            $this->queryDB("INSERT INTO LOG.TXNPASSBOOK_TBL (ID,TRANSACTIONID,AMOUNT,CURRENCYID,REQUESTEDOPT,PERFORMEDOPT,STATUS,CLIENTID) VALUES (104,1001001, 5000,208," . Constants::iRefundRequested . ",NULL,'done',113)");
            $this->queryDB("INSERT INTO LOG.TXNPASSBOOK_TBL (ID,TRANSACTIONID,AMOUNT,CURRENCYID,REQUESTEDOPT,PERFORMEDOPT,STATUS,EXTREF,CLIENTID) VALUES (105,1001001, 5000,208,NULL," . Constants::iPAYMENT_REFUNDED_STATE . ",'inprogress',104,113)");
        }*/

        $this->queryDB("INSERT INTO CLIENT.SUREPAY_TBL (CLIENTID, RESEND, NOTIFY, EMAIL, ENABLED, MAX) VALUES (113, 1, 10, NULL, TRUE, 3);");

        $xml = $this->getCallbackDoc(1001001, '900-55150298', $pspID, $iTransStatus);
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(202, $iStatus);
        $this->assertEquals("", $sReplyBody);


        $affectedRows=0;
        $retries = 0;
        while ($retries++ <= 10) {
            $res = $this->queryDB("SELECT STATEID FROM LOG.MESSAGE_TBL WHERE TXNID = 1001001 AND STATEID=1999");
            $this->assertTrue(is_resource($res));
            $affectedRows=0;
            while ($row = pg_fetch_assoc($res)) {
               $affectedRows++;
            }
            if ($affectedRows >= 6) {
                break;
            }
            sleep(2);// As callback happens asynchroniously, sleep a bit here in order to wait for transaction to complete in other thread
        }
		$this->assertEquals(6, $affectedRows);
    }

}
