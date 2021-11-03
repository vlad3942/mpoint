<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package:
 * File Name: SplitPaymentAuthorizeTest
 */

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';


class SplitPaymentAuthorizeTest extends baseAPITest
{
    protected $_aMPOINT_CONN_INFO;

    public function __construct()
    {
        parent::__construct();
        $this->constHTTPClient();
    }

    public function constHTTPClient()
    {
        
        $this->bIgnoreErrors = true;
        global $aMPOINT_CONN_INFO;
        $aMPOINT_CONN_INFO['path'] = "/mApp/api/authorize.php";
        $aMPOINT_CONN_INFO["contenttype"] = "text/xml";
        $this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO));
    }

    public function testInvalidVoucherAmountNormal()
    {
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] . "://" . $this->_aMPOINT_CONN_INFO["host"] . "/_test/simulators/mticket/callback.php";
        $pspID = Constants::iDSB_PSP;

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled) VALUES (10099, " . Constants::iVOUCHER_CARD . ", $pspID, false)"); //Authorize must be possible even with disabled cardac
        $this->queryDB("INSERT INTO client.services_tbl (clientid, legacy_flow_enabled) VALUES(10099, true);");

        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '501910******3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 208, 100, 4001, '103-1418291', 2, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid,sessionid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 1, 100, '103-1418291', '" . $sCallbackURL . "', 2, '127.0.0.1', TRUE, 208,1,2,208)");

        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001001, 2,208," . Constants::iInitializeRequested . ",NULL,'done',10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001001, 2,208,NULL," . Constants::iINPUT_VALID_STATE . ",'done',100,10099)");


        $xml = $this->getAuthDoc(10099, 1100, 1001001, 100);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="52">Amount is more than pending amount:  5</status></root>', $sReplyBody);

    }


    public function testInvalidVoucherAmountSplit()
    {
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] . "://" . $this->_aMPOINT_CONN_INFO["host"] . "/_test/simulators/mticket/callback.php";
        $pspID = Constants::iDSB_PSP;

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled) VALUES (10099, " . Constants::iVOUCHER_CARD . ", $pspID, false)"); //Authorize must be possible even with disabled cardac
        $this->queryDB("INSERT INTO client.services_tbl (clientid, legacy_flow_enabled) VALUES(10099, true);");

        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '501910******3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 208, 100, 4001, '103-1418291', 2, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid,sessionid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 1, 100, '103-1418291', '" . $sCallbackURL . "', 2, '127.0.0.1', TRUE, 208,1,2,208)");

        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001001, 2,208," . Constants::iInitializeRequested . ",NULL,'done',10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001001, 2,208,NULL," . Constants::iINPUT_VALID_STATE . ",'done',100,10099)");

        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('sessiontype', 2, true, 10099, 'client', 0);");

        $xml = $this->getAuthDoc(10099, 1100, 1001001, 100);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="53">Amount is more than pending amount:  5</status></root>', $sReplyBody);

    }


    protected function getAuthDoc($client, $account, $txn = 1, $amount = 100,$aDccParams=null,$currecyid = null,$hmac = null,$voucherFirst=true,$cardOnly=false)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<root>';
        $xml .= '<authorize-payment client-id="' . $client . '" account="' . $account . '">';
        $xml .= '<transaction id="' . $txn . '">';

        $voucher = '<voucher id="61775" order-no="800-123456">';
        $voucher .= '<amount country-id="100">'.($amount-95).'</amount>';
        $voucher .= '</voucher>';

        if($voucherFirst==true && $cardOnly==false){
            $xml .= $voucher;
        }
        $xml .= '<card id="61775" type-id="1">';
        $xml .= '<amount country-id="100"';
        if(isset($currecyid) === true) $xml .= ' currency-id="'.$currecyid.'"';
        $xml .= '>';
        if(isset($aDccParams) && empty($aDccParams[1]) === false)
        {
            $xml .=  ($amount-5) * (int)$aDccParams[1] . '</amount>';
        }
        else  $xml .=  ($amount-5) . '</amount>';
        $xml .= '</card>';
        if($voucherFirst==False && $cardOnly==false){
            $xml .= $voucher;
        }
        if(isset($hmac)=== true) $xml .= '<hmac>'.$hmac.'</hmac>';
        if(isset($aDccParams))
        {
            $xml .= '<foreign-exchange-info>';
            if(empty($aDccParams[0]) === false)
            {
                $xml .= '<id>'.$aDccParams[0].'</id>';
            }
            if(empty($aDccParams[1]) === false)
            {
                $xml .= '<conversion-rate>'.$aDccParams[1].'</conversion-rate>';
            }

            if(empty($aDccParams[2]) === false) { $xml .= '<sale-currencyid>'.$aDccParams[2].'</sale-currencyid>'; }
            if(empty($aDccParams[3]) === false) { $xml .= '<sale-amount>'.$aDccParams[3].'</sale-amount>'; }
            $xml .= '</foreign-exchange-info>';
        }
        $xml .= '</transaction>';
        $xml .= '<password>profilePass</password>';
        $xml .= '<client-info platform="iOS" version="1.00" language="da">';
        $xml .= '<mobile country-id="100" operator-id="10000">28882861</mobile>';
        $xml .= '<email>jona@oismail.com</email>';
        $xml .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
        $xml .= '</client-info>';
        $xml .= '</authorize-payment>';
        $xml .= '</root>';

        return $xml;
    }

    public function testSuccessfulAuthorizationVoucherFirst()
    {
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] . "://" . $this->_aMPOINT_CONN_INFO["host"] . "/_test/simulators/mticket/callback.php";
        $pspID = Constants::iDSB_PSP;

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled) VALUES (10099, " . Constants::iVOUCHER_CARD . ", $pspID, false)"); //Authorize must be possible even with disabled cardac
        $this->queryDB("INSERT INTO client.services_tbl (clientid, legacy_flow_enabled) VALUES(10099, true);");

        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid,expire) VALUES (1, 10099, 1100, 208, 100, 4001, '103-1418291', 200, 9876543210, '', '127.0.0.1', -1, 1,(NOW() + interval '1 hour'));");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid,sessionid,convertedamount,convertedcurrencyid, euaid) VALUES (1001001, 100, 10099, 1100, 1, 100, '103-1418291', '" . $sCallbackURL . "', 200, '127.0.0.1', TRUE, 208,1,2,208, 5001)");

        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001001, 200,208," . Constants::iInitializeRequested . ",NULL,'done',10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001001, 200,208,NULL," . Constants::iINPUT_VALID_STATE . ",'done',100,10099)");


        $pspID = Constants::iAMEX_ACQUIRER;

        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (2, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 1, $pspID, '501910******3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 1, $pspID, true, 1)");
        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('sessiontype', 2, true, 10099, 'client', 0);");


        $xml = $this->getAuthDoc(10099, 1100, 1001001, 100);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="2000">Payment authorized</status></root>', $sReplyBody);

        $res =  $this->queryDB("SELECT id FROM Log.txnpassbook_tbl where transactionid= 1001001 and status= 'done' and performedopt=2000 " );
		$this->assertIsResource($res);
		$this->assertEquals(1, pg_num_rows($res));

		$res =  $this->queryDB("SELECT id FROM Log.txnpassbook_tbl where transactionid= 1 and status= 'inprogress' and performedopt=2000 " );
		$this->assertIsResource($res);
		$this->assertEquals(1, pg_num_rows($res));

        $aStates = [];
        $retries = 0;
        while ($retries++ <= 30)
        {
            $aStates = [];
            $res = $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001  ORDER BY id ASC");
            $this->assertIsResource($res);
            while ($row = pg_fetch_assoc($res)) {
                $aStates[] = $row["stateid"];
            }

            $res_s = $this->queryDB("SELECT stateid FROM log.session_tbl WHERE id = 1 and stateid=4031  ORDER BY id ASC");
            $this->assertIsResource($res_s);
            while ($row = pg_fetch_assoc($res)) {
                $aStates[] = $row["stateid"];
            }
            usleep(2000000);// As callback happens asynchroniously, sleep a bit here in order to wait for transaction to complete in other thread
            if (count($aStates) >= 5 && pg_num_rows($res_s) == 1)
            {
                usleep(2000000);// As callback happens asynchroniously, sleep a bit here in order to wait for transaction to complete in other thread
                break;
            }
        }



        $this->assertEquals(5, count($aStates) );

        $res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1 ORDER BY ID ASC");
		$this->assertTrue(is_resource($res) );

		$aStates = array();
		while ($row = pg_fetch_assoc($res) )
		{
			$aStates[] = $row["stateid"];
		}

        $this->assertEquals(2, count($aStates) );

		$s = 0;
		$this->assertEquals(Constants::iTRANSACTION_CREATED, $aStates[$s++]);
		$this->assertEquals(Constants::iPAYMENT_WITH_ACCOUNT_STATE, $aStates[$s++]);

		$res =  $this->queryDB("SELECT id FROM Log.Session_Tbl where id=1 and sessiontypeid=2");
        $this->assertTrue(is_resource($res) );

        $res =  $this->queryDB("SELECT value FROM Log.additional_data_tbl where externalid= 1 and name= 'linked_txn_id'" );
        $this->assertTrue(is_resource($res) );
        $linkedTxnId = pg_fetch_all($res);
        $this->assertEquals(1001001, $linkedTxnId[0]['value'] );

        $res =  $this->queryDB("SELECT value FROM Log.additional_data_tbl where externalid=1001001  and name= 'linked_txn_id'" );
        $this->assertTrue(is_resource($res) );
        $linkedTxnId = pg_fetch_all($res);
        $this->assertEquals(1, $linkedTxnId[0]['value'] );

        $res =  $this->queryDB("SELECT id FROM Log.split_session_tbl where sessionid=1" );
        $this->assertTrue(is_resource($res) );
        $split_session_id = pg_fetch_all($res);
        $res =  $this->queryDB("SELECT transaction_id FROM Log.split_details_tbl where split_session_id=".$split_session_id[0]['id'] );
        $this->assertTrue(is_resource($res) );
        $splitTxns = array();
        while ($row = pg_fetch_assoc($res) )
        {
            $splitTxns[] = $row["transaction_id"];
        }
        $this->assertEquals(2, count($splitTxns));
    }

    public function testSuccessfulDCCAuthorizationVoucherFirst()
    {
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] . "://" . $this->_aMPOINT_CONN_INFO["host"] . "/_test/simulators/mticket/callback.php";
        $pspID = Constants::iDSB_PSP;

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid, enabled) VALUES (10099,100,840, true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled) VALUES (10099, " . Constants::iVOUCHER_CARD . ", $pspID, false)"); //Authorize must be possible even with disabled cardac
        $this->queryDB("INSERT INTO client.services_tbl (clientid, legacy_flow_enabled) VALUES(10099, true);");

        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 208, 100, 4001, '103-1418291', 200, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid,sessionid,convertedamount,convertedcurrencyid, euaid) VALUES (1001001, 100, 10099, 1100, 1, 100, '103-1418291', '" . $sCallbackURL . "', 200, '127.0.0.1', TRUE, 208,1,200,208, 5001)");

        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001001, 200,208," . Constants::iInitializeRequested . ",NULL,'done',10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001001, 200,208,NULL," . Constants::iINPUT_VALID_STATE . ",'done',100,10099)");


        $pspID = Constants::iAMEX_ACQUIRER;

        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (2, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 1, $pspID, '501910******3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid,dccenabled) VALUES (10099, 1, $pspID, true, 1,true)");
        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('sessiontype', 2, true, 10099, 'client', 0);");

        $aDccParams = array(
            "12345",
            "4","208","95"
        );
        $xml = $this->getAuthDoc(10099, 1100, 1001001, 100,$aDccParams,840,'2dba6ae4be12fd89aa81a875db15fe445f2a0513bdd7a8cf6887e2db409f688490177d70793037df0d6fb51f8754680419a195c127455707b8de7fa42e776409');

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="2000">Payment authorized</status></root>', $sReplyBody);

        $res =  $this->queryDB("SELECT id FROM Log.txnpassbook_tbl where transactionid= 1001001 and status= 'done' and performedopt=2000 " );
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));

        $res =  $this->queryDB("SELECT id FROM Log.txnpassbook_tbl where transactionid= 1 and status= 'inprogress' and performedopt=2000 " );
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));

        $res =  $this->queryDB("SELECT externalid FROM log.externalreference_tbl where  type=".Constants::iForeignExchange);
        $this->assertTrue(is_resource($res) );
        $this->assertEquals(1, pg_num_rows($res));
        $res =  $this->queryDB("SELECT convertedcurrencyid FROM Log.Transaction_Tbl where convertedcurrencyid = 840 and currencyid=208 and convertedamount=380");
        $this->assertTrue(is_resource($res) );
        $this->assertEquals(1, pg_num_rows($res));

        $aStates = [];
        $retries = 0;
        while ($retries++ <= 30)
        {
            $aStates = [];
            $res = $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001  ORDER BY id ASC");
            $this->assertIsResource($res);
            while ($row = pg_fetch_assoc($res)) {
                $aStates[] = $row["stateid"];
            }
            usleep(2000000);// As callback happens asynchroniously, sleep a bit here in order to wait for transaction to complete in other thread
            if (count($aStates) >= 5) { break; }
        }

        $this->assertEquals(5, count($aStates) );

        $res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1 ORDER BY ID ASC");
        $this->assertTrue(is_resource($res) );

        $aStates = array();
        while ($row = pg_fetch_assoc($res) )
        {
            $aStates[] = $row["stateid"];
        }

        $this->assertEquals(2, count($aStates) );

        $s = 0;
        $this->assertEquals(Constants::iTRANSACTION_CREATED, $aStates[$s++]);
        $this->assertEquals(Constants::iPAYMENT_WITH_ACCOUNT_STATE, $aStates[$s++]);

        $res =  $this->queryDB("SELECT id FROM Log.Session_Tbl where id=1 and sessiontypeid=2");
        $this->assertTrue(is_resource($res) );

        $res =  $this->queryDB("SELECT id FROM Log.split_session_tbl where sessionid=1" );
        $this->assertTrue(is_resource($res) );
        $split_session_id = pg_fetch_all($res);
        $res =  $this->queryDB("SELECT transaction_id FROM Log.split_details_tbl where split_session_id=".$split_session_id[0]['id'] );
        $this->assertTrue(is_resource($res) );
        $splitTxns = array();
        while ($row = pg_fetch_assoc($res) )
        {
            $splitTxns[] = $row["transaction_id"];
        }
        $this->assertEquals(2, count($splitTxns));
    }


    public function testSuccessfulAuthorizationCardFirst()
    {
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] . "://" . $this->_aMPOINT_CONN_INFO["host"] . "/_test/simulators/mticket/callback.php";
        $pspID = Constants::iDSB_PSP;

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled) VALUES (10099, " . Constants::iVOUCHER_CARD . ", $pspID, false)"); //Authorize must be possible even with disabled cardac
        $this->queryDB("INSERT INTO client.services_tbl (clientid, legacy_flow_enabled) VALUES(10099, true);");

        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
		$this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 208, 100, 4001, '103-1418291', 200, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid,sessionid,convertedamount,convertedcurrencyid, euaid) VALUES (1001001, 100, 10099, 1100, 1, 100, '103-1418291', '" . $sCallbackURL . "', 200, '127.0.0.1', TRUE, 208,1,2,208, 5001)");

        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001001, 200,208," . Constants::iInitializeRequested . ",NULL,'done',10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001001, 200,208,NULL," . Constants::iINPUT_VALID_STATE . ",'done',100,10099)");


        $pspID = Constants::iAMEX_ACQUIRER;

        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (2, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 1, $pspID, '501910******3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 1, $pspID, true, 1)");
        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('sessiontype', 2, true, 10099, 'client', 0);");


        $xml = $this->getAuthDoc(10099, 1100, 1001001, 100,null,null,null,false);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="2000">Payment authorized</status></root>', $sReplyBody);

        $res =  $this->queryDB("SELECT id FROM Log.txnpassbook_tbl where transactionid= 1001001 and status= 'inprogress' and performedopt=2000 " );
		$this->assertIsResource($res);
		$this->assertEquals(1, pg_num_rows($res));

		$res =  $this->queryDB("SELECT id FROM Log.txnpassbook_tbl where transactionid= 1 " );
		$this->assertIsResource($res);
		$this->assertEquals(0, pg_num_rows($res));

		$res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1 ORDER BY ID ASC");
		$this->assertTrue(is_resource($res) );

		$aStates = array();
		while ($row = pg_fetch_assoc($res) )
		{
			$aStates[] = $row["stateid"];
		}

        $this->assertEquals(1, count($aStates) );

		$s = 0;
		$this->assertEquals(Constants::iTRANSACTION_CREATED, $aStates[$s++]);

		$res =  $this->queryDB("SELECT id FROM Log.Session_Tbl where id=1 and sessiontypeid=2");
        $this->assertTrue(is_resource($res) );

        $res =  $this->queryDB("SELECT value FROM Log.additional_data_tbl where externalid= 1 and name= 'linked_txn_id'" );
        $this->assertTrue(is_resource($res) );
        $linkedTxnId = pg_fetch_all($res);
        $this->assertEquals(1001001, $linkedTxnId[0]['value'] );

        $res =  $this->queryDB("SELECT value FROM Log.additional_data_tbl where externalid=1001001  and name= 'linked_txn_id'" );
        $this->assertTrue(is_resource($res) );
        $linkedTxnId = pg_fetch_all($res);
        $this->assertEquals(1, $linkedTxnId[0]['value'] );

        $res =  $this->queryDB("SELECT id FROM Log.split_session_tbl where sessionid=1" );
        $this->assertTrue(is_resource($res) );
        $split_session_id = pg_fetch_all($res);
        $res =  $this->queryDB("SELECT transaction_id FROM Log.split_details_tbl where split_session_id=".$split_session_id[0]['id'] );
        $this->assertTrue(is_resource($res) );
        $splitTxns = array();
        while ($row = pg_fetch_assoc($res) )
        {
            $splitTxns[] = $row["transaction_id"];
        }
        $this->assertEquals(2, count($splitTxns));
    }

    public function testSuccessfulDCCAuthorizationCardFirst()
    {
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] . "://" . $this->_aMPOINT_CONN_INFO["host"] . "/_test/simulators/mticket/callback.php";
        $pspID = Constants::iDSB_PSP;

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid, enabled) VALUES (10099,100,840, true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled) VALUES (10099, " . Constants::iVOUCHER_CARD . ", $pspID, false)"); //Authorize must be possible even with disabled cardac
        $this->queryDB("INSERT INTO client.services_tbl (clientid, legacy_flow_enabled) VALUES(10099, true);");

        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 208, 100, 4001, '103-1418291', 200, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid,sessionid,convertedamount,convertedcurrencyid, euaid) VALUES (1001001, 100, 10099, 1100, 1, 100, '103-1418291', '" . $sCallbackURL . "', 200, '127.0.0.1', TRUE, 208,1,200,208, 5001)");

        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001001, 200,208," . Constants::iInitializeRequested . ",NULL,'done',10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001001, 200,208,NULL," . Constants::iINPUT_VALID_STATE . ",'done',100,10099)");


        $pspID = Constants::iAMEX_ACQUIRER;

        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (2, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 1, $pspID, '501910******3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid,dccenabled) VALUES (10099, 1, $pspID, true, 1,true)");
        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('sessiontype', 2, true, 10099, 'client', 0);");


        $aDccParams = array(
            "12345",
            "4","208","95"
        );
        $xml = $this->getAuthDoc(10099, 1100, 1001001, 100,$aDccParams,840,'df71f2bfd28803159cec82017c01a6c023174e81b8db85c0c4c8a5ad0df98c31f5a8455a19dfe1aa90b4881eeaf0693d7242a6346621cedf3acdae7acd20a1ab',false);


        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="2000">Payment authorized</status></root>', $sReplyBody);

        $res =  $this->queryDB("SELECT id FROM Log.txnpassbook_tbl where transactionid= 1001001 and status= 'inprogress' and performedopt=2000 " );
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));

        $res =  $this->queryDB("SELECT id FROM Log.txnpassbook_tbl where transactionid= 1 " );
        $this->assertIsResource($res);
        $this->assertEquals(0, pg_num_rows($res));

        $res =  $this->queryDB("SELECT externalid FROM log.externalreference_tbl where txnid=1001001 and type=".Constants::iForeignExchange);
        $this->assertTrue(is_resource($res) );
        $this->assertEquals(1, pg_num_rows($res));
        $res =  $this->queryDB("SELECT convertedcurrencyid FROM Log.Transaction_Tbl where id=1001001 and convertedcurrencyid = 840 and currencyid=208 and convertedamount=380");
        $this->assertTrue(is_resource($res) );
        $this->assertEquals(1, pg_num_rows($res));

        $res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1 ORDER BY ID ASC");
        $this->assertTrue(is_resource($res) );
        $aStates = array();
        while ($row = pg_fetch_assoc($res) )
        {
            $aStates[] = $row["stateid"];
        }

        $this->assertEquals(1, count($aStates) );

        $s = 0;
        $this->assertEquals(Constants::iTRANSACTION_CREATED, $aStates[$s++]);

        $res =  $this->queryDB("SELECT id FROM Log.Session_Tbl where id=1 and sessiontypeid=2");
        $this->assertTrue(is_resource($res) );

        $res =  $this->queryDB("SELECT id FROM Log.split_session_tbl where sessionid=1" );
        $this->assertTrue(is_resource($res) );
        $split_session_id = pg_fetch_all($res);
        $res =  $this->queryDB("SELECT transaction_id FROM Log.split_details_tbl where split_session_id=".$split_session_id[0]['id'] );
        $this->assertTrue(is_resource($res) );
        $splitTxns = array();
        while ($row = pg_fetch_assoc($res) )
        {
            $splitTxns[] = $row["transaction_id"];
        }
        $this->assertEquals(2, count($splitTxns));
    }

    public function testVoucherRedemptionDeniedByIssuer()
    {
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] . "://" . $this->_aMPOINT_CONN_INFO["host"] . "/_test/simulators/mticket/callback.php";
        $pspID = Constants::iDSB_PSP;
        $this->bIgnoreErrors = true;

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled) VALUES (10099, " . Constants::iVOUCHER_CARD . ", $pspID, false)"); //Authorize must be possible even with disabled cardac
        $this->queryDB("INSERT INTO client.services_tbl (clientid, legacy_flow_enabled) VALUES(10099, true);");

        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '501910******3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid,expire) VALUES (1, 10099, 1100, 208, 100, 4001, '103-1418291', 11, 9876543210, '', '127.0.0.1', -1, 2,(NOW() + interval '1 hour'));");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid,sessionid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 1, 100, '103-1418291', '" . $sCallbackURL . "', 11, '127.0.0.1', TRUE, 208,1,11,208)");
        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('IS_LEGACY_CALLBACK_FLOW', 'true', 10099, 'client', 0);");

        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001001, 11,208," . Constants::iInitializeRequested . ",NULL,'done',10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001001,11,208,NULL," . Constants::iINPUT_VALID_STATE . ",'done',100,10099)");
        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('sessiontype', 2, true, 10099, 'client', 0);");

        $xml = $this->getAuthDoc(10099, 1100, 1001001, 106);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(402, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="43">Insufficient balance on voucher</status></root>', $sReplyBody);
        $aStates = [];
        $retries = 0;
        while ($retries++ <= 5)
        {
            $res = $this->queryDB("SELECT t.extid, t.pspid, t.amount, m.stateid FROM Log.Transaction_Tbl t, Log.Message_Tbl m WHERE m.txnid = t.id AND t.id = 1001001 ORDER BY m.id ASC");
            $this->assertTrue(is_resource($res));

            $aStates = [];
            $trow = NULL;
            while ($row = pg_fetch_assoc($res)) {
                $trow = $row;
                $aStates[] = $row["stateid"];
            }

            usleep(2000000);// As callback happens asynchroniously, sleep a bit here in order to wait for transaction to complete in other thread

            if (count($aStates) >= 5) { break; }
        }

        $this->assertEquals(NULL, $trow["extid"]);
        $this->assertEquals($pspID, $trow["pspid"]);
        $this->assertEquals(11, $trow["amount"]);

        $this->assertCount(5, $aStates);
        $this->assertEquals(2010, $aStates[0]);
        $this->assertEquals(1990, $aStates[1]);
        $this->assertEquals(1991, $aStates[2]);
        $this->assertEquals(1992, $aStates[3]);
        $this->assertEquals(1990, $aStates[4]);

    }

    public function testSplitPaymentInvalidCombinations()
    {
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] . "://" . $this->_aMPOINT_CONN_INFO["host"] . "/_test/simulators/mticket/callback.php";
        $pspID = Constants::iAMEX_ACQUIRER;

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");

        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (2, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");

        //split payment combinations
        $this->queryDB("INSERT INTO client.split_configuration_tbl (id,client_id, name, is_one_step_auth, enabled) VALUES (1,10099, 'Card+APM', true, true);");
        $this->queryDB("INSERT INTO client.split_combination_tbl (id,split_config_id, payment_type, sequence_no) VALUES (1, 1, 1, 1);");
        $this->queryDB("INSERT INTO client.split_combination_tbl (id,split_config_id, payment_type, sequence_no) VALUES (2, 1, 4, 2);");

        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('sessiontype', 2, true, 10099, 'client', 0);");
        $this->queryDB("INSERT INTO client.services_tbl (clientid, legacy_flow_enabled) VALUES(10099, true);");

        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 1, $pspID, '501910******3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 1, $pspID, true, 1)");

        //first card txn
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid,expire) VALUES (1, 10099, 1100, 208, 100, 4001, '900-55150298', 10000, 9876543210, '', '127.0.0.1', -1, 2,(NOW() + interval '1 hour'));");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, cardid,pspid, callbackurl, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1001001, '900-55150298', 100, 10099, 1100, 100, 1,$pspID, '" . $sCallbackURL . "', 5000, '127.0.0.1', TRUE, 1, 1,5000)");
        $this->queryDB("INSERT INTO log.split_session_tbl(id,sessionid,status) VALUES (1, 1,'Active');");
        $this->queryDB("INSERT INTO log.split_details_tbl(id,split_session_id, transaction_id, sequence_no,payment_status) VALUES (2, 1, 1001001, 1,'Success');");
        // second card txn
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, orderid, typeid, clientid, accountid, countryid, callbackurl, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1001002, '900-55150298', 100, 10099, 1100, 100, '" . $sCallbackURL . "', 5000, '127.0.0.1', TRUE, 1, 1,5000)");

        $xml = $this->getAuthDoc(10099, 1100, 1001002, 5000,null,null,null,false,true);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(502, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="99">The given request combination is not configured for the client</status></root>', $sReplyBody);
    }

    public function testSuccessfulAuthorizationCardCardSplit()
    {
        $pspID = Constants::iAMEX_ACQUIRER;
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");

        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('sessiontype', 2, true, 10099, 'client', 0);");
        $this->queryDB("INSERT INTO client.services_tbl (clientid, legacy_flow_enabled) VALUES(10099, true);");

        //split payment combinations
        $this->queryDB("INSERT INTO client.split_configuration_tbl (id,client_id, name, is_one_step_auth, enabled) VALUES (1,10099, 'Card+Card', false, true);");
        $this->queryDB("INSERT INTO client.split_combination_tbl (id,split_config_id, payment_type, sequence_no) VALUES (1, 1, 1, 1);");
        $this->queryDB("INSERT INTO client.split_combination_tbl (id,split_config_id, payment_type, sequence_no) VALUES (2, 1, 1, 2);");

        //As per talk with Jona and Simon 2016-07-19 it should not be possible to authorize a disabled card, since the client can ignore flags sent from initialize
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 1, $pspID, true, 1)");

        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 1, $pspID, '501910******3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 208, 100, 4001, '103-1418291', 10000, 9876543210, '', '127.0.0.1', -1, 2);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, cardid,pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid,sessionid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 1, 1, $pspID, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE, 208, 1,5000,208)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iINPUT_VALID_STATE. ")");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_INIT_WITH_PSP_STATE. ")");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iCB_ACCEPTED_STATE. ")");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iCB_CONSTRUCTED_STATE. ")");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iCB_CONNECTED_STATE. ")");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");
        $this->queryDB("INSERT INTO log.split_session_tbl(id,sessionid,status) VALUES (1, 1,'Active');");
        $this->queryDB("INSERT INTO log.split_details_tbl(id,split_session_id, transaction_id, sequence_no,payment_status) VALUES (2, 1, 1001001, 1,'Success');");

        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid,sessionid,convertedamount,convertedcurrencyid) VALUES (1001002, 100, 10099, 1100, 1,  $pspID, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE, 208, 1,5000,208)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001002, ". Constants::iINPUT_VALID_STATE. ")");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001002, 5000,208," . Constants::iInitializeRequested . ",NULL,'done',10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001002, 5000,208,NULL," . Constants::iINPUT_VALID_STATE . ",'done',100,10099)");

        $xml = $this->getAuthDoc(10099, 1100, 1001002, 5005,null,null,null,false,true);
        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="2000">Payment authorized</status></root>', $sReplyBody);

        $res =  $this->queryDB("SELECT id FROM Log.txnpassbook_tbl where transactionid= 1001002 and status= 'inprogress' and performedopt=2000 " );
        $this->assertIsResource($res);
        $this->assertEquals(1, pg_num_rows($res));

        $res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001002 ORDER BY ID ASC");
        $this->assertTrue(is_resource($res) );
        $aStates = array();
        while ($row = pg_fetch_assoc($res) )
        {
            $aStates[] = $row["stateid"];
        }
        $this->assertEquals(2, count($aStates) );

        $s = 0;
        $this->assertEquals(Constants::iINPUT_VALID_STATE, $aStates[$s++]);
        $this->assertEquals(Constants::iPAYMENT_WITH_ACCOUNT_STATE, $aStates[$s++]);

        $res =  $this->queryDB("SELECT id FROM Log.Session_Tbl where id=1 and sessiontypeid=2");
        $this->assertTrue(is_resource($res) );

        $res =  $this->queryDB("SELECT id FROM Log.split_session_tbl where sessionid=1" );
        $this->assertTrue(is_resource($res) );
        $split_session_id = pg_fetch_all($res);
        $res =  $this->queryDB("SELECT transaction_id FROM Log.split_details_tbl where split_session_id=".$split_session_id[0]['id'] );
        $this->assertTrue(is_resource($res) );
        $splitTxns = array();
        while ($row = pg_fetch_assoc($res) )
        {
            $splitTxns[] = $row["transaction_id"];
        }
        $this->assertEquals(2, count($splitTxns));
    }

}