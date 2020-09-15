<?php

require_once __DIR__. '/authorizeAPITest.php';

class EZYFraudCheckAuthorizeAPITest extends AuthorizeAPITest
{
    public function testPreAuthFraudCheckAccept()
    {
        $pspID = Constants::iWIRE_CARD_PSP;
        $fraudCheckPspID = Constants::iEZY_PSP;

        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (2, 113, 14, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 14, '-1')");

        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (3, 113, 60, 'EZY')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 60, '-1')");

        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (4, 113, 36, 'mvault')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 36, '-1')");


        $this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 15)");
        $this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 8)");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid, enabled, stateid) VALUES (113, 15, $pspID, 100,true, 1)");
        $this->queryDB("INSERT INTO client.cardaccess_tbl (clientid, cardid, pspid, countryid, enabled, stateid, psp_type) VALUES (113, 8, $fraudCheckPspID, 100, true, 1, 9)");//psp_type = Constants::iPROCESSOR_TYPE_FRAUD_GATEWAY
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid, enabled, stateid,walletid) VALUES (113, 8, $pspID, 100, true, 1,14)");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid, enabled, stateid) VALUES (113, 8, $pspID, 100, true, 1)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid,sessionid,convertedamount,convertedcurrencyid,token) VALUES (1001001, 100, 113, 1100, 1,  $pspID, 100, '103-1418291', '" . $sCallbackURL . "', 5000, '127.0.0.1', TRUE, 208, 1,5000,208,'93736e0408d5cd3793615f6e132c89a8f32337483a74739674a5bb2a9c18f6eb91eae4960e5ff9bad1bf62e60282de3c0605ececa6a82f7d14cbe5305fd1983d')");
        $this->queryDB("INSERT INTO log.additional_data_tbl(name, value, type, externalid) VALUES('FCTxnID', '243001', 'Transaction','1001001')");

        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001001, 5000,208,". Constants::iInitializeRequested. ",NULL,'done',113)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001001, 5000,208,NULL,". Constants::iINPUT_VALID_STATE. ",'done',100,113)");

        $xml = $this->getAuthDoc(113, 1100, 1001001, 5000, 'profilePass');

        $this->_httpClient->connect();


        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(200, $iStatus);
        $this->assertContains('<?xml version="1.0" encoding="UTF-8"?><root><status code="2000">Payment authorized</status></root>', $sReplyBody);

        $res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY ID ASC");
        $this->assertTrue(is_resource($res) );

        $aStates = array();
        while ($row = pg_fetch_assoc($res) )
        {
            $aStates[] = $row["stateid"];
        }

        $this->assertEquals(2, count($aStates) );
        $this->assertContains(Constants::iPRE_FRAUD_CHECK_INITIATED_STATE,$aStates );
        $this->assertContains(Constants::iPRE_FRAUD_CHECK_ACCEPTED_STATE,$aStates );
    }


    public function testPreAuthFraudCheckRejected()
    {
        $pspID = Constants::iWIRE_CARD_PSP;
        $fraudCheckPspID = Constants::iEZY_PSP;

        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (2, 113, 14, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 14, '-1')");

        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (3, 113, 60, 'EZY')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 60, '-1')");

        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (4, 113, 36, 'mvault')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 36, '-1')");


        $this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 15)");
        $this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 8)");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid, enabled, stateid) VALUES (113, 15, $pspID, 100,true, 1)");
        $this->queryDB("INSERT INTO client.cardaccess_tbl (clientid, cardid, pspid, countryid, enabled, stateid, psp_type) VALUES (113, 8, $fraudCheckPspID, 100, true, 1, 9)");//psp_type = Constants::iPROCESSOR_TYPE_FRAUD_GATEWAY
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid, enabled, stateid,walletid) VALUES (113, 8, $pspID, 100, true, 1,14)");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid, enabled, stateid) VALUES (113, 8, $pspID, 100, true, 1)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid,sessionid,convertedamount,convertedcurrencyid,token) VALUES (1001001, 100, 113, 1100, 1,  $pspID, 100, '103-1418291', '" . $sCallbackURL . "', 5000, '127.0.0.1', TRUE, 208, 1,5000,208,'93736e0408d5cd3793615f6e132c89a8f32337483a74739674a5bb2a9c18f6eb91eae4960e5ff9bad1bf62e60282de3c0605ececa6a82f7d14cbe5305fd1983d')");
        $this->queryDB("INSERT INTO log.additional_data_tbl(name, value, type, externalid) VALUES('FCTxnID', '243002', 'Transaction','1001001')");

        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001001, 5000,208,". Constants::iInitializeRequested. ",NULL,'done',113)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001001, 5000,208,NULL,". Constants::iINPUT_VALID_STATE. ",'done',100,113)");

        $xml = $this->getAuthDoc(113, 1100, 1001001, 5000, 'profilePass');

        $this->_httpClient->connect();


        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(200, $iStatus);
        $this->assertContains('<?xml version="1.0" encoding="UTF-8"?><root><status code="2010">Authorization Declined Due to Failed Fraud Check And Authorization is not attempted.</status></root>', $sReplyBody);

        $res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY ID ASC");
        $this->assertTrue(is_resource($res) );

        $aStates = array();
        while ($row = pg_fetch_assoc($res) )
        {
            $aStates[] = $row["stateid"];
        }


        $this->assertContains(Constants::iPAYMENT_REJECTED_STATE,$aStates );
        $this->assertContains(Constants::iPRE_FRAUD_CHECK_INITIATED_STATE,$aStates );
        $this->assertContains(Constants::iPRE_FRAUD_CHECK_REJECTED_STATE, $aStates);

    }
    public function getCallbackDoc($transactionId, $orderId, $pspID, $iTransStatus, $bSendToken = true)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<root>';
        $xml .= '<callback>';
        $xml .= '<psp-config id="'.$pspID.'">';
        $xml .= '<name>CellpointMobileCOM</name>';
        $xml .= '</psp-config>';
        $xml .= '<transaction id="'.$transactionId.'" order-no="'.$orderId.'" external-id="-1">';
        $xml .= '<amount country-id="100" currency="DKK">5000</amount>';
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

    public function testPostAuthFraudCheckAccept()
    {
        $pspID = Constants::iWIRE_CARD_PSP;
        $fraudCheckPspID = Constants::iEZY_PSP;

        $sCallbackURL = '';

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (2, 113, 14, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 14, '-1')");

        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (3, 113, 60, 'EZY')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 60, '-1')");

        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (4, 113, 36, 'mvault')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 36, '-1')");


        $this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 15)");
        $this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 8)");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid, enabled, stateid) VALUES (113, 15, $pspID, 100,true, 1)");
        $this->queryDB("INSERT INTO client.cardaccess_tbl (clientid, cardid, pspid, countryid, enabled, stateid, psp_type) VALUES (113, 8, $fraudCheckPspID, 100, true, 1, 10)");//psp_type = Constants::iPROCESSOR_TYPE_FRAUD_GATEWAY
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid, enabled, stateid,walletid) VALUES (113, 8, $pspID, 100, true, 1,14)");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid, enabled, stateid) VALUES (113, 8, $pspID, 100, true, 1)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid,sessionid,convertedamount,convertedcurrencyid,token) VALUES (1001001, 100, 113, 1100, 1,  $pspID, 100, '103-1418291', '" . $sCallbackURL . "', 5000, '127.0.0.1', TRUE, 208, 1,5000,208,'93736e0408d5cd3793615f6e132c89a8f32337483a74739674a5bb2a9c18f6eb91eae4960e5ff9bad1bf62e60282de3c0605ececa6a82f7d14cbe5305fd1983d')");
        $this->queryDB("INSERT INTO log.additional_data_tbl(name, value, type, externalid) VALUES('FCTxnID', '243001', 'Transaction','1001001')");

        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001001, 5000,208,". Constants::iInitializeRequested. ",NULL,'done',113)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001001, 5000,208,NULL,". Constants::iINPUT_VALID_STATE. ",'done',100,113)");

        $xml = $this->getAuthDoc(113, 1100, 1001001, 5000, 'profilePass');

        $this->_httpClient->connect();


        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(200, $iStatus);
        $this->assertContains('<?xml version="1.0" encoding="UTF-8"?><root><status code="2000">Payment authorized</status></root>', $sReplyBody);


        global $aMPOINT_CONN_INFO;
        $aMPOINT_CONN_INFO['path'] = "/callback/general.php";
        $aMPOINT_CONN_INFO["contenttype"] = "text/xml";
        $httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO) );

        $xml = $this->getCallbackDoc(1001001,'tst233',$pspID,Constants::iPAYMENT_ACCEPTED_STATE);
        $httpClient->connect();
        $iStatus = $httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $httpClient->getReplyBody();
        $this->assertEquals(200, $iStatus);
        $res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY ID ASC");
        $this->assertTrue(is_resource($res) );

        $aStates = array();
        while ($row = pg_fetch_assoc($res) )
        {
            $aStates[] = $row["stateid"];
        }

        $this->assertContains(Constants::iPOST_FRAUD_CHECK_INITIATED_STATE,$aStates );
        $this->assertContains(Constants::iPOST_FRAUD_CHECK_ACCEPTED_STATE, $aStates);
    }


    public function testPostAuthFraudCheckRejectedWithNoRollback()
    {
        $pspID = Constants::iWIRE_CARD_PSP;
        $fraudCheckPspID = Constants::iEZY_PSP;

        $sCallbackURL = "";

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (2, 113, 14, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 14, '-1')");

        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (3, 113, 60, 'EZY')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 60, '-1')");

        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (4, 113, 36, 'mvault')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 36, '-1')");


        $this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 15)");
        $this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 8)");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid, enabled, stateid) VALUES (113, 15, $pspID, 100,true, 1)");
        $this->queryDB("INSERT INTO client.cardaccess_tbl (clientid, cardid, pspid, countryid, enabled, stateid, psp_type) VALUES (113, 8, $fraudCheckPspID, 100, true, 1, 10)");//psp_type = Constants::iPROCESSOR_TYPE_FRAUD_GATEWAY
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid, enabled, stateid,walletid) VALUES (113, 8, $pspID, 100, true, 1,14)");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid, enabled, stateid) VALUES (113, 8, $pspID, 100, true, 1)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid,sessionid,convertedamount,convertedcurrencyid,token) VALUES (1001001, 100, 113, 1100, 1,  $pspID, 100, '103-1418291', '" . $sCallbackURL . "', 5000, '127.0.0.1', TRUE, 208, 1,5000,208,'93736e0408d5cd3793615f6e132c89a8f32337483a74739674a5bb2a9c18f6eb91eae4960e5ff9bad1bf62e60282de3c0605ececa6a82f7d14cbe5305fd1983d')");
        $this->queryDB("INSERT INTO log.additional_data_tbl(name, value, type, externalid) VALUES('FCTxnID', '243002', 'Transaction','1001001')");

        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001001, 5000,208,". Constants::iInitializeRequested. ",NULL,'done',113)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001001, 5000,208,NULL,". Constants::iINPUT_VALID_STATE. ",'done',100,113)");

        $xml = $this->getAuthDoc(113, 1100, 1001001, 5000, 'profilePass');

        $this->_httpClient->connect();


        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(200, $iStatus);
        $this->assertContains('<?xml version="1.0" encoding="UTF-8"?><root><status code="2000">Payment authorized</status></root>', $sReplyBody);


        global $aMPOINT_CONN_INFO;
        $aMPOINT_CONN_INFO['path'] = "/callback/general.php";
        $aMPOINT_CONN_INFO["contenttype"] = "text/xml";
        $httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO) );

        $xml = $this->getCallbackDoc(1001001,'tst233',$pspID,Constants::iPAYMENT_ACCEPTED_STATE);
        $httpClient->connect();
        $this->bIgnoreErrors = true;
        $iStatus = $httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $httpClient->getReplyBody();
        $this->assertEquals(200, $iStatus);
        $res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY ID ASC");
        $this->assertTrue(is_resource($res) );

        $aStates = array();
        while ($row = pg_fetch_assoc($res) )
        {
            $aStates[] = $row["stateid"];
        }

        $this->assertContains(Constants::iPOST_FRAUD_CHECK_INITIATED_STATE,$aStates );
        $this->assertContains(Constants::iPOST_FRAUD_CHECK_REJECTED_STATE, $aStates);

    }

    public function testPostAuthFraudCheckRejectedWithRollback()
    {
        $pspID = Constants::iWIRE_CARD_PSP;
        $fraudCheckPspID = Constants::iEZY_PSP;

        $sCallbackURL = "";

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (113, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (113, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 113)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 113, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 113, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (2, 113, 14, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 14, '-1')");

        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (3, 113, 60, 'EZY')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 60, '-1')");

        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (4, 113, 36, 'mvault')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 36, '-1')");


        $this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 15)");
        $this->queryDB("INSERT INTO System.CardPricing_Tbl (pricepointid, cardid) VALUES (-208, 8)");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid, enabled, stateid) VALUES (113, 15, $pspID, 100,true, 1)");
        $this->queryDB("INSERT INTO client.cardaccess_tbl (clientid, cardid, pspid, countryid, enabled, stateid, psp_type) VALUES (113, 8, $fraudCheckPspID, 100, true, 1, 10)");//psp_type = Constants::iPROCESSOR_TYPE_FRAUD_GATEWAY
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid, enabled, stateid,walletid) VALUES (113, 8, $pspID, 100, true, 1,14)");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, countryid, enabled, stateid) VALUES (113, 8, $pspID, 100, true, 1)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 113, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid,sessionid,convertedamount,convertedcurrencyid,token) VALUES (1001001, 100, 113, 1100, 1,  $pspID, 100, '103-1418291', '" . $sCallbackURL . "', 5000, '127.0.0.1', TRUE, 208, 1,5000,208,'93736e0408d5cd3793615f6e132c89a8f32337483a74739674a5bb2a9c18f6eb91eae4960e5ff9bad1bf62e60282de3c0605ececa6a82f7d14cbe5305fd1983d')");
        $this->queryDB("INSERT INTO log.additional_data_tbl(name, value, type, externalid) VALUES('FCTxnID', '243002', 'Transaction','1001001')");
        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) VALUES('ISROLLBACK_ON_FRAUD_FAIL', 'true', 113, 'client', 0);");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001001, 5000,208,". Constants::iInitializeRequested. ",NULL,'done',113)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001001, 5000,208,NULL,". Constants::iINPUT_VALID_STATE. ",'done',100,113)");

        $xml = $this->getAuthDoc(113, 1100, 1001001, 5000, 'profilePass');

        $this->_httpClient->connect();


        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(200, $iStatus);
        $this->assertContains('<?xml version="1.0" encoding="UTF-8"?><root><status code="2000">Payment authorized</status></root>', $sReplyBody);


        global $aMPOINT_CONN_INFO;
        $aMPOINT_CONN_INFO['path'] = "/callback/general.php";
        $aMPOINT_CONN_INFO["contenttype"] = "text/xml";
        $httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO) );

        $xml = $this->getCallbackDoc(1001001,'tst233',$pspID,Constants::iPAYMENT_ACCEPTED_STATE);
        $httpClient->connect();
        $this->bIgnoreErrors = true;
        $iStatus = $httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $httpClient->getReplyBody();
        $this->assertEquals(200, $iStatus);
        $res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY ID ASC");
        $this->assertTrue(is_resource($res) );

        $aStates = array();
        while ($row = pg_fetch_assoc($res) )
        {
            $aStates[] = $row["stateid"];
        }

        $this->assertContains(Constants::iPOST_FRAUD_CHECK_INITIATED_STATE,$aStates );
        $this->assertContains(Constants::iPOST_FRAUD_CHECK_REJECTED_STATE, $aStates);
        $this->assertContains(Constants::iPAYMENT_CANCELLED_STATE, $aStates);

    }

    public function getAuthDoc($client, $account, $txn=1, $amount=100, $euaPasswd='', $intAccountId=0, $clientpasswd='', $currecyid = null)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<root>';
        $xml .= '<authorize-payment client-id="'. $client .'" account="'. $account .'">';
        $xml .= '<transaction type-id="10091" id="'. $txn .'">';
        $xml .= '<card type-id="8">';
        $xml .= '<amount country-id="100"';
        if(isset($currecyid) === true)
            $xml .= ' currency-id="'.$currecyid.'"';
        $xml .= '>'. $amount .'</amount>';
        $xml .= '<card-holder-name>CellPointMobie</card-holder-name>
				<card-number>4112344112344113</card-number>
				<expiry>11/24</expiry>
				<cvc>411</cvc>
				<address country-id="200">
					<full-name>TestCellPointMobile Test</full-name>
					<street>Karve Road Pune</street>
					<postal-code>416010</postal-code>
					<city>Pune</city>
					<state>maharashtra</state>
				</address>';
        $xml .= '</card>';
        $xml .= '</transaction>';
        $xml .= '<client-info platform="iOS" version="1.00" language="da">';
        $xml .= '<mobile country-id="100" operator-id="10000">28882861</mobile>';
        $xml .= '<email>jona@oismail.com</email>';
        $xml .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
        $xml .= '</client-info>';
        $xml .= '</authorize-payment>';
        $xml .= '</root>';

        return $xml;
    }

    public function testSuccessfulAuthorize()
    {

    }
    public function testSuccessfulAuthorizeIncludingAutoCapture()
    {

    }

    public function testSuccessfulAuthorizeWithCurrency()
    {

    }
}