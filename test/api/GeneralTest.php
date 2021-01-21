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
        $this->bIgnoreErrors = true;
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
        $pspID = Constants::iWIRE_CARD_PSP;
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd,salt) VALUES (10018, 1, 100, 'Test Client', 'Tuser', 'Tpass','23lkhfgjh24qsdfkjh')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10018, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10018, 'app')");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10018, 'CPM', true)");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10018, $pspID, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, '-1')");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid,dccenabled) VALUES (10018, 8, $pspID,100,true)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (50011, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10018, 50011)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 50011, 8, $pspID, '501910******3742', '06/24', TRUE, 10018, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");
        $this->queryDB("INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid, enabled) VALUES (10018,100,840, true)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10018, 1100, 208, 100, 4001, '1513-005', 5000, 29612109, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,currencyid,euaid,convertedamount,convertedcurrencyid) VALUES (1001012, 100, 10018, 1100, 100, $pspID, '1512', '1234abc', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,840,50011,5000,840)");
        $this->queryDB("INSERT INTO log.txnpassbook_tbl(id, transactionid, amount, currencyid, requestedopt, performedopt, status,clientid) VALUES(102291, 1001012, 5000, 840, 5014, NULL, 'done', 10018)");
        $this->queryDB("INSERT INTO log.txnpassbook_tbl(transactionid, amount, currencyid,  performedopt, status, extref, extrefidentifier, clientid) VALUES ( 1001012, 5000, 840,  1001, 'done', '102291', 'log.txnpassbook_tbl', 10018)");
        $this->queryDB("INSERT INTO Log.Paymentroute_Tbl (id, sessionid, pspid, preference) VALUES (111, 1, 18, 2)");
        $this->queryDB("INSERT INTO Log.Paymentroute_Tbl (id, sessionid, pspid, preference) VALUES (112, 1, 30, 3)");

        $iTxnID = 1001012;
        $obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $this->_OBJ_DB);
        $obj_mCard = new CreditCard($this->_OBJ_DB, $this->_OBJ_TXT, $obj_TxnInfo);
        $obj_CardXML = simpledom_load_string($obj_mCard->getCards(5000));
        $obj_Elem = $obj_CardXML->xpath("/cards/item[@type-id=8 and @state-id=1 and @walletid = '']");

        $this->_aHTTP_CONN_INFO["wire-card"]["paths"]["auth"] = "/_test/simulators/wire-card/auth-declined.php";
        $obj_Processor = PaymentProcessor::produceConfig($this->_OBJ_DB, $this->_OBJ_TXT, $obj_TxnInfo, intval($obj_Elem["pspid"]), $this->_aHTTP_CONN_INFO);
        $response = $obj_Processor->authorize($obj_Elem);
        $code = (int)$response->code;
        $this->assertEquals(Constants::iPAYMENT_SOFT_DECLINED_STATE, $code);
        if($code === Constants::iPAYMENT_SOFT_DECLINED_STATE){
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
            $this->assertEquals(Constants::iPAYMENT_SOFT_DECLINED_STATE, (int)$response->code);
        }
    }

    public function tearDown() : void
    {
        $this->_OBJ_DB->disConnect();
        parent::tearDown();
    }

}
