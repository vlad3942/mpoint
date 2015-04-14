<?php

require_once __DIR__ . '/../inc/testinclude.php';

abstract class CaptureAPITest extends mPointBaseAPITest
{
    protected $_aMPOINT_CONN_INFO;

    public function __construct()
    {
        $this->constHTTPClient();
    }

    public function constHTTPClient()
    {
        global $aMPOINT_CONN_INFO;
        $aMPOINT_CONN_INFO['path'] = "/buy/capture.php";
        $aMPOINT_CONN_INFO["contenttype"] = "application/x-www-form-urlencoded";
        $this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO) );
    }

    protected function testSuccessfulCapture($pspID)
    {
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name) VALUES (113, 1, 100, 'Test Client')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '1')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (113, 17, $pspID)"); //Mobilepay
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, callbackurl, amount, ip, enabled, keywordid) VALUES (1001001, 100, 113, 1100, 100, $pspID, '1', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders(), 'clientid=113&account=1100&mpointid=1001001&amount=5000');
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals("msg=1000", $sReplyBody);

        $res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001");
        $this->assertTrue(is_resource($res) );

        $aStates = array();
        while ($row = pg_fetch_assoc($res) )
        {
            $aStates[] = $row["stateid"];
        }

        $this->assertTrue(is_int(array_search(Constants::iPAYMENT_CAPTURED_STATE, $aStates) ) );
    }

}
