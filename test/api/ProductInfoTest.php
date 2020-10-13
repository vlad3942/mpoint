<?php

use api\classes\ProductInfo;

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class ProductInfoTest extends baseAPITest
{
    public function setUp() : void
    {
        parent::setUp(FALSE);
    }

    public function test__construct()
    {
        $additional_data = new ProductInfo('Sample', 2,200);
        $json_string = json_encode($additional_data);
        $this->assertEquals('{"name":"Sample","quantity":2,"price":200}', $json_string);
    }

    public function testEmpty__construct()
    {
        $additional_data = new ProductInfo(NULL, 1, 200);
        $json_string = json_encode($additional_data);
        $this->assertEquals('[]', $json_string);

    }
}