<?php
/**
 * User: jot
 * Date: 24-03-15
 * Time: 19:46
 */

require_once __DIR__. '/payAPITest.php';

class DCCPayAPITest extends PayAPITest
{
    public function testSuccessfulPay()
    {
        $pspID = Constants::iWIRE_CARD_PSP;
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd,salt) VALUES (10018, 1, 100, 'Test Client', 'Tuser', 'Tpass','23lkhfgjh24qsdfkjh')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10018, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10018, 'app')");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10018, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10018, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid,dccenabled) VALUES (10018, 8, $pspID,100,true)");
        $this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) SELECT C.currencyid * -1 AS pricepointid, 8 FROM System.Country_Tbl C, System.Card_Tbl Card WHERE C.id = 100 GROUP BY pricepointid;");
        $this->queryDB("INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid, enabled) VALUES (10018,100,840, true)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10018, 1100, 208, 100, 4001, '1513-005', 5000, 29612109, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,currencyid,convertedamount,convertedcurrencyid) VALUES (1001011, 100, 10018, 1100, 100, $pspID, '1512', '1234abc', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,840,5000,840)");

        $aDccParams = array(
            "12345",
            "4","840","5000"
        );
        $xml = $this->getPayDoc(10018, 1100, 1001011,8, false, 208,20000,'4874b565db376dffc0801d91e2bdb5b9d2a3f610917753204cd15a30357e42984cabceb6055495a6f95a7f761270dc9e9c9a04fd45d2a4992f36f3f8799a77f5',$aDccParams);
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(200, $iStatus);
        $this->assertContains('<?xml version="1.0" encoding="UTF-8"?><root><psp-info id="'. $pspID. '" merchant-account="4216310"  type="1">', $sReplyBody);
        $res =  $this->queryDB("SELECT externalid FROM log.externalreference_tbl where txnid=1001011 and type=".Constants::iForeignExchange);

        $this->assertTrue(is_resource($res) );
        $res =  $this->queryDB("SELECT convertedcurrencyid FROM Log.Transaction_Tbl where id=1001011 and convertedcurrencyid = 208 and currencyid=840 and convertedamount=20000");
        $this->assertTrue(is_resource($res)&& pg_num_rows($res) == 1 );
    }

}