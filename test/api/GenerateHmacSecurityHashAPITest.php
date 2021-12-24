<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class GenerateHmacSecurityHashAPITest extends baseAPITest
{
	protected $_aMPOINT_CONN_INFO;

	public function __construct()
	{
        parent::__construct();
        $this->bIgnoreErrors = true;
		$this->constHTTPClient();
	}

	public function constHTTPClient()
	{
		global $aMPOINT_CONN_INFO;
		$aMPOINT_CONN_INFO['path'] = "/mApp/api/generate_hmac_security_hash.php";
		$aMPOINT_CONN_INFO["contenttype"] = "text/xml";
		$this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($this->_aMPOINT_CONN_INFO));
	}

    protected function getDoc($clientid, $hmacType="", $nonce="")
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<root>';
        $xml .= '<generate-hmac-security-hash>';
        $xml .= '<transactions>';
		$xml .= '<transaction>';		
		$xml .= '<hmac-type>'.$hmacType.'</hmac-type>';
		$xml .= '<unique-reference>101</unique-reference>';
		$xml .= '<client-id>'.$clientid.'</client-id>';
		$xml .= '<order-no>CY973</order-no>';
		$xml .= '<amount>200</amount>';
		if(empty($nonce) === false){
			$xml .= '<nonce>'.$nonce.'</nonce>';
		}
		$xml .= '<country-id>640</country-id>';
		$xml .= '<sale-amount>200</sale-amount>';
		$xml .= '<sale-currency>392</sale-currency>';
		$xml .= '<client-info>';
        $xml .= '<mobile>9898989898</mobile>';
        $xml .= '<mobile-country>640</mobile-country>';
        $xml .= '<email>jona@oismail.com</email>';
        $xml .= '<device-id></device-id>';
        $xml .= '</client-info>';        
        $xml .= '</transaction>';        
        $xml .= '</transactions>';        
        $xml .= '</generate-hmac-security-hash>';
        $xml .= '</root>';

        return $xml;
    }

    public function testBadRequestInvalidRequestBody()
    {
		$this->_httpClient->connect();
		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), '<xml></xml>');
		$sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(400, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root></root>', $sReplyBody);
    }


    public function testUnsupportedMediaType()
	{
		$this->_httpClient->connect();
		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), '<xl</xl>');
		$sReplyBody = $this->_httpClient->getReplyBody();
		$this->assertEquals(415, $iStatus);
		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="415">Invalid XML Document</status></root>', $sReplyBody);
	}


    public function testGenerateRegularHmac()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
		$xml = $this->getDoc(10099, '', 123456);
		$this->_httpClient->connect();
		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();
	  	$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><hmac-security-hashes><hmac-security-hash><unique_reference>101</unique_reference><init_token>18524a48db73503fe266fa5e583f1f11c27a7a482c63ff24ca2abd72b2869c1e320eb4ffa1f12ba1e0e45f1307735a5c0f1effb385ef5ce0e7e687a0c4bd181d</init_token><hmac>03474f0a133a327a8b97952fb37e88bba6ad9873167a524670c7e33e1201692d566a57a7c10f3968d104f3fcc57a90f1a5fe22fa4d24a6beedbc3121efc3d8c5</hmac></hmac-security-hash></hmac-security-hashes></root>', $sReplyBody);
	}
	
	public function testGenerateFxHmac()
    {
	    $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
		$xml = $this->getDoc(10099, 'FX', 123456);
		$this->_httpClient->connect();
		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();
	  	$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><hmac-security-hashes><hmac-security-hash><unique_reference>101</unique_reference><init_token>18524a48db73503fe266fa5e583f1f11c27a7a482c63ff24ca2abd72b2869c1e320eb4ffa1f12ba1e0e45f1307735a5c0f1effb385ef5ce0e7e687a0c4bd181d</init_token><hmac>10d12c7cbb5dffaa1383d520b30b6e1e6e5776bbef3a07062ce5c698cd73706a3216d3d8d759a51e1f8e2b4ffc2d3af6079891f71069e557c85c51bdf6f49442</hmac></hmac-security-hash></hmac-security-hashes></root>', $sReplyBody);
	}
	
	public function testRegularHmacWithoutInitToken()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
		$xml = $this->getDoc(10099);
		$this->_httpClient->connect();
		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();
	  	$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><hmac-security-hashes><hmac-security-hash><unique_reference>101</unique_reference><hmac>03474f0a133a327a8b97952fb37e88bba6ad9873167a524670c7e33e1201692d566a57a7c10f3968d104f3fcc57a90f1a5fe22fa4d24a6beedbc3121efc3d8c5</hmac></hmac-security-hash></hmac-security-hashes></root>', $sReplyBody);
	}
	
	public function testInvalidClient()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
		$xml = $this->getDoc(10095);
		$this->_httpClient->connect();
		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();
	  	$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><hmac-security-hashes></hmac-security-hashes></root>', $sReplyBody);
    }

}
