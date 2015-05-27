<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

abstract class AuthorizeAPITest extends mPointBaseAPITest
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

	protected function getAuthDoc($client, $account, $txn=1, $amount=100, $euaPasswd='', $intAccountId=0, $clientpasswd='')
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml .= '<root>';
		$xml .= '<authorize-payment client-id="'. $client .'" account="'. $account .'">';
		$xml .= '<transaction id="'. $txn .'">';
		$xml .= '<card id="61775" type-id="1009">';
		$xml .= '<amount country-id="100">'. $amount .'</amount>';
//		$xml .= '<card-number>5272342200069702</card-number>';
//		$xml .= '<expiry>03/31</expiry>';
//		$xml .= '<cryptogram type="3ds">AKh96OOsGf2HAIDEhKulAoABFA==</cryptogram>';
		$xml .= '</card>';
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
		$this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");
		$this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '5019********3742', '06/24', TRUE, 113, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
		$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled) VALUES (1001001, 100, 113, 1100, 1,  $pspID, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE)");

		$xml = $this->getAuthDoc(113, 1100, 1001001, 100, 'profilePass');

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

		$this->assertTrue(is_int(array_search(Constants::iPAYMENT_ACCEPTED_STATE, $aStates) ) );
		$this->assertTrue(is_int(array_search(Constants::iPAYMENT_WITH_ACCOUNT_STATE, $aStates) ) );
	}

}