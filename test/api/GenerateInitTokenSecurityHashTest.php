<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class GenerateInitTokenSecurityHashTest extends baseAPITest
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
		$aMPOINT_CONN_INFO['path'] = "/mApp/api/generate_init_token_security_hash.php";
		$aMPOINT_CONN_INFO["contenttype"] = "text/xml";
		$this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($this->_aMPOINT_CONN_INFO));
	}

    protected function getDoc($clientid, $acceptUrl="")
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<root>';
        $xml .= '<init_token_parameters>';
        $xml .= '<init_token_parameter_details>';
		$xml .= '<init_token_parameter_detail>';		
		$xml .= '<unique_reference_identifier>101</unique_reference_identifier>';
		$xml .= '<client_id>'.$clientid.'</client_id>';
		$xml .= '<nonce>123456</nonce>';
		if($acceptUrl != ''){
			$xml .= '<accept_url>'.$acceptUrl.'</accept_url>';
		}
        $xml .= '</init_token_parameter_detail>';
        $xml .= '</init_token_parameter_details>';
        $xml .= '</init_token_parameters>';
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


    public function testGenerateInitToken()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
		$xml = $this->getDoc(10099);
		$this->_httpClient->connect();
		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();
	  	$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><init_token_response><security_token_detail><unique_reference_identifier>101</unique_reference_identifier><token>18524a48db73503fe266fa5e583f1f11c27a7a482c63ff24ca2abd72b2869c1e320eb4ffa1f12ba1e0e45f1307735a5c0f1effb385ef5ce0e7e687a0c4bd181d</token></security_token_detail></init_token_response></root>', $sReplyBody);
	}
	
	public function testGenerateInitTokenWithAcceptURL()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
		$xml = $this->getDoc(10099, 'http://www');
		$this->_httpClient->connect();
		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody();
	  	$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><init_token_response><security_token_detail><unique_reference_identifier>101</unique_reference_identifier><token>8674328ce684aabe01c11f1c60a28fdadb4314b1646aa019bb2f4cc5237991654b420dcbe705ae49d5f366e5aae8f83eee390edf03a54942846ab63809f62d00</token></security_token_detail></init_token_response></root>', $sReplyBody);
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
	  	$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><init_token_response><security_token_detail><unique_reference_identifier>101</unique_reference_identifier><status>Invalid client detail: 10095</status></security_token_detail></init_token_response></root>', $sReplyBody);
    }

}
