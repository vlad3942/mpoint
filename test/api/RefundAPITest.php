<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

abstract class RefundAPITest extends baseAPITest
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
        $aMPOINT_CONN_INFO['path'] = "/buy/refund.php";
        $aMPOINT_CONN_INFO["contenttype"] = "application/x-www-form-urlencoded";
        $this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO) );
    }

    protected function testSuccessfulRefund($pspID)
    {
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name, username, passwd) VALUES (1, 10099, $pspID, '1', 'Tusername', 'Tpassword')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10099, 17, $pspID)"); //Mobilepay
        $this->queryDB("INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type,scope) VALUES ('IS_LEGACY', 'true', 10099, 'client',0)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 208, 100, 4001, '1513-2001', 5000, 29612109, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1001001,". Constants::iPURCHASE_VIA_APP .", 10099, 1100, 100, $pspID, '1515', '1513-2001', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,5000)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_CAPTURED_STATE. ")");

        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001001, 5000,208,". Constants::iInitializeRequested. ",NULL,'done',10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001001, 5000,208,NULL,". Constants::iINPUT_VALID_STATE. ",'done',100,10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (102,1001001, 5000,208,". Constants::iAuthorizeRequested. ",NULL,'done',10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (103,1001001, 5000,208,NULL,". Constants::iPAYMENT_ACCEPTED_STATE. ",'done',102,10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (104,1001001, 5000,208,". Constants::iCaptureRequested. ",NULL,'done',10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (105,1001001, 5000,208,NULL,". Constants::iPAYMENT_CAPTURED_STATE. ",'done',104,10099)");

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders(), 'username=Tusername&password=Tpassword&clientid=10099&account=1100&mpointid=1001001&orderid=1513-2001&amount=5000');
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals("msg=1000", $sReplyBody);

        $retries=0;
        $aStates = [];
        while ($retries++ <= 4)
        {
            $aStates = [];
            $res = $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY ID ASC");
            $this->assertTrue(is_resource($res));
            while ($row = pg_fetch_assoc($res)) {
                $aStates[] = (int)$row["stateid"];
            }
            if (count($aStates) >= 4) { break; }
            usleep(50000);// As callback happens asynchroniously, sleep a bit here in order to wait for transaction to complete in other thread
        }

        $this->assertContains(Constants::iPAYMENT_REFUNDED_STATE, $aStates);

        //Check refund amount got updated
		$res =  $this->queryDB("SELECT refund FROM Log.transaction_Tbl WHERE id = 1001001");
		$this->assertTrue(is_resource($res) );

		$aRefundAmount = array();
		while ($row = pg_fetch_assoc($res) )
		{
			$aRefundAmount[] = $row["refund"];
		}
		$this->assertEquals(5000, $aRefundAmount[0]);
    }

    protected function testSuccessfulRefundWithAID($pspID)
    {
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name, username, passwd) VALUES (1, 10099, $pspID, '1', 'Tusername', 'Tpassword')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10099, 17, $pspID)"); //Mobilepay
        $this->queryDB("INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type,scope) VALUES ('IS_LEGACY', 'true', 10099, 'client',0)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 208, 100, 4001, '1513-2001', 5000, 29612109, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1001001,". Constants::iPURCHASE_VIA_APP .", 10099, 1100, 100, $pspID, '1515', '1513-2001', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,5000)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_CAPTURED_STATE. ")");
        $this->queryDB("INSERT INTO log.additional_data_tbl(name, value, type, externalid) VALUES('FCTxnID', '243001', 'Transaction','1001001')");
        $this->queryDB("INSERT INTO Log.Order_Tbl (id, orderref, txnid, countryid, amount, quantity, productsku, productname, productdescription, productimageurl, points, reward,fees) VALUES (10, 'SOCGN6', 1001001, 100, 100, 1, '103-1418291', 'return journey', 'return journey', 'https://www.cpm.com', 300, 1, 0);");
        $this->queryDB("INSERT INTO Log.Flight_Tbl (id, service_class,mkt_flight_number, departure_airport, arrival_airport, op_airline_code, order_id, arrival_date, departure_date, tag, trip_count, service_level, departure_countryid, arrival_countryid, departure_timezone) VALUES (10, 'X', '1850', 'CEB', 'MNL', 'PR', '10', '2020-05-23 13:55:00', '2020-05-23 12:40:00', '1', '2', '3', 200, 200, '+08:30')");
        $this->queryDB("INSERT INTO log.passenger_tbl (id, first_name, last_name, type, order_id, created, modified, title, email, mobile, country_id, amount, seq) VALUES (24, 'dan', 'dan', 'ADT', 10, '2021-04-09 13:06:23.420245', '2021-04-09 13:06:23.420245', 'Mr', 'dan@dan.com', '9187231231', '640', 0, 1)");
        $this->queryDB("INSERT INTO log.additional_data_tbl (id, name, value, type, externalid) VALUES (109, 'loyality_id', '345rtyu', 'Passenger', 24);");
        $this->queryDB("INSERT INTO log.billing_summary_tbl (id, order_id, journey_ref, bill_type, description, amount, currency, created, modified, profile_seq, trip_tag, trip_seq, product_code, product_category, product_item) VALUES (45, 10, '', 'Fare', 'adult', '60', 'PHP', '2021-04-09 13:06:23.336965', '2021-04-09 13:06:23.336965', 0, 0, 0, 'ABF', 'FARE', 'Base fare for adult')");
        $this->queryDB("INSERT INTO log.billing_summary_tbl (id, order_id, journey_ref, bill_type, description, amount, currency, created, modified, profile_seq, trip_tag, trip_seq, product_code, product_category, product_item) VALUES (46, 10, '', 'Add-on', 'adult', '60', 'PHP', '2021-04-09 13:06:23.353398', '2021-04-09 13:06:23.353398', 1, 2, 2, 'ABF', 'FARE', 'Base fare for adult')");

        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001001, 5000,208,". Constants::iInitializeRequested. ",NULL,'done',10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001001, 5000,208,NULL,". Constants::iINPUT_VALID_STATE. ",'done',100,10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (102,1001001, 5000,208,". Constants::iAuthorizeRequested. ",NULL,'done',10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (103,1001001, 5000,208,NULL,". Constants::iPAYMENT_ACCEPTED_STATE. ",'done',102,10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (104,1001001, 5000,208,". Constants::iCaptureRequested. ",NULL,'done',10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (105,1001001, 5000,208,NULL,". Constants::iPAYMENT_CAPTURED_STATE. ",'done',104,10099)");

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders(), 'username=Tusername&password=Tpassword&clientid=10099&account=1100&mpointid=1001001&orderid=1513-2001&amount=5000');
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals("msg=1000", $sReplyBody);

        $retries=0;
        $aStates = [];
        while ($retries++ <= 4)
        {
            $aStates = [];
            $res = $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY ID ASC");
            $this->assertTrue(is_resource($res));
            while ($row = pg_fetch_assoc($res)) {
                $aStates[] = (int)$row["stateid"];
            }
            if (count($aStates) >= 4) { break; }
            usleep(50000);// As callback happens asynchroniously, sleep a bit here in order to wait for transaction to complete in other thread
        }

        $this->assertContains(Constants::iPAYMENT_REFUNDED_STATE, $aStates);

        //Check refund amount got updated
        $res =  $this->queryDB("SELECT refund FROM Log.transaction_Tbl WHERE id = 1001001");
        $this->assertTrue(is_resource($res) );

        $aRefundAmount = array();
        while ($row = pg_fetch_assoc($res) )
        {
            $aRefundAmount[] = $row["refund"];
        }
        $this->assertEquals(5000, $aRefundAmount[0]);
    }

	protected function testSuccessfulCancelTriggeredByRefund($pspID)
	{
		$sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name, username, passwd) VALUES (1, 10099, $pspID, '1', 'Tusername', 'Tpassword')");
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid) VALUES (10099, 17, $pspID)"); //Mobilepay
        $this->queryDB("INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type,scope) VALUES ('IS_LEGACY', 'true', 10099, 'client',0)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 208, 100, 4001, '1513-2000', 5000, 29612109, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1001001,". Constants::iPURCHASE_VIA_APP .", 10099, 1100, 100, $pspID, '1515', '1513-2000', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,5000)");
		$this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");

		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001001, 5000,208,". Constants::iInitializeRequested. ",NULL,'done',10099)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001001, 5000,208,NULL,". Constants::iINPUT_VALID_STATE. ",'done',100,10099)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (102,1001001, 5000,208,". Constants::iAuthorizeRequested. ",NULL,'done',10099)");
		$this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (103,1001001, 5000,208,NULL,". Constants::iPAYMENT_ACCEPTED_STATE. ",'done',102,10099)");

		$this->_httpClient->connect();

		$iStatus = $this->_httpClient->send($this->constHTTPHeaders(), 'username=Tusername&password=Tpassword&clientid=10099&account=1100&mpointid=1001001&orderid=1513-2000&amount=5000');
		$sReplyBody = $this->_httpClient->getReplyBody();

		$this->assertEquals(200, $iStatus);
		$this->assertEquals("msg=1001", $sReplyBody);

		$res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY id ASC");
		$this->assertTrue(is_resource($res) );

        $retries=0;
        $aStates = [];
        while ($retries++ <= 4)
        {
            $aStates = [];
            $res = $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY ID ASC");
            $this->assertTrue(is_resource($res));
            while ($row = pg_fetch_assoc($res)) {
                $aStates[] = (int)$row["stateid"];
            }
            if (count($aStates) >= 4) { break; }
            usleep(50000);// As callback happens asynchroniously, sleep a bit here in order to wait for transaction to complete in other thread
        }

		$this->assertContains(Constants::iPAYMENT_CANCELLED_STATE, $aStates);
	}


}
