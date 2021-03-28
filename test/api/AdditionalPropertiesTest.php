<?php
/**
 * Created by IntelliJ IDEA.
 * User: Vikas Gupta
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package: mPoint Test Suite
 * File Name:AdditionalPropertiesTest.php
 */

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once sAPI_CLASS_PATH . 'simpledom.php';
require_once(sCLASS_PATH ."/core/AdditionalProperties.php");

class ClientRouteConfigurationsTest extends baseAPITest
{
    private $_OBJ_DB;

    public function setUp() : void
    {
        parent::setUp(true);
        $this->_OBJ_DB = RDB::produceDatabase($this->mPointDBInfo);
    }

    public function testSuccessGetAdditionalProperty() : void
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.Route_Tbl (id, clientid, providerid) VALUES (1, 10099, 18)");
        $this->queryDB("INSERT INTO Client.Routeconfig_Tbl (id, routeid, name, capturetype, mid, username, password) VALUES (1, 1, 'Wirecard_VISA', 2, 'TESTMID', 'username', 'password')");
        $this->queryDB("INSERT INTO Client.AdditionalProperty_Tbl (key, value, externalid, type,scope) VALUES ('PAYPAL_MID_MYR', 'tsto1654', 1, 'merchant',1)");
        $this->queryDB("INSERT INTO client.additionalproperty_tbl (key, value, externalid, type,scope) VALUES ('PAYPAL_USERNAME_MYR', 'test123', 1, 'merchant',1)");

        $aObj_AdditionalProperties = AdditionalProperties::produceConfig($this->_OBJ_DB, 1, 'merchant', );
        $xml = '';
        $this->assertIsArray($aObj_AdditionalProperties);
        if (empty($aObj_AdditionalProperties) === false) {
            $xml = '<additional_data>';
            foreach ($aObj_AdditionalProperties as $additionalProperty) {
                if ($additionalProperty instanceof AdditionalProperties) {
                    $xml .= $additionalProperty->toXML();
                }
            }
            $xml .= '</additional_data>';
        }
        $this->assertStringContainsString('<additional_data><param><key>PAYPAL_MID_MYR</key><value>tsto1654</value></param><param><key>PAYPAL_USERNAME_MYR</key><value>test123</value></param></additional_data>', $xml);
    }

    public function testEmptyGetAdditionalProperty() : void
    {
        $this->queryDB("INSERT INTO Client.Client_Tbl (id, flowid, countryid, name, username, passwd) VALUES (10099, 1, 100, 'Test Client', 'Tuser', 'Tpass')");
        $this->queryDB("INSERT INTO Client.Route_Tbl (id, clientid, providerid) VALUES (1, 10099, 18)");
        $this->queryDB("INSERT INTO Client.Routeconfig_Tbl (id, routeid, name, capturetype, mid, username, password) VALUES (1, 1, 'Wirecard_VISA', 2, 'TESTMID', 'username', 'password')");

        $aObj_AdditionalProperties = AdditionalProperties::produceConfig($this->_OBJ_DB, 1, 'merchant', );
        $xml = '';
        $this->assertIsArray($aObj_AdditionalProperties);
        if (empty($aObj_AdditionalProperties) === false) {
            $xml = '<additional_data>';
            foreach ($aObj_AdditionalProperties as $additionalProperty) {
                if ($additionalProperty instanceof AdditionalProperties) {
                    $xml .= $additionalProperty->toXML();
                }
            }
            $xml .= '</additional_data>';
        }
        $this->assertEquals('', $xml);
    }

    public function tearDown() : void
    {
        $this->_OBJ_DB->disConnect();
        parent::tearDown();
    }

}