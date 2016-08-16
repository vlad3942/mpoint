<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class ThreeDSecureAPITest extends baseAPITest
{

    protected $_aMPOINT_CONN_INFO;
	protected $s3DSecureURL;

	public function __construct()
    {
        $this->constHTTPClient();
    }

    public function constHTTPClient()
    {
        global $aMPOINT_CONN_INFO;
        $aMPOINT_CONN_INFO['path'] = "/mApp/api/3dsecure.php";
        $aMPOINT_CONN_INFO["contenttype"] = "text/xml";
        $this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
		$this->s3DSecureURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/parse-3dsecure-challenge.php";
		$this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO) );
    }

	protected function getRequestDoc($client, $account, $txn=1001001, $contentType='text/html', $url='http://cellpointmobile.com')
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml .= '<root>';
		$xml .= '<request-3dsecure client-id="'. $client .'" account="'. $account .'">';
		$xml .= '<transaction id="'. $txn .'">800-1234</transaction>';
		$xml .= '<challenge content-type="'. $contentType .'" url="'. $url .'">';
		$xml .= ' &lt;html&gt;&lt;body&gt;Lorem Ipsum&lt;/body&gt;&lt;/html&gt;';
		$xml .= '</challenge>';
		$xml .= '<client-info platform="iOS" version="1.00" language="da">';
		$xml .= '<mobile country-id="100" operator-id="10000">28882861</mobile>';
		$xml .= '<email>jona@oismail.com</email>';
		$xml .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
		$xml .= '</client-info>';
		$xml .= '</request-3dsecure>';
		$xml .= '</root>';

		return $xml;
	}

	protected function responseDoc()
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml .= '<root>';
		$xml .= '	<parsed-challenge>';
		$xml .= '		<scheme-logo>2</scheme-logo>';
		$xml .= '		<member-logo>';
		$xml .= '			<url>http://acs4.3dsecure.no/mdpayacs/logos/netstech_small.png</url>';
		$xml .= '		</member-logo>';
		$xml .= '		<price>DKK 1.00</price>';
		$xml .= '		<date>20160623 14:08:43</date>';
		$xml .= '		<card-number-mask>XXXX XXXX XXXX 1071</card-number-mask>';
		$xml .= '		<pam type-id="1">45302XXX62</pam>';
		$xml .= '		<action type-id="1">';
		$xml .= '			<url content-type="application/x-www-form-urlencoded" method="post" type-id="1">';
		$xml .= '				https://acs4.3dsecure.no/mdpayacs/pareq;mdsessionid=6F5B8B6D6966FE27230A5F91B8D8E72F';
		$xml .= '			</url>';
		$xml .= '			<password type-id="1">otp</password>';
		$xml .= '		</action>';
		$xml .= '		<action type-id="2">';
		$xml .= '			<url method="get" type-id="1">';
		$xml .= '				https://acs4.3dsecure.no/mdpayacs/pareq;mdsessionid=6F5B8B6D6966FE27230A5F91B8D8E72F?resend=true';
		$xml .= '			</url>';
		$xml .= '		</action>';
		$xml .= '		<action type-id="3">';
		$xml .= '			<url method="get" type-id="2">';
		$xml .= '				https://acs4.3dsecure.no/mdpayacs/pareq;mdsessionid=6F5B8B6D6966FE27230A5F91B8D8E72F?ads=true';
		$xml .= '			</url>';
		$xml .= '		</action>';
		$xml .= '	</parsed-challenge>';
		$xml .= '</root>';

		return $xml;
	}

	public function testSuccess()
	{
		$simulatorConf = array('error' => 0);
		trigger_error("PARSE-3DSECURE-CHALLENGE SIMULATOR ::: ". serialize($simulatorConf) );

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.Url_Tbl (urltypeid, clientid, url) VALUES (". ClientConfig::iPARSE_3DSECURE_CHALLENGE_URL .", 113, '". $this->s3DSecureURL ."')");
		$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid) VALUES (1001001, 100, 113, 1100, 100, 2, '1512', '800-1234', '', 5000, '127.0.0.1', TRUE, 1)");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iINPUT_VALID_STATE .")");

		$xml = $this->getRequestDoc(113, 1100);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
		$this->assertXmlStringEqualsXmlString($this->responseDoc(), $sReplyBody);

		$res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY ID ASC");
		$this->assertTrue(is_resource($res) );

		$aStates = array();
		while ($row = pg_fetch_assoc($res) )
		{
			$aStates[] = $row["stateid"];
		}

		$this->assertEquals(2, count($aStates) );
		$this->assertEquals(Constants::iINPUT_VALID_STATE, $aStates[0]);
		$this->assertEquals(Constants::i3D_SECURE_ACTIVATED_STATE, $aStates[1]);
	}

	public function testUnrecognized3DSecureProvider()
	{
		$simulatorConf = array('error' => 91);
		trigger_error("PARSE-3DSECURE-CHALLENGE SIMULATOR ::: ". serialize($simulatorConf) );

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.Url_Tbl (urltypeid, clientid, url) VALUES (". ClientConfig::iPARSE_3DSECURE_CHALLENGE_URL .", 113, '". $this->s3DSecureURL ."')");
		$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid) VALUES (1001001, 100, 113, 1100, 100, 2, '1512', '800-1234', '', 5000, '127.0.0.1', TRUE, 1)");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iINPUT_VALID_STATE .")");

		$xml = $this->getRequestDoc(113, 1100);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(501, $iStatus);
		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="91">Unrecognized 3D Secure Provider</status></root>', $sReplyBody);

		$res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY ID ASC");
		$this->assertTrue(is_resource($res) );

		$aStates = array();
		while ($row = pg_fetch_assoc($res) )
		{
			$aStates[] = $row["stateid"];
		}

		$this->assertEquals(2, count($aStates) );
		$this->assertEquals(Constants::iINPUT_VALID_STATE, $aStates[0]);
		$this->assertEquals(Constants::i3D_SECURE_ACTIVATED_STATE, $aStates[1]);
	}

	public function testRequiredFieldsMissing()
	{
		$simulatorConf = array('error' => 92);
		trigger_error("PARSE-3DSECURE-CHALLENGE SIMULATOR ::: ". serialize($simulatorConf) );

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.Url_Tbl (urltypeid, clientid, url) VALUES (". ClientConfig::iPARSE_3DSECURE_CHALLENGE_URL .", 113, '". $this->s3DSecureURL ."')");
		$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid) VALUES (1001001, 100, 113, 1100, 100, 2, '1512', '800-1234', '', 5000, '127.0.0.1', TRUE, 1)");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iINPUT_VALID_STATE .")");

		$xml = $this->getRequestDoc(113, 1100);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(502, $iStatus);
		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="92">Missing required fields in parsed 3dsecure challenge: - pam - pam@type</status></root>', $sReplyBody);

		$res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY ID ASC");
		$this->assertTrue(is_resource($res) );

		$aStates = array();
		while ($row = pg_fetch_assoc($res) )
		{
			$aStates[] = $row["stateid"];
		}

		$this->assertEquals(2, count($aStates) );
		$this->assertEquals(Constants::iINPUT_VALID_STATE, $aStates[0]);
		$this->assertEquals(Constants::i3D_SECURE_ACTIVATED_STATE, $aStates[1]);
	}

	public function testUnknownResponseFromChallengeParser()
	{
		$simulatorConf = array('error' => 93);
		trigger_error("PARSE-3DSECURE-CHALLENGE SIMULATOR ::: ". serialize($simulatorConf) );

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.Url_Tbl (urltypeid, clientid, url) VALUES (". ClientConfig::iPARSE_3DSECURE_CHALLENGE_URL .", 113, '". $this->s3DSecureURL ."')");
		$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid) VALUES (1001001, 100, 113, 1100, 100, 2, '1512', '800-1234', '', 5000, '127.0.0.1', TRUE, 1)");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iINPUT_VALID_STATE .")");

		$xml = $this->getRequestDoc(113, 1100);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(502, $iStatus);
		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="93">Unknown response from 3dsecure provider challenge parser</status></root>', $sReplyBody);

		$res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY ID ASC");
		$this->assertTrue(is_resource($res) );

		$aStates = array();
		while ($row = pg_fetch_assoc($res) )
		{
			$aStates[] = $row["stateid"];
		}

		$this->assertEquals(2, count($aStates) );
		$this->assertEquals(Constants::iINPUT_VALID_STATE, $aStates[0]);
		$this->assertEquals(Constants::i3D_SECURE_ACTIVATED_STATE, $aStates[1]);
	}

	public function testMalformedResponseFromEndpoint()
	{
		$simulatorConf = array('error' => 94);
		trigger_error("PARSE-3DSECURE-CHALLENGE SIMULATOR ::: ". serialize($simulatorConf) );

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.Url_Tbl (urltypeid, clientid, url) VALUES (". ClientConfig::iPARSE_3DSECURE_CHALLENGE_URL .", 113, '". $this->s3DSecureURL ."')");
		$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid) VALUES (1001001, 100, 113, 1100, 100, 2, '1512', '800-1234', '', 5000, '127.0.0.1', TRUE, 1)");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iINPUT_VALID_STATE .")");

		$xml = $this->getRequestDoc(113, 1100);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->bIgnoreErrors = true;
		$this->assertEquals(502, $iStatus);
		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="94">Could not parse response from 3D Secure Challenge Parser for Transaction ID: 1001001 using URL: http://mpoint.local.cellpointmobile.com/_test/simulators/parse-3dsecure-challenge.php</status></root>', $sReplyBody);

		$res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY ID ASC");
		$this->assertTrue(is_resource($res) );

		$aStates = array();
		while ($row = pg_fetch_assoc($res) )
		{
			$aStates[] = $row["stateid"];
		}

		$this->assertEquals(2, count($aStates) );
		$this->assertEquals(Constants::iINPUT_VALID_STATE, $aStates[0]);
		$this->assertEquals(Constants::i3D_SECURE_ACTIVATED_STATE, $aStates[1]);
	}

	public function testHTTPErrorFromEndpoint()
	{
		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.Url_Tbl (urltypeid, clientid, url) VALUES (". ClientConfig::iPARSE_3DSECURE_CHALLENGE_URL .", 113, 'really.wrongdnsname')");
		$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid) VALUES (1001001, 100, 113, 1100, 100, 2, '1512', '800-1234', '', 5000, '127.0.0.1', TRUE, 1)");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iINPUT_VALID_STATE .")");

		$xml = $this->getRequestDoc(113, 1100);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->bIgnoreErrors = true;
		$this->assertEquals(502, $iStatus);
		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="95">Communication with 3D Secure Challenge parser failed for Transaction ID: 1001001 using URL: really.wrongdnsname</status></root>', $sReplyBody);

		$res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY ID ASC");
		$this->assertTrue(is_resource($res) );

		$aStates = array();
		while ($row = pg_fetch_assoc($res) )
		{
			$aStates[] = $row["stateid"];
		}

		$this->assertEquals(2, count($aStates) );
		$this->assertEquals(Constants::iINPUT_VALID_STATE, $aStates[0]);
		$this->assertEquals(Constants::i3D_SECURE_ACTIVATED_STATE, $aStates[1]);
	}
}
