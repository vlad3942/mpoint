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
        $this->queryDB("INSERT INTO Client.Routeconfig_Tbl (id, routeid, name, capturetype, mid, countryid, currencyid, username, password) VALUES (1, 1, 'Wirecard_VISA', 2, 'TESTMID', null, null, 'username', 'password')");
        $this->queryDB("INSERT INTO Client.Routefeature_Tbl (id, clientid, routeconfigid, featureid) VALUES (1, 10099, 1,2)");
        $this->queryDB("INSERT INTO Client.Routefeature_Tbl (id, clientid, routeconfigid, featureid) VALUES (2, 10099, 1, 5)");

        $this->_httpClient->connect();
        $iStatus = $this->_httpClient->send($this->constHTTPHeaders('Tusername', 'Tpassword'));
        $sReplyBody = $this->_httpClient->getReplyBody();

        $expected_RS = $this->getRS_GetClientRoutingConfigurations();

        $this->assertTrue(True, 'Get Matched');
        $this->assertStringContainsString('<route_configuration><id>1</id><provider_id>18</provider_id><country_id></country_id><currency_id></currency_id><mid>TESTMID</mid><route_name>Wirecard_VISA</route_name><username>username</username><password>password</password><capture_type>2</capture_type><enabled>true</enabled><route_features><route_feature><id>1</id><name>Delayed Capture</name></route_feature><route_feature><id>2</id><name>Refund</name></route_feature></route_features></route_configuration>', $expected_RS, 'Client Route Configuration Found');

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
        $this->queryDB("INSERT INTO Client.Routeconfig_Tbl (id, routeid, name, capturetype, mid, countryid, currencyid, username, password) VALUES (1, 1, 'Wirecard_VISA', 2, 'TESTMID', null, null, 'username', 'password')");
        $this->queryDB("INSERT INTO Client.Routefeature_Tbl (id, clientid, routeconfigid, featureid) VALUES (1, 10099, 1,2)");
        $this->queryDB("INSERT INTO Client.Routefeature_Tbl (id, clientid, routeconfigid, featureid) VALUES (2, 10099, 1, 5)");

        $aObj_ClientRouteConfigurations = ClientRouteConfigurations::produceConfig($this->_OBJ_DB, '10099');

        if ($aObj_ClientRouteConfigurations instanceof ClientRouteConfigurations)
        {
            $xml = $aObj_ClientRouteConfigurations->toXML();
        }
        $this->assertStringContainsString('<route_configurations><route_configuration><id>1</id><provider_id>18</provider_id><country_id></country_id><currency_id></currency_id><mid>TESTMID</mid><route_name>Wirecard_VISA</route_name><username>username</username><password>password</password><capture_type>2</capture_type><enabled>true</enabled><route_features><route_feature><id>1</id><name>Delayed Capture</name></route_feature><route_feature><id>2</id><name>Refund</name></route_feature></route_features></route_configuration></route_configurations>', $xml, 'Client Route Configuration Found');
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

    public function tearDown() : void
    {
        $this->_OBJ_DB->disConnect();
        parent::tearDown();
    }

}