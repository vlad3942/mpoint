<?php

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';


class ValidateTest extends baseAPITest
{

    private $_OBJ_DB;
    protected $_aHTTP_CONN_INFO;

    public function setUp() : void
    {
        parent::setUp(TRUE);
        global $aHTTP_CONN_INFO;
        $this->bIgnoreErrors = true;
        $this->_aHTTP_CONN_INFO = $aHTTP_CONN_INFO;
        $this->_OBJ_DB = RDB::produceDatabase($this->mPointDBInfo);
        $this->_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH . sLANG ."/global.txt", sLANGUAGE_PATH . sLANG ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");
    }

    public function testSuccessfulValExchangeServiceInfo()
    {
		$obj_mPoint = new Validate();
		$exchangeServiceInfo = $obj_mPoint->valExchangeServiceInfo($this->_OBJ_DB, 13);
        $this->assertEquals(1,$exchangeServiceInfo);
	}

    public function testFailureValExchangeServiceInfo()
    {
        $obj_mPoint = new Validate();
        $exchangeServiceInfo = $obj_mPoint->valExchangeServiceInfo($this->_OBJ_DB, 11);
        $this->assertEquals(10,$exchangeServiceInfo);
    }

    public function tearDown() : void
    {
        $this->_OBJ_DB->disConnect();
        parent::tearDown();
    }

}
