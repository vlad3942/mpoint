<?php
/**
 * User: jot
 * Date: 24-03-15
 * Time: 19:46
 */

require_once __DIR__. '/callbackAPITest.php';

class DCCCallbackAPITest extends CallbackAPITest
{
    public function testSuccessfulDCCCallback()
    {
        $pspID = Constants::iWIRE_CARD_PSP;
        $externalRefId = Constants::iForeignExchange;
        $sCallbackURL = '';
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd,salt) VALUES (10018, 1, 100, 'Test Client', 'Tuser', 'Tpass','23lkhfgjh24qsdfkjh')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10018, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10018, 'app')");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10018, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10018, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid,dccenabled) VALUES (10018, 8, $pspID,100,true)");
        $this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT C.currencyid * -1 AS pricepointid, 8 FROM System.Country_Tbl C, System.Card_Tbl Card WHERE C.id = 100 GROUP BY pricepointid;");
        $this->queryDB("INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid, enabled) VALUES (10018,100,840, true)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (100, 10018, 1100, 208, 100, 4001, '1513-005', 5000, 29612109, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,currencyid,convetredcurrencyid,convertedamount) VALUES (1001014, 100, 10018, 1100, 100, $pspID, '1512', '1234abc', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 100,208,840,1000)");
        $this->queryDB("INSERT INTO Log.externalreference_tbl (txnid,externalid,pspid,type) VALUES (1001014, 100, $pspID,$externalRefId)");

        $xml = $this->getCallbackDoc(1001014, '900-55150298', $pspID, Constants::iPAYMENT_ACCEPTED_STATE,false);
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl where txnid=1001014 and stateid=".Constants::iCBFX_CONSTRUCTED_STATE);
        $this->assertTrue(is_resource($res) && pg_num_rows($res) == 1);
        $res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl where txnid=1001014 and stateid=".Constants::iCBFX_ACCEPTED_STATE);
        $this->assertTrue(is_resource($res) && pg_num_rows($res) == 1 );

        $xml = $this->getCallbackDoc(1001014, '900-55150298', $pspID, Constants::iPAYMENT_CAPTURED_STATE,false);
        $this->constHTTPClient();
        $this->_httpClient->connect();
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl where txnid=1001014 and stateid=".Constants::iCBFX_CONSTRUCTED_STATE);
        $this->assertTrue(is_resource($res) && pg_num_rows($res) == 2);
        $res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl where txnid=1001014 and stateid=".Constants::iCBFX_ACCEPTED_STATE);
        $this->assertTrue(is_resource($res) && pg_num_rows($res) == 2 );

    }

}