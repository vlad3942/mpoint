<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anna Lagad
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package:
 * File Name:RoutingServiceTest.php
 */

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once sAPI_CLASS_PATH . 'simpledom.php';
require_once __DIR__ . '/../../api/classes/clientinfo.php';
require_once __DIR__ . '/../../api/classes/core/card.php';
require_once __DIR__ . '/../../api/classes/routing_service.php';
require_once sCLASS_PATH . '/routing_service_response.php';
require_once sCLASS_PATH . '/FailedPaymentMethodConfig.php';
require_once(sCLASS_PATH . '/payment_route.php');


class RoutingServiceTest extends baseAPITest
{
    private $_OBJ_DB;
    protected $_aHTTP_CONN_INFO;

    public function setUp() : void
    {
        parent::setUp(TRUE);
        global $aHTTP_CONN_INFO;
        $this->bIgnoreErrors = true;
        $this->_aHTTP_CONN_INFO = $aHTTP_CONN_INFO;
        $this->_OBJ_DB = RDB::produceDatabase($this->mPointDBInfo);
    }

    protected function getInitDoc($client, $account, $country, $currency = null, $sessionId=null)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<root>';
        $xml .= '<initialize-payment client-id="'. $client .'" account="'. $account .'">';
        $xml .= '<transaction order-no="1234abc" type-id="30" session-id="'.$sessionId.'">';
        $xml .= '<amount country-id="'.$country.'" currency-id ="'.$currency.'">200</amount>';
        $xml .= '</transaction>';
        $xml .= $this->getClientInfo();
        $xml .= '</initialize-payment>';
        $xml .= '</root>';

