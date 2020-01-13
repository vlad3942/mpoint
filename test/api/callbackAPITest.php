<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class CallbackAPITest extends baseAPITest
{
    protected $_aMPOINT_CONN_INFO;

    public function __construct()
    {
        $this->constHTTPClient();
    }

    public function constHTTPClient()
    {
        global $aMPOINT_CONN_INFO;
        $aMPOINT_CONN_INFO['path'] = "/callback/general.php";
        $aMPOINT_CONN_INFO["contenttype"] = "text/xml";
        $this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO) );
    }

    private function getTransStatus($iTransStatus)
    {
        switch ($iTransStatus)
        {
            case Constants::iPAYMENT_ACCEPTED_STATE:
                $status = '<status code="2000">Transaction is Authorized.</status>';
                break;
            case Constants::iPAYMENT_CAPTURED_STATE:
                $status = '<status code="2001">Transaction is Captured.</status>';
                break;
            case Constants::iPAYMENT_CANCELLED_STATE:
                $status = '<status code="2002">Transaction is Cancelled.</status>';
                break;
            case Constants::iPAYMENT_REFUNDED_STATE:
                $status = '<status code="2003">Transaction is Refunded.</status>';
                break;
            default:
                $status = '<status code="999">Unknown Error.</status>';
        }
        return $status;
    }

    private function getCallbackDoc($transactionId, $orderId, $pspID, $iTransStatus, $bSendToken = true)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<root>';
        $xml .= '<callback>';
        $xml .= '<psp-config id="'.$pspID.'">';
        $xml .= '<name>CellpointMobileCOM</name>';
        $xml .= '</psp-config>';
        $xml .= '<transaction id="'.$transactionId.'" order-no="'.$orderId.'" external-id="-1">';
        $xml .= '<amount country-id="100" currency="DKK">10000</amount>';
        $xml .= '<card type-id="8">';
        $xml .= '<card-number>401200******6002</card-number>';
        if($bSendToken == true) {
            $xml .= '<token>4819253888096002</token>';
        }
        $xml .= '<expiry>';
        $xml .= '<month>01</month>';
        $xml .= '<year>20</year>';
        $xml .= '</expiry>';
        $xml .= '</card>';
        $xml .= '</transaction>';
        $xml .= $this->getTransStatus($iTransStatus);
        $xml .= '<approval-code>035747</approval-code>';
        $xml .= '</callback>';
        $xml .= '</root>';

        return $xml;
    }


    public function successfulCallbackAccepted($pspID, $iTransStatus)
    {
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, ".$pspID.", '1')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (113, 8, $pspID)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '900-55150298', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, pspid, callbackurl, amount, ip, enabled, keywordid, sessionid) VALUES (1001001, '900-55150298', 100, 113, 1100, 100, null, '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1)");
        $this->queryDB("INSERT INTO log.message_tbl (txnid, stateid) VALUES (1001001, $iTransStatus)");
        $this->queryDB("INSERT INTO log.message_tbl (txnid, stateid) VALUES (1001001, 1991)");
        $this->queryDB("INSERT INTO log.message_tbl (txnid, stateid) VALUES (1001001, 1992)");
        $this->queryDB("INSERT INTO log.message_tbl (txnid, stateid) VALUES (1001001, 1990)");

        $xml = $this->getCallbackDoc(1001001, '900-55150298', $pspID, $iTransStatus);
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(202, $iStatus);
        $this->assertEquals("", $sReplyBody);

        $res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001");
        $this->assertTrue(is_resource($res) );

        $aStates = array();
        while ($row = pg_fetch_assoc($res) )
        {
            $aStates[] = $row["stateid"];
        }

        $this->assertEquals(4, count($aStates));
        $this->assertTrue(is_int(array_search($iTransStatus, $aStates) ) );
    }

    public function successfulAutoCapture($pspID, $iTransStatus)
    {
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd, auto_capture) VALUES (113, 1, 100, 'Test Client', 'Tusername', 'Tpassword', true)");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, ".$pspID.", '1')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (113, 8, $pspID)");

        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");

        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '900-55150298', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, pspid, callbackurl, amount, ip, enabled, keywordid, sessionid, auto_capture) VALUES (1001001, '900-55150298', 100, 113, 1100, 100, null, '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1, 1)");

        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status) VALUES (100,1001001, 5000,208,". Constants::iInitializeRequested. ",NULL,'done')");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref) VALUES (101,1001001, 5000,208,NULL,". Constants::iINPUT_VALID_STATE. ",'done',100)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status) VALUES (102,1001001, 5000,208,". Constants::iAuthorizeRequested. ",NULL,'done')");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref) VALUES (103,1001001, 5000,208,NULL,". Constants::iPAYMENT_ACCEPTED_STATE. ",'done',102)");

        $xml = $this->getCallbackDoc(1001001, '900-55150298', $pspID, $iTransStatus, false);
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(202, $iStatus);
        $this->assertEquals("", $sReplyBody);

        $res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001");
        $this->assertTrue(is_resource($res) );

        $aStates = array();
        $retries = 0;
        while ($retries++ <= 5)
        {
            $res = $this->queryDB("SELECT t.extid, t.pspid, t.amount, m.stateid FROM Log.Transaction_Tbl t, Log.Message_Tbl m WHERE m.txnid = t.id AND t.id = 1001001 ORDER BY m.id ASC");
            $this->assertTrue(is_resource($res) );
            $aStates = array();
            $trow = null;
            while ($row = pg_fetch_assoc($res) )
            {
                $trow = $row;
                $aStates[] = $row["stateid"];
            }
            if (count($aStates) >= 9) { break; }
            usleep(200000);// As callback happens asynchroniously, sleep a bit here in order to wait for transaction to complete in other thread
        }

        $this->assertTrue(is_int(array_search(Constants::iPAYMENT_ACCEPTED_STATE, $aStates) ) );
        $this->assertTrue(is_int(array_search(Constants::iPAYMENT_CAPTURED_STATE, $aStates) ) );
    }

    public function successfulNoAutoCapture($pspID, $iTransStatus)
    {
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd, auto_capture) VALUES (113, 1, 100, 'Test Client', 'Tusername', 'Tpassword', false)");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, ".$pspID.", '1')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (113, 8, $pspID)");

        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (113, 5001)");

        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '900-55150298', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, pspid, callbackurl, amount, ip, enabled, keywordid, sessionid, auto_capture) VALUES (1001001, '900-55150298', 100, 113, 1100, 100, null, '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1, 0)");

        $xml = $this->getCallbackDoc(1001001, '900-55150298', $pspID, $iTransStatus, false);
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(202, $iStatus);
        $this->assertEquals("", $sReplyBody);

        $res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001");
        $this->assertTrue(is_resource($res) );

        $aStates = array();
        $retries = 0;
        while ($retries++ <= 5)
        {
            $res = $this->queryDB("SELECT t.extid, t.pspid, t.amount, m.stateid FROM Log.Transaction_Tbl t, Log.Message_Tbl m WHERE m.txnid = t.id AND t.id = 1001001 ORDER BY m.id ASC");
            $this->assertTrue(is_resource($res) );
            $aStates = array();
            $trow = null;
            while ($row = pg_fetch_assoc($res) )
            {
                $trow = $row;
                $aStates[] = $row["stateid"];
            }
            if (count($aStates) >= 4) { break; }
            usleep(200000);// As callback happens asynchroniously, sleep a bit here in order to wait for transaction to complete in other thread
        }

        $this->assertTrue(is_int(array_search(Constants::iPAYMENT_ACCEPTED_STATE, $aStates) ) );
    }
}
