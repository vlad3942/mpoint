<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class GetGoMobileConfigurationsAPITest extends baseAPITest
{
	protected $_aMPOINT_CONN_INFO;

	public function __construct()
	{
		$this->constHTTPClient();
	}

	public function constHTTPClient()
	{
		global $aMPOINT_CONN_INFO;
		$aMPOINT_CONN_INFO['path'] = "/mConsole/api/get_client-gomobile-configurations.php";
		$aMPOINT_CONN_INFO["contenttype"] = "text/xml";
		$this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
		$this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO) );
	}

	protected function getGoMobileConfigDoc($client)
	{
	    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml .= '<root>';
		$xml .= '<get-client-gomobile-configurations>';
        $xml .= '<clients>';
        $xml .= '<client-id>'. $client .'</client-id>';
        $xml .= '</clients>';
        $xml .= '</get-client-gomobile-configurations>';
        $xml .= '</root>';
		return $xml;
	}

	public function testSuccessfulGoMobileConfigResponse()
	{
        $iClientID = 113;
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 113, 'app')");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type) VALUES ('GOMOBILE_SMS_CHANNEL','123',113,'client')");

        $xml = $this->getGoMobileConfigDoc($iClientID);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><client-gomobile-configurations><client-config id="113"><gomobile-configuration-params><gomobile-configuration-param id="1" name="GOMOBILE_SMS_CHANNEL">123</gomobile-configuration-param></gomobile-configuration-params></client-config></client-gomobile-configurations></root>', $sReplyBody);

        return $sReplyBody;
	}

}