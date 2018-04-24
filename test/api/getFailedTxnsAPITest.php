<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class GetFailedTransactionsAPITest extends baseAPITest
{
	protected $_aMPOINT_CONN_INFO;

	public function __construct()
	{
		$this->constHTTPClient();
	}

	public function constHTTPClient()
	{
		global $aMPOINT_CONN_INFO;
		$aMPOINT_CONN_INFO['path'] = "/mConsole/api/get_failed-transactions.php";
		$aMPOINT_CONN_INFO["contenttype"] = "text/xml";
		$this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
		$this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO) );
	}

	protected function getFailedTxnConfigDoc($client)
	{
	    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml .= '<root>';
		$xml .= '<get-failed-transactions sub-type = "1">';
        $xml .= '<clients>';
        $xml .= '<client-id>'. $client .'</client-id>';
        $xml .= '</clients>';
        $xml .= '<start-date>'.date('Y-m-d')."T".date('H:i:s',strtotime('-10 minutes')).'</start-date>';
        $xml .= '<end-date>'.date('Y-m-d')."T".date('H:i:s',strtotime('+10 minutes')).'</end-date>';
        $xml .= '</get-failed-transactions>';
        $xml .= '</root>';
		return $xml;
	}

	public function testSuccessfulFailedTxnsResponse()
	{
        $iClientID = 113;
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
        $sCurrentTS = date('Y-m-d H:i:s');
        $sCurrentTSDB = str_replace(' ', 'T', $sCurrentTS);

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 113, 'app')");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
        $this->queryDB("INSERT INTO Log.Session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) 
                                                            VALUES (1, 113, 1100, 208, 100, 4001, '1513-005', 5000, 29612109, '', '127.0.0.1', -1, 1)");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,created, modified) 
                                                        VALUES (1001001, 40, 113, 1100, 100, '1512', '1513-005', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,'".$sCurrentTS."','".$sCurrentTS."')");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid, data) 
                                                        VALUES (1001001, 1001, 'Test Transaction Data')");

        $xml = $this->getFailedTxnConfigDoc($iClientID);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><failed-transactions><transaction id="1001001" type-id="40" state-id="1001" order-no="1513-005" external-id="1512" mode="0"><client id="113">Test Client</client><communication-channels></communication-channels><sub-account id="1100" markup="app"></sub-account><amount country-id="100" currency="DKK" format="{PRICE} {CURRENCY}">5000</amount><customer></customer><ip>127.0.0.1</ip><timestamp>'.$sCurrentTSDB.'</timestamp></transaction></failed-transactions></root>', $sReplyBody);
        return $sReplyBody;
	}

}