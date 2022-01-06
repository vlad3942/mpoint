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
        $xml .= '<root>';
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
		$xml = $this->getDoc(10099, '');
		$this->_httpClient->connect();
		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();
	  	$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><security_token_details><security_token_detail><unique_reference_identifier>101</unique_reference_identifier><token>03474f0a133a327a8b97952fb37e88bba6ad9873167a524670c7e33e1201692d566a57a7c10f3968d104f3fcc57a90f1a5fe22fa4d24a6beedbc3121efc3d8c5</token></security_token_detail></security_token_details></root>', $sReplyBody);
	}
	
	public function testGenerateFxHmac()
    {
	    $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
		$xml = $this->getDoc(10099, 'FX');
		$this->_httpClient->connect();
		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();
	  	$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><security_token_details><security_token_detail><unique_reference_identifier>101</unique_reference_identifier><token>10d12c7cbb5dffaa1383d520b30b6e1e6e5776bbef3a07062ce5c698cd73706a3216d3d8d759a51e1f8e2b4ffc2d3af6079891f71069e557c85c51bdf6f49442</token></security_token_detail></security_token_details></root>', $sReplyBody);
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
	  	$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><security_token_details></security_token_details></root>', $sReplyBody);
    }

}
