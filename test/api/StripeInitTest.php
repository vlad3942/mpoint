<?php

require_once __DIR__ . '/InitializeAPIValidationTest.php';

class StripeInitTest extends InitializeAPIValidationTest
{
    public function testSuccessfullInit()
    {
        $pspID = Constants::iSTRIPE_PSP;
        $clientId = 10099;
        $accountId = 1100;
        $countryId = 100;
        $currencyId = 170;
        $captureTypeId = 2;
        $pspTypeId = 2;
        $cardId = 7;

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES ($clientId, 1, $countryId, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES ($clientId, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES ($accountId, $clientId, 'app')");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, $clientId, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, $clientId, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES ($accountId, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid, psp_type, capture_type) VALUES ($clientId, $cardId, $pspID, $countryId, $pspTypeId, $captureTypeId)");
        $this->queryDB("INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid, enabled) VALUES ($clientId, $countryId, $currencyId, true)");

        $xml = $this->getInitDoc($clientId, $accountId, $currencyId, null, 1000, null, null, null, null, null, null, '2.0', 0, $countryId );

        $this->_httpClient->connect();
        $this->bIgnoreErrors = true; //User Warning Expected
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertStringContainsString('transaction',$sReplyBody);
    }

}