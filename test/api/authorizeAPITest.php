<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

abstract class AuthorizeAPITest extends baseAPITest
{
	protected $_aMPOINT_CONN_INFO;

	public function __construct()
	{
		$this->constHTTPClient();
	}

	public function constHTTPClient()
	{
		global $aMPOINT_CONN_INFO;
		$aMPOINT_CONN_INFO['path'] = "/mApp/api/authorize.php";
		$aMPOINT_CONN_INFO["contenttype"] = "text/xml";
		$this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
		$this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO));
	}

	protected function getAuthDoc($client, $account, $txn=1, $amount=100, $euaPasswd='', $intAccountId=0, $clientpasswd='', $currecyid = null,$hmac=null,$cardid=2,$aDccParams=null)
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml .= '<root>';
		$xml .= '<authorize-payment client-id="'. $client .'" account="'. $account .'">';
		$xml .= '<transaction id="'. $txn .'"';
        $xml .='>';
		$xml .= '<card id="61775" type-id="'.$cardid.'">';
		$xml .= '<amount country-id="100"';
		if(isset($currecyid) === true)
		    $xml .= ' currency-id="'.$currecyid.'"';
		$xml .= '>'. $amount .'</amount>';
//		$xml .= '<card-number>5272342200069702</card-number>';
//		$xml .= '<expiry>03/31</expiry>';
//		$xml .= '<cryptogram type="3ds">AKh96OOsGf2HAIDEhKulAoABFA==</cryptogram>';
		$xml .= '</card>';
        if(isset($hmac)=== true) $xml .= '<hmac>'.$hmac.'</hmac>';
        if(isset($aDccParams))
        {
            $xml .= '<foreign-exchange-info>';
            if(empty($aDccParams[0]) === false)
            {
                $xml .= '<id>'.$aDccParams[0].'</id>';
            }
            if(empty($aDccParams[1]) === false)
            {
                $xml .= '<conversion-rate>'.$aDccParams[1].'</conversion-rate>';
            }

            if(empty($aDccParams[2]) === false) { $xml .= '<sale-currencyid>'.$aDccParams[2].'</sale-currencyid>'; }
            if(empty($aDccParams[3]) === false) { $xml .= '<sale-amount>'.$aDccParams[3].'</sale-amount>'; }
            $xml .= '</foreign-exchange-info>';
        }
		$xml .= '</transaction>';
		if ($intAccountId > 0)
		{
			$secret = sha1($client. $clientpasswd);
			$xml .= '<auth-token>'. htmlspecialchars(General::genToken($intAccountId, $secret), ENT_NOQUOTES) .'</auth-token>';
		}
		else
		{
			$xml .= '<password>'. $euaPasswd. '</password>';
		}
		$xml .= '<client-info platform="iOS" version="1.00" language="da">';
		$xml .= '<mobile country-id="100" operator-id="10000">28882861</mobile>';
		$xml .= '<email>jona@oismail.com</email>';
		$xml .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
		$xml .= '</client-info>';
		$xml .= '</authorize-payment>';
		$xml .= '</root>';

		return $xml;
	}

	protected function testSuccessfulAuthorize($pspID)
	{
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 2)");
		//As per talk with Jona and Simon 2016-07-19 it should not be possible to authorize a disabled card, since the client can ignore flags sent from initialize
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (113, 2, $pspID, true, 1)");
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '501910******3742', '06/24', TRUE, 113, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid,sessionid,convertedamount,convetredcurrencyid) VALUES (1001001, 100, 113, 1100, 1,  $pspID, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE, 208, 1,5000,208)");

		$xml = $this->getAuthDoc(113, 1100, 1001001, 5000, 'profilePass');

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="100">Payment Authorized using Stored Card</status></root>', $sReplyBody);

		$res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY ID ASC");
		$this->assertTrue(is_resource($res) );

		$aStates = array();
		while ($row = pg_fetch_assoc($res) )
		{
			$aStates[] = $row["stateid"];
		}

		//TODO: Rewrite test so it supports both Netaxept and DIBS. Netaxept completes the txn within the callback, DIBS does it during the authorize API flow
		if ($pspID == Constants::iDIBS_PSP) { $this->assertEquals(5, count($aStates) ); }
		else { $this->assertEquals(3, count($aStates) ); }

		$s = 0;
		//TODO: Rewrite test so it supports both Netaxept and DIBS. Netaxept completes the txn within the callback, DIBS does it during the authorize API flow
		$this->assertEquals(Constants::iPAYMENT_WITH_ACCOUNT_STATE, $aStates[$s++]);
		if ($pspID == Constants::iNETAXEPT_PSP) { $this->assertEquals(Constants::iCARD_PURCHASE_TYPE, $aStates[$s++]); }
		$this->assertEquals(Constants::iPAYMENT_ACCEPTED_STATE, $aStates[$s++]);
		if ($pspID == Constants::iDIBS_PSP)
		{
			$this->assertEquals(Constants::iCB_CONSTRUCTED_STATE, $aStates[$s++]);
			$this->assertEquals(Constants::iCB_CONNECTED_STATE, $aStates[$s++]);
			$this->assertEquals(Constants::iCB_ACCEPTED_STATE, $aStates[$s++]);
		}

		/* Test that euaid has been set on txn */
		$res =  $this->queryDB("SELECT t.euaid, et.accountid FROM Log.Transaction_Tbl t LEFT JOIN Enduser.Transaction_Tbl et ON et.txnid = t.id WHERE t.id = 1001001");
		$this->assertTrue(is_resource($res) );
		$row = pg_fetch_assoc($res);

		$this->assertEquals(5001, $row["euaid"]);
		//TODO: Rewrite test so it supports both Netaxept and DIBS. Netaxept completes the txn within the callback, DIBS does it during the authorize API flow
		if ($pspID == Constants::iDIBS_PSP) { $this->assertEquals(5001, $row["accountid"]); }
	}

    protected function testSuccessfulAuthorizeWithCurrency($pspID)
    {
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-840, 2)");
        //As per talk with Jona and Simon 2016-07-19 it should not be possible to authorize a disabled card, since the client can ignore flags sent from initialize
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (113, 2, $pspID, true, 1)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '501910******3742', '06/24', TRUE, 113, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 840, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,currencyid,sessionid,convertedamount,convetredcurrencyid) VALUES (1001001, 100, 113, 1100, 1,  $pspID, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE, 840, 1,5000,840)");
        $this->queryDB("INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid, enabled) VALUES (113,100,840, true)");
        $xml = $this->getAuthDoc(113, 1100, 1001001, 5000, 'profilePass', 0,'',840);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="100">Payment Authorized using Stored Card</status></root>', $sReplyBody);

        $res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY ID ASC");
        $this->assertTrue(is_resource($res) );

        $aStates = array();
        while ($row = pg_fetch_assoc($res) )
        {
            $aStates[] = $row["stateid"];
        }

        //TODO: Rewrite test so it supports both Netaxept and DIBS. Netaxept completes the txn within the callback, DIBS does it during the authorize API flow
        if ($pspID == Constants::iDIBS_PSP) { $this->assertEquals(5, count($aStates) ); }
        else { $this->assertEquals(3, count($aStates) ); }

        $s = 0;
        //TODO: Rewrite test so it supports both Netaxept and DIBS. Netaxept completes the txn within the callback, DIBS does it during the authorize API flow
        $this->assertEquals(Constants::iPAYMENT_WITH_ACCOUNT_STATE, $aStates[$s++]);
        if ($pspID == Constants::iNETAXEPT_PSP) { $this->assertEquals(Constants::iCARD_PURCHASE_TYPE, $aStates[$s++]); }
        $this->assertEquals(Constants::iPAYMENT_ACCEPTED_STATE, $aStates[$s++]);
        if ($pspID == Constants::iDIBS_PSP)
        {
            $this->assertEquals(Constants::iCB_CONSTRUCTED_STATE, $aStates[$s++]);
            $this->assertEquals(Constants::iCB_CONNECTED_STATE, $aStates[$s++]);
            $this->assertEquals(Constants::iCB_ACCEPTED_STATE, $aStates[$s++]);
        }

        /* Test that euaid has been set on txn */
        $res =  $this->queryDB("SELECT t.euaid, et.accountid FROM Log.Transaction_Tbl t LEFT JOIN Enduser.Transaction_Tbl et ON et.txnid = t.id WHERE t.id = 1001001");
        $this->assertTrue(is_resource($res) );
        $row = pg_fetch_assoc($res);

        $this->assertEquals(5001, $row["euaid"]);
        //TODO: Rewrite test so it supports both Netaxept and DIBS. Netaxept completes the txn within the callback, DIBS does it during the authorize API flow
        if ($pspID == Constants::iDIBS_PSP) { $this->assertEquals(5001, $row["accountid"]); }
    }

	protected function testSuccessfulAuthorizeIncludingAutoCapture($pspID)
	{
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 2)");
		//As per talk with Jona and Simon 2016-07-19 it should not be possible to authorize a disabled card, since the client can ignore flags sent from initialize
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid,capture_type) VALUES (113, 2, $pspID, true, 1, 3)");
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '501910******3742', '06/24', TRUE, 113, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, auto_capture, enabled,sessionid,convertedamount) VALUES (1001001, 100, 113, 1100, 1,  $pspID, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', 3, TRUE, 1,5000)");

		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001001, 5000,208,". Constants::iInitializeRequested. ",NULL,'done',113)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001001, 5000,208,NULL,". Constants::iINPUT_VALID_STATE. ",'done',100,113)");

		$xml = $this->getAuthDoc(113, 1100, 1001001, 5000, 'profilePass');

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="100">Payment Authorized using Stored Card</status></root>', $sReplyBody);

		$res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001");
		$this->assertTrue(is_resource($res) );

		$aStates = array();
		while ($row = pg_fetch_assoc($res) )
		{
			$aStates[] = $row["stateid"];
		}

		//TODO: Rewrite test so it supports both Netaxept and DIBS. Netaxept completes the txn within the callback, DIBS does it during the authorize API flow
		if ($pspID == Constants::iDIBS_PSP) { $this->assertEquals(9, count($aStates) ); }
		else { $this->assertEquals(7, count($aStates) ); }

		$s = 0;
		//TODO: Rewrite test so it supports both Netaxept and DIBS. Netaxept completes the txn within the callback, DIBS does it during the authorize API flow
		$this->assertEquals(Constants::iPAYMENT_WITH_ACCOUNT_STATE, $aStates[$s++]);
		if ($pspID == Constants::iNETAXEPT_PSP) { $this->assertEquals(Constants::iCARD_PURCHASE_TYPE, $aStates[$s++]); }
		$this->assertEquals(Constants::iPAYMENT_ACCEPTED_STATE, $aStates[$s++]);
		if ($pspID == Constants::iDIBS_PSP)
		{
			$this->assertEquals(Constants::iCB_CONSTRUCTED_STATE, $aStates[$s++]);
			$this->assertEquals(Constants::iCB_CONNECTED_STATE, $aStates[$s++]);
			$this->assertEquals(Constants::iCB_ACCEPTED_STATE, $aStates[$s++]);
		}

		$this->assertEquals(Constants::iPAYMENT_CAPTURED_STATE, $aStates[$s++]);

		if ($pspID == Constants::iDIBS_PSP)
		{
			$this->assertEquals(Constants::iCB_CONSTRUCTED_STATE, $aStates[$s++]);
			$this->assertEquals(Constants::iCB_CONNECTED_STATE, $aStates[$s++]);
			$this->assertEquals(Constants::iCB_ACCEPTED_STATE, $aStates[$s++]);
		}

		$this->assertContains("Message: CAPTURE APPROVED BY PSP ". $pspID, parent::getErrorLogContent() );


		/* Test that euaid has been set on txn */
		$res =  $this->queryDB("SELECT t.euaid, et.accountid FROM Log.Transaction_Tbl t LEFT JOIN Enduser.Transaction_Tbl et ON et.txnid = t.id WHERE t.id = 1001001");
		$this->assertTrue(is_resource($res) );
		$row = pg_fetch_assoc($res);

		$this->assertEquals(5001, $row["euaid"]);
		//TODO: Rewrite test so it supports both Netaxept and DIBS. Netaxept completes the txn within the callback, DIBS does it during the authorize API flow
		if ($pspID == Constants::iDIBS_PSP) { $this->assertEquals(5001, $row["accountid"]); }
	}

}
