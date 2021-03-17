<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anna Lagad
 * Copyright: Cellpoint Digital
 * Link: http://www.cellpointdigital.com
 * Project: mPoint
 * File Name:FxServiceTypeTest.php
 */

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once sAPI_CLASS_PATH . 'simpledom.php';
require_once __DIR__ . '/../../api/classes/crs/FxServiceType.php';

class FxServiceTypeTest extends baseAPITest
{

    private $_OBJ_DB;

    public function setUp() : void
    {
        parent::setUp(true);
        $this->_OBJ_DB = RDB::produceDatabase($this->mPointDBInfo);
    }

    public function testSuccessGetFxServiceType()
    {
        $aObj_FxServiceType = FxServiceType::produceConfig($this->_OBJ_DB);
        $xml = '<fx_service_types>';
        foreach ($aObj_FxServiceType as $obj_FxServiceType)
        {
            if ( ($obj_FxServiceType instanceof FxServiceType) === true)
            {
                $xml .= $obj_FxServiceType->toXML();
            }
        }
        $xml .= '</fx_service_types>';

        $this->assertStringContainsString('<fx_service_types><fx_service_type><id>11</id><name>DCC Opt</name><enabled>true</enabled></fx_service_type><fx_service_type><id>12</id><name>DCC Not opt</name><enabled>true</enabled></fx_service_type><fx_service_type><id>21</id><name>MCP opt</name><enabled>true</enabled></fx_service_type><fx_service_type><id>22</id><name>MCP Not opt</name><enabled>true</enabled></fx_service_type><fx_service_type><id>31</id><name>External MCP opt</name><enabled>true</enabled></fx_service_type><fx_service_type><id>32</id><name>External MCP Not opt</name><enabled>true</enabled></fx_service_type><fx_service_type><id>41</id><name>PCC Opt</name><enabled>true</enabled></fx_service_type><fx_service_type><id>42</id><name>PCC Not opt</name><enabled>true</enabled></fx_service_type></fx_service_types>', $xml);
    }

    public function tearDown() : void
    {
        $this->_OBJ_DB->disConnect();
        parent::tearDown();
    }


}
