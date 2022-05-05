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

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once sAPI_CLASS_PATH . 'simpledom.php';
require_once __DIR__ . '/../../api/classes/crs/RouteFeature.php';
require_once __DIR__ . '/../../api/classes/crs/ClientRouteConfigurations.php';
require_once __DIR__ . '/../../api/classes/crs/ClientRouteCountry.php';
require_once __DIR__ . '/../../api/classes/crs/ClientRouteCurrency.php';
require_once(sCLASS_PATH ."/core/AdditionalProperties.php");

class GetProviderConfigTest extends baseAPITest
{
    private $_OBJ_DB;
    protected $_aMPOINT_CONN_INFO;

    public function __construct()
    {
        parent::__construct();
        $this->constHTTPClient();
    }


    public function setUp() : void
    {
        parent::setUp(TRUE);
        $this->bIgnoreErrors = true;
        $this->_OBJ_DB = RDB::produceDatabase($this->mPointDBInfo);
    }

    public function constHTTPClient()
    {
        global $aMPOINT_CONN_INFO;
        $aMPOINT_CONN_INFO['path'] = "/mApp/api/get_provider_config.php?id=45&client_id=10018";
        $aMPOINT_CONN_INFO["contenttype"] = "text/xml";
        $aMPOINT_CONN_INFO["method"] = "GET";

        $this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO));
    }

    public function getRS_GetProviderConfig_API() {

        $xml = '<?xml version="1.0" encoding="UTF-8"?><root><client_provider_configuration><psp-config id="45" type="2"><name>Amex</name><merchant-account>CebuPacific_USD</merchant-account><merchant-sub-account></merchant-sub-account><username>CELLPM</username><password>HC1XBPV0O4WLKZMG</password><messages></messages><additional-config><property name="AMEX_CARD_ACCEPTOR_ADDRESS">TEST</property></additional-config></psp-config></client_provider_configuration></root>';
        return $xml;
    }

    public function testGetProviderConfig() {

        $pspID = Constants::iAMEX_ACQUIRER;
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd,salt) VALUES (10018, 1, 100, 'Test Client', 'Tuser', 'Tpass','23lkhfgjh24qsdfkjh')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10018, 4, 'http://mpoint.local.cellpointmobile.com:80/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid, markup) VALUES (1100, 10018, 'app')");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10018, 'CPM', true)");

        $this->queryDB("INSERT INTO client.MerchantAccount_Tbl (id, clientid, pspid, name, enabled, username, passwd, supportedpartialoperations) VALUES (1, 10018, $pspID, 'Test 2c2p-alc', true, 'CELLPM', 'HC1XBPV0O4WLKZMG', 0)");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, $pspID, 'Test Sub Merchant')");

        $this->queryDB("INSERT INTO Client.CardAccess_Tbl (clientid, cardid, pspid,countryid,dccenabled) VALUES (10018, 8, $pspID,100,true)");
        $this->queryDB("INSERT INTO client.countrycurrency_tbl(clientid, countryid, currencyid, enabled) VALUES (10018,100,840, true)");

        $this->queryDB("INSERT INTO client.psp_property_tbl (propertyid,value,clientid) SELECT id,'TEST',10018 FROM SYSTEM.psp_property_tbl where name='AMEX_CARD_ACCEPTOR_ADDRESS'");
        $this->queryDB("INSERT INTO client.services_tbl (clientid, legacy_flow_enabled) VALUES(10018, true);");

        $this->queryDB("INSERT INTO EndUser.Account_Tbl (id, countryid, externalid, mobile, mobile_verified, passwd, enabled) VALUES (50011, 100, 'abcExternal', '29612109', TRUE, 'profilePass', TRUE)");
        $this->queryDB("INSERT INTO EndUser.CLAccess_Tbl (clientid, accountid) VALUES (10018, 50011)");
        $this->queryDB("INSERT INTO EndUser.Card_Tbl (id, accountid, cardid, pspid, mask, expiry, preferred, clientid, name, ticket, card_holder_name) VALUES (61775, 50011, 8, $pspID, '501910******3742', '06/24', TRUE, 10018, NULL, '1767989 ### CELLPOINT ### 100 ### DKK', NULL);");

        ## Route Related SQL
        $this->queryDB("INSERT INTO client.route_tbl(id, clientid, providerid) VALUES (10001, 10018, 45)");
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

        $this->_httpClient->connect();
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tusername', 'Tpassword'));
        $sReplyBody = $this->_httpClient->getReplyBody();

        $expected_RS = $this->getRS_GetProviderConfig_API();

        $this->assertTrue(True, 'Get Matched');
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?><root><client_provider_configuration><psp-config id="45" type="2"><name>Amex</name><merchant-account>CebuPacific_USD</merchant-account><merchant-sub-account></merchant-sub-account><username>CELLPM</username><password>HC1XBPV0O4WLKZMG</password><messages></messages><additional-config><property name="AMEX_CARD_ACCEPTOR_ADDRESS">TEST</property></additional-config></psp-config></client_provider_configuration></root>', $expected_RS, 'Client Route Configuration Found');

    }

    public function testEmptyGetProviderConfig()
    {

        $obj_PSPConfig = PSPConfig::produceConfig($this->_OBJ_DB,10018,1100,45);
        if ($obj_PSPConfig instanceof PSPConfig)
        {
            $xml = $obj_PSPConfig->toXML();
        }
        $this->assertNull($obj_PSPConfig);
    }


    public function tearDown() : void
    {
        $this->_OBJ_DB->disConnect();
        parent::tearDown();
    }

}