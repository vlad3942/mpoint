<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anna Lagad
 * Copyright: Cellpoint Digital
 * Link: http://www.cellpointdigital.com
 * Project: mPoint
 * File Name:RouteFeatureTest.php
 */

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once __DIR__ . '/../../api/classes/crs/RouteFeature.php';
require_once __DIR__ . '/../../api/classes/pspconfig.php';

class PSPConfigTest extends baseAPITest
{

    private $_OBJ_DB;

    public function setUp() : void
    {
        parent::setUp(true);
        $this->_OBJ_DB = RDB::produceDatabase($this->mPointDBInfo);
    }

    /**
     * Class PSPConfig produceConfig
     * @throws \ErrorException
     */
    public function testSuccessPSPConfig()
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
        $obj_PSPConfig = PSPConfig::produceConfig($this->_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), $pspID);

        $this->assertInstanceOf('PSPConfig', $obj_PSPConfig);

        $toXML = $obj_PSPConfig->toXML();
        $this->assertStringContainsString('<psp-config id="18" type="1"><name>Wire Card</name><merchant-account>Test 2c2p-alc</merchant-account><merchant-sub-account>Test Sub Merchant</merchant-sub-account>', $toXML, 'Error :: To XML not matched');

        $toAttributeLessXML = $obj_PSPConfig->toAttributeLessXML();
        $this->assertStringContainsString('<pspConfig><id>18</id><type>1</type><name>Wire Card</name><merchantAccount>Test 2c2p-alc</merchantAccount><merchantSubAccount>Test Sub Merchant</merchantSubAccount>', $toAttributeLessXML, 'Error :: Attribute less XML not matched');

        $toRouteConfigXML = $obj_PSPConfig->toRouteConfigXML();
        $this->assertStringContainsString('<route_configuration><id>-1</id><route_id>18</route_id><name>Wire Card</name><mid></mid><username></username><password></password><route_features></route_features></route_configuration>', $toRouteConfigXML, 'Error :: Route Configuration not matched');
    }

    /**
     * Class PSPConfig Produce configurations
     * @throws \ErrorException
     */
    public function testSuccessPSPConfiguration()
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
        $obj_PSPConfig = PSPConfig::produceConfiguration($this->_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), $pspID, $obj_TxnInfo->getRouteConfigID());

        $this->assertInstanceOf('PSPConfig', $obj_PSPConfig);

        $this->assertEquals('Test Sub Merchant', $obj_PSPConfig->getMerchantSubAccount(), 'Error:: Merchant SubAccount name missing');
        $toXML = $obj_PSPConfig->toXML();
        $this->assertStringContainsString('<psp-config id="18" type="1"><name>Wire Card</name><merchant-account>Test 2c2p-alc</merchant-account><merchant-sub-account>Test Sub Merchant</merchant-sub-account><username>CELLPM</username><password>HC1XBPV0O4WLKZMG</password><messages></messages><additional-config><property name="3DVERIFICATION">true</property><property name="TEST_MPI">true</property></additional-config></psp-config>', $toXML, 'Error :: To XML not matched');

        $toAttributeLessXML = $obj_PSPConfig->toAttributeLessXML();
        $this->assertStringContainsString('<pspConfig><id>18</id><type>1</type><name>Wire Card</name><merchantAccount>Test 2c2p-alc</merchantAccount><merchantSubAccount>Test Sub Merchant</merchantSubAccount><username>CELLPM</username><password>HC1XBPV0O4WLKZMG</password><messages></messages><additionalConfig><property><name>3DVERIFICATION</name><value>true</value></property><property><name>TEST_MPI</name><value>true</value></property></additionalConfig></pspConfig>', $toAttributeLessXML, 'Error :: Attribute less XML not matched');

        $aMerchantAccountDetails = array('merchantaccount' => 'Test 2c2p-alc', 'username' => 'CELLPM', 'password' => 'HC1XBPV0O4WLKZMG');
        $toAttributeLessXML = $obj_PSPConfig->toAttributeLessXML(2, $aMerchantAccountDetails);
        $this->assertStringContainsString('<pspConfig><id>18</id><type>1</type><name>Wire Card</name><merchantAccount>Test 2c2p-alc</merchantAccount><merchantSubAccount>Test Sub Merchant</merchantSubAccount><username>CELLPM</username>', $toAttributeLessXML, 'Error :: Attribute less XML not matched with additional');

        $toRouteConfigXML = $obj_PSPConfig->toRouteConfigXML();
        $this->assertStringContainsString('<id>1126</id><route_id>18</route_id><name>Wire Card</name><mid>CebuPacific_USD</mid><username>CELLPM</username>', $toRouteConfigXML, 'Error :: Route Configuration not matched');
    }

    /**
     * Class PSPConfig Produce configurations on Failed
     * @throws \ErrorException
     */
    public function testFailedPSPConfiguration()
    {
        $this->bIgnoreErrors = true; //User Warning Expected
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

        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (50011, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10018, 50011)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 50011, 8, $pspID, '501910******3742', '06/24', TRUE, 10018, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");

        # Transaction Related Entry
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10018, 1100, 208, 100, 4001, '1513-005', 5000, 29612109, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, countryid, pspid, extid, orderid, callbackurl, amount, ip, enabled, keywordid, sessionid,currencyid,euaid,convertedamount,convertedcurrencyid, routeconfigid) VALUES (1001012, 100, 10018, 1100, 100, $pspID, '1512', '1234abc', '". $sCallbackURL. "', 5000, '127.0.0.1', TRUE, 1, 1,840,50011,5000,840, -1)");

        $iTxnID = 1001012;
        $obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $this->_OBJ_DB);
        $obj_PSPConfig = PSPConfig::produceConfiguration($this->_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), $pspID, $obj_TxnInfo->getRouteConfigID());

        $this->assertNotInstanceOf('PSPConfig', $obj_PSPConfig);
        $this->assertNull($obj_PSPConfig);
    }

    public function tearDown() : void
    {
        $this->_OBJ_DB->disConnect();
        parent::tearDown();
    }
}