<?php

use api\classes\StateInfo;

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class StateInfoTest extends baseAPITest
{
    public function setUp()
    {
        parent::setUp(FALSE);
    }

    public function test__construct()
    {
        $stateInfo = new StateInfo(2010, 20103, 'Transaction Failed');
        $json_string = json_encode($stateInfo);
        $this->assertEquals('{"code":2010,"sub_code":20103,"message":"Transaction Failed"}', $json_string);
    }

    public function testStateInfoWithoutSubcode()
    {
        $stateInfo = new StateInfo(2010, NULL, 'Transaction Failed');
        $json_string = json_encode($stateInfo);
        $this->assertEquals('{"code":2010,"message":"Transaction Failed"}', $json_string);
    }

    public function testStateInfoWithoutMessage()
    {
        $stateInfo = new StateInfo(2010, 20103);
        $json_string = json_encode($stateInfo);
        $this->assertEquals('{"code":2010,"sub_code":20103}', $json_string);
    }

    public function testStateInfoWithCode()
    {
        $stateInfo = new StateInfo(2010);
        $json_string = json_encode($stateInfo);
        $this->assertEquals('{"code":2010}', $json_string);
    }

    public function testStateInfoWithWrongCode()
    {
        $stateInfo = new StateInfo(999);
        $json_string = json_encode($stateInfo);
        $this->assertEquals('[]', $json_string);
    }


}