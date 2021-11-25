<?php
require_once __DIR__ . '/AuthorizeAPITest.php';

class MPIAuthenticateAPITest extends AuthorizeAPITest
{

    public function testSuccessfulAuthenticate()
    {
        $pspID = 4;
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, 47, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 47, '-1')");
        //As per talk with Jona and Simon 2016-07-19 it should not be possible to authorize a disabled card, since the client can ignore flags sent from initialize
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 2, $pspID, true, 1)");
        $this->queryDB("INSERT INTO client.services_tbl (clientid, legacy_flow_enabled,mpi_enabled) VALUES(10099, false,true);");
        $this->queryDB("INSERT INTO client.mpi_config_tbl (clientid, providerid,version,pmid) VALUES(10099, 47,'1.0',8);");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 8, $pspID, '501910******3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid,sessionid,convertedamount,convertedcurrencyid,cardid) VALUES (1001001, 100, 10099, 1100, 1,  $pspID, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE, 208, 1,5000,208,8)");
        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('IS_LEGACY_CALLBACK_FLOW', 'true', 10099, 'client', 0);");
        $this->queryDB("INSERT INTO Client.Route_Tbl (id, clientid, providerid) VALUES (1, 10099, $pspID)");
        $this->queryDB("INSERT INTO Client.Routeconfig_Tbl (id, routeid, name, capturetype, mid, username, password) VALUES (17, 1, 'Wirecard_VISA', 2, 'TESTMID', 'username', 'password')");
        $this->queryDB("INSERT INTO Client.Routeconfig_Tbl (id, routeid, name, capturetype, mid, username, password) VALUES (18, 1, 'Wirecard_VISA', 2, 'TESTMID', 'username', 'password')");
        $this->queryDB("INSERT INTO Client.Routefeature_Tbl (id, clientid, routeconfigid, featureid) VALUES (1, 10099, 18,20)");


        $xml = $this->getAuthDoc(10099, 1100, 1001001, 5000, 'profilePass',0,'',null,null,8);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals($iStatus,303);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="2005" sub-code="2005002">3D Secure Verification Required</status><web-method></web-method><return-url></return-url><card-mask>401636******0010</card-mask><expiry>01/24</expiry><token>81770143dcb3ca014999c13501532a1c0b19229a287520f90499bfbce4eac0cf80869a4fce4adfafd6a5055f4517b63056fa5a8c012e820e7a7a95b621aeaf3a</token></root>',$sReplyBody);
        $res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY ID ASC");
        $this->assertTrue(is_resource($res) );

        $aStates = array();
        while ($row = pg_fetch_assoc($res) )
        {
            $aStates[] = $row["stateid"];
        }
        $this->assertEquals(count($aStates),3);
        $this->assertEquals($aStates[0],2008);
        $this->assertEquals($aStates[1],2005);
        $this->assertEquals($aStates[2],2005002);

        $res =  $this->queryDB("SELECT token FROM Log.TRANSACTION_tbl WHERE id = 1001001  ORDER BY ID ASC");
        $this->assertTrue(is_resource($res) );
        $token = '';
        while ($row = pg_fetch_assoc($res) )
        {
            $token = $row["token"];
        }
        $this->assertEquals($token,"81770143dcb3ca014999c13501532a1c0b19229a287520f90499bfbce4eac0cf80869a4fce4adfafd6a5055f4517b63056fa5a8c012e820e7a7a95b621aeaf3a");
    }
}