        return $xml;
    }

    protected function getPayDoc($client, $account, $country, $currency = null)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<root>';
        $xml .= '<pay client-id="'. $client .'" account="'. $account .'">';
        $xml .= '<transaction id="1001001" store-card="false">';
        $xml .= '<card type-id="7">';
        $xml .= '<amount country-id="'.$country.'" currency-id ="'.$currency.'">200</amount>';
        $xml .= '</card>';
        $xml .= '</transaction>';
        $xml .= $this->getClientInfo();
        $xml .= '</pay>';
        $xml .= '</root>';

        return $xml;
    }

    protected function getClientInfo()
    {
        $xml  = '<client-info platform="iOS" version="1.00" language="da">';
        $xml .= '<mobile country-id="100" operator-id="10000">28882861</mobile>';
        $xml .= '<email>jona@oismail.com</email>';
        $xml .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
        $xml .= '</client-info>';

        return $xml;
    }

    public function testGetPaymentMethods()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 18, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, cardid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, 1,  18, 8, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, cardid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001002, 100, 10099, 1100, 1,  18, 8, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001001, 5000,208,". Constants::iInitializeRequested. ",NULL,'done',10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001001, 5000,208,NULL,". Constants::iINPUT_VALID_STATE. ",'done',100,10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (102,1001001, 5000,208,". Constants::iAuthorizeRequested. ",NULL,'inprocess',10099)");

        $xml = $this->getInitDoc(10099, 1100, 100, 208, 10);
        $obj_DOM = simpledom_load_string($xml);


        $iTxnID = 1001001;
        $obj_FailedPaymentMethod = null;
        $sessionId = (string)$obj_DOM->{'initialize-payment'}->transaction["session-id"];
        if(empty($sessionId)===false){
            $obj_FailedPaymentMethod = FailedPaymentMethodConfig::produceFailedTxnInfoFromSession($this->_OBJ_DB, $sessionId, 10099);
        }
        $obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $this->_OBJ_DB);
        $obj_ClientInfo = ClientInfo::produceInfo($obj_DOM->{'initialize-payment'}->{'client-info'}, CountryConfig::produceConfig($this->_OBJ_DB, (integer) $obj_DOM->{'initialize-payment'}->{'client-info'}->mobile["country-id"]), $_SERVER['HTTP_X_FORWARDED_FOR']);
        $obj_RS = new RoutingService($obj_TxnInfo, $obj_ClientInfo, $this->_aHTTP_CONN_INFO['routing-service'], $obj_DOM->{'initialize-payment'}["client-id"], $obj_DOM->{'initialize-payment'}->transaction->amount["country-id"], $obj_DOM->{'initialize-payment'}->transaction->amount["currency-id"], $obj_DOM->{'initialize-payment'}->transaction->amount, null, null, null, $obj_FailedPaymentMethod);

        if($obj_RS instanceof RoutingService)
        {
            $obj_PaymentMethodResponse = $obj_RS->getPaymentMethods();

            if($obj_PaymentMethodResponse instanceof RoutingServiceResponse)
            {

                $aObjPaymentMethods = $obj_PaymentMethodResponse->getPaymentMethods();
                $this->assertEquals(2, count($aObjPaymentMethods->payment_methods->payment_method) );

                $aCardId = array();
                $aPSPType = array();
                foreach ($aObjPaymentMethods->payment_methods->payment_method as $paymentMethod)
                {
                    $aCardId[] = $paymentMethod->id;
                    $aPSPType[] = $paymentMethod->psp_type;
                }

                $this->assertContains(17, $aCardId);
                $this->assertContains(18, $aCardId);
                $this->assertContains(1, $aPSPType);
                $this->assertContains(2, $aPSPType);
            }
        }
    }

    public function testEmptyGetPaymentMethods()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 18, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, 1,  18, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");

        $xml = $this->getInitDoc(10099, 1100, 100);
        $obj_DOM = simpledom_load_string($xml);

        $iTxnID = 1001001;
        $obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $this->_OBJ_DB);
        $obj_ClientInfo = ClientInfo::produceInfo($obj_DOM->{'initialize-payment'}->{'client-info'}, CountryConfig::produceConfig($this->_OBJ_DB, (integer) $obj_DOM->{'initialize-payment'}->{'client-info'}->mobile["country-id"]), $_SERVER['HTTP_X_FORWARDED_FOR']);
        $obj_RS = new RoutingService($obj_TxnInfo, $obj_ClientInfo, $this->_aHTTP_CONN_INFO['routing-service'], $obj_DOM->{'initialize-payment'}["client-id"], $obj_DOM->{'initialize-payment'}->transaction->amount["country-id"], $obj_DOM->{'initialize-payment'}->transaction->amount["currency-id"], $obj_DOM->{'initialize-payment'}->transaction->amount);
        if($obj_RS instanceof RoutingService)
        {
            $obj_PaymentMethodResponse = $obj_RS->getPaymentMethods();
            $this->assertEmpty($obj_PaymentMethodResponse);
        }

    }

    public function testDefaultCountryCurrecnyGetPaymentMethods()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 18, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, 1,  18, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");

        $xml = $this->getInitDoc(10099, 1100, 100);
        $obj_DOM = simpledom_load_string($xml);

        $iTxnID = 1001001;
        $obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $this->_OBJ_DB);
        $obj_ClientInfo = ClientInfo::produceInfo($obj_DOM->{'initialize-payment'}->{'client-info'}, CountryConfig::produceConfig($this->_OBJ_DB, (integer) $obj_DOM->{'initialize-payment'}->{'client-info'}->mobile["country-id"]), $_SERVER['HTTP_X_FORWARDED_FOR']);
        $obj_RS = new RoutingService($obj_TxnInfo, $obj_ClientInfo, $this->_aHTTP_CONN_INFO['routing-service'], $obj_DOM->{'initialize-payment'}["client-id"], $obj_DOM->{'initialize-payment'}->transaction->amount["country-id"], null, $obj_DOM->{'initialize-payment'}->transaction->amount);
        if($obj_RS instanceof RoutingService)
        {
            $obj_PaymentMethodResponse = $obj_RS->getPaymentMethods();
            if($obj_PaymentMethodResponse instanceof RoutingServiceResponse) {
                $this->assertInstanceOf(RoutingServiceResponse::class, $obj_PaymentMethodResponse);
            }
        }

    }

    public function testNegativeScenarioGetPaymentMethod()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 18, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, 1,  18, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");

        $xml = $this->getInitDoc(10099, 1100, 100, 208);
        $obj_DOM = simpledom_load_string($xml);

        $this->_aHTTP_CONN_INFO['routing-service']['port'] = '';

        $iTxnID = 1001001;
        $obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $this->_OBJ_DB);
        $obj_ClientInfo = ClientInfo::produceInfo($obj_DOM->{'initialize-payment'}->{'client-info'}, CountryConfig::produceConfig($this->_OBJ_DB, (integer) $obj_DOM->{'initialize-payment'}->{'client-info'}->mobile["country-id"]), $_SERVER['HTTP_X_FORWARDED_FOR']);
        $obj_RS = new RoutingService($obj_TxnInfo, $obj_ClientInfo, $this->_aHTTP_CONN_INFO['routing-service'], $obj_DOM->{'initialize-payment'}["client-id"], $obj_DOM->{'initialize-payment'}->transaction->amount["country-id"], $obj_DOM->{'initialize-payment'}->transaction->amount["currency-id"], $obj_DOM->{'initialize-payment'}->transaction->amount);
        if($obj_RS instanceof RoutingService)
        {
            $obj_PaymentMethodResponse = $obj_RS->getPaymentMethods();
            $this->assertEmpty($obj_PaymentMethodResponse);
        }

    }

    public function testGetRoute()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 18, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, 1,  18, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");

        $xml = $this->getPayDoc(10099, 1100, 100, 208);
        $obj_DOM = simpledom_load_string($xml);

        $iTxnID = 1001001;
        $obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $this->_OBJ_DB);
        $obj_ClientInfo = ClientInfo::produceInfo($obj_DOM->pay->{'client-info'}, CountryConfig::produceConfig($this->_OBJ_DB, (integer) $obj_DOM->pay->{'client-info'}->mobile["country-id"]), $_SERVER['HTTP_X_FORWARDED_FOR']);
        $obj_RS = new RoutingService($obj_TxnInfo, $obj_ClientInfo, $this->_aHTTP_CONN_INFO['routing-service'], $obj_DOM->pay["client-id"], $obj_DOM->pay->transaction->card->amount["country-id"], $obj_DOM->pay->transaction->card->amount["currency-id"], $obj_DOM->pay->transaction->card->amount, $obj_DOM->pay->transaction["id"], $obj_DOM->pay->transaction->card["type-id"], 100);

        if($obj_RS instanceof RoutingService)
        {
            $objTxnRoute = new PaymentRoute($this->_OBJ_DB, $obj_TxnInfo->getSessionId());
            $iPrimaryRoute = $obj_RS->getAndStoreRoute($objTxnRoute);
            $this->assertEquals(18, $iPrimaryRoute);
        }
    }

    public function testEmptyGetRoute()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 18, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, 1,  18, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");

        $xml = $this->getPayDoc(10099, 1100, 100);
        $obj_DOM = simpledom_load_string($xml);

        $iTxnID = 1001001;
        $obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $this->_OBJ_DB);
        $obj_ClientInfo = ClientInfo::produceInfo($obj_DOM->pay->{'client-info'}, CountryConfig::produceConfig($this->_OBJ_DB, (integer) $obj_DOM->pay->{'client-info'}->mobile["country-id"]), $_SERVER['HTTP_X_FORWARDED_FOR']);
        $obj_RS = new RoutingService($obj_TxnInfo, $obj_ClientInfo, $this->_aHTTP_CONN_INFO['routing-service'], $obj_DOM->pay["client-id"], $obj_DOM->pay->transaction->card->amount["country-id"], $obj_DOM->pay->transaction->card->amount["currency-id"], $obj_DOM->pay->transaction->card->amount, $obj_DOM->pay->transaction["id"], $obj_DOM->pay->transaction->card["type-id"], 100);

        if($obj_RS instanceof RoutingService)
        {
            $objTxnRoute = new PaymentRoute($this->_OBJ_DB, $obj_TxnInfo->getSessionId());
            $iPrimaryRoute = $obj_RS->getAndStoreRoute($objTxnRoute);
            $this->assertEquals(-1, $iPrimaryRoute);
        }
    }

    public function testDefaultCountryCurrencyEmptyGetRoute()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 18, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, 1,  18, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");

        $xml = $this->getPayDoc(10099, 1100, 100);
        $obj_DOM = simpledom_load_string($xml);

        $iTxnID = 1001001;
        $obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $this->_OBJ_DB);
        $obj_ClientInfo = ClientInfo::produceInfo($obj_DOM->pay->{'client-info'}, CountryConfig::produceConfig($this->_OBJ_DB, (integer) $obj_DOM->pay->{'client-info'}->mobile["country-id"]), $_SERVER['HTTP_X_FORWARDED_FOR']);
        $obj_RS = new RoutingService($obj_TxnInfo, $obj_ClientInfo, $this->_aHTTP_CONN_INFO['routing-service'], $obj_DOM->pay["client-id"], $obj_DOM->pay->transaction->card->amount["country-id"], null, $obj_DOM->pay->transaction->card->amount, $obj_DOM->pay->transaction["id"], $obj_DOM->pay->transaction->card["type-id"], 100);

        if($obj_RS instanceof RoutingService)
        {
            $objTxnRoute = new PaymentRoute($this->_OBJ_DB, $obj_TxnInfo->getSessionId());
            $iPrimaryRoute = $obj_RS->getAndStoreRoute($objTxnRoute);
            $this->assertEquals(18, $iPrimaryRoute);
        }
    }

    public function testNegativeScenarioGetRoute()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 18, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, 1,  18, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");

        $xml = $this->getPayDoc(10099, 1100, 100, 208);
        $obj_DOM = simpledom_load_string($xml);

        $this->_aHTTP_CONN_INFO['routing-service']['port'] = '';

        $iTxnID = 1001001;
        $obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $this->_OBJ_DB);
        $obj_ClientInfo = ClientInfo::produceInfo($obj_DOM->pay->{'client-info'}, CountryConfig::produceConfig($this->_OBJ_DB, (integer) $obj_DOM->pay->{'client-info'}->mobile["country-id"]), $_SERVER['HTTP_X_FORWARDED_FOR']);
        $obj_RS = new RoutingService($obj_TxnInfo, $obj_ClientInfo, $this->_aHTTP_CONN_INFO['routing-service'], $obj_DOM->pay["client-id"], $obj_DOM->pay->transaction->card->amount["country-id"], $obj_DOM->pay->transaction->card->amount["currency-id"], $obj_DOM->pay->transaction->card->amount, $obj_DOM->pay->transaction["id"], $obj_DOM->pay->transaction->card["type-id"], 100);

        if($obj_RS instanceof RoutingService)
        {
            $obj_RouteResponse = $obj_RS->getRoute();
            $this->assertEmpty($obj_RouteResponse);
        }
    }

    public function testFlightInfoToXML()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 18, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, 1,  18, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");
        $this->queryDB("INSERT INTO log.order_tbl (id, txnid, countryid, amount, productsku, productname, productdescription, productimageurl, points, reward, quantity, created, modified, enabled, orderref, fees) VALUES (24, 1001001, 640, 125056, 'product-ticket', 'ONE WAY', 'MNL-CEB', '', 0, 0, 1, '2021-04-09 10:25:06.395114', '2021-04-09 10:25:06.395114', true, 'FIU9YAN', 0)");
        $this->queryDB("INSERT INTO log.flight_tbl (id, service_class, departure_airport, arrival_airport, op_airline_code, order_id, arrival_date, departure_date, created, modified, mkt_flight_number, tag, trip_count, service_level, departure_countryid, arrival_countryid, departure_timezone, op_flight_number, arrival_timezone, mkt_airline_code, departure_city, arrival_city, aircraft_type, arrival_terminal, departure_terminal) VALUES (22, 'Z', 'MNL', 'CEB', '5J', 24, '2021-03-07 21:05:00.000000', '2021-03-07 19:35:00.000000', '2021-04-09 10:25:06.513775', '2021-04-09 10:25:06.513775', '563', '1', '1', '3', 640, 640, '+08:00', '1', '+08:00', '5J', 'Ninoy Aquino International Airport', 'Mactan Cebu International Airport', 'Aircraft Boeing-737-9', '2', '1')");
        $this->queryDB("INSERT INTO log.additional_data_tbl (id, name, value, type, created, modified, externalid) VALUES (10, 'fare_basis', 'we543s3', 'Flight', '2021-04-06 09:18:21.094984', '2021-04-06 09:18:21.094984', 22)");

        $flightObj = FlightInfo::produceConfigurations($this->_OBJ_DB, 24);
        // new xml
        $GLOBALS['oldOrderXml'] = false;
        $xml = $flightObj[0]->toXML();
        $this->assertEquals('<trip tag="1" seq="1"><origin external-id="MNL" country-id="640" time-zone="+08:00" terminal="1">Ninoy Aquino International Airport</origin><destination external-id="CEB" country-id="640" time-zone="+08:00" terminal="2">Mactan Cebu International Airport</destination><departure-time>2021-03-07T19:35:00Z</departure-time><arrival-time>2021-03-07T21:05:00Z</arrival-time><booking-class>Z</booking-class><service-level id="3">Economy</service-level><transportation code="5J" number="1"><carriers><carrier code="5J" type-id="Aircraft Boeing-737-9"><number>563</number></carrier></carriers></transportation><additional-data><param name="fare_basis">we543s3</param></additional-data></trip>', $xml);

        //old xml
        $GLOBALS['oldOrderXml'] = true;
        $xml = $flightObj[0]->toXML();
        $this->assertEquals('<flight-detail tag="1" trip-count="1" service-level="3"><service-class>Z</service-class><flight-number>563</flight-number><departure-airport>MNL</departure-airport><arrival-airport>CEB</arrival-airport><airline-code>5J</airline-code><departure-date>2021-03-07 19:35:00</departure-date><arrival-date>2021-03-07 21:05:00</arrival-date><departure-country>640</departure-country><arrival-country>640</arrival-country><time-zone>+08:00</time-zone><additional-data><param name="fare_basis">we543s3</param></additional-data></flight-detail>', $xml);

    }

    public function testPassengerInfoToXML()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 18, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, 1,  18, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");
        $this->queryDB("INSERT INTO log.order_tbl (id, txnid, countryid, amount, productsku, productname, productdescription, productimageurl, points, reward, quantity, created, modified, enabled, orderref, fees) VALUES (24, 1001001, 640, 125056, 'product-ticket', 'ONE WAY', 'MNL-CEB', '', 0, 0, 1, '2021-04-09 10:25:06.395114', '2021-04-09 10:25:06.395114', true, 'FIU9YAN', 0)");
        $this->queryDB("INSERT INTO log.passenger_tbl (id, first_name, last_name, type, order_id, created, modified, title, email, mobile, country_id, amount, seq) VALUES (24, 'dan', 'dan', 'ADT', 24, '2021-04-09 13:06:23.420245', '2021-04-09 13:06:23.420245', 'Mr', 'dan@dan.com', '9187231231', '640', 0, 1)");
        $this->queryDB("INSERT INTO log.additional_data_tbl (id, name, value, type, created, modified, externalid) VALUES (109, 'loyality_id', '345rtyu', 'Passenger', '2021-04-09 13:06:23.406019', '2021-04-09 13:06:23.406019', 24);");

        $passengerObj = PassengerInfo::produceConfigurations($this->_OBJ_DB, 24);

        // new xml
        $GLOBALS['oldOrderXml'] = false;
        $xml = $passengerObj[0]->toXML();
        $this->assertEquals('<profile><seq>1</seq><title>Mr</title><first-name>dan</first-name><last-name>dan</last-name><type>ADT</type><contact-info><email>dan@dan.com</email><mobile country-id="640">9187231231</mobile></contact-info><additional-data><param name="loyality_id">345rtyu</param></additional-data></profile>', $xml);

        //old xml
        $GLOBALS['oldOrderXml'] = true;
        $xml = $passengerObj[0]->toXML();
        $this->assertEquals('<passenger-detail><title>Mr</title><first-name>dan</first-name><last-name>dan</last-name><type>ADT</type><contact-info><email>dan@dan.com</email><mobile country-id="640">9187231231</mobile></contact-info><additional-data><param name="loyality_id">345rtyu</param></additional-data></passenger-detail>', $xml);

    }

    public function tearDown() : void
    {
        $this->_OBJ_DB->disConnect();
        parent::tearDown();
    }

}
