<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class PayAPITest extends mPointBaseAPITest
{
	protected $_aMPOINT_CONN_INFO;

	public function __construct()
	{
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

	protected function getPayDoc($client, $account, $txn=1, $card=7)
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml .= '<root>';
		$xml .= '<pay client-id="'. $client .'" account="'. $account .'">';
		$xml .= '<transaction id="'. $txn .'" store-card="false">';
		$xml .= '<card type-id="'. $card .'">';
		$xml .= '<amount country-id="100">200</amount>';
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

	protected function testSuccessfulPay($pspID, $cardID)
	{
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 113, 'app')");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (113, $cardID, $pspID)");
		$this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT C.id * -1 AS pricepointid, $cardID FROM System.Country_Tbl C, System.Card_Tbl Card WHERE C.id = 100 GROUP BY pricepointid;");
		$this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid) VALUES (1001001, 100, 113, 1100, 100, $pspID, '1512', '1513-005', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1)");

		$xml = $this->getPayDoc(113, 1100, 1001001, $cardID);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
		$this->assertContains('<?xml version="1.0" encoding="UTF-8"?><root><psp-info id="'. $pspID. '" merchant-account="4216310">', $sReplyBody);

		return $sReplyBody;
	}

}