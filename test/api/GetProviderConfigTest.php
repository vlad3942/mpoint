<?php
/**
 * Created by IntelliJ IDEA.
 * User: Vikas Gupta
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package: mPoint Test Suite
 * File Name:ClientRouteConfigurationsTest.php
 */
use api\classes\merchantservices\Repositories\ReadOnlyConfigRepository;
require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once sAPI_CLASS_PATH . 'simpledom.php';
require_once __DIR__ . '/../../api/classes/crs/RouteFeature.php';
require_once __DIR__ . '/../../api/classes/crs/ClientRouteConfigurations.php';
require_once __DIR__ . '/../../api/classes/crs/ClientRouteCountry.php';
require_once __DIR__ . '/../../api/classes/crs/ClientRouteCurrency.php';
require_once(sCLASS_PATH ."/core/AdditionalProperties.php");
require_once(sCLASS_PATH ."/clientinfo.php");
require_once(sCLASS_PATH ."/core/card.php");
require_once(sCLASS_PATH ."/credit_card.php");


class GetProviderConfigTest extends baseAPITest{
    private $_OBJ_DB;
    protected $_aMPOINT_CONN_INFO;

    public function __construct(){
        parent::__construct();
        $this->constHTTPClient();
    }


    public function setUp() : void {
        parent::setUp(TRUE);
        $this->bIgnoreErrors = true;
        $this->_OBJ_DB = RDB::produceDatabase($this->mPointDBInfo);
    }

