<?php

use api\classes\PSPData;

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class PSPDataTest extends baseAPITest
{
    public function setUp() : void
    {
        parent::setUp(FALSE);
    }

    public function test__construct()
    {
        $pspData = new PSPData(18,'Wirecard',  '23b7d8c8-b2a6-4817-af0a-af24ab66fd83');
        $json_string = json_encode($pspData);
        $this->assertEquals('{"id":18,"name":"Wirecard","external_id":"23b7d8c8-b2a6-4817-af0a-af24ab66fd83"}', $json_string);
    }

    public function testPSPWithoutName()
    {
        $pspData = new PSPData(18, NULL, '23b7d8c8-b2a6-4817-af0a-af24ab66fd83');
        $json_string = json_encode($pspData);
        $this->assertEquals('{"id":18,"external_id":"23b7d8c8-b2a6-4817-af0a-af24ab66fd83"}', $json_string);
    }

    public function testPSPWithoutExternalId()
    {
        $pspData = new PSPData(18,'Wirecard');
        $json_string = json_encode($pspData);
        $this->assertEquals('{"id":18,"name":"Wirecard"}', $json_string);
    }

    public function testPSPDataWithoutOptionalData()
    {
        $pspData = new PSPData(18);
        $json_string = json_encode($pspData);
        $this->assertEquals('{"id":18}', $json_string);
    }

    public function testWrongPSPId()
    {
        $pspData = new PSPData(-1);
        $json_string = json_encode($pspData);
        $this->assertEquals('[]', $json_string);
    }
}