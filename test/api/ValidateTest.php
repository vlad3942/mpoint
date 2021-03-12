<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';


class ValidateTest extends baseAPITest
{
    private $_OBJ_DB;
    public function setUp() : void
    {
        parent::setUp(TRUE);
        $this->_OBJ_DB = RDB::produceDatabase($this->mPointDBInfo);
    }

    public function testSuccessfulValFXServiceType()
    {
		$obj_mPoint = new Validate();
		$fxServiceType = $obj_mPoint->valFXServiceType($this->_OBJ_DB, 13);
        $this->assertEquals(1,$fxServiceType);
	}

    public function testFailureValFXServiceType()
    {
        $obj_mPoint = new Validate();
        $fxServiceType = $obj_mPoint->valFXServiceType($this->_OBJ_DB, 11);
        $this->assertEquals(10,$fxServiceType);
    }

    public function tearDown() : void
    {
        $this->_OBJ_DB->disConnect();
        parent::tearDown();
    }

}
