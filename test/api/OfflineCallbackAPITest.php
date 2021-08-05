<?php
/**
 * User: jot
 * Date: 24-03-15
 * Time: 19:46
 */

require_once __DIR__ . '/CallbackAPITest.php';

class OfflineCallbackAPITest extends CallbackAPITest
{

    public function getOfflineCallbackDoc($transactionId, $orderId, $pspID, $cardId, $iTransStatus, $currencyId = 208, $amount = 5000)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<root>';
        $xml .= '<callback>';
        $xml .= '<psp-config id="'.$pspID.'">';
        $xml .= '<name>CellpointMobileCOM</name>';
        $xml .= '</psp-config>';
        $xml .= '<transaction id="'.$transactionId.'" order-no="'.$orderId.'" external-id="-1">';
        $xml .= '<amount country-id="100" currency-id="'.$currencyId.'" currency="DKK">'.$amount.'</amount>';
        $xml .= '<card type-id="'.$cardId.'">';
        $xml .= '</card>';
        $xml .= '</transaction>';
        $xml .= $this->getTransStatus($iTransStatus);
        $xml .= '</callback>';
        $xml .= '</root>';

        return $xml;
    }

    private function setUpPrerequisite(int $pspID, int $cardId, int $stateid, int $currencyId = 208, $amount= 5000)
    {
        $sCallbackURL = '';

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd,salt) VALUES (10018, 1, 100, 'Test Client', 'Tuser', 'Tpass','23lkhfgjh24qsdfkjh')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10018, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10018, 'app')");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10018, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10018, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid,dccenabled) VALUES (10018, $cardId, $pspID,100,true)");
        $this->queryDB("INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid, enabled) VALUES (10018,100,840, true)");
        $this->queryDB("INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type,scope) VALUES ('IS_LEGACY', 'true', 10018, 'client',0)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (100, 10018, 1100, 208, 100, 4001, '1513-005', 5000, 29612109, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,currencyid,convertedcurrencyid,convertedamount,conversionrate) VALUES (1001014, 100, 10018, 1100, 100, $pspID, '1512', '1234abc', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 100,208,208,5000,1)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001014, 100,208,". Constants::iInitializeRequested. ",NULL,'done',10018)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001014, 100,208,NULL,". Constants::iINPUT_VALID_STATE. ",'done',100,10018)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (102,1001014, 5000,208,". Constants::iAuthorizeRequested. ",NULL,'done',10018)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (103,1001014, 5000,208,NULL,". Constants::iPAYMENT_ACCEPTED_STATE. ",'inprogress',102,10018)");

        $xml = $this->getOfflineCallbackDoc(1001014, '900-55150298', $pspID, $cardId, $stateid, $currencyId, $amount);
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        sleep(1);
        $res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl where txnid=1001014 and stateid=".$stateid);
        $this->assertIsResource($res);
        $this->assertEquals(1,pg_num_rows($res));
    }

    public function testSuccessfulCallback()
    {
        $this->setUpPrerequisite( Constants::iCEBUPAYMENTCENTER_APM, Constants::iCEBUPAYMENTCENTEROFFLINE,Constants::iPAYMENT_ACCEPTED_STATE);

        $res =  $this->queryDB("SELECT id FROM Log.txnpassbook_tbl where transactionid= 1001014 and status= 'done' and performedopt=2000" );
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));

    }

     public function testRequestCancelledCallback()
    {
        $this->setUpPrerequisite( Constants::iCEBUPAYMENTCENTER_APM, Constants::iCEBUPAYMENTCENTEROFFLINE,Constants::iPAYMENT_REQUEST_CANCELLED_STATE);

        $res =  $this->queryDB("SELECT id FROM Log.txnpassbook_tbl where transactionid= 1001014 and status= 'error' and performedopt=2000" );
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));
    }

    public function testRequestExpiredCallback()
    {
        $this->setUpPrerequisite( Constants::iCEBUPAYMENTCENTER_APM, Constants::iCEBUPAYMENTCENTEROFFLINE,Constants::iPAYMENT_REQUEST_EXPIRED_STATE);

        $res =  $this->queryDB("SELECT id FROM Log.txnpassbook_tbl where transactionid= 1001014 and status= 'inprogress' and performedopt=2000" );
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));
    }

    public function testPaymentPendingCallback()
    {
        $this->setUpPrerequisite( Constants::iCEBUPAYMENTCENTER_APM, Constants::iCEBUPAYMENTCENTEROFFLINE,Constants::iPAYMENT_PENDING_STATE);

        $res =  $this->queryDB("SELECT id FROM Log.txnpassbook_tbl where transactionid= 1001014 and status= 'inprogress' and performedopt=2000" );
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));


        $res =  $this->queryDB("SELECT stateid, data FROM Log.Message_Tbl  ORDER BY ID ASC");
		$this->assertTrue(is_resource($res) );
    }

    public function testCallbackWithCurrencyChange()
    {
        $this->setUpPrerequisite( Constants::iDragonPay_AGGREGATOR, Constants::iDRAGONPAYOFFLINE,Constants::iPAYMENT_ACCEPTED_STATE, 640, 1000);

        $res =  $this->queryDB("SELECT * FROM Log.txnpassbook_tbl where transactionid= 1001014 and performedopt=2000 and amount=1000 and currencyid = 640 and status= 'done'" );

        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));

    }

    public function testCancelledCallbackWithCurrencyChange()
    {
        $this->setUpPrerequisite( Constants::iDragonPay_AGGREGATOR, Constants::iDRAGONPAYOFFLINE,Constants::iPAYMENT_REQUEST_CANCELLED_STATE, 640, 1000);

        $res =  $this->queryDB("SELECT * FROM Log.txnpassbook_tbl where transactionid= 1001014 and performedopt=2000 and amount=1000 and currencyid = 640 and status= 'error'" );

        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));
    }

    public function testExpiredCallbackWithCurrencyChange()
    {
        $this->setUpPrerequisite( Constants::iDragonPay_AGGREGATOR, Constants::iDRAGONPAYOFFLINE,Constants::iPAYMENT_REQUEST_EXPIRED_STATE, 640, 1000);

        $res =  $this->queryDB("SELECT * FROM Log.txnpassbook_tbl where transactionid= 1001014 and performedopt=2000 and amount=1000 and currencyid = 640 and status= 'inprogress'" );

        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));

    }
}