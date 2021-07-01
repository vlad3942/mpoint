<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anna Lagad
 * Copyright: Cellpoint Digital
 * Link: http://www.cellpointdigital.com
 * Project: mPoint
 * Package:
 * File Name:GeneralTest.php
 */

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once sAPI_CLASS_PATH . 'simpledom.php';
require_once(sCLASS_PATH ."/payment_processor.php");
require_once(sINTERFACE_PATH ."/cpm_psp.php");
require_once(sCLASS_PATH ."/credit_card.php");
require_once(sCLASS_PATH ."/wirecard.php");
require_once sCLASS_PATH . '/txn_passbook.php';
require_once sCLASS_PATH . '/passbookentry.php';
require_once(sCLASS_PATH ."/payment_route.php");

class GeneralTest extends baseAPITest
{

    private $_OBJ_DB;
    protected $_aHTTP_CONN_INFO;

    public function setUp() : void
    {
        parent::setUp(TRUE);
        global $aHTTP_CONN_INFO;
        $this->_aHTTP_CONN_INFO = $aHTTP_CONN_INFO;
        $this->_OBJ_DB = RDB::produceDatabase($this->mPointDBInfo);
        $this->_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH . sLANG ."/global.txt", sLANGUAGE_PATH . sLANG ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");
    }