    public function constHTTPClient(){
        global $aMPOINT_CONN_INFO;
        $aMPOINT_CONN_INFO['path'] = "/mApp/api/get_provider_config.php";
        $aMPOINT_CONN_INFO["contenttype"] = "text/xml";
        $aMPOINT_CONN_INFO["method"] = "POST";

        $this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO));
    }

    protected function getProviderConfigReq($clientid, $txnid, $cardid){
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<root>';
        $xml .= '<client_provider_configuration>';
        $xml .= '<clientid>'.$clientid.'</clientid>';
        $xml .= '<transaction>';
        $xml .= '<id>'.$txnid.'</id>';
        $xml .= '<cardid>'.$cardid.'</cardid>';
        $xml .= '</transaction>';
        $xml .= '<client-info platform="iOS" version="1.00" language="da">';
		$xml .= '<mobile country-id="100" operator-id="10000">28882861</mobile>';
		$xml .= '<email>jona@oismail.com</email>';
		$xml .= '<customer-ref>jona@oismail.com</customer-ref>';
	    $xml .= '</client-info>';
        $xml .= '</client_provider_configuration>';
        $xml .= '</root>';
        return $xml;
    }

    public function testGetRouteConfiguration(){
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");

        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");

        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, 40, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (2, 10099, 62, '4216310')");

        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 40, '-1')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 62, '-1')");

        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 8, 40, true, 1)");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 8, 62, true, 1)");

        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 200, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 16, 40, '501910******3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");

        # Set Is Legacy Code
        $this->queryDB("INSERT INTO client.services_tbl (clientid, legacy_flow_enabled) VALUES(10099, false);");

        # Route Related SQL
        $this->queryDB("INSERT INTO client.route_tbl(id, clientid, providerid) VALUES (10001, 10099, 40)");
        $this->queryDB("INSERT INTO client.route_tbl(id, clientid, providerid) VALUES (10002, 10099, 62)");

        $this->queryDB("INSERT INTO client.routeconfig_tbl( id, routeid, name, capturetype, mid, username, password, enabled) VALUES (17, 10001, '2c2p-alc_Master_VISA', 2, 'CebuPacific', 'CELLPM', 'HC1XBPV0O4WLKZMG', 'true')");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (17)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (17)");

        $this->queryDB("INSERT INTO client.routeconfig_tbl( id, routeid, name, capturetype, mid, username, password, enabled) VALUES (18, 10002, 'Firstdata', 2, 'first-data', 'user', 'password', 'true')");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (18)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (18)");

        # Transaction Related Entry
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 840, 200, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, euaid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid,sessionid,convertedamount,convertedcurrencyid) VALUES (1001002, 100, 10099, 1100, 1, 5001, 200, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE, 840, 1,5000,840)");

        $clientid = 10099;
        $txnid = 1001002;
        $cardid = 7;
        $xml = $this->getProviderConfigReq($clientid, $txnid, $cardid);
        $this->_httpClient->connect();
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(200, $iStatus);
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?><client_provider_configuration><psp-config id="62" type="1"><name>FIRST DATA</name><merchant-account>first-data</merchant-account><merchant-sub-account></merchant-sub-account><username>user</username><password>password</password><messages></messages><additional-config></additional-config></psp-config><route_configuration><id>18</id><route_id>62</route_id><name>FIRST DATA</name><mid>first-data</mid><username>user</username><password>password</password><route_features></route_features></route_configuration></client_provider_configuration>', $sReplyBody);
    }

    public function testFailureRouteNotFound(){
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");

        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");

        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, 40, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (2, 10099, 62, '4216310')");

        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 40, '-1')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 62, '-1')");

        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 8, 40, true, 1)");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 8, 62, true, 1)");

        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 200, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 16, 40, '501910******3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");

        # Set Is Legacy Code
        $this->queryDB("INSERT INTO client.services_tbl (clientid, legacy_flow_enabled) VALUES(10099, false);");

        # Route Related SQL
        $this->queryDB("INSERT INTO client.route_tbl(id, clientid, providerid) VALUES (10001, 10099, 40)");
        $this->queryDB("INSERT INTO client.route_tbl(id, clientid, providerid) VALUES (10002, 10099, 62)");

        $this->queryDB("INSERT INTO client.routeconfig_tbl( id, routeid, name, capturetype, mid, username, password, enabled) VALUES (17, 10001, '2c2p-alc_Master_VISA', 2, 'CebuPacific', 'CELLPM', 'HC1XBPV0O4WLKZMG', 'true')");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (17)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (17)");

        $this->queryDB("INSERT INTO client.routeconfig_tbl( id, routeid, name, capturetype, mid, username, password, enabled) VALUES (18, 10002, 'Firstdata', 2, 'first-data', 'user', 'password', 'true')");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (18)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (18)");

        # Transaction Related Entry
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 840, 200, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, euaid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid,sessionid,convertedamount,convertedcurrencyid) VALUES (1001002, 100, 10099, 1100, 1, 5001, 200, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE, 840, 1,5000,840)");

        $clientid = 10099;
        $txnid = 1001002;
        $cardid = 1550;
        $xml = $this->getProviderConfigReq($clientid, $txnid, $cardid);
        $this->_httpClient->connect();
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(400, $iStatus);
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?><status><code>24</code><text_code>24</text_code><description>The selected payment card is not available</description></status>', $sReplyBody);

    }

    public function testFailureTransactionNotFound(){
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");

        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");

        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, 40, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (2, 10099, 62, '4216310')");

        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 40, '-1')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 62, '-1')");

        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 8, 40, true, 1)");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 8, 62, true, 1)");

        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 200, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 16, 40, '501910******3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");

        # Set Is Legacy Code
        $this->queryDB("INSERT INTO client.services_tbl (clientid, legacy_flow_enabled) VALUES(10099, false);");

        # Route Related SQL
        $this->queryDB("INSERT INTO client.route_tbl(id, clientid, providerid) VALUES (10001, 10099, 40)");
        $this->queryDB("INSERT INTO client.route_tbl(id, clientid, providerid) VALUES (10002, 10099, 62)");

        $this->queryDB("INSERT INTO client.routeconfig_tbl( id, routeid, name, capturetype, mid, username, password, enabled) VALUES (17, 10001, '2c2p-alc_Master_VISA', 2, 'CebuPacific', 'CELLPM', 'HC1XBPV0O4WLKZMG', 'true')");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (17)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (17)");

        $this->queryDB("INSERT INTO client.routeconfig_tbl( id, routeid, name, capturetype, mid, username, password, enabled) VALUES (18, 10002, 'Firstdata', 2, 'first-data', 'user', 'password', 'true')");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (18)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (18)");

        # Transaction Related Entry
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 840, 200, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, euaid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid,sessionid,convertedamount,convertedcurrencyid) VALUES (1001002, 100, 10099, 1100, 1, 5001, 200, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE, 840, 1,5000,840)");

        $clientid = 10099;
        $txnid = 1001001;
        $cardid = 7;
        $xml = $this->getProviderConfigReq($clientid, $txnid, $cardid);
        $this->_httpClient->connect();
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(400, $iStatus);
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?><status><code>404</code><text_code>404</text_code><description>Transaction with ID:1001001 not found.</description></status>', $sReplyBody);
    }

    public function testSuccessfullPspConfignNode(){
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");

        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");

        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, 40, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (2, 10099, 62, '4216310')");

        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 40, '-1')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 62, '-1')");

        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 8, 40, true, 1)");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 8, 62, true, 1)");

        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 200, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 16, 40, '501910******3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");

        # Set Is Legacy Code
        $this->queryDB("INSERT INTO client.services_tbl (clientid, legacy_flow_enabled) VALUES(10099, false);");

        # Route Related SQL
        $this->queryDB("INSERT INTO client.route_tbl(id, clientid, providerid) VALUES (10001, 10099, 40)");
        $this->queryDB("INSERT INTO client.route_tbl(id, clientid, providerid) VALUES (10002, 10099, 62)");

        $this->queryDB("INSERT INTO client.routeconfig_tbl( id, routeid, name, capturetype, mid, username, password, enabled) VALUES (17, 10001, '2c2p-alc_Master_VISA', 2, 'CebuPacific', 'CELLPM', 'HC1XBPV0O4WLKZMG', 'true')");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (17)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (17)");

        $this->queryDB("INSERT INTO client.routeconfig_tbl( id, routeid, name, capturetype, mid, username, password, enabled) VALUES (18, 10002, 'Firstdata', 2, 'first-data', 'user', 'password', 'true')");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (18)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (18)");

        # Transaction Related Entry
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 840, 200, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, euaid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid,sessionid,convertedamount,convertedcurrencyid) VALUES (1001002, 100, 10099, 1100, 1, 5001, 200, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE, 840, 1,5000,840)");

        $xml = '<?xml version="1.0" encoding="UTF-8"?><root><client_provider_configuration><clientid>10099</clientid><accountid>1100</accountid><pspid>62</pspid></client_provider_configuration></root>';
        $this->_httpClient->connect();
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(200, $iStatus);
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?><client_provider_configuration><psp-config id="62" type="1"><name>FIRST DATA</name><merchant-account>4216310</merchant-account><merchant-sub-account>-1</merchant-sub-account><username>empty</username><password>empty</password><messages></messages><additional-config></additional-config></psp-config></client_provider_configuration>', $sReplyBody);
    }

    public function testFailurePspIdNotFound(){
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");

        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");

        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (1, 10099, 40, '4216310')");
        $this->queryDB("INSERT INTO Client.MerchantAccount_Tbl (id, clientid, pspid, name) VALUES (2, 10099, 62, '4216310')");

        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 40, '-1')");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 62, '-1')");

        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 8, 40, true, 1)");
        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid, enabled, stateid) VALUES (10099, 8, 62, true, 1)");

        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (5001, 200, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10099, 5001)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 5001, 16, 40, '501910******3742', '06/24', TRUE, 10099, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");

        # Set Is Legacy Code
        $this->queryDB("INSERT INTO client.services_tbl (clientid, legacy_flow_enabled) VALUES(10099, false);");

        # Route Related SQL
        $this->queryDB("INSERT INTO client.route_tbl(id, clientid, providerid) VALUES (10001, 10099, 40)");
        $this->queryDB("INSERT INTO client.route_tbl(id, clientid, providerid) VALUES (10002, 10099, 62)");

        $this->queryDB("INSERT INTO client.routeconfig_tbl( id, routeid, name, capturetype, mid, username, password, enabled) VALUES (17, 10001, '2c2p-alc_Master_VISA', 2, 'CebuPacific', 'CELLPM', 'HC1XBPV0O4WLKZMG', 'true')");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (17)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (17)");

        $this->queryDB("INSERT INTO client.routeconfig_tbl( id, routeid, name, capturetype, mid, username, password, enabled) VALUES (18, 10002, 'Firstdata', 2, 'first-data', 'user', 'password', 'true')");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (18)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (18)");

        # Transaction Related Entry
        $this->queryDB("INSERT INTO log.session_tbl (id, clientid, accountid, currencyid, countryid, stateid, orderid, amount, mobile, deviceid, ipaddress, externalid, sessiontypeid) VALUES (1, 10099, 1100, 840, 200, 4001, '103-1418291', 5000, 9876543210, '', '127.0.0.1', -1, 1);");
        $this->queryDB("INSERT INTO Log.Transaction_Tbl (id, typeid, clientid, accountid, keywordid, euaid, countryid, orderid, callbackurl, amount, ip, enabled, currencyid,sessionid,convertedamount,convertedcurrencyid) VALUES (1001002, 100, 10099, 1100, 1, 5001, 200, '103-1418291', '". $sCallbackURL ."', 5000, '127.0.0.1', TRUE, 840, 1,5000,840)");

        $xml = '<?xml version="1.0" encoding="UTF-8"?><root><client_provider_configuration><clientid>10099</clientid><accountid>1100</accountid><pspid>68</pspid></client_provider_configuration></root>';
        $this->_httpClient->connect();
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tuser', 'Tpass'), $xml);
        $sReplyBody = $this->_httpClient->getReplyBody();
        $this->assertEquals(400, $iStatus);
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?><status><code>99</code><text_code>99</text_code><description>Invalid PSP</description></status>', $sReplyBody);
    }

    public function tearDown() : void {
        $this->_OBJ_DB->disConnect();
        parent::tearDown();
    }

}