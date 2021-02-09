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
require_once __DIR__ . '/../../api/classes/payment_route.php';

class PaymentRouteTest extends baseAPITest
{

    private $_OBJ_DB;
    protected $_aHTTP_CONN_INFO;

    public function setUp(): void
    {
        parent::setUp(TRUE);
        global $aHTTP_CONN_INFO;
        $this->bIgnoreErrors = true;
        $this->_aHTTP_CONN_INFO = $aHTTP_CONN_INFO;
        $this->_OBJ_DB = RDB::produceDatabase($this->mPointDBInfo);
    }

    public function testSuccessSetAlternateRoute()
    {
        $iTxnID = 1001001;
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES ($iTxnID, 100, 10099, 1100, 1,  18, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");

        ## Route Related SQL
        $this->queryDB("INSERT INTO client.route_tbl(id, clientid, providerid) VALUES (10001, 10099, 1)");
        $this->queryDB("INSERT INTO client.routeconfig_tbl( id, routeid, name, capturetype, mid, username, password, enabled) VALUES (1126, 10001, '2c2p-alc_Master_VISA_USD', 2, 'CebuPacific_USD', 'CELLPM', 'HC1XBPV0O4WLKZMG', 'true')");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (1126)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (1126)");

        $this->queryDB("INSERT INTO client.routeconfig_tbl( id, routeid, name, capturetype, mid, username, password, enabled) VALUES (1127, 10001, '2c2p-alc_Master_VISA_USD', 2, 'CebuPacific_USD', 'CELLPM', 'HC1XBPV0O4WLKZMG', 'true')");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (1127)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (1127)");


        $obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $this->_OBJ_DB);

        $aRoutes = array(
            array('id' => 1126, 'preference' => 1),
            array('id' => 1127, 'preference' => 3)
        );

        if (count ( $aRoutes ) > 0) {
            $objPaymentRoute = new PaymentRoute($this->_OBJ_DB, $obj_TxnInfo->getSessionId());
            $result = $objPaymentRoute->setAlternateRoute($aRoutes);
            $this->assertTrue($result);

        }

    }

    public function testFailedSetAlternateRoute()
    {
        $iTxnID = 1001002;
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES ($iTxnID, 100, 10099, 1100, 1,  18, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");

        ## Route Related SQL
        // $this->queryDB("INSERT INTO client.route_tbl(id, clientid, providerid) VALUES (10001, 10099, 40)");
        // $this->queryDB("INSERT INTO client.routeconfig_tbl( id, routeid, name, capturetype, mid, username, password, enabled) VALUES (1126, 10001, '2c2p-alc_Master_VISA_USD', 2, 'CebuPacific_USD', 'CELLPM', 'HC1XBPV0O4WLKZMG', 'true')");
        // $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (1126)");
        // $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (1126)");

        $obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $this->_OBJ_DB);

        $aRoutes = array(
            array('id' => 1126, 'preference' => 2)
        );

        if (count ( $aRoutes ) > 0) {
            $objPaymentRoute = new PaymentRoute($this->_OBJ_DB, $obj_TxnInfo->getSessionId());
            $result = $objPaymentRoute->setAlternateRoute($aRoutes);
            $this->assertFalse($result);
        }

    }


    public function testInvalidInputParams()
    {
        $iTxnID = 1001003;
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES ($iTxnID, 100, 10099, 1100, 1,  18, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");

        ## Route Related SQL
        // $this->queryDB("INSERT INTO client.route_tbl(id, clientid, providerid) VALUES (10001, 10099, 40)");
        //
        // $this->queryDB("INSERT INTO client.routeconfig_tbl( id, routeid, name, capturetype, mid, username, password, enabled) VALUES (1112, 10001, '2c2p-alc_Master_VISA_USD', 2, 'CebuPacific_USD', 'CELLPM', 'HC1XBPV0O4WLKZMG', 'true')");
        // $this->queryDB("INSERT INTO client.routeconfig_tbl( id, routeid, name, capturetype, mid, username, password, enabled) VALUES (1113, 10001, '2c2p-alc_Master_VISA_USD', 2, 'CebuPacific_USD', 'CELLPM', 'HC1XBPV0O4WLKZMG', 'true')");

        $obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $this->_OBJ_DB);

        $aRoutes = array(
            array('id' => 1112, 'preference' => 2),
            array('id' => 1113, 'preference' => 3)
        );

        if (count ( $aRoutes ) > 0) {
            $objPaymentRoute = new PaymentRoute($this->_OBJ_DB, $obj_TxnInfo->getSessionId());
            $result = $objPaymentRoute->setAlternateRoute($aRoutes);
            $this->assertFalse($result);
        }
    }

