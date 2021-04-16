<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anna Lagad
 * Copyright: Cellpoint Digital
 * Link: http://www.cellpointdigital.com
 * Project: mPoint
 * File Name:ClientRouteConfigTest.php
 */

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once sAPI_CLASS_PATH . 'simpledom.php';
require_once __DIR__ . '/../../api/classes/crs/ClientRouteConfig.php';

class ClientRouteConfigTest extends baseAPITest
{

    private $_OBJ_DB;

    public function setUp() : void
    {
        parent::setUp(true);
        $this->_OBJ_DB = RDB::produceDatabase($this->mPointDBInfo);
    }

    public function testSuccessGetClientRouteConfig()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 18, '-1')");
        $this->queryDB("INSERT INTO Client.Route_Tbl (id, clientid, providerid) VALUES (1, 10099, 18)");
        $this->queryDB("INSERT INTO Client.Routeconfig_Tbl (id, routeid, name, capturetype, mid, username, password) VALUES (1, 1, 'Wirecard_VISA', 2, 'TESTMID', 'username', 'password')");

        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (1)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (1)");

        $this->queryDB("INSERT INTO Client.Routefeature_Tbl (id, clientid, routeconfigid, featureid) VALUES (1, 10099, 1,2)");
        $this->queryDB("INSERT INTO Client.Routefeature_Tbl (id, clientid, routeconfigid, featureid) VALUES (2, 10099, 1, 5)");

        $aObj_ClientRouteConfig = ClientRouteConfig::produceConfig($this->_OBJ_DB, 10099);
        $this->assertIsArray($aObj_ClientRouteConfig);
        $xml = '<payment_providers>';
        foreach ($aObj_ClientRouteConfig as $obj_RC)
        {
            $this->assertInstanceOf(ClientRouteConfig::class, $obj_RC);

            if ( ($obj_RC instanceof ClientRouteConfig) === true)
            {
                $xml .= $obj_RC->toXML();
            }
        }
        $xml .= '</payment_providers>';

        $this->assertStringContainsString('<payment_providers><payment_provider><id>1</id><name>Wire Card</name><route_configurations><route_configuration><id>1</id><route_name>Wirecard_VISA</route_name></route_configuration></route_configurations></payment_provider></payment_providers>', $xml);
    }

    public function testEmptyGetClientRouteConfig()
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 18, '-1')");

        $aObj_ClientRouteConfig = ClientRouteConfig::produceConfig($this->_OBJ_DB, 10099);
        $this->assertIsArray($aObj_ClientRouteConfig);
        $xml = '<payment_providers>';
        foreach ($aObj_ClientRouteConfig as $obj_RC)
        {
            $this->assertInstanceOf(ClientRouteConfig::class, $obj_RC);

            if ( ($obj_RC instanceof ClientRouteConfig) === true)
            {
                $xml .= $obj_RC->toXML();
            }
        }
        $xml .= '</payment_providers>';

        $this->assertStringContainsString('<payment_providers></payment_providers>', $xml);
    }

    protected function getDoc() : string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<root>';
        $xml .= '<route_configuration>';
        $xml .= '<id>1</id>';
        $xml .= '<client_id>10099</client_id>';
        $xml .= '<route_id>1</route_id>';
        $xml .= '<mid>testMID</mid>';
        $xml .= '<route_name>Test_route_name</route_name>';
        $xml .= '<username>testusername</username>';
        $xml .= '<password>testpassword</password>';
        $xml .= '<capture_type>1</capture_type>';
        $xml .= '<enabled>true</enabled>';
        $xml .= '<country_ids>';
        $xml .= '<country_id>200</country_id>';
        $xml .= '</country_ids>';
        $xml .= '<currency_ids>';
        $xml .= '<currency_id>840</currency_id>';
        $xml .= '</currency_ids>';
        $xml .= '</route_configuration>';
        $xml .= '</root>';
        return $xml;
    }

    public function testSuccessAddRoute() : void
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 18, '-1')");
        $this->queryDB("INSERT INTO Client.Route_Tbl (id, clientid, providerid) VALUES (1, 10099, 18)");

        $xml = $this->getDoc();
        $obj_DOM = simpledom_load_string($xml);
        $objClientRouteConfig = new ClientRouteConfig();
        $objClientRouteConfig->setInputParams($this->_OBJ_DB, $obj_DOM->{'route_configuration'});
        $response = $objClientRouteConfig->AddRoute();
        $xml = '<route_configuration_response>';
        $xml .=  $objClientRouteConfig->processResponse($response);
        $xml .= '</route_configuration_response>';
        $this->assertStringContainsString('<route_configuration_response><status>Success</status><route_config_id>1</route_config_id><message>Route Configuration Created Successfully.</message></route_configuration_response>', $xml);
    }

    public function testFailToAddRoute() : void
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 18, '-1')");
        $this->queryDB("INSERT INTO Client.Route_Tbl (id, clientid, providerid) VALUES (1, 10099, 18)");

        $xml = $this->getDoc();
        $obj_DOM = simpledom_load_string($xml);
        $objClientRouteConfig = new ClientRouteConfig();
        $objClientRouteConfig->setInputParams($this->_OBJ_DB, $obj_DOM->{'route_configuration'});
        $response = $objClientRouteConfig->AddRoute();
        $xml = '<route_configuration_response>';
        $xml .=  $objClientRouteConfig->processResponse($response);
        $xml .= '</route_configuration_response>';
        $this->assertStringContainsString('<route_configuration_response><status>Success</status><route_config_id>1</route_config_id><message>Route Configuration Created Successfully.</message></route_configuration_response>', $xml);

        $objClientRouteConfig->setInputParams($this->_OBJ_DB, $obj_DOM->{'route_configuration'});
        $response = $objClientRouteConfig->AddRoute();
        $xml = '<route_configuration_response>';
        $xml .=  $objClientRouteConfig->processResponse($response);
        $xml .= '</route_configuration_response>';
        $this->assertStringContainsString('<route_configuration_response><status>Fail</status><route_config_id>1</route_config_id><message>Unable to Create Route Configuration. </message></route_configuration_response>', $xml);
    }

    public function testSuccessUpdateRoute() :void
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.URL_Tbl (clientid, urltypeid, url) VALUES (10099, 4, 'http://mpoint.local.cellpointmobile.com/')");
        $this->queryDB("INSERT INTO Client.Account_Tbl (id, clientid) VALUES (1100, 10099)");
        $this->queryDB("INSERT INTO Client.Keyword_Tbl (id, clientid, name, standard) VALUES (1, 10099, 'CPM', TRUE)");
        $this->queryDB("INSERT INTO Client.MerchantSubAccount_Tbl (accountid, pspid, name) VALUES (1100, 18, '-1')");
        $this->queryDB("INSERT INTO Client.Route_Tbl (id, clientid, providerid) VALUES (1, 10099, 18)");
        $this->queryDB("INSERT INTO Client.Routeconfig_Tbl (id, routeid, name, capturetype, mid, username, password) VALUES (1, 1, 'Wirecard_VISA', 2, 'TESTMID', 'username', 'password')");
        $this->queryDB("INSERT INTO client.routecountry_tbl (routeconfigid) VALUES (1)");
        $this->queryDB("INSERT INTO client.routecurrency_tbl (routeconfigid) VALUES (1)");

        $xml = $this->getDoc();
        $obj_DOM = simpledom_load_string($xml);
        $objClientRouteConfig = new ClientRouteConfig();
        $objClientRouteConfig->setInputParams($this->_OBJ_DB, $obj_DOM->{'route_configuration'});
        $response = $objClientRouteConfig->updateRoute();
        $xml = '<route_configuration_response>';
        $xml .=  $objClientRouteConfig->getUpdateRouteResponseAsXML($response);
        $xml .= '</route_configuration_response>';
        $this->assertStringContainsString('<route_configuration_response><status>Success</status><message>Route Configuration Updated Successfully.</message></route_configuration_response>', $xml);
    }

    public function tearDown() : void
    {
        $this->_OBJ_DB->disConnect();
        parent::tearDown();
    }


}
