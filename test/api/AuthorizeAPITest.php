<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

abstract class AuthorizeAPITest extends baseAPITest
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
        $aMPOINT_CONN_INFO['path'] = "/mApp/api/authorize.php";
        $aMPOINT_CONN_INFO["contenttype"] = "text/xml";
        $this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO));
    }

    protected function getAuthDoc($client, $account, $txn=1, $amount=100, $euaPasswd='', $intAccountId=0, $clientpasswd='', $currecyid = null,$hmac=null,$cardid=2,$aDccParams=null, $bInstallment=false)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<root>';
        $xml .= '<authorize-payment client-id="'. $client .'" account="'. $account .'">';
        $xml .= '<transaction id="'. $txn .'"';
        $xml .='>';
        $xml .= '<card id="61775" type-id="'.$cardid.'">';
        $xml .= '<amount country-id="100"';
        if(isset($currecyid) === true)
            $xml .= ' currency-id="'.$currecyid.'"';
        $xml .= '>'. $amount .'</amount>';
//		$xml .= '<card-number>5272342200069702</card-number>';
//		$xml .= '<expiry>03/31</expiry>';
//		$xml .= '<cryptogram type="3ds">AKh96OOsGf2HAIDEhKulAoABFA==</cryptogram>';
        $xml .= '</card>';
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
        if($bInstallment === true)
        {
            $xml .= '<installment>';
            $xml .= '<value>5</value>';
            $xml .= '</installment>';
        }
        $xml .= '</transaction>';
        if ($intAccountId > 0)
        {
            $secret = sha1($client. $clientpasswd);
            $xml .= '<auth-token>'. htmlspecialchars(General::genToken($intAccountId, $secret), ENT_NOQUOTES) .'</auth-token>';
        }
        else
        {
            $xml .= '<password>'. $euaPasswd. '</password>';
        }
        $xml .= '<client-info platform="iOS" version="1.00" language="da">';
        $xml .= '<mobile country-id="100" operator-id="10000">28882861</mobile>';
        $xml .= '<email>jona@oismail.com</email>';
        $xml .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
        $xml .= '</client-info>';
        $xml .= '</authorize-payment>';
        $xml .= '</root>';

        return $xml;
    }

    protected function testSuccessfulAuthorize($pspID)
    {
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        //As per talk with Jona and Simon 2016-07-19 it should not be possible to authorize a disabled card, since the client can ignore flags sent from initialize
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 2, $pspID, true, 1)");
        $this->queryDB("INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type,scope) VALUES ('IS_LEGACY', 'true', 10099, 'client',0)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '501910******3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid,sessionid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 1,  $pspID, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE, 208, 1,5000,208)");
        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('IS_LEGACY_CALLBACK_FLOW', 'true', 10099, 'client', 0);");

        $xml = $this->getAuthDoc(10099, 1100, 1001001, 5000, 'profilePass');

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="100">Payment Authorized using Stored Card</status></root>', $sReplyBody);

        $res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY ID ASC");
        $this->assertTrue(is_resource($res) );

        $aStates = array();
        while ($row = pg_fetch_assoc($res) )
        {
            $aStates[] = $row["stateid"];
        }

        //TODO: Rewrite test so it supports both Netaxept and DIBS. Netaxept completes the txn within the callback, DIBS does it during the authorize API flow
        if ($pspID == Constants::iDIBS_PSP) { $this->assertEquals(6, count($aStates) ); }
        else { $this->assertEquals(3, count($aStates) ); }

        $s = 0;
        //TODO: Rewrite test so it supports both Netaxept and DIBS. Netaxept completes the txn within the callback, DIBS does it during the authorize API flow
        $this->assertEquals(Constants::iPAYMENT_WITH_ACCOUNT_STATE, $aStates[$s++]);
        if ($pspID == Constants::iNETAXEPT_PSP) { $this->assertEquals(Constants::iCARD_PURCHASE_TYPE, $aStates[$s++]); }
        $this->assertEquals(Constants::iPAYMENT_ACCEPTED_STATE, $aStates[$s++]);
        if ($pspID == Constants::iDIBS_PSP)
        {
            $this->assertEquals(Constants::iCB_ACCEPTED_STATE, $aStates[$s++]);
            $this->assertEquals(Constants::iCB_CONSTRUCTED_STATE, $aStates[$s++]);
            $this->assertEquals(Constants::iCB_CONNECTED_STATE, $aStates[$s++]);
            $this->assertEquals(Constants::iCB_ACCEPTED_STATE, $aStates[$s++]);
        }

        /* Test that euaid has been set on txn */
        $res =  $this->queryDB("SELECT t.euaid, et.accountid FROM Log.Transaction_Tbl t LEFT JOIN Enduser.Transaction_Tbl et ON et.txnid = t.id WHERE t.id = 1001001");
        $this->assertTrue(is_resource($res) );
        $row = pg_fetch_assoc($res);

        $this->assertEquals(5001, $row["euaid"]);
        //TODO: Rewrite test so it supports both Netaxept and DIBS. Netaxept completes the txn within the callback, DIBS does it during the authorize API flow
        if ($pspID == Constants::iDIBS_PSP) { $this->assertEquals(5001, $row["accountid"]); }
    }

    protected function testSuccessfulAuthorizeWithAID($pspID)
    {
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        //As per talk with Jona and Simon 2016-07-19 it should not be possible to authorize a disabled card, since the client can ignore flags sent from initialize
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 2, $pspID, true, 1)");
        $this->queryDB("INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type,scope) VALUES ('IS_LEGACY', 'true', 10099, 'client',0)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '501910******3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid,sessionid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 1,  $pspID, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE, 208, 1,5000,208)");
        $this->queryDB("INSERT INTO log.additional_data_tbl(name, value, type, externalid) VALUES('FCTxnID', '243001', 'Transaction','1001001')");
        $this->queryDB("INSERT INTO Log.Order_Tbl (id, orderref, txnid, countryid, amount, quantity, productsku, productname, productdescription, productimageurl, points, reward,fees) VALUES (10, 'SOCGN6', 1001001, 100, 100, 1, '103-1418291', 'return journey', 'return journey', 'https://www.cpm.com', 300, 1, 0);");
        $this->queryDB("INSERT INTO Log.Flight_Tbl (id, service_class,mkt_flight_number, departure_airport, arrival_airport, op_airline_code, order_id, arrival_date, departure_date, tag, trip_count, service_level, departure_countryid, arrival_countryid, departure_timezone) VALUES (10, 'X', '1850', 'CEB', 'MNL', 'PR', '10', '2020-05-23 13:55:00', '2020-05-23 12:40:00', '1', '2', '3', 200, 200, '+08:30')");
        $this->queryDB("INSERT INTO log.passenger_tbl (id, first_name, last_name, type, order_id, created, modified, title, email, mobile, country_id, amount, seq) VALUES (24, 'dan', 'dan', 'ADT', 10, '2021-04-09 13:06:23.420245', '2021-04-09 13:06:23.420245', 'Mr', 'dan@dan.com', '9187231231', '640', 0, 1)");
        $this->queryDB("INSERT INTO log.additional_data_tbl (id, name, value, type, externalid) VALUES (109, 'loyality_id', '345rtyu', 'Passenger', 24);");
        $this->queryDB("INSERT INTO log.billing_summary_tbl (id, order_id, journey_ref, bill_type, description, amount, currency, created, modified, profile_seq, trip_tag, trip_seq, product_code, product_category, product_item) VALUES (45, 10, '', 'Fare', 'adult', '60', 'PHP', '2021-04-09 13:06:23.336965', '2021-04-09 13:06:23.336965', 0, 0, 0, 'ABF', 'FARE', 'Base fare for adult')");
        $this->queryDB("INSERT INTO log.billing_summary_tbl (id, order_id, journey_ref, bill_type, description, amount, currency, created, modified, profile_seq, trip_tag, trip_seq, product_code, product_category, product_item) VALUES (46, 10, '', 'Add-on', 'adult', '60', 'PHP', '2021-04-09 13:06:23.353398', '2021-04-09 13:06:23.353398', 1, 2, 2, 'ABF', 'FARE', 'Base fare for adult')");

        $xml = $this->getAuthDoc(10099, 1100, 1001001, 5000, 'profilePass');

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="100">Payment Authorized using Stored Card</status></root>', $sReplyBody);

        $res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY ID ASC");
        $this->assertTrue(is_resource($res) );

        $aStates = array();
        while ($row = pg_fetch_assoc($res) )
        {
            $aStates[] = $row["stateid"];
        }

        //TODO: Rewrite test so it supports both Netaxept and DIBS. Netaxept completes the txn within the callback, DIBS does it during the authorize API flow
        if ($pspID == Constants::iDIBS_PSP) { $this->assertEquals(5, count($aStates) ); }
        else { $this->assertEquals(3, count($aStates) ); }

        $s = 0;
        //TODO: Rewrite test so it supports both Netaxept and DIBS. Netaxept completes the txn within the callback, DIBS does it during the authorize API flow
        $this->assertEquals(Constants::iPAYMENT_WITH_ACCOUNT_STATE, $aStates[$s++]);
        if ($pspID == Constants::iNETAXEPT_PSP) { $this->assertEquals(Constants::iCARD_PURCHASE_TYPE, $aStates[$s++]); }
        $this->assertEquals(Constants::iPAYMENT_ACCEPTED_STATE, $aStates[$s++]);
        if ($pspID == Constants::iDIBS_PSP)
        {
            $this->assertEquals(Constants::iCB_CONSTRUCTED_STATE, $aStates[$s++]);
            $this->assertEquals(Constants::iCB_CONNECTED_STATE, $aStates[$s++]);
            $this->assertEquals(Constants::iCB_ACCEPTED_STATE, $aStates[$s++]);
        }

        /* Test that euaid has been set on txn */
        $res =  $this->queryDB("SELECT t.euaid, et.accountid FROM Log.Transaction_Tbl t LEFT JOIN Enduser.Transaction_Tbl et ON et.txnid = t.id WHERE t.id = 1001001");
        $this->assertTrue(is_resource($res) );
        $row = pg_fetch_assoc($res);

        $this->assertEquals(5001, $row["euaid"]);
        //TODO: Rewrite test so it supports both Netaxept and DIBS. Netaxept completes the txn within the callback, DIBS does it during the authorize API flow
        if ($pspID == Constants::iDIBS_PSP) { $this->assertEquals(5001, $row["accountid"]); }
    }

    protected function testSuccessfulAuthorizeWithCurrency($pspID)
    {
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        //As per talk with Jona and Simon 2016-07-19 it should not be possible to authorize a disabled card, since the client can ignore flags sent from initialize
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 2, $pspID, true, 1)");
        $this->queryDB("INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type,scope) VALUES ('IS_LEGACY', 'true', 10099, 'client',0)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '501910******3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 840, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,currencyid,sessionid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10099, 1100, 1,  $pspID, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE, 840, 1,5000,840)");
        $this->queryDB("INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid, enabled) VALUES (10099,100,840, true)");
        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('IS_LEGACY_CALLBACK_FLOW', 'true', 10099, 'client', 0);");
        $xml = $this->getAuthDoc(10099, 1100, 1001001, 5000, 'profilePass', 0,'',840);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="100">Payment Authorized using Stored Card</status></root>', $sReplyBody);

        $res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY ID ASC");
        $this->assertTrue(is_resource($res) );

        $aStates = array();
        while ($row = pg_fetch_assoc($res) )
        {
            $aStates[] = $row["stateid"];
        }

        //TODO: Rewrite test so it supports both Netaxept and DIBS. Netaxept completes the txn within the callback, DIBS does it during the authorize API flow
        if ($pspID == Constants::iDIBS_PSP) { $this->assertEquals(6, count($aStates) ); }
        else { $this->assertEquals(3, count($aStates) ); }

        $s = 0;
        //TODO: Rewrite test so it supports both Netaxept and DIBS. Netaxept completes the txn within the callback, DIBS does it during the authorize API flow
        $this->assertEquals(Constants::iPAYMENT_WITH_ACCOUNT_STATE, $aStates[$s++]);
        if ($pspID == Constants::iNETAXEPT_PSP) { $this->assertEquals(Constants::iCARD_PURCHASE_TYPE, $aStates[$s++]); }
        $this->assertEquals(Constants::iPAYMENT_ACCEPTED_STATE, $aStates[$s++]);
        if ($pspID == Constants::iDIBS_PSP)
        {
            $this->assertEquals(Constants::iCB_ACCEPTED_STATE, $aStates[$s++]);
            $this->assertEquals(Constants::iCB_CONSTRUCTED_STATE, $aStates[$s++]);
            $this->assertEquals(Constants::iCB_CONNECTED_STATE, $aStates[$s++]);
            $this->assertEquals(Constants::iCB_ACCEPTED_STATE, $aStates[$s++]);
        }

        /* Test that euaid has been set on txn */
        $res =  $this->queryDB("SELECT t.euaid, et.accountid FROM Log.Transaction_Tbl t LEFT JOIN Enduser.Transaction_Tbl et ON et.txnid = t.id WHERE t.id = 1001001");
        $this->assertTrue(is_resource($res) );
        $row = pg_fetch_assoc($res);

        $this->assertEquals(5001, $row["euaid"]);
        //TODO: Rewrite test so it supports both Netaxept and DIBS. Netaxept completes the txn within the callback, DIBS does it during the authorize API flow
        if ($pspID == Constants::iDIBS_PSP) { $this->assertEquals(5001, $row["accountid"]); }
    }

    protected function testSuccessfulAuthorizeIncludingAutoCapture($pspID)
    {
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        //As per talk with Jona and Simon 2016-07-19 it should not be possible to authorize a disabled card, since the client can ignore flags sent from initialize
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid,capture_type) VALUES (10099, 2, $pspID, true, 1, 3)");
        $this->queryDB("INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type,scope) VALUES ('IS_LEGACY', 'true', 10099, 'client',0)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, $pspID, '501910******3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, auto_capture, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, 1,  $pspID, 5001, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', 3, TRUE, 1,5000)");
        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('IS_LEGACY_CALLBACK_FLOW', 'true', 10099, 'client', 0);");

        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001001, 5000,208,". Constants::iInitializeRequested. ",NULL,'done',10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001001, 5000,208,NULL,". Constants::iINPUT_VALID_STATE. ",'done',100,10099)");

        $xml = $this->getAuthDoc(10099, 1100, 1001001, 5000, 'profilePass');

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="100">Payment Authorized using Stored Card</status></root>', $sReplyBody);

        $res =  $this->queryDB("SELECT stateid FROM Log.Message_Tbl WHERE txnid = 1001001 ORDER BY id ASC");
        $this->assertTrue(is_resource($res) );

        $aStates = array();
        while ($row = pg_fetch_assoc($res) )
        {
            $aStates[] = $row["stateid"];
        }

        //TODO: Rewrite test so it supports both Netaxept and DIBS. Netaxept completes the txn within the callback, DIBS does it during the authorize API flow
        if ($pspID == Constants::iDIBS_PSP) { $this->assertEquals(11, count($aStates) ); }
        else { $this->assertEquals(7, count($aStates) ); }

        $s = 0;
        //TODO: Rewrite test so it supports both Netaxept and DIBS. Netaxept completes the txn within the callback, DIBS does it during the authorize API flow
        $this->assertEquals(Constants::iPAYMENT_WITH_ACCOUNT_STATE, $aStates[$s++]);
        if ($pspID == Constants::iNETAXEPT_PSP) { $this->assertEquals(Constants::iCARD_PURCHASE_TYPE, $aStates[$s++]); }
        $this->assertEquals(Constants::iPAYMENT_ACCEPTED_STATE, $aStates[$s++]);
        if(env('APP_ENV') === 'local')
        { //On local Google Pub is not setup so callback will fail
            $this->assertEquals(Constants::iCB_REJECTED_STATE, $aStates[$s++]);
        }
        else
        {
            $this->assertEquals(Constants::iCB_ACCEPTED_STATE, $aStates[$s++]);
        }
        if ($pspID == Constants::iDIBS_PSP)
        {
            $this->assertEquals(Constants::iCB_CONSTRUCTED_STATE, $aStates[$s++]);
            $this->assertEquals(Constants::iCB_CONNECTED_STATE, $aStates[$s++]);
            $this->assertEquals(Constants::iCB_ACCEPTED_STATE, $aStates[$s++]);
        }

        $this->assertEquals(Constants::iPAYMENT_CAPTURED_STATE, $aStates[$s++]);

        if ($pspID == Constants::iDIBS_PSP)
        {
            $this->assertEquals(Constants::iCB_ACCEPTED_STATE, $aStates[$s++]);
            $this->assertEquals(Constants::iCB_CONSTRUCTED_STATE, $aStates[$s++]);
            $this->assertEquals(Constants::iCB_CONNECTED_STATE, $aStates[$s++]);
            $this->assertEquals(Constants::iCB_ACCEPTED_STATE, $aStates[$s++]);
        }

        $this->assertContains("Message: CAPTURE APPROVED BY PSP ". $pspID, parent::getErrorLogContent() );


        /* Test that euaid has been set on txn */
        $res =  $this->queryDB("SELECT t.euaid, et.accountid FROM Log.Transaction_Tbl t LEFT JOIN Enduser.Transaction_Tbl et ON et.txnid = t.id WHERE t.id = 1001001");
        $this->assertTrue(is_resource($res) );
        $row = pg_fetch_assoc($res);

        $this->assertEquals(5001, $row["euaid"]);
        //TODO: Rewrite test so it supports both Netaxept and DIBS. Netaxept completes the txn within the callback, DIBS does it during the authorize API flow
        if ($pspID == Constants::iDIBS_PSP) { $this->assertEquals(5001, $row["accountid"]); }
    }
}