<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class GetStaticRoutesAPIValidationTest extends baseAPITest
{

    protected $_aMPOINT_CONN_INFO;

    public function constHTTPClient($clientId)
    {
        global $aMPOINT_CONN_INFO;
        $aMPOINT_CONN_INFO['path'] = '/mApp/api/get_routes.php?client_id='.$clientId;
        $aMPOINT_CONN_INFO["method"] = 'GET';
        $aMPOINT_CONN_INFO["contenttype"] = 'text/xml';
        $this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
		$this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO) );
    }

    public function testUnauthorized()
    {
        $this->constHTTPClient(113);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders());
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(401, $iStatus);
        $this->assertContains('<status code="401">Authorization required</status>', $sReplyBody);
    }

    public function testWrongUsernamePassword()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");

        $this->constHTTPClient(113);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Twrong'));
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(401, $iStatus);
        $this->assertContains('<status code="401">Username / Password doesn\'t match</status>', $sReplyBody);
    }

    public function testBadRequestUndefinedClient()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, enabled) VALUES (114, 1, 100, 'Test Client', true)");

        $this->constHTTPClient(null);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'));
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(400, $iStatus);
        $this->assertContains('<status code="1">Undefined Client ID</status>', $sReplyBody);
    }

    public function testBadRequestInvalidClient()
    {
        $this->constHTTPClient(9999);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'));
		$sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(400, $iStatus);
		$this->assertContains('<status code="2">Invalid Client ID</status>', $sReplyBody);
    }

    public function testBadRequestUnknownClient()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, enabled) VALUES (114, 1, 100, 'Test Client', true)");

        $this->constHTTPClient(113);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(400, $iStatus);
        $this->assertContains('<status code="3">Unknown Client ID</status>', $sReplyBody);
    }

    public function testBadRequestDisabledClient()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, enabled) VALUES (113, 1, 100, 'Test Client', false)");

        $this->constHTTPClient(113);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'));
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(400, $iStatus);
        $this->assertContains('<?xml version="1.0" encoding="UTF-8"?><root><status code="4">Client Disabled</status></root>', $sReplyBody);
    }

	public function testSuccessPaymentMethods()
    {
		
		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid, enabled, stateid) VALUES (113, 7, 18, 608,true, 2)");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid, enabled, stateid) VALUES (113, 8, 18, 608,true, 2)");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid, enabled, stateid) VALUES (113, 7, 17, 200,true, 2)");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid, enabled, stateid) VALUES (113, 8, 17, 200,true, 2)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");

		
        $this->constHTTPClient(113);

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'));
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
		$this->assertContains('<?xml version="1.0" encoding="UTF-8"?><root><routes><route><card_type_id>7</card_type_id><country_id>608</country_id><currency_id>null</currency_id><psp_id>18</psp_id><enabled>true</enabled><payment_type>1</payment_type></route><route><card_type_id>8</card_type_id><country_id>608</country_id><currency_id>null</currency_id><psp_id>18</psp_id><enabled>true</enabled><payment_type>1</payment_type></route><route><card_type_id>7</card_type_id><country_id>200</country_id><currency_id>null</currency_id><psp_id>17</psp_id><enabled>true</enabled><payment_type>1</payment_type></route><route><card_type_id>8</card_type_id><country_id>200</country_id><currency_id>null</currency_id><psp_id>17</psp_id><enabled>true</enabled><payment_type>1</payment_type></route></routes></root>', $sReplyBody);
	}

}

