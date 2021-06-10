<?php


require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';


class PostStatusAPITest extends baseAPITest
{
    protected $_aMPOINT_CONN_INFO;

    public function __construct()
    {
        parent::__construct();
        $this->constHTTPClient();
    }

    public function constHTTPClient()
    {
        global $aMPOINT_CONN_INFO;
        $aMPOINT_CONN_INFO['path'] = "/mApp/api/post_status.php";
        $aMPOINT_CONN_INFO["contenttype"] = "application/x-www-form-urlencoded";
        $aMPOINT_CONN_INFO["method"] = "POST";
        $this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO) );
    }

    protected function getPostSessionStatusCallbackDoc($id, $amount, $orderno, $typeId, $statusCode)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<root>';
        $xml .= '<callback>';
        $xml .= '<session id="'. $id .'" order-no="'.$orderno.'" type-id="'.$typeId.'">';
        $xml .= '<amount country-id="100">'. $amount .'</amount>';
        $xml .= '</session>';
        $xml .= '<status code="' . $statusCode . '"/>';
        $xml .= '</callback>';
        $xml .= '</root>';

        return $xml;
    }

    public function testPostSessionState()
    {
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name, username, passwd) VALUES (1, 10099, 25, '1', 'Tusername', 'Tpassword')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 25, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10099, 17, 25)");
        $this->queryDB("INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type,scope) VALUES ('IS_LEGACY', 'true', 10099, 'client',0)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 208, 100, 4001, '1513-2001', 5000, 29612109, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1001001,". Constants::iPURCHASE_VIA_APP .", 10099, 1100, 100, 25, '1515', '1513-2001', '$sCallbackURL', 5000, '127.0.0.1', TRUE, 1, 1,5000)");
        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('IS_LEGACY_CALLBACK_FLOW', 'true', 10099, 'client', 0);");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_CAPTURED_STATE. ")");

        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001001, 5000,208,". Constants::iInitializeRequested. ",NULL,'done',10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001001, 5000,208,NULL,". Constants::iINPUT_VALID_STATE. ",'done',100,10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (102,1001001, 5000,208,". Constants::iAuthorizeRequested. ",NULL,'done',10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (103,1001001, 5000,208,NULL,". Constants::iPAYMENT_ACCEPTED_STATE. ",'done',102,10099)");

        $xml = $this->getPostSessionStatusCallbackDoc(1, 5000, '1513-2001', Constants::iPURCHASE_VIA_APP, '4020');

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);

        $this->assertEquals(200, $iStatus);

        $res =  $this->queryDB("SELECT t.stateid FROM Log.Session_tbl t WHERE t.id = 1");
        $this->assertTrue(is_resource($res) );
        $row = pg_fetch_assoc($res);
        $this->assertEquals(4020, $row["stateid"]);

        $res =  $this->queryDB("SELECT count(id) FROM Log.Message_Tbl t WHERE t.txnid = 1001001");
        $this->assertTrue(is_resource($res) );
        $row = pg_fetch_assoc($res);
        $this->assertEquals(7, $row["count"]);
    }

    public function testPostSessionInvalidState()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name, username, passwd) VALUES (1, 10099, 25, '1', 'Tusername', 'Tpassword')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 25, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10099, 17, 25)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 208, 100, 4030, '1513-2001', 5000, 29612109, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1001001,". Constants::iPURCHASE_VIA_APP .", 10099, 1100, 100, 25, '1515', '1513-2001', '', 5000, '127.0.0.1', TRUE, 1, 1,5000)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_CAPTURED_STATE. ")");

        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001001, 5000,208,". Constants::iInitializeRequested. ",NULL,'done',10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001001, 5000,208,NULL,". Constants::iINPUT_VALID_STATE. ",'done',100,10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (102,1001001, 5000,208,". Constants::iAuthorizeRequested. ",NULL,'done',10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (103,1001001, 5000,208,NULL,". Constants::iPAYMENT_ACCEPTED_STATE. ",'done',102,10099)");

        $xml = $this->getPostSessionStatusCallbackDoc(1, 5000, '1513-2001', Constants::iPURCHASE_VIA_APP, '4020');

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);

        $this->assertEquals(200, $iStatus);

        $res =  $this->queryDB("SELECT t.stateid FROM Log.Session_tbl t WHERE t.id = 1");
        $this->assertTrue(is_resource($res) );
        $row = pg_fetch_assoc($res);
        $this->assertEquals(4030, $row["stateid"]);

        $res =  $this->queryDB("SELECT count(id) FROM Log.Message_Tbl t WHERE t.txnid = 1001001");
        $row = pg_fetch_assoc($res);
        $this->assertEquals(2, $row['count']);
    }
}