    public function testSuccessGetAlternateRoute()
    {
        $iTxnID = 1001004;
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES ($iTxnID, 100, 10099, 1100, 1,  18, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");

        ## Route Related SQL
        $this->queryDB("INSERT INTO client.route_tbl(id, clientid, providerid) VALUES (10001, 10099, 1)");
        $this->queryDB("INSERT INTO client.routeconfig_tbl( id, routeid, name, capturetype, mid, username, password, enabled) VALUES (1126, 10001, '2c2p-alc_Master_VISA_USD', 2, 'CebuPacific_USD', 'CELLPM', 'HC1XBPV0O4WLKZMG', 'true')");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (1126)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (1126)");

        $this->queryDB("INSERT INTO client.routeconfig_tbl( id, routeid, name, capturetype, mid, username, password, enabled) VALUES (1127, 10001, '2c2p-alc_Master_VISA_USD', 2, 'CebuPacific_USD', 'CELLPM', 'HC1XBPV0O4WLKZMG', 'true')");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (1127)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (1127)");

        # Payment Route Table
        $this->queryDB("INSERT INTO Log.Paymentroute_Tbl (id, sessionid, routeconfigid, preference) VALUES (111, 10, 1126, 2)");
        $this->queryDB("INSERT INTO Log.Paymentroute_Tbl (id, sessionid, routeconfigid, preference) VALUES (112, 10, 1127, 3)");


        $obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $this->_OBJ_DB);

        $objPaymentRoute = new PaymentRoute($this->_OBJ_DB, $obj_TxnInfo->getSessionId());
        $iSecondAlternateRoute = $objPaymentRoute->getAlternateRoute(Constants::iSECOND_ALTERNATE_ROUTE);
        $this->assertEquals(1126, $iSecondAlternateRoute);
        $iThirdAlternateRoute = $objPaymentRoute->getAlternateRoute(Constants::iTHIRD_ALTERNATE_ROUTE);
        $this->assertEquals(1127, $iThirdAlternateRoute);
    }

    public function testFailedGetAlternateRoute()
    {
        $iTxnID = 1001005;
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES ($iTxnID, 100, 10099, 1100, 1,  18, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");

        ## Route Related SQL
        $this->queryDB("INSERT INTO client.route_tbl(id, clientid, providerid) VALUES (10001, 10099, 1)");
        $this->queryDB("INSERT INTO client.routeconfig_tbl( id, routeid, name, capturetype, mid, username, password, enabled) VALUES (1126, 10001, '2c2p-alc_Master_VISA_USD', 2, 'CebuPacific_USD', 'CELLPM', 'HC1XBPV0O4WLKZMG', 'true')");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (1126)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (1126)");

        $this->queryDB("INSERT INTO client.routeconfig_tbl( id, routeid, name, capturetype, mid, username, password, enabled) VALUES (1127, 10001, '2c2p-alc_Master_VISA_USD', 2, 'CebuPacific_USD', 'CELLPM', 'HC1XBPV0O4WLKZMG', 'true')");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (1127)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (1127)");

        # Payment Route Table
        $this->queryDB("INSERT INTO Log.Paymentroute_Tbl (id, sessionid, routeconfigid, preference) VALUES (111, 10, 1126, 2)");
        $this->queryDB("INSERT INTO Log.Paymentroute_Tbl (id, sessionid, routeconfigid, preference) VALUES (112, 10, 1127, 3)");

        $obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $this->_OBJ_DB);

        $objPaymentRoute = new PaymentRoute($this->_OBJ_DB, $obj_TxnInfo->getSessionId());
        $iSecondAlternateRoute = $objPaymentRoute->getAlternateRoute(4);
        $this->assertEquals(0, $iSecondAlternateRoute);

    }

    public function testGetAlternateRouteFailureScenario()
    {
        $iTxnID = 1001006;
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES ($iTxnID, 100, 10099, 1100, 1,  18, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");

        ############################
        ## Route Related SQL
        $this->queryDB("INSERT INTO client.route_tbl(id, clientid, providerid) VALUES (10001, 10099, 1)");
        $this->queryDB("INSERT INTO client.routeconfig_tbl( id, routeid, name, capturetype, mid, username, password, enabled) VALUES (1126, 10001, '2c2p-alc_Master_VISA_USD', 2, 'CebuPacific_USD', 'CELLPM', 'HC1XBPV0O4WLKZMG', 'true')");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (1126)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (1126)");

        $this->queryDB("INSERT INTO client.routeconfig_tbl( id, routeid, name, capturetype, mid, username, password, enabled) VALUES (1127, 10001, '2c2p-alc_Master_VISA_USD', 2, 'CebuPacific_USD', 'CELLPM', 'HC1XBPV0O4WLKZMG', 'true')");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (1127)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (1127)");

        # Payment Route Table
        $this->queryDB("INSERT INTO Log.Paymentroute_Tbl (id, sessionid, routeconfigid, preference) VALUES (111, 10, 1126, 2)");
        $this->queryDB("INSERT INTO Log.Paymentroute_Tbl (id, sessionid, routeconfigid, preference) VALUES (112, 10, 1127, 3)");

        $obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $this->_OBJ_DB);

        $this->tearDown();
        $this->mPointDBInfo['port'] = 5400;
        $this->_OBJ_DB = RDB::produceDatabase($this->mPointDBInfo);

        $objPaymentRoute = new PaymentRoute($this->_OBJ_DB, $obj_TxnInfo->getSessionId());
        $iSecondAlternateRoute = $objPaymentRoute->getAlternateRoute(4);
        $this->assertEquals(0, $iSecondAlternateRoute);
    }

    public function testSetAlternateRouteFailureScenario()
    {
        $iTxnID = 1001007;
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (10, 10099, 1100, 208, 100, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, pspid, euaid, countryid, orderid, callbackurl, amount, ip, enabled,sessionid,convertedamount) VALUES ($iTxnID, 100, 10099, 1100, 1,  18, 5001, 100, '103-1418291', 'test.com', 5000, '127.0.0.1', TRUE,10,5000)");

        $obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $this->_OBJ_DB);

        $iRoutes = 18;
        $objPaymentRoute = new PaymentRoute($this->_OBJ_DB, $obj_TxnInfo->getSessionId());
        $result = $objPaymentRoute->setAlternateRoute($iRoutes);
        $this->assertFalse($result);
    }


    public function tearDown():void
    {
        $this->_OBJ_DB->disConnect();
        parent::tearDown();
    }

}
