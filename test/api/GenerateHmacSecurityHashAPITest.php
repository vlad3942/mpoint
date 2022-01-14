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

    protected function getDoc($clientid, $hmacType="")
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<hmac_parameters>';
        $xml .= '<hmac_parameter_details>';
		$xml .= '<hmac_parameter_detail>';		
		$xml .= '<hmac_type>'.$hmacType.'</hmac_type>';
		$xml .= '<unique_reference_identifier>101</unique_reference_identifier>';
		$xml .= '<client_id>'.$clientid.'</client_id>';
		$xml .= '<order_number>CY973</order_number>';
		$xml .= '<amount>200</amount>';		
		$xml .= '<country_id>640</country_id>';
		$xml .= '<sale_amount>200</sale_amount>';
		$xml .= '<sale_currency>392</sale_currency>';
		$xml .= '<client_info>';
        $xml .= '<mobile>9898989898</mobile>';
        $xml .= '<mobile_country>640</mobile_country>';
        $xml .= '<email>jona@oismail.com</email>';
        $xml .= '<device_id></device_id>';
        $xml .= '</client_info>';        
        $xml .= '</hmac_parameter_detail>';        
        $xml .= '</hmac_parameter_details>';        
        $xml .= '</hmac_parameters>';

        return $xml;
    }

    public function testBadRequestInvalidRequestBody()
    {
		$this->_httpClient->connect();
		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), '<xml></xml>');
		$sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(400, $iStatus);
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?><status><code>400</code><description>Wrong operation: </description><uuid>', $sReplyBody);
    }


    public function testUnsupportedMediaType()
	{
		$this->_httpClient->connect();
		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), '<xl</xl>');
		$sReplyBody = $this->_httpClient->getReplyBody();
		$this->assertEquals(415, $iStatus);
		$this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?><status><code>415</code><description>Invalid XML Document</description><uuid>', $sReplyBody);
	}


    public function testGenerateRegularHmac()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd, salt) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass', 'salt')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
		$xml = $this->getDoc(10099, '');
		$this->_httpClient->connect();
		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();
	  	$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><security_token_details><security_token_detail><unique_reference_identifier>101</unique_reference_identifier><token>2fe86f669ea608d424390d5faffa7539101625cd604892aa2a448cb8c62842a600a9e4eb941c70b313de2b0cd66a25f0aac65aab70c524cd88eb94e0e6f0217b</token></security_token_detail></security_token_details>', $sReplyBody);
	}
	
	public function testGenerateFxHmac()
    {
	    $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd, salt) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass', 'salt')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
		$xml = $this->getDoc(10099, 'FX');
		$this->_httpClient->connect();
		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();
	  	$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><security_token_details><security_token_detail><unique_reference_identifier>101</unique_reference_identifier><token>a6ad6f9f6f0e59a20b58626212a4ddd4c762439e109d406675be26d2010b91cb75f009d09a4c7c9e4d73144e7def4dfa97bc2639fe4b132472e697131ce99e72</token></security_token_detail></security_token_details>', $sReplyBody);
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
		$this->assertEquals(400, $iStatus);
	  	$this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?><status><code>400</code><description>Invalid client detail: 10095</description><uuid>', $sReplyBody);
    }
	
	public function testMissingSalt()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
		$xml = $this->getDoc(10099);
		$this->_httpClient->connect();
		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();
		$this->assertEquals(400, $iStatus);
	  	$this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?><status><code>400</code><description>The salt setup has not been configured for the client: 10099</description><uuid>', $sReplyBody);
    }

}
