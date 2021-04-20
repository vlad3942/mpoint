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

class ClientRouteConfigurationsTest extends baseAPITest
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
        $aMPOINT_CONN_INFO['path'] = "/mConsole/api/get_client_route_configurations.php?client_id=10099";
        $aMPOINT_CONN_INFO["contenttype"] = "text/xml";
        $aMPOINT_CONN_INFO["method"] = "GET";

        $this->_aMPOINT_CONN_INFO = $aMPOINT_CONN_INFO;
        $this->_httpClient = new HTTPClient(new Template(), HTTPConnInfo::produceConnInfo($aMPOINT_CONN_INFO));
    }

    public function getRS_GetClientRoutingConfigurations() {

        $xml = '<?xml version="1.0" encoding="UTF-8"?><root><route_configurations><route_configuration><id>1</id><provider_id>18</provider_id><country_id></country_id><currency_id></currency_id><mid>TESTMID</mid><route_name>Wirecard_VISA</route_name><username>username</username><password>password</password><capture_type>2</capture_type><enabled>true</enabled><route_features><route_feature><id>1</id><name>Delayed Capture</name></route_feature><route_feature><id>2</id><name>Refund</name></route_feature></route_features></route_configuration></route_configurations></root>';
        return $xml;
    }

    public function testGetClientRoutingConfigurations_API() {

        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.Route_Tbl (id, clientid, providerid) VALUES (1, 10099, 18)");
        $this->queryDB("INSERT INTO Client.Routeconfig_Tbl (id, routeid, name, capturetype, mid, username, password) VALUES (1, 1, 'Wirecard_VISA', 2, 'TESTMID', 'username', 'password')");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (1)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (1)");
        $this->queryDB("INSERT INTO Client.Routefeature_Tbl (id, clientid, routeconfigid, featureid) VALUES (1, 10099, 1,2)");
        $this->queryDB("INSERT INTO Client.Routefeature_Tbl (id, clientid, routeconfigid, featureid) VALUES (2, 10099, 1, 5)");

        $this->_httpClient->connect();
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tusername', 'Tpassword'));
        $sReplyBody = $this->_httpClient->getReplyBody();

        $expected_RS = $this->getRS_GetClientRoutingConfigurations();

        $this->assertTrue(True, 'Get Matched');
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?><root><route_configurations><route_configuration><id>1</id><provider_id>18</provider_id><country_id></country_id><currency_id></currency_id><mid>TESTMID</mid><route_name>Wirecard_VISA</route_name><username>username</username><password>password</password><capture_type>2</capture_type><enabled>true</enabled><route_features><route_feature><id>1</id><name>Delayed Capture</name></route_feature><route_feature><id>2</id><name>Refund</name></route_feature></route_features></route_configuration></route_configurations></root>', $expected_RS, 'Client Route Configuration Found');

    }

    /**
     * Test For Covering Class base
     * @throws \ErrorException
     * @throws \HTTPConnectionException
     * @throws \HTTPSendException
     */
    public function testGetClientRoutingConfigurations()
    {
        # SQL Entries
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.Route_Tbl (id, clientid, providerid) VALUES (1, 10099, 18)");
        $this->queryDB("INSERT INTO Client.Routeconfig_Tbl (id, routeid, name, capturetype, mid, username, password) VALUES (1, 1, 'Wirecard_VISA', 2, 'TESTMID', 'username', 'password')");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (1)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (1)");
        $this->queryDB("INSERT INTO Client.Routefeature_Tbl (id, clientid, routeconfigid, featureid) VALUES (1, 10099, 1,2)");
        $this->queryDB("INSERT INTO Client.Routefeature_Tbl (id, clientid, routeconfigid, featureid) VALUES (2, 10099, 1, 5)");
        $this->queryDB("INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type,scope) VALUES ('PAYPAL_MID_MYR', 'tsto1654', 1, 'merchant',1)");
        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) VALUES ('PAYPAL_USERNAME_MYR', 'test123', 1, 'merchant',1)");

        $aObj_ClientRouteConfigurations = ClientRouteConfigurations::produceConfig($this->_OBJ_DB, '10099');

        if ($aObj_ClientRouteConfigurations instanceof ClientRouteConfigurations)
        {
            $xml = $aObj_ClientRouteConfigurations->toXML();
        }
        $this->assertStringContainsString('<route_configurations><route_configuration><id>1</id><route_id>1</route_id><provider_id>18</provider_id><mid>TESTMID</mid><route_name>Wirecard_VISA</route_name><username>username</username><password>password</password><capture_type>2</capture_type><enabled>true</enabled><route_features><route_feature><id>2</id><name>Delayed Capture</name></route_feature><route_feature><id>5</id><name>Refund</name></route_feature></route_features><country_ids><country_id>0</country_id></country_ids><currency_ids><currency_id>0</currency_id></currency_ids><additional_data><param><key>PAYPAL_MID_MYR</key><value>tsto1654</value><scope>1</scope></param><param><key>PAYPAL_USERNAME_MYR</key><value>test123</value><scope>1</scope></param></additional_data></route_configuration></route_configurations>', $xml, 'Client Route Configuration Found');
    }


    /**
     * Empty Test For Covering Class
     * @throws \ErrorException
     * @throws \HTTPConnectionException
     * @throws \HTTPSendException
     */
    public function testEmptyGetClientRoutingConfigurations()
    {

        $aObj_ClientRouteConfigurations = ClientRouteConfigurations::produceConfig($this->_OBJ_DB, '10099');

        if ($aObj_ClientRouteConfigurations instanceof ClientRouteConfigurations)
        {
            $xml = $aObj_ClientRouteConfigurations->toXML();
        }

        $this->assertStringContainsString('<route_configurations></route_configurations>', $xml, 'No Client Route Configuration Found :: ' . $xml);
    }

    public function testSuccessDeleteRouteConfig() : void
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.Route_Tbl (id, clientid, providerid) VALUES (1, 10099, 18)");
        $this->queryDB("INSERT INTO Client.Routeconfig_Tbl (id, routeid, name, capturetype, mid, username, password) VALUES (1, 1, 'Wirecard_VISA', 2, 'TESTMID', 'username', 'password')");

        $status = ClientRouteConfigurations::deleteRouteConfig($this->_OBJ_DB, 1);
        $this->assertTrue($status);
    }

    public function testFailDeleteRouteConfig() : void
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.Route_Tbl (id, clientid, providerid) VALUES (1, 10099, 18)");
        $this->queryDB("INSERT INTO Client.Routeconfig_Tbl (id, routeid, name, capturetype, mid, username, password) VALUES (1, 1, 'Wirecard_VISA', 2, 'TESTMID', 'username', 'password')");

        $status = ClientRouteConfigurations::deleteRouteConfig($this->_OBJ_DB, 1001);
        $this->assertFalse($status);
    }

    public function tearDown() : void
    {
        $this->_OBJ_DB->disConnect();
        parent::tearDown();
    }

}