<?php

require_once __DIR__ . '/../inc/testinclude.php';

class CaptureAPITest extends mPointBaseAPITest
{

    public function __construct()
    {
        global $aMPOINT_CONN_INFO;
        $aMPOINT_CONN_INFO['path'] = "/buy/capture.php";
        $aMPOINT_CONN_INFO["contenttype"] = "application/x-www-form-urlencoded";
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO) );
    }


    public function testPaymentAlreadyCaptured()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name) VALUES (113, 1, 100, 'Test Client')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, amount, ip, enabled) VALUES (1001001, 100, 113, 1100, 100, 5000, '127.0.0.1', TRUE)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_CAPTURED_STATE. ")");

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders(), 'clientid=113&account=1100&mpointid=1001001');
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(400, $iStatus);
        $this->assertEquals("msg=176", $sReplyBody);
    }

    public function testPaymentRefunded()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name) VALUES (113, 1, 100, 'Test Client')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, amount, ip, enabled) VALUES (1001001, 100, 113, 1100, 100, 5000, '127.0.0.1', TRUE)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_CAPTURED_STATE. ")");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_REFUNDED_STATE. ")");

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders(), 'clientid=113&account=1100&mpointid=1001001');
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(400, $iStatus);
        $this->assertEquals("msg=177", $sReplyBody);
    }

//
//    public function testInvalidTransactionState()
//    {
//        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name) VALUES (113, 1, 100, 'Test Client')");
//        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
//        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
//        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, amount, ip, enabled) VALUES (1001001, 100, 113, 1100, 100, 5000, '127.0.0.1', FALSE)");
//
//        global $_OBJ_DB;
//        global $_OBJ_TXT;
//        global $_OBJ_HTTP;
//
//        // Unknown Client ID
//        ob_end_clean();
//        ob_start();
//        $_REQUEST = array();
//        $_REQUEST['clientid'] = 113;
//        $_REQUEST['account'] = 1100;
//        $_REQUEST['mpointid'] = 1001001;
//
//        include 'capture.php';
//        $body = ob_get_clean();
//
//        $this->assertTrue($_OBJ_HTTP instanceof HTTPMock);
//        $this->assertEquals($body, "msg=173");
//    }
//
//    public function testTransactionDisabled()
//    {
//        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name) VALUES (113, 1, 100, 'Test Client')");
//        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
//        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
//        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, amount, ip, enabled) VALUES (1001001, 100, 113, 1100, 100, 5000, '127.0.0.1', FALSE)");
//        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");
//
//        global $_OBJ_DB;
//        global $_OBJ_TXT;
//        global $_OBJ_HTTP;
//
//        ob_end_clean();
//        ob_start();
//        $_REQUEST = array();
//        $_REQUEST['clientid'] = 113;
//        $_REQUEST['account'] = 1100;
//        $_REQUEST['mpointid'] = 1001001;
//
//        include 'capture.php';
//        $body = ob_get_clean();
//
//        $this->assertTrue($_OBJ_HTTP instanceof HTTPMock);
//        $this->assertEquals($body, "msg=174");
//    }
//
//    public function testPaymentRejectedState()
//    {
//        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name) VALUES (113, 1, 100, 'Test Client')");
//        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
//        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
//        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, amount, ip) VALUES (1001001, 100, 113, 1100, 100, 5000, '127.0.0.1')");
//        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");
//        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_REJECTED_STATE. ")");
//
//        global $_OBJ_DB;
//        global $_OBJ_TXT;
//        global $_OBJ_HTTP;
//
//        ob_end_clean();
//        ob_start();
//        $_REQUEST = array();
//        $_REQUEST['clientid'] = 113;
//        $_REQUEST['account'] = 1100;
//        $_REQUEST['mpointid'] = 1001001;
//
//        include 'capture.php';
//        $body = ob_get_clean();
//
//        $this->assertTrue($_OBJ_HTTP instanceof HTTPMock);
//        $this->assertEquals($body, "msg=175");
//    }
//
//    public function testPaymentAlreadyCapturedState()
//    {
//        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name) VALUES (113, 1, 100, 'Test Client')");
//        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
//        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
//        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, amount, ip) VALUES (1001001, 100, 113, 1100, 100, 5000, '127.0.0.1')");
//        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");
//        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_CAPTURED_STATE. ")");
//
//        global $_OBJ_DB;
//        global $_OBJ_TXT;
//        global $_OBJ_HTTP;
//
//        ob_end_clean();
//        ob_start();
//        $_REQUEST = array();
//        $_REQUEST['clientid'] = 113;
//        $_REQUEST['account'] = 1100;
//        $_REQUEST['mpointid'] = 1001001;
//
//        include 'capture.php';
//        $body = ob_get_clean();
//
//        $this->assertTrue($_OBJ_HTTP instanceof HTTPMock);
//        $this->assertEquals($body, "msg=176");
//    }
//
//    public function testPaymentRefundedState()
//    {
//        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name) VALUES (113, 1, 100, 'Test Client')");
//        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
//        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
//        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, amount, ip) VALUES (1001001, 100, 113, 1100, 100, 5000, '127.0.0.1')");
//        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");
//        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_CAPTURED_STATE. ")");
//        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_REFUNDED_STATE. ")");
//
//        global $_OBJ_DB;
//        global $_OBJ_TXT;
//        global $_OBJ_HTTP;
//
//        ob_end_clean();
//        ob_start();
//        $_REQUEST = array();
//        $_REQUEST['clientid'] = 113;
//        $_REQUEST['account'] = 1100;
//        $_REQUEST['mpointid'] = 1001001;
//
//        include 'capture.php';
//        $body = ob_get_clean();
//
//        $this->assertTrue($_OBJ_HTTP instanceof HTTPMock);
//        $this->assertEquals($body, "msg=177");
//    }
//
//    public function testInvalidAmount()
//    {
//        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name) VALUES (113, 1, 100, 'Test Client')");
//        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
//        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', true)");
//        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, amount, ip) VALUES (1001001, 100, 113, 1100, 100, 5000, '127.0.0.1')");
//        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");
//
//        global $_OBJ_DB;
//        global $_OBJ_TXT;
//        global $_OBJ_HTTP;
//
//        ob_end_clean();
//        ob_start();
//        $_REQUEST = array();
//        $_REQUEST['clientid'] = 113;
//        $_REQUEST['account'] = 1100;
//        $_REQUEST['mpointid'] = 1001001;
//
//        //Undefined amount
//        include 'capture.php';
//        $body = ob_get_clean();
//
//        $this->assertTrue($_OBJ_HTTP instanceof HTTPMock);
//        $this->assertEquals("msg=51", $body);
//
//
//        ob_end_clean();
//        ob_start();
//
//        //Too small amount
//        $_REQUEST['amount'] = 0;
//        include 'capture.php';
//        $body = ob_get_clean();
//
//        $this->assertTrue($_OBJ_HTTP instanceof HTTPMock);
//        $this->assertEquals("msg=52", $body);
//
//
//        ob_end_clean();
//        ob_start();
//
//        //Too large amount
//        $_REQUEST['amount'] = 10000;
//        include 'capture.php';
//        $body = ob_get_clean();
//
//        $this->assertTrue($_OBJ_HTTP instanceof HTTPMock);
//        $this->assertEquals("msg=53", $body);
//    }


}