    public function testSuccessfulGetPresentmentCurrencies()
    {
		$pspID = Constants::iWIRE_CARD_PSP;

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd,salt) VALUES (10018, 1, 100, 'Test Client', 'Tuser', 'Tpass','23lkhfgjh24qsdfkjh')" );
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10018, 4, 'http://mpoint.local.cellpointmobile.com:80/')" );
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10018, 'app')" );
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10018, 'CPM', true)" );
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10018, $pspID, '4216310')" );
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')" );
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid,dccenabled) VALUES (10018, 8, $pspID,100,true)" );
		$this->queryDB("INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid, enabled) VALUES (10018,100,840, true)" );
		$this->queryDB("INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (8,10018,840,156,'true','true')" );
		$this->queryDB("INSERT INTO client.card_currency_mapping_tbl (card_id,client_id,sale_currency_id,settlement_currency_id,is_presentment,enabled) VALUES (8,10018,840,360,'true','true')" );

		$presentmentCurrencies = array();

		$obj_mPoint = new General($this->_OBJ_DB, $this->_OBJ_TXT);
		$presentmentCurrencies = $obj_mPoint->getPresentmentCurrencies($this->_OBJ_DB, 10018, 8, 840);
		$this->assertCount(2,$presentmentCurrencies);
	}

    public function testFailureGetPresentmentCurrencies()
    {
		$pspID = Constants::iWIRE_CARD_PSP;

		$this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd,salt) VALUES (10018, 1, 100, 'Test Client', 'Tuser', 'Tpass','23lkhfgjh24qsdfkjh')" );
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10018, 4, 'http://mpoint.local.cellpointmobile.com:80/')" );
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10018, 'app')" );
		$this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10018, 'CPM', true)" );
		$this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10018, $pspID, '4216310')" );
		$this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')" );
		$this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid,dccenabled) VALUES (10018, 8, $pspID,100,true)" );
		$this->queryDB("INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid, enabled) VALUES (10018,100,840, true)" );

		$presentmentCurrencies = array();

		$obj_mPoint = new General($this->_OBJ_DB, $this->_OBJ_TXT);
		$presentmentCurrencies = $obj_mPoint->getPresentmentCurrencies($this->_OBJ_DB, 10018, 8, 840);
		$this->assertCount(0, $presentmentCurrencies);
	}

    public function testAuthWithAlternateRouteScenario()
    {
        $this->bIgnoreErrors = true;
        $pspID = Constants::iWIRE_CARD_PSP;
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd,salt) VALUES (10018, 1, 100, 'Test Client', 'Tuser', 'Tpass','23lkhfgjh24qsdfkjh')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10018, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10018, 'app')");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10018, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10018, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid,dccenabled) VALUES (10018, 8, $pspID,100,true)");
        $this->queryDB("INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type,scope) VALUES ('IS_LEGACY', 'true', 10018, 'client',0)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (50011, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10018, 50011)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 50011, 8, $pspID, '501910******3742', '06/24', TRUE, 10018, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid, enabled) VALUES (10018,100,840, true)");

        ## Route Related SQL
        $this->queryDB("INSERT INTO client.route_tbl(id, clientid, providerid) VALUES (10001, 10018, 18)");
        $this->queryDB("INSERT INTO client.routeconfig_tbl( id, routeid, name, capturetype, mid, username, password, enabled) VALUES (1126, 10001, '2c2p-alc_Master_VISA_USD', 2, 'CebuPacific_USD', 'CELLPM', 'HC1XBPV0O4WLKZMG', 'true')");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (1126)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (1126)");

        $this->queryDB("INSERT INTO client.routeconfig_tbl( id, routeid, name, capturetype, mid, username, password, enabled) VALUES (1127, 10001, '2c2p-alc_Master_VISA_USD', 2, 'CebuPacific_USD', 'CELLPM', 'HC1XBPV0O4WLKZMG', 'true')");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (1127)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (1127)");

        $this->queryDB("INSERT INTO Log.Paymentroute_Tbl (id, sessionid, routeconfigid, preference) VALUES (111, 1, 1126, 2)");
        $this->queryDB("INSERT INTO Log.Paymentroute_Tbl (id, sessionid, routeconfigid, preference) VALUES (112, 1, 1127, 3)");

        # Transaction Related Entry
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10018, 1100, 208, 100, 4001, '1513-005', 5000, 29612109, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,currencyid,euaid,convertedamount,convertedcurrencyid, routeconfigid) VALUES (1001012, 100, 10018, 1100, 100, $pspID, '1512', '1234abc', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,840,50011,5000,840, 1126)");
        $this->queryDB("INSERT INTO log.txnpassbook_tbl(id, transactionid, amount, currencyid, requestedopt, performedopt, status,clientid) VALUES(102291, 1001012, 5000, 840, 5014, NULL, 'done', 10018)");
        $this->queryDB("INSERT INTO log.txnpassbook_tbl(transactionid, amount, currencyid,  performedopt, status, extref, extrefidentifier, clientid) VALUES ( 1001012, 5000, 840,  1001, 'done', '102291', 'log.txnpassbook_tbl', 10018)");


        $iTxnID = 1001012;
        $obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $this->_OBJ_DB);
        $obj_mCard = new CreditCard($this->_OBJ_DB, $this->_OBJ_TXT, $obj_TxnInfo);
        $obj_CardXML = simpledom_load_string($obj_mCard->getCards(5000));
        $obj_Elem = $obj_CardXML->xpath("/cards/item[@type-id=8 and @state-id=1 and @walletid = '']");

        $this->_aHTTP_CONN_INFO["wire-card"]["paths"]["auth"] = "/_test/simulators/wire-card/auth-declined.php";
        $obj_Processor = PaymentProcessor::produceConfig($this->_OBJ_DB, $this->_OBJ_TXT, $obj_TxnInfo, intval($obj_Elem["pspid"]), $this->_aHTTP_CONN_INFO);
        $response = $obj_Processor->authorize($obj_Elem);
        $code = (int)$response->code;
        $subCode = (int)$response->sub_code;
        $this->assertEquals(Constants::iPAYMENT_REJECTED_STATE, $code);
        $this->assertEquals(2010301, $subCode);
        if($code === Constants::iPAYMENT_REJECTED_STATE && $obj_TxnInfo->hasEitherSoftDeclinedState($subCode) === true ){
            $objTxnRoute = new PaymentRoute($this->_OBJ_DB, $obj_TxnInfo->getSessionId());
            $iAlternateRoute = $objTxnRoute->getAlternateRoute(Constants::iSECOND_ALTERNATE_ROUTE);
            $this->assertGreaterThanOrEqual(1, $iAlternateRoute);
            $this->_aHTTP_CONN_INFO["wire-card"]["paths"]["auth"] = "/_test/simulators/wire-card/auth.php";
            $obj_mPoint = new General($this->_OBJ_DB, $this->_OBJ_TXT);
            if(empty($iAlternateRoute) === false){
                $response = $obj_mPoint->authWithAlternateRoute($obj_TxnInfo, $iAlternateRoute, $this->_aHTTP_CONN_INFO, $obj_Elem);
                $this->assertEquals(2000, (int)$response->code);
            }
        }

        //Auth With Alternate Route Negetive Scenario
        $obj_Elem = $obj_CardXML->xpath("/cards/item[@type-id=7 and @state-id=1 and @walletid = '']");
        $objTxnRoute = new PaymentRoute($this->_OBJ_DB, $obj_TxnInfo->getSessionId());
        $iAlternateRoute = $objTxnRoute->getAlternateRoute(Constants::iSECOND_ALTERNATE_ROUTE);
        $this->assertGreaterThanOrEqual(1, $iAlternateRoute);
        $obj_mPoint = new General($this->_OBJ_DB, $this->_OBJ_TXT);
        if(empty($iAlternateRoute) === false){
            $response= $obj_mPoint->authWithAlternateRoute($obj_TxnInfo, $iAlternateRoute, $this->_aHTTP_CONN_INFO, $obj_Elem);
            $this->assertEquals(400, (int)$response->code);
        }

        $this->_aHTTP_CONN_INFO["wire-card"]["paths"]["auth"] = "/_test/simulators/wire-card/auth-declined.php";
        $obj_Elem = $obj_CardXML->xpath("/cards/item[@type-id=8 and @state-id=1 and @walletid = '']");
        $objTxnRoute = new PaymentRoute($this->_OBJ_DB, $obj_TxnInfo->getSessionId());
        $iAlternateRoute = $objTxnRoute->getAlternateRoute(Constants::iSECOND_ALTERNATE_ROUTE);
        $this->assertGreaterThanOrEqual(1, $iAlternateRoute);
        $obj_mPoint = new General($this->_OBJ_DB, $this->_OBJ_TXT);
        if(empty($iAlternateRoute) === false){
            $response = $obj_mPoint->authWithAlternateRoute($obj_TxnInfo, $iAlternateRoute, $this->_aHTTP_CONN_INFO, $obj_Elem);
            $this->assertEquals(Constants::iPAYMENT_REJECTED_STATE, (int)$response->code);
            $this->assertEquals(2010301, (int)$response->sub_code);
        }
    }

    public function testAuthWithAlternateRouteHardDeclineScenario()
    {
        $this->bIgnoreErrors = true;
        $pspID = Constants::iWIRE_CARD_PSP;
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd,salt) VALUES (10018, 1, 100, 'Test Client', 'Tuser', 'Tpass','23lkhfgjh24qsdfkjh')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10018, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10018, 'app')");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10018, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10018, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid,dccenabled) VALUES (10018, 8, $pspID,100,true)");
        $this->queryDB("INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type,scope) VALUES ('IS_LEGACY', 'true', 10018, 'client',0)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (50011, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10018, 50011)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 50011, 8, $pspID, '501910******3742', '06/24', TRUE, 10018, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid, enabled) VALUES (10018,100,840, true)");

        ## Route Related SQL
        $this->queryDB("INSERT INTO client.route_tbl(id, clientid, providerid) VALUES (10001, 10018, 18)");
        $this->queryDB("INSERT INTO client.routeconfig_tbl( id, routeid, name, capturetype, mid, username, password, enabled) VALUES (1126, 10001, '2c2p-alc_Master_VISA_USD', 2, 'CebuPacific_USD', 'CELLPM', 'HC1XBPV0O4WLKZMG', 'true')");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (1126)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (1126)");

        $this->queryDB("INSERT INTO client.routeconfig_tbl( id, routeid, name, capturetype, mid, username, password, enabled) VALUES (1127, 10001, '2c2p-alc_Master_VISA_USD', 2, 'CebuPacific_USD', 'CELLPM', 'HC1XBPV0O4WLKZMG', 'true')");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (1127)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (1127)");

        $this->queryDB("INSERT INTO Log.Paymentroute_Tbl (id, sessionid, routeconfigid, preference) VALUES (111, 1, 1126, 2)");
        $this->queryDB("INSERT INTO Log.Paymentroute_Tbl (id, sessionid, routeconfigid, preference) VALUES (112, 1, 1127, 3)");

        # Transaction Related Entry
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10018, 1100, 208, 100, 4001, '1513-005', 5000, 29612109, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,currencyid,euaid,convertedamount,convertedcurrencyid, routeconfigid) VALUES (1001012, 100, 10018, 1100, 100, $pspID, '1512', '1234abc', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,840,50011,5000,840, 1126)");
        $this->queryDB("INSERT INTO log.txnpassbook_tbl(id, transactionid, amount, currencyid, requestedopt, performedopt, status,clientid) VALUES(102291, 1001012, 5000, 840, 5014, NULL, 'done', 10018)");
        $this->queryDB("INSERT INTO log.txnpassbook_tbl(transactionid, amount, currencyid,  performedopt, status, extref, extrefidentifier, clientid) VALUES ( 1001012, 5000, 840,  1001, 'done', '102291', 'log.txnpassbook_tbl', 10018)");


        $iTxnID = 1001012;
        $obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $this->_OBJ_DB);
        $obj_mCard = new CreditCard($this->_OBJ_DB, $this->_OBJ_TXT, $obj_TxnInfo);
        $obj_CardXML = simpledom_load_string($obj_mCard->getCards(5000));
        $obj_Elem = $obj_CardXML->xpath("/cards/item[@type-id=8 and @state-id=1 and @walletid = '']");

        $this->_aHTTP_CONN_INFO["wire-card"]["paths"]["auth"] = "/_test/simulators/wire-card/auth-hard-declined.php";
        $obj_Processor = PaymentProcessor::produceConfig($this->_OBJ_DB, $this->_OBJ_TXT, $obj_TxnInfo, intval($obj_Elem["pspid"]), $this->_aHTTP_CONN_INFO);
        $response = $obj_Processor->authorize($obj_Elem);
        $code = (int)$response->code;
        $subCode = (int)$response->sub_code;
        $this->assertEquals(Constants::iPAYMENT_REJECTED_STATE, $code);
        $this->assertEquals(2010305, $subCode);
        $this->assertNotTrue($obj_TxnInfo->hasEitherSoftDeclinedState($subCode));
    }

    public function testCreateTxnFromTxn()
    {
        $pspID = Constants::iWIRE_CARD_PSP;
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd,salt) VALUES (10018, 1, 100, 'Test Client', 'Tuser', 'Tpass','23lkhfgjh24qsdfkjh')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10018, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10018, 'app')");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10018, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type,scope) VALUES ('IS_LEGACY', 'true', 10018, 'client',0)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10018, 1100, 208, 100, 4001, '1513-005', 5000, 29612109, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,currencyid,convertedamount,convertedcurrencyid) VALUES (1001012, 100, 10018, 1100, 100, $pspID, '1512', '1234abc', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,840,5000,840)");
        $this->queryDB("INSERT INTO log.txnpassbook_tbl(id, transactionid, amount, currencyid, requestedopt, performedopt, status,clientid) VALUES(102291, 1001012, 5000, 840, 5014, NULL, 'done', 10018)");
        $this->queryDB("INSERT INTO log.txnpassbook_tbl(transactionid, amount, currencyid,  performedopt, status, extref, extrefidentifier, clientid) VALUES ( 1001012, 5000, 840,  1001, 'done', '102291', 'log.txnpassbook_tbl', 10018)");

        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,currencyid,convertedamount,convertedcurrencyid) VALUES (1001022, 100, 10018, 1100, 100, $pspID, '1512', '1234abc', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,840,5000,840)");
        $this->queryDB("INSERT INTO log.txnpassbook_tbl(id, transactionid, amount, currencyid, requestedopt, performedopt, status,clientid) VALUES(102292, 1001022, 5000, 840, 5014, NULL, 'done', 10018)");
        $this->queryDB("INSERT INTO log.txnpassbook_tbl(transactionid, amount, currencyid,  performedopt, status, extref, extrefidentifier, clientid) VALUES ( 1001022, 5000, 840,  1001, 'done', '102291', 'log.txnpassbook_tbl', 10018)");


        $iTxnID = 1001012;
        $obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $this->_OBJ_DB);
        $obj_general = new General($this->_OBJ_DB, $this->_OBJ_TXT);
        $newTxn = $obj_general->createTxnFromTxn($obj_TxnInfo, 20);

        $res =  $this->queryDB("SELECT id FROM Log.Transaction_Tbl where id= {$newTxn->getId()} and amount = 20 and typeid = 100 and clientid = 10018 and accountid = 1100 and countryid = 100 and orderid = '1234abc' and sessionid = 1" );
		$this->assertIsResource($res);
		$this->assertEquals(1, pg_num_rows($res) );

		$res =  $this->queryDB("SELECT id FROM Log.txnpassbook_tbl where transactionid= {$newTxn->getId()} " );
		$this->assertIsResource($res);
		$this->assertEquals(2, pg_num_rows($res));

		$res =  $this->queryDB("SELECT id FROM Log.additional_data_tbl where externalid= '{$newTxn->getId()}'" );
		$this->assertIsResource($res);
		$this->assertEquals(1, pg_num_rows($res));

        $iTxnID = 1001022;
        $obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $this->_OBJ_DB);
        $obj_general = new General($this->_OBJ_DB, $this->_OBJ_TXT);
		$additionalTxnData = [];
        $additionalTxnData[0]['name'] = 'voucherid';
        $additionalTxnData[0]['value'] = 'voucher';
        $additionalTxnData[0]['type'] = 'Transaction';
		$newTxn = $obj_general->createTxnFromTxn($obj_TxnInfo, 20, FALSE, "18", $additionalTxnData);

        $res =  $this->queryDB("SELECT id FROM Log.Transaction_Tbl where id= {$newTxn->getId()} and amount = 20 and typeid = 100 and clientid = 10018 and accountid = 1100 and countryid = 100 and orderid = '1234abc' and sessionid = 1 and pspid = 18" );
		$this->assertIsResource($res);
		$this->assertEquals(1, pg_num_rows($res) );

		$res =  $this->queryDB("SELECT id FROM Log.txnpassbook_tbl where transactionid= {$newTxn->getId()} " );
		$this->assertIsResource($res);
		$this->assertEquals(0, pg_num_rows($res));

		$res =  $this->queryDB("SELECT id FROM Log.additional_data_tbl where externalid= '{$newTxn->getId()}' " );
		$this->assertIsResource($res);
		$this->assertEquals(2, pg_num_rows($res));

    }

    /**
     * Test General::producePSPConfigObject With Legacy
     * @throws \ErrorException
     */
    public function testProducePSPConfigObjectWithLegacy()
    {
        $pspID = Constants::iWIRE_CARD_PSP;
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd,salt) VALUES (10018, 1, 100, 'Test Client', 'Tuser', 'Tpass','23lkhfgjh24qsdfkjh')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10018, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10018, 'app')");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10018, 'CPM', true)");

        $this->queryDB("INSERT INTO client.MerchantAccount_Tbl (id, clientid, pspid, name, enabled, username, passwd, supportedpartialoperations) VALUES (1, 10018, $pspID, 'Test 2c2p-alc', true, 'CELLPM', 'HC1XBPV0O4WLKZMG', 0)");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, 'Test Sub Merchant')");

        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid,dccenabled) VALUES (10018, 8, $pspID,100,true)");
        $this->queryDB("INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid, enabled) VALUES (10018,100,840, true)");

        # Set Is Legacy Code
        $this->queryDB("INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type,scope) VALUES ('IS_LEGACY', 'false', 10018, 'client',2)");

        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value,  enabled, externalid, type, scope) VALUES ('3DVERIFICATION', 'true', true, 10001, 'merchant', 2)");
        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value,  enabled, externalid, type, scope) VALUES ('TEST_MPI', 'true', true, 10001, 'merchant', 2)");

        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (50011, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10018, 50011)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 50011, 8, $pspID, '501910******3742', '06/24', TRUE, 10018, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");

        ## Route Related SQL
        $this->queryDB("INSERT INTO client.route_tbl(id, clientid, providerid) VALUES (10001, 10018, 18)");
        $this->queryDB("INSERT INTO client.routeconfig_tbl( id, routeid, name, capturetype, mid, username, password, enabled) VALUES (1126, 10001, '2c2p-alc_Master_VISA_USD', 2, 'CebuPacific_USD', 'CELLPM', 'HC1XBPV0O4WLKZMG', 'true')");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (1126)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (1126)");

        $this->queryDB("INSERT INTO client.routeconfig_tbl( id, routeid, name, capturetype, mid, username, password, enabled) VALUES (1127, 10001, '2c2p-alc_Master_VISA_USD', 2, 'CebuPacific_USD', 'CELLPM', 'HC1XBPV0O4WLKZMG', 'true')");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (1127)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (1127)");

        $this->queryDB("INSERT INTO Log.Paymentroute_Tbl (id, sessionid, routeconfigid, preference) VALUES (111, 1, 1126, 2)");
        $this->queryDB("INSERT INTO Log.Paymentroute_Tbl (id, sessionid, routeconfigid, preference) VALUES (112, 1, 1127, 3)");

        # Transaction Related Entry
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10018, 1100, 208, 100, 4001, '1513-005', 5000, 29612109, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,currencyid,euaid,convertedamount,convertedcurrencyid, routeconfigid) VALUES (1001012, 100, 10018, 1100, 100, $pspID, '1512', '1234abc', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,840,50011,5000,840, 1126)");

        $iTxnID = 1001012;
        $obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $this->_OBJ_DB);

        $obj_PSPConfig = General::producePSPConfigObject($this->_OBJ_DB, $obj_TxnInfo, $pspID);
        $this->assertInstanceOf('PSPConfig', $obj_PSPConfig, 'Error: Missing PSP Configuration Object');

        $obj_PSPConfig = General::producePSPConfigObject($this->_OBJ_DB, $obj_TxnInfo, $pspID, true);
        $this->assertInstanceOf('PSPConfig', $obj_PSPConfig, 'Error: Missing PSP Configuration Object with Force Legacy');
    }


    /**
     * Test General::producePSPConfigObject With Legacy
     * @throws \ErrorException
     */
    public function testProducePSPConfigObject()
    {
        $pspID = Constants::iWIRE_CARD_PSP;
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd,salt) VALUES (10018, 1, 100, 'Test Client', 'Tuser', 'Tpass','23lkhfgjh24qsdfkjh')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10018, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10018, 'app')");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10018, 'CPM', true)");

        $this->queryDB("INSERT INTO client.MerchantAccount_Tbl (id, clientid, pspid, name, enabled, username, passwd, supportedpartialoperations) VALUES (1, 10018, $pspID, 'Test 2c2p-alc', true, 'CELLPM', 'HC1XBPV0O4WLKZMG', 0)");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, 'Test Sub Merchant')");

        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid,dccenabled) VALUES (10018, 8, $pspID,100,true)");
        $this->queryDB("INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid, enabled) VALUES (10018,100,840, true)");

        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value,  enabled, externalid, type, scope) VALUES ('3DVERIFICATION', 'true', true, 10001, 'merchant', 2)");
        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value,  enabled, externalid, type, scope) VALUES ('TEST_MPI', 'true', true, 10001, 'merchant', 2)");
        $this->queryDB("INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type,scope) VALUES ('IS_LEGACY', 'true', 10018, 'client',0)");

        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (50011, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10018, 50011)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 50011, 8, $pspID, '501910******3742', '06/24', TRUE, 10018, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");

        ## Route Related SQL
        $this->queryDB("INSERT INTO client.route_tbl(id, clientid, providerid) VALUES (10001, 10018, 18)");
        $this->queryDB("INSERT INTO client.routeconfig_tbl( id, routeid, name, capturetype, mid, username, password, enabled) VALUES (1126, 10001, '2c2p-alc_Master_VISA_USD', 2, 'CebuPacific_USD', 'CELLPM', 'HC1XBPV0O4WLKZMG', 'true')");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (1126)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (1126)");

        $this->queryDB("INSERT INTO client.routeconfig_tbl( id, routeid, name, capturetype, mid, username, password, enabled) VALUES (1127, 10001, '2c2p-alc_Master_VISA_USD', 2, 'CebuPacific_USD', 'CELLPM', 'HC1XBPV0O4WLKZMG', 'true')");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (1127)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (1127)");

        $this->queryDB("INSERT INTO Log.Paymentroute_Tbl (id, sessionid, routeconfigid, preference) VALUES (111, 1, 1126, 2)");
        $this->queryDB("INSERT INTO Log.Paymentroute_Tbl (id, sessionid, routeconfigid, preference) VALUES (112, 1, 1127, 3)");

        # Transaction Related Entry
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10018, 1100, 208, 100, 4001, '1513-005', 5000, 29612109, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,currencyid,euaid,convertedamount,convertedcurrencyid, routeconfigid) VALUES (1001012, 100, 10018, 1100, 100, $pspID, '1512', '1234abc', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,840,50011,5000,840, 1126)");

        $iTxnID = 1001012;
        $obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $this->_OBJ_DB);
        $obj_PSPConfig = General::producePSPConfigObject($this->_OBJ_DB, $obj_TxnInfo, $pspID);
        $this->assertInstanceOf('PSPConfig', $obj_PSPConfig, 'Error: Missing PSP Configuration Object');
    }

    public function testIsAutoFetchBalance()
    {
        $pspID = Constants::iTRAVELFUND_VOUCHER;
        $userType = UserType::iRegisterUser;
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
        $authenticateURL = $this->_aMPOINT_CONN_INFO['protocol'] . '://' . $this->_aMPOINT_CONN_INFO['host']. '/_test/simulators/mprofile/ciam/get-customer-profile.php';

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
		$this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 2, '".$authenticateURL."')");
		$this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");

        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");

        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid) VALUES (1001001, 100, 10099, 1100, 1,  $pspID, 100, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE,10)");

        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('SSO_PREFERENCE', 'STRICT', 10099, 'client', 0)");
		$this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('autoFetchBalance', 'true', 10099, 'client', 0)");
		$this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('fetchBalanceUserType', '{\"1\":2}', 10099, 'client', 0)");
		$this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, externalid, type, scope) VALUES ('fetchBalancePaymentMethods', '{\"1\":26}', 10099, 'client', 0)");
        $this->queryDB("INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type,scope) VALUES ('IS_LEGACY', 'true', 10099, 'client',0)");

        $iTxnID = 1001001;
        $obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $this->_OBJ_DB);

        $misc = array();
        $misc['additionaldata']['customer-type'] = $userType;
        $obj_TxnInfo = TxnInfo::produceInfo($obj_TxnInfo->getID(),$this->_OBJ_DB, $obj_TxnInfo, $misc);

        $obj_general = new General($this->_OBJ_DB, $this->_OBJ_TXT);
        $isAutoFetchBalance = $obj_general->isAutoFetchBalance($obj_TxnInfo, 26);

        $this->assertEquals(1, $isAutoFetchBalance);
        $this->assertTrue($isAutoFetchBalance);
    }

    public function testGetPaymentStatusComplete()
    {
        $pspID = Constants::iWIRE_CARD_PSP;
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd,salt) VALUES (10018, 1, 100, 'Test Client', 'Tuser', 'Tpass','23lkhfgjh24qsdfkjh')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10018, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10018, 'app')");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10018, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type,scope) VALUES ('IS_LEGACY', 'true', 10018, 'client',0)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10018, 1100, 208, 100, 4001, '1513-005', 5000, 29612109, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,currencyid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10018, 1100, 100, $pspID, '1512', '1234abc', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,840,5000,840)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");

        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,currencyid,convertedamount,convertedcurrencyid) VALUES (1001002, 100, 10018, 1100, 100, $pspID, '1512', '1234abc', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,840,5000,840)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001002, ". Constants::iPAYMENT_INIT_WITH_PSP_STATE. ")");

        $checkPaymentStatus = General::getPaymentStatus($this->_OBJ_DB, 1001001,1,1001002);
        $this->assertEquals('Pending', $checkPaymentStatus);

        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001002, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");
        $checkPaymentStatus = General::getPaymentStatus($this->_OBJ_DB, 1001001,1,1001002);
        $this->assertEquals('Complete', $checkPaymentStatus);

        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001002, ". Constants::iPRE_FRAUD_CHECK_CONNECTION_FAILED_STATE. ")");
        $checkPaymentStatus = General::getPaymentStatus($this->_OBJ_DB, 1001001,1,1001002);
        $this->assertEquals('Failed', $checkPaymentStatus);
    }

    public function testGetPaymentStatusPending()
    {
        $pspID = Constants::iWIRE_CARD_PSP;
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd,salt) VALUES (10018, 1, 100, 'Test Client', 'Tuser', 'Tpass','23lkhfgjh24qsdfkjh')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10018, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10018, 'app')");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10018, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type,scope) VALUES ('IS_LEGACY', 'true', 10018, 'client',0)");

        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10018, 1100, 208, 100, 4001, '1513-005', 5000, 29612109, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,currencyid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10018, 1100, 100, $pspID, '1512', '1234abc', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,840,5000,840)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_INIT_WITH_PSP_STATE. ")");

        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,currencyid,convertedamount,convertedcurrencyid) VALUES (1001002, 100, 10018, 1100, 100, $pspID, '1512', '1234abc', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,840,5000,840)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001002, ". Constants::iPAYMENT_INIT_WITH_PSP_STATE. ")");

        $checkPaymentStatus = General::getPaymentStatus($this->_OBJ_DB, 1001001,1,1001002);
        $this->assertEquals('Pending', $checkPaymentStatus);

        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001002, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");
        $checkPaymentStatus = General::getPaymentStatus($this->_OBJ_DB, 1001001,1,1001002);
        $this->assertEquals('Pending', $checkPaymentStatus);
    }

    public function testGetPaymentStatusFailed()
    {
        $pspID = Constants::iWIRE_CARD_PSP;
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd,salt) VALUES (10018, 1, 100, 'Test Client', 'Tuser', 'Tpass','23lkhfgjh24qsdfkjh')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10018, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10018, 'app')");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10018, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type,scope) VALUES ('IS_LEGACY', 'true', 10018, 'client',0)");

        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10018, 1100, 208, 100, 4001, '1513-005', 5000, 29612109, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,currencyid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10018, 1100, 100, $pspID, '1512', '1234abc', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,840,5000,840)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_REJECTED_STATE. ")");

        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,currencyid,convertedamount,convertedcurrencyid) VALUES (1001002, 100, 10018, 1100, 100, $pspID, '1512', '1234abc', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,840,5000,840)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001002, ". Constants::iPAYMENT_INIT_WITH_PSP_STATE. ")");

        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001002, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");
        $checkPaymentStatus = General::getPaymentStatus($this->_OBJ_DB, 1001001,1,1001002);
        $this->assertEquals('Failed', $checkPaymentStatus);

        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001002, ". Constants::iPRE_FRAUD_CHECK_CONNECTION_FAILED_STATE. ")");
        $checkPaymentStatus = General::getPaymentStatus($this->_OBJ_DB, 1001001,1,1001002);
        $this->assertEquals('Failed', $checkPaymentStatus);
    }

    public function testCheckTxnStatus()
    {
        $pspID = Constants::iWIRE_CARD_PSP;
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd,salt) VALUES (10018, 1, 100, 'Test Client', 'Tuser', 'Tpass','23lkhfgjh24qsdfkjh')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10018, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10018, 'app')");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10018, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type,scope) VALUES ('IS_LEGACY', 'true', 10018, 'client',0)");

        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10018, 1100, 208, 100, 4001, '1513-005', 5000, 29612109, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,currencyid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10018, 1100, 100, $pspID, '1512', '1234abc', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,840,5000,840)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");

        $checkTxnStatus = General::checkTxnStatus($this->_OBJ_DB, Constants::iPAYMENT_ACCEPTED_STATE,1001001);
        $this->assertEquals(1, $checkTxnStatus);

        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_REJECTED_STATE. ")");
        $checkTxnStatus = General::checkTxnStatus($this->_OBJ_DB, Constants::iPAYMENT_REJECTED_STATE,1001001,true);
        $this->assertEquals(1, $checkTxnStatus);

        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPRE_FRAUD_CHECK_CONNECTION_FAILED_STATE. ")");
        $checkTxnStatus = General::checkTxnStatus($this->_OBJ_DB, Constants::iPRE_FRAUD_CHECK_CONNECTION_FAILED_STATE,1001001);
        $this->assertEquals(0, $checkTxnStatus);

        $checkTxnStatus = General::checkTxnStatus($this->_OBJ_DB, Constants::iPRE_FRAUD_CHECK_CONNECTION_FAILED_STATE,1001001,true);
        $this->assertEquals(1, $checkTxnStatus);
    }

    public function testGetLinkedTransactions()
    {
        $pspID = Constants::iWIRE_CARD_PSP;
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd,salt) VALUES (10018, 1, 100, 'Test Client', 'Tuser', 'Tpass','23lkhfgjh24qsdfkjh')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10018, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10018, 'app')");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10018, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type,scope) VALUES ('IS_LEGACY', 'true', 10018, 'client',0)");

        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10018, 1100, 208, 100, 4001, '1513-005', 5000, 29612109, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,currencyid,convertedamount,convertedcurrencyid) VALUES (1001001, 100, 10018, 1100, 100, $pspID, '1512', '1234abc', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,840,5000,840)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001001, ". Constants::iPAYMENT_ACCEPTED_STATE. ")");

        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,currencyid,convertedamount,convertedcurrencyid) VALUES (1001002, 100, 10018, 1100, 100, $pspID, '1512', '1234abc', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,840,5000,840)");
        $this->queryDB("INSERT INTO Log.Message_Tbl (txnid, stateid) VALUES (1001002, ". Constants::iPAYMENT_INIT_WITH_PSP_STATE. ")");

        $checkLinkedTxn = General::getLinkedTransactions($this->_OBJ_DB, 1001002,1001001,1);
        $this->assertEquals('<linked_transactions><transaction_details><id>1001001</id><status>Complete</status></transaction_details><transaction_details><id>1001002</id><status>Pending</status></transaction_details></linked_transactions>', $checkLinkedTxn);
    }

    public function tearDown() : void
    {
        $this->_OBJ_DB->disConnect();
        parent::tearDown();
    }

}
