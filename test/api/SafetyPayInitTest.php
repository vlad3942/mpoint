<?php
/**
 * User: Nitin Gaikwad
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package:
 * File Name:SafetyPayInitTest.php
 */

require_once __DIR__. '/initializeAPIValidationTest.php';

class SafetyPayInitTest extends InitializeAPIValidationTest
{
    public function testSuccessfulSafetyPayInit()
    {
        $pspID = Constants::iSAFETYPAY_AGGREGATOR;

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10099, 'app')");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid,psp_type) VALUES (10099, 63, $pspID, 405, 7)");
        $this->queryDB("INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid, enabled) VALUES (10099,100,170, true)");

        $xml = $this->getInitDoc(10099, 1100, 170, null, 1000, null, null, null, null, null, null, '2.0', 0, 405 );

        $this->_httpClient->connect();
        $this->bIgnoreErrors = true; //User Warning Expected
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertStringContainsString('payment-method',$sReplyBody);
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