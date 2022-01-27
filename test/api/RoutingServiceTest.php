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

    public function testGetWalletPaymentMethods()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 2, 18, '5019********3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, 'Wallet103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, cardid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001001, 100, 10099, 1100, 1,  18, 8, 5001, 100, 'Wallet103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, cardid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES (1001002, 100, 10099, 1100, 1,  18, 8, 5001, 100, 'Wallet103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001001, 5000,208,". Constants::iInitializeRequested. ",NULL,'done',10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001001, 5000,208,NULL,". Constants::iINPUT_VALID_STATE. ",'done',100,10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (102,1001001, 5000,208,". Constants::iAuthorizeRequested. ",NULL,'inprocess',10099)");
        $this->queryDB("INSERT INTO client.services_tbl (clientid, legacy_flow_enabled) VALUES(10099, false);");

        $this->queryDB("INSERT INTO Client.merchantaccount_tbl
    (clientid, pspid, \"name\",  enabled, username, passwd, stored_card, supportedpartialoperations)
VALUES(10099, 14, 'EFS100001149', true, 'Paymaya acq', 'sk-aXQdorOOF0zGMfyVAzTH9CbAFvqq1Oc7PAXcDlrz5zz', NULL, 0)");

        $this->queryDB("INSERT INTO Client.merchantsubaccount_tbl
    (accountid, pspid, \"name\", enabled)
VALUES(1100, 14, 'paymaya acq',  true)");

        $this->queryDB("INSERT INTO Client.additionalproperty_tbl(\"key\", value, enabled, externalid, \"type\", \"scope\") VALUES( 'IS_LEGACY', 'false',  true, 10099, 'client', 0)");



        $xml = $this->getInitDoc(10099, 1100, 100, 208, 10);
        $obj_DOM = simpledom_load_string($xml);
        $_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH . sLANG ."/global.txt", sLANGUAGE_PATH . sLANG ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");

        $iTxnID = 1001001;
        $obj_FailedPaymentMethod = null;
        $sessionId = (string)$obj_DOM->{'initialize-payment'}->transaction["session-id"];
        if(empty($sessionId)===false){
            $obj_FailedPaymentMethod = FailedPaymentMethodConfig::produceFailedTxnInfoFromSession($this->_OBJ_DB, $sessionId, 10099);
        }
        $obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $this->_OBJ_DB);
        $obj_ClientInfo = ClientInfo::produceInfo($obj_DOM->{'initialize-payment'}->{'client-info'}, CountryConfig::produceConfig($this->_OBJ_DB, (integer) $obj_DOM->{'initialize-payment'}->{'client-info'}->mobile["country-id"]), $_SERVER['HTTP_X_FORWARDED_FOR']);
        $obj_RS = new RoutingService($obj_TxnInfo, $obj_ClientInfo, $this->_aHTTP_CONN_INFO['routing-service'], $obj_DOM->{'initialize-payment'}["client-id"], $obj_DOM->{'initialize-payment'}->transaction->amount["country-id"], $obj_DOM->{'initialize-payment'}->transaction->amount["currency-id"], $obj_DOM->{'initialize-payment'}->transaction->amount, null, null, null, $obj_FailedPaymentMethod);

        $this->assertInstanceOf(RoutingService::class, $obj_RS);

        if($obj_RS instanceof RoutingService)
        {
            $obj_PaymentMethodResponse = $obj_RS->getPaymentMethods();

            $this->assertInstanceOf(RoutingServiceResponse::class, $obj_PaymentMethodResponse);

            if($obj_PaymentMethodResponse instanceof RoutingServiceResponse)
            {
                $aObjPaymentMethods = $obj_PaymentMethodResponse->getPaymentMethods();

                $this->assertEquals(3, count($aObjPaymentMethods->payment_methods->payment_method) );

                $aCardId = array();
                $aPSPType = array();
                foreach ($aObjPaymentMethods->payment_methods->payment_method as $paymentMethod)
                {
                    $aCardId[] = $paymentMethod->id;
                    $aPSPType[] = $paymentMethod->psp_type;
                }

                $this->assertContains(17, $aCardId);
                $this->assertContains(18, $aCardId);
                $this->assertContains(15, $aCardId);
                $this->assertContains(1, $aPSPType);
                $this->assertContains(2, $aPSPType);
                $this->assertContains(3, $aPSPType);

                $aWalletCardId = [];
                foreach ($aObjPaymentMethods->card_schemes as $iProviderId => $iCardId)
                {
                    $aWalletCardId = $iCardId;
                }
                $this->assertContains(7, $aWalletCardId);
                $this->assertContains(8, $aWalletCardId);

                // Processor initialize API

                $obj_Processor = WalletProcessor::produceConfig($this->_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, 15, $this->_aHTTP_CONN_INFO);
                $obj_Processor->setWalletCardSchemes($obj_PaymentMethodResponse->getCardSchemes());
                $initResponseXML = $obj_Processor->initialize();
                $aSupportedCards = (array) $initResponseXML->head->supported_cards->supported_card;

                $this->assertContains('VISA', $aSupportedCards);
                $this->assertContains('Master Card', $aSupportedCards);

            }
        }
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

        $this->assertInstanceOf(RoutingService::class, $obj_RS);

        if($obj_RS instanceof RoutingService)
        {
            $obj_PaymentMethodResponse = $obj_RS->getPaymentMethods();

            $this->assertInstanceOf(RoutingServiceResponse::class, $obj_PaymentMethodResponse);

            if($obj_PaymentMethodResponse instanceof RoutingServiceResponse)
            {
                $aObjPaymentMethods = $obj_PaymentMethodResponse->getPaymentMethods();
                $this->assertEquals(3, count($aObjPaymentMethods->payment_methods->payment_method) );

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

        $this->assertInstanceOf(RoutingService::class, $obj_RS);

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

    public function testToAttributeLessOrderDataXML()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tusername', 'Tpassword')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', true)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 208, 100, 4001, '1513-2001', 5000, 29612109, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, extid, orderid, amount, ip, enabled, keywordid, sessionid,convertedamount) VALUES (1001001,". Constants::iPURCHASE_VIA_APP .", 10099, 1100, 100, '1515', '1513-2001',  5000, '127.0.0.1', TRUE, 1, 1,5000)");
        $this->queryDB("INSERT INTO log.additional_data_tbl(name, value, type, externalid) VALUES('FCTxnID', '243001', 'Transaction','1001001')");
        $this->queryDB("INSERT INTO Log.Order_Tbl (id, orderref, txnid, countryid, amount, quantity, productsku, productname, productdescription, productimageurl, points, reward,fees) VALUES (10, 'SOCGN6', 1001001, 100, 100, 1, '103-1418291', 'return journey', 'return journey', 'https://www.cpm.com', 300, 1, 0);");
        $this->queryDB("INSERT INTO Log.Flight_Tbl (id, service_class,mkt_flight_number, departure_airport, arrival_airport, op_airline_code, order_id, arrival_date, departure_date, tag, trip_count, service_level, departure_countryid, arrival_countryid, departure_timezone) VALUES (10, 'X', '1850', 'CEB', 'MNL', 'PR', '10', '2020-05-23 13:55:00', '2020-05-23 12:40:00', '1', '2', '3', 200, 200, '+08:30')");
        $this->queryDB("INSERT INTO log.passenger_tbl (id, first_name, last_name, type, order_id, created, modified, title, email, mobile, country_id, amount, seq) VALUES (24, 'dan', 'dan', 'ADT', 10, '2021-04-09 13:06:23.420245', '2021-04-09 13:06:23.420245', 'Mr', 'dan@dan.com', '9187231231', '640', 0, 1)");
        $this->queryDB("INSERT INTO log.additional_data_tbl (id, name, value, type, externalid) VALUES (109, 'loyality_id', '345rtyu', 'Passenger', 24);");
        $this->queryDB("INSERT INTO log.billing_summary_tbl (id, order_id, journey_ref, bill_type, description, amount, currency, created, modified, profile_seq, trip_tag, trip_seq, product_code, product_category, product_item) VALUES (45, 10, '', 'Fare', 'adult', '60', 'PHP', '2021-04-09 13:06:23.336965', '2021-04-09 13:06:23.336965', 1, 0, 0, 'ABF', 'FARE', 'Base fare for adult')");
        $this->queryDB("INSERT INTO log.billing_summary_tbl (id, order_id, journey_ref, bill_type, description, amount, currency, created, modified, profile_seq, trip_tag, trip_seq, product_code, product_category, product_item) VALUES (46, 10, '', 'Add-on', 'adult', '60', 'PHP', '2021-04-09 13:06:23.353398', '2021-04-09 13:06:23.353398', 1, 2, 2, 'ABF', 'FARE', 'Base fare for adult')");

        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,clientid) VALUES (100,1001001, 5000,208,". Constants::iInitializeRequested. ",NULL,'done',10099)");
        $this->queryDB("INSERT INTO Log.txnpassbook_Tbl (id,transactionid,amount,currencyid,requestedopt,performedopt,status,extref,clientid) VALUES (101,1001001, 5000,208,NULL,". Constants::iINPUT_VALID_STATE. ",'done',100,10099)");

        $obj_TxnInfo = TxnInfo::produceInfo(1001001, $this->_OBJ_DB);
        $obj_TxnInfo->produceOrderConfig($this->_OBJ_DB);
        $obj_ClientInfo = ClientInfo::produceInfo(simpledom_load_string($this->getClientInfo()), CountryConfig::produceConfig($this->_OBJ_DB, 10), '0.0.0.0');

        $this->_aHTTP_CONN_INFO['routing-service']['port'] = '';
        $obj_RS = new RoutingService($obj_TxnInfo, $obj_ClientInfo, $this->_aHTTP_CONN_INFO['routing-service'], 10099, 100, 208, 5000);

        if($obj_RS instanceof RoutingService)
        {
            $getMockMethod = self::getReflectionMethod('RoutingService', 'toAttributeLessOrderDataXML');
            self::assertEquals("<orders><line_item><product><name>return journey</name><sku>103-1418291</sku><description>return journey</description><airline_data><flight_details><flight_detail><tag>1</tag><trip_count>2</trip_count><service_level>3</service_level><service_class>X</service_class><departure_date>2020-05-23T12:40:00Z</departure_date><arrival_date>2020-05-23T01:55:00Z</arrival_date><departure_country>200</departure_country><arrival_country>200</arrival_country><departure_airport><iata>CEB</iata></departure_airport><arrival_airport><iata>MNL</iata></arrival_airport><time_zone>+08:30</time_zone></flight_detail></flight_details><billing_summary><fare_detail><fare><profile_seq>1</profile_seq><description>adult</description><currency>PHP</currency><amount>60</amount><product_code>ABF</product_code><product_category>FARE</product_category><product_item>Base fare for adult</product_item></fare></fare_detail><add_ons><add_on><profile_seq>1</profile_seq><trip_tag>2</trip_tag><trip_seq>2</trip_seq><description>adult</description><currency>PHP</currency><amount>60</amount><product_code>ABF</product_code><product_category>FARE</product_category><product_item>Base fare for adult</product_item></add_on></add_ons></billing_summary></airline_data></product></line_item><amount><country_id>100</country_id><value>100</value></amount></orders>", $getMockMethod->invoke($obj_RS));
        }else
        {
            self::assertTrue(false);
        }

    }

    public function testGetPaymentMethodRequestXmlWithFxServiceId()
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
        $obj_TxnInfo->setFXServiceTypeID(11);
        $obj_ClientInfo = ClientInfo::produceInfo($obj_DOM->{'initialize-payment'}->{'client-info'}, CountryConfig::produceConfig($this->_OBJ_DB, (integer) $obj_DOM->{'initialize-payment'}->{'client-info'}->mobile["country-id"]), $_SERVER['HTTP_X_FORWARDED_FOR']);
        $obj_RS = new RoutingService($obj_TxnInfo, $obj_ClientInfo, $this->_aHTTP_CONN_INFO['routing-service'], $obj_DOM->{'initialize-payment'}["client-id"], $obj_DOM->{'initialize-payment'}->transaction->amount["country-id"], null, $obj_DOM->{'initialize-payment'}->transaction->amount);

        $this->assertInstanceOf(RoutingService::class, $obj_RS);

        if($obj_RS instanceof RoutingService)
        {
            $getMockMethod = self::getReflectionMethod('RoutingService', 'getPaymentMethodSearchCriteriaXml');
            self::assertStringContainsString("<foreign_exchange_info><service_type_id>11</service_type_id></foreign_exchange_info>", $getMockMethod->invoke($obj_RS));
        } else {
            self::fail();
        }
    }

    public function testGetPaymentMethodRequestXmlWithoutFxServiceId()
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

        $this->assertInstanceOf(RoutingService::class, $obj_RS);

        if($obj_RS instanceof RoutingService)
        {
            $getMockMethod = self::getReflectionMethod('RoutingService', 'getPaymentMethodSearchCriteriaXml');
            self::assertStringNotContainsString("<foreign_exchange_info><service_type_id>11</service_type_id></foreign_exchange_info>", $getMockMethod->invoke($obj_RS));
        } else {
            self::fail();
        }
    }

    public function tearDown() : void
    {
        $this->_OBJ_DB->disConnect();
        parent::tearDown();
    }

}
