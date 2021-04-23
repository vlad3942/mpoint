<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class PayAPITest extends baseAPITest
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
		$aMPOINT_CONN_INFO['path'] = "/mApp/api/pay.php";
		$aMPOINT_CONN_INFO["contenttype"] = "text/xml";
		$this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
		$this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO) );
	}

	protected function getPayDoc($client, $account, $txn=1, $card=7, $store=false, $contryid=100, $currencyid=-1,$amount=100,$hmac=null,$aDccParams=null , $sso_preference=null, $authtoken=null, $profileid=null, $mobile=null, $email=null,$customerref=null)
	{
		$sStore = $store ? 'true' : 'false';

		$xml = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml .= '<root>';
		$xml .= '<pay client-id="'. $client .'" account="'. $account .'">';
		$xml .= '<transaction id="'. $txn .'" store-card="'. $sStore .'"';
		$xml .='>';
		$xml .= '<card type-id="'. $card .'">';
        $xml .= '<amount country-id="'.$contryid.'"';
        if($currencyid>0) $xml .= ' currency-id="'.$currencyid.'"';
        $xml .= '>'.$amount.'</amount>';
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
		if(isset($authtoken) === true)
        {
		    $xml .= '<auth-token>'.$authtoken.'</auth-token>';
        }

        if(isset($sso_preference) === true && ($sso_preference === 'STRICT'))
        {	
        	if(isset($profileid) === true) {
				$xml .= '<client-info platform="iOS" version="1.00" language="da" profileid= "'.$profileid.'">';
			} else {
				$xml .= '<client-info platform="iOS" version="1.00" language="da" >';
			}
        	

        	if(isset($mobile) === true) {
				$xml .= '<mobile country-id="100" operator-id="10000">'.$mobile.'</mobile>';
			} 
			if(isset($email) === true) {
				$xml .= '<email>'.$email.'</email>';
			} 
			if(isset($customerref) === true) {
				$xml .= '<customer-ref>'.$customerref.'</customer-ref>';
			} 
        } 
        else 
        {
        	$xml .= '<client-info platform="iOS" version="1.00" language="da">';
			$xml .= '<mobile country-id="100" operator-id="10000">28882861</mobile>';
			$xml .= '<email>jona@oismail.com</email>';
        }
		
		$xml .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
		$xml .= '</client-info>';
		$xml .= '</pay>';
		$xml .= '</root>';

		return $xml;
	}

	protected function testSuccessfulPay($pspID, $cardID,$typeId=1, $pspType = 1, $countryid=100,$currencyid=208, $finalStateId = 1009)
	{
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10099, 'app')");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,psp_type) VALUES (10099, $cardID, $pspID, $pspType)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, ".$currencyid.", ".$countryid .", 4001, '1513-005', 5000, 29612109, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, ".$countryid .", $pspID, '1512', '1513-005', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,5000)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001001, 200,208," . Constants::iInitializeRequested . ",NULL,'done',10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001001, 200,208,NULL," . Constants::iINPUT_VALID_STATE . ",'done',100,10099)");

		$xml = $this->getPayDoc(10099, 1100, 1001001, $cardID, false, $countryid, $currencyid);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
		$this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?><root><psp-info id="'. $pspID. '" merchant-account="4216310"  type="'.$typeId.'">', $sReplyBody);
		if($finalStateId !== null)
        {
		    $this->assertStringContainsString('<status code="'. $finalStateId .'">', $sReplyBody);
        }

		$res =  $this->queryDB("SELECT id FROM Enduser.Account_Tbl");
		$this->assertTrue(is_resource($res) );

		$this->assertEquals(0, pg_num_rows($res) );

		if($typeId !== 1 && $typeId !== 2 && $typeId !== 3)
        {
            $res =  $this->queryDB("SELECT id FROM Log.txnpassbook_tbl where transactionid= 1001001 and status= 'inprogress' and performedopt=2000" );
		    $this->assertIsResource($res);
		    $this->assertEquals(1, pg_num_rows($res));
        }

		$res =  $this->queryDB("SELECT id FROM Log.Session_Tbl where id=1 and sessiontypeid=1");
        $this->assertTrue(is_resource($res) );

		return $sReplyBody;
	}


	/**
	*	SSO_PREFERENCE - LOOSE OR null
	*/
	protected function testSuccessfulPayWithSSO($pspID, $cardID,$typeId=1, $pspType = 1, $countryid=100,$currencyid=208, $sso_preference=null)
	{
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com:80/')");

		if(isset($sso_preference) === true)
		{	
			$this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('SSO_PREFERENCE', '".$sso_preference."', 10099, 'client', 0)"); 
		}	
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10099, 'app')");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,psp_type) VALUES (10099, $cardID, $pspID, $pspType)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, ".$currencyid.", ".$countryid .", 4001, '1513-005', 5000, 29612109, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, ".$countryid .", $pspID, '1512', '1513-005', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,5000)");

		$xml = $this->getPayDoc(10099, 1100, 1001001, $cardID, false, $countryid, $currencyid, 100, null, null, $sso_preference, 'success');

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
		$this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?><root><psp-info id="'. $pspID. '" merchant-account="4216310"  type="'.$typeId.'">', $sReplyBody);

		$res =  $this->queryDB("SELECT id FROM Log.Session_Tbl where id=1 and sessiontypeid=1");
        $this->assertTrue(is_resource($res) );

	}


	protected function testSuccessfulPayWithSSOStrict_AuthToken($pspID, $cardID,$typeId=1, $pspType = 1, $countryid=100,$currencyid=208, $sso_preference=null)
	{
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com:80/')");

		if(isset($sso_preference) === true)
		{	
			$this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('SSO_PREFERENCE', '".$sso_preference."', 10099, 'client', 0)"); 
		}	
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10099, 'app')");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,psp_type) VALUES (10099, $cardID, $pspID, $pspType)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, ".$currencyid.", ".$countryid .", 4001, '1513-005', 5000, 29612109, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, ".$countryid .", $pspID, '1512', '1513-005', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,5000)");

		$xml = $this->getPayDoc(10099, 1100, 1001001, $cardID, false, $countryid, $currencyid, 100, null, null, $sso_preference);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();


		$this->assertEquals(400, $iStatus);
		$this->assertStringContainsString('Auth token or SSO token not received', $sReplyBody);


	}

	protected function testSuccessfulPayWithSSOStrict_Authurl($pspID, $cardID,$typeId=1, $pspType = 1, $countryid=100,$currencyid=208, $sso_preference=null)
	{
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com:80/')");

		if(isset($sso_preference) === true)
		{	
			$this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('SSO_PREFERENCE', '".$sso_preference."', 10099, 'client', 0)"); 
		}	
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10099, 'app')");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,psp_type) VALUES (10099, $cardID, $pspID, $pspType)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, ".$currencyid.", ".$countryid .", 4001, '1513-005', 5000, 29612109, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, ".$countryid .", $pspID, '1512', '1513-005', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,5000)");

		$xml = $this->getPayDoc(10099, 1100, 1001001, $cardID, false, $countryid, $currencyid, 100, null, null, $sso_preference, 'success');

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(400, $iStatus);
		$this->assertStringContainsString('Auth url not configured', $sReplyBody);

	}

	protected function testSuccessfulPayWithSSOStrict_CustomerInfo($pspID, $cardID,$typeId=1, $pspType = 1, $countryid=100,$currencyid=208, $sso_preference=null)
	{
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
		
		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com:80/')");

		$this->queryDB("INSERT INTO Client.URL_Tbl (urltypeid, clientid, url) VALUES (2, 10099, 'http://mpoint.local.cellpointmobile.com/_test/simulators/auth.php')");

		if(isset($sso_preference) === true)
		{	
			$this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('SSO_PREFERENCE', '".$sso_preference."', 10099, 'client', 0)"); 
		}	
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10099, 'app')");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,psp_type) VALUES (10099, $cardID, $pspID, $pspType)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, ".$currencyid.", ".$countryid .", 4001, '1513-005', 5000, 29612109, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, ".$countryid .", $pspID, '1512', '1513-005', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,5000)");

		$xml = $this->getPayDoc(10099, 1100, 1001001, $cardID, false, $countryid, $currencyid, 100, null, null, $sso_preference, 'success', null, null, null, null);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(400, $iStatus);
		$this->assertStringContainsString('Mandatory fields are missing', $sReplyBody);
	}

	protected function testSuccessfulPayWithSSOStrict_ValidCIAM($pspID, $cardID,$typeId=1, $pspType = 1, $countryid=100,$currencyid=208, $sso_preference=null)
	{
		//$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
		$authenticateURL = $sCallbackURL = $this->_aMPOINT_CONN_INFO['protocol'] . '://' . $this->_aMPOINT_CONN_INFO['host']. '/_test/simulators/login.php';
		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 2, '".$authenticateURL."')");

		if(isset($sso_preference) === true)
		{	
			$this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('SSO_PREFERENCE', '".$sso_preference."', 10099, 'client', 0)"); 
		}	
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10099, 'app')");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,psp_type) VALUES (10099, $cardID, $pspID, $pspType)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, ".$currencyid.", ".$countryid .", 4001, '1513-005', 5000, 29612109, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, ".$countryid .", $pspID, '1512', '1513-005', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,5000)");

		$xml = $this->getPayDoc(10099, 1100, 1001001, $cardID, false, $countryid, $currencyid, 100, null, null, $sso_preference, 'success', 8983456, 9898989898, 'Karishan.Kumar@cellpointmobile.com', 'Karishan.Kumar@cellpointmobile.com');

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
	}

	protected function testSuccessfulPayWithSSOStrict_InvalidCIAM($pspID, $cardID,$typeId=1, $pspType = 1, $countryid=100,$currencyid=208, $sso_preference=null)
	{
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
		
		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com:80/')");

		$this->queryDB("INSERT INTO Client.URL_Tbl (urltypeid, clientid, url) VALUES (2, 10099, 'http://mpoint.local.cellpointmobile.com/_test/simulators/auth.php')");

		if(isset($sso_preference) === true)
		{	
			$this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('SSO_PREFERENCE', '".$sso_preference."', 10099, 'client', 0)"); 
		}	
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10099, 'app')");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,psp_type) VALUES (10099, $cardID, $pspID, $pspType)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, ".$currencyid.", ".$countryid .", 4001, '1513-005', 5000, 29612109, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, ".$countryid .", $pspID, '1512', '1513-005', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,5000)");

		$xml = $this->getPayDoc(10099, 1100, 1001001, $cardID, false, $countryid, $currencyid, 100, null, null, $sso_preference, 'fail', 8983456, 9898989898, 'Karishan.Kumar@cellpointmobile.com', 'Karishan.Kumar@cellpointmobile.com');

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		
		$this->assertEquals(400, $iStatus);
		$this->assertStringContainsString('Profile authentication failed', $sReplyBody);
	}

    protected function testSuccessfulPayWithAID($pspID, $cardID, $typeId=1, $pspType = 1, $countryid=100, $currencyid=208)
    {
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10099, 'app')");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,psp_type) VALUES (10099, $cardID, $pspID, $pspType)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, ".$currencyid.", ".$countryid .", 4001, '1513-005', 5000, 29612109, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, ".$countryid .", $pspID, '1512', '1513-005', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,5000)");
        $this->queryDB("INSERT INTO log.additional_data_tbl(name, value, type, externalid) VALUES('FCTxnID', '243001', 'Transaction','1001001')");
        $this->queryDB("INSERT INTO Log.Order_Tbl (id, orderref, txnid, countryid, amount, quantity, productsku, productname, productdescription, productimageurl, points, reward,fees) VALUES (10, 'SOCGN6', 1001001, 100, 100, 1, '103-1418291', 'return journey', 'return journey', 'https://www.cpm.com', 300, 1, 0);");
        $this->queryDB("INSERT INTO Log.Flight_Tbl (id, service_class,mkt_flight_number, departure_airport, arrival_airport, op_airline_code, order_id, arrival_date, departure_date, tag, trip_count, service_level, departure_countryid, arrival_countryid, departure_timezone) VALUES (10, 'X', '1850', 'CEB', 'MNL', 'PR', '10', '2020-05-23 13:55:00', '2020-05-23 12:40:00', '1', '2', '3', 200, 200, '+08:30')");
        $this->queryDB("INSERT INTO log.passenger_tbl (id, first_name, last_name, type, order_id, created, modified, title, email, mobile, country_id, amount, seq) VALUES (24, 'dan', 'dan', 'ADT', 10, '2021-04-09 13:06:23.420245', '2021-04-09 13:06:23.420245', 'Mr', 'dan@dan.com', '9187231231', '640', 0, 1)");
        $this->queryDB("INSERT INTO log.additional_data_tbl (id, name, value, type, created, modified, externalid) VALUES (109, 'loyality_id', '345rtyu', 'Passenger', '2021-04-09 13:06:23.406019', '2021-04-09 13:06:23.406019', 24);");
        $this->queryDB("INSERT INTO log.billing_summary_tbl (id, order_id, journey_ref, bill_type, type_id, description, amount, currency, created, modified, profile_seq, trip_tag, trip_seq, product_code, product_category, product_item) VALUES (45, 10, '', 'Fare', 1, 'adult', '60', 'PHP', '2021-04-09 13:06:23.336965', '2021-04-09 13:06:23.336965', 0, 0, 0, 'ABF', 'FARE', 'Base fare for adult')");
        $this->queryDB("INSERT INTO log.billing_summary_tbl (id, order_id, journey_ref, bill_type, type_id, description, amount, currency, created, modified, profile_seq, trip_tag, trip_seq, product_code, product_category, product_item) VALUES (46, 10, '', 'Add-on', 0, 'adult', '60', 'PHP', '2021-04-09 13:06:23.353398', '2021-04-09 13:06:23.353398', 1, 2, 2, 'ABF', 'FARE', 'Base fare for adult')");

        $xml = $this->getPayDoc(10099, 1100, 1001001, $cardID, false, $countryid, $currencyid);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(200, $iStatus);
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?><root><psp-info id="'. $pspID. '" merchant-account="4216310"  type="'.$typeId.'">', $sReplyBody);

        $res =  $this->queryDB("SELECT id FROM Enduser.Account_Tbl");
        $this->assertTrue(is_resource($res) );

        $this->assertEquals(0, pg_num_rows($res) );
        $this->bIgnoreErrors = true;
        return $sReplyBody;
    }


}