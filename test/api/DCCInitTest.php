<?php
/**
 * User: SAGAR BADAVE
 */

require_once __DIR__. '/initializeAPIValidationTest.php';

class DCCInitTest extends InitializeAPIValidationTest
{
    public function testSuccessfulDCCInit()
    {
        $pspID = Constants::iWIRE_CARD_PSP;

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd,salt) VALUES (10018, 1, 100, 'Test Client', 'Tuser', 'Tpass','23lkhfgjh24qsdfkjh')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10018, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10018, 'app')");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10018, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10018, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid,dccenabled) VALUES (10018, 8, $pspID,100,true)");
        $this->queryDB("INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid, enabled) VALUES (10018,100,840, true)");

        $xml = $this->getInitDoc(10018, 1100, 840, null, 1000,'ebed76a1736c4a755e0ed8ec38c58a0b7abb409cfb82bdb40bd3e9a63208b5016a5f68a8a01dbee6f2cc2dada268af743a7fc4ecc4208d912fd1915538a58c1a');

        $this->_httpClient->connect();
        $this->bIgnoreErrors = true; //User Warning Expected
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertStringContainsString('dcc="true"',$sReplyBody);
    }

    public function testSuccessfulDCCPresentmentInit()
    {
		$pspID = Constants::iWIRE_CARD_PSP;

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd,salt) VALUES (10018, 1, 100, 'Test Client', 'Tuser', 'Tpass','23lkhfgjh24qsdfkjh')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10018, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10018, 'app')");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10018, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10018, $pspID, '4216310')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid,dccenabled) VALUES (10018, 8, $pspID,100,true)");
		$this->queryDB("INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid, enabled) VALUES (10018,100,840, true)");

		$this->queryDB("INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (8,10018,840,156,'true','true')");
		$this->queryDB("INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (8,10018,840,360,'true','true')");

		$xml = $this->getInitDoc(10018, 1100, 840, null, 1000, 'ebed76a1736c4a755e0ed8ec38c58a0b7abb409cfb82bdb40bd3e9a63208b5016a5f68a8a01dbee6f2cc2dada268af743a7fc4ecc4208d912fd1915538a58c1a');

		$this->_httpClient->connect();
		$this->bIgnoreErrors = true; // User Warning Expected
		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
		$sReplyBody = $this->_httpClient->getReplyBody ();
		$this->assertStringContainsString('dcc="true"', $sReplyBody );
		$this->assertStringContainsString('presentment-currency="true"', $sReplyBody );
		$this->assertStringContainsString('<settlement-currencies><settlement-currency><id>156</id></settlement-currency><settlement-currency><id>360</id></settlement-currency></settlement-currencies>', $sReplyBody);
	}

    public function testFailureDCCPresentmentInit()
    {
		$pspID = Constants::iWIRE_CARD_PSP;

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd,salt) VALUES (10018, 1, 100, 'Test Client', 'Tuser', 'Tpass','23lkhfgjh24qsdfkjh')" );
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10018, 4, 'http://mpoint.local.cellpointmobile.com:80/')" );
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10018, 'app')" );
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10018, 'CPM', true)" );
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10018, $pspID, '4216310')" );
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')" );
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid,dccenabled) VALUES (10018, 8, $pspID,100,true)" );
		$this->queryDB("INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid, enabled) VALUES (10018,100,840, true)" );

		$xml = $this->getInitDoc(10018, 1100, 840, null, 1000, 'ebed76a1736c4a755e0ed8ec38c58a0b7abb409cfb82bdb40bd3e9a63208b5016a5f68a8a01dbee6f2cc2dada268af743a7fc4ecc4208d912fd1915538a58c1a' );

		$this->_httpClient->connect ();
		$this->bIgnoreErrors = true; // User Warning Expected
		$iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass' ), $xml );
		$sReplyBody = $this->_httpClient->getReplyBody ();
		$this->assertStringContainsString('dcc="true"', $sReplyBody );
		$this->assertStringContainsString('presentment-currency="false"', $sReplyBody );
	}

    public function testBadRequestInvalidRequestBody()
    {
        $this->markTestIncomplete('Duplicate Test case - Already covered in InitializeAPIValidationTest ');
    }

	public function failed_testUnsupportedMediaType()
	{
	    $this->markTestIncomplete('Duplicate Test case - Already covered in InitializeAPIValidationTest ');
	}

    public function testBadRequestDisabledClient()
    {
        $this->markTestIncomplete('Duplicate Test case - Already covered in InitializeAPIValidationTest ');
    }

    public function testDisabledAccount()
    {
        $this->markTestIncomplete('Duplicate Test case - Already covered in InitializeAPIValidationTest ');
	}

	public function testUnauthorized()
	{
	    $this->markTestIncomplete('Duplicate Test case - Already covered in InitializeAPIValidationTest ');
	}

	public function testWrongUsernamePassword()
	{
	    $this->markTestIncomplete('Duplicate Test case - Already covered in InitializeAPIValidationTest ');
	}

	public function testEmptyCardConfiguration()
	{
	    $this->markTestIncomplete('Duplicate Test case - Already covered in InitializeAPIValidationTest ');
    }

    public function testEmptyCardConfigurationWithCurrency()
    {
        $this->markTestIncomplete('Duplicate Test case - Already covered in InitializeAPIValidationTest ');
    }

	public function testSoftDisabledCardType()
	{
	    $this->markTestIncomplete('Duplicate Test case - Already covered in InitializeAPIValidationTest ');
	}

	public function testHardDisabledCardType()
	{
	    $this->markTestIncomplete('Duplicate Test case - Already covered in InitializeAPIValidationTest ');
	}

    public function testEmptyCurrencyId()
    {
        $this->markTestIncomplete('Duplicate Test case - Already covered in InitializeAPIValidationTest ');
    }

    public function testEuaIdPasswordFlow()
	{
	    $this->markTestIncomplete('Duplicate Test case - Already covered in InitializeAPIValidationTest ');
	}

    public function testSSOFailForStoredCard()
	{
	    $this->markTestIncomplete('Duplicate Test case - Already covered in InitializeAPIValidationTest ');
	}

	public function testSSOTimeoutForStoredCard()
	{
	    $this->markTestIncomplete('Duplicate Test case - Already covered in InitializeAPIValidationTest ');
	}

	public function testSSOSuccessForStoredCard()
	{
	    $this->markTestIncomplete('Duplicate Test case - Already covered in InitializeAPIValidationTest ');
    }

	public function testSSOMissingAuthToken()
	{
	    $this->markTestIncomplete('Duplicate Test case - Already covered in InitializeAPIValidationTest ');
    }

    public function testInvalidTransactionAmount()
    {
        $this->markTestIncomplete('Duplicate Test case - Already covered in InitializeAPIValidationTest ');
    }

    public function testAttemptNumber()
    {
        $this->markTestIncomplete('Duplicate Test case - Already covered in InitializeAPIValidationTest ');
    }

    public function testStaticRouteLevelConfiguration()
	{
	    $this->markTestIncomplete('Duplicate Test case - Already covered in InitializeAPIValidationTest ');
	}

	public function testCardNodes()
	{
	    $this->markTestIncomplete('Duplicate Test case - Already covered in InitializeAPIValidationTest ');
	}

	public function testInvalidEmailAddress()
    {
        $this->markTestIncomplete('Duplicate Test case - Already covered in InitializeAPIValidationTest ');
    }

    public function testInvalidFXServiceTypeID()
    {
        $this->markTestIncomplete('Duplicate Test case - Already covered in InitializeAPIValidationTest ');
    }
    public function testStoredFXServiceTypeID()
    {
        $this->markTestIncomplete('Duplicate Test case - Already covered in InitializeAPIValidationTest ');
    }
}