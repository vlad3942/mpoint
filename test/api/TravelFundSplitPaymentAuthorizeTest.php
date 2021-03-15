<?php
/**
 * User: Chaitenya Yadav
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package:
 * File Name: TravelFundSplitPaymentAuthorizeTest
 */

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';


class TravelFundSplitPaymentAuthorizeTest extends baseAPITest
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

    protected function getAuthDoc($client, $account, $txn = 1, $amount = 100,$aDccParams=null,$currecyid = null,$hmac = null)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<root>';
        $xml .= '<authorize-payment client-id="' . $client . '" account="' . $account . '">';
        $xml .= '<transaction id="' . $txn . '">';
        $xml .= '<voucher id="UIISTD">';
        $xml .= '<amount country-id="100">'.($amount-95).'</amount>';
        $xml .= '</voucher>';
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
        $xml .= '<additional-data>';
		$xml .= '<param name="session_token">UABxckvo1ecYZQJCjeSDIvseZ7vn</param>';
		$xml .= '</additional-data>';
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
        $this->bIgnoreErrors = true;
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] . "://" . $this->_aMPOINT_CONN_INFO["host"] . "/_test/simulators/mticket/callback.php";
        $pspID = Constants::iTRAVELFUND_VOUCHER;

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled) VALUES (10099, " . Constants::iVOUCHER_CARD . ", $pspID, false)"); //Authorize must be possible even with disabled cardac
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


        $xml = $this->getAuthDoc(10099, 1100, 1001001, 100);

        $this->_httpClient->connect();

        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();

        $this->assertEquals(200, $iStatus);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><root><status code="2000">Payment authorized</status></root>', $sReplyBody);

		$res =  $this->queryDB("SELECT id FROM Log.txnpassbook_tbl where transactionid= 1 and status= 'inprogress' and performedopt=2000 " );
		$this->assertIsResource($res);
		$this->assertEquals(1, pg_num_rows($res));

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
    }

    public function testSuccessfulAuthorizationCardFirst()
    {
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] . "://" . $this->_aMPOINT_CONN_INFO["host"] . "/_test/simulators/mticket/callback.php";
        $pspID = Constants::iTRAVELFUND_VOUCHER;

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("UPDATE Client.Client_Tbl SET smsrcpt = false where id = 10099");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled) VALUES (10099, " . Constants::iVOUCHER_CARD . ", $pspID, false)"); //Authorize must be possible even with disabled cardac
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
        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, enabled, externalid, type, scope) VALUES ('isVoucherPreferred', 'false', true, 10099, 'client', 0);");


        $xml = $this->getAuthDoc(10099, 1100, 1001001, 100);

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
    }
}