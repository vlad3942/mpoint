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
        $this->queryDB("INSERT INTO Client.Routeconfig_Tbl (id, routeid, name, capturetype, mid, countryid, currencyid, username, password) VALUES (1, 1, 'Wirecard_VISA', 2, 'TESTMID', null, null, 'username', 'password')");
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

        $this->assertStringContainsString('<payment_providers><payment_provider><id>18</id><name>Wire Card</name><route_configurations><route_configuration><id>1</id><route_name>Wirecard_VISA</route_name></route_configuration></route_configurations></payment_provider></payment_providers>', $xml);
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



    public function tearDown() : void
    {
        $this->_OBJ_DB->disConnect();
        parent::tearDown();
    }


}
