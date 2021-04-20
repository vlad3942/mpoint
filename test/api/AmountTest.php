<?php

use api\classes\Amount;

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';

class AmountTest extends baseAPITest
{
    public function setUp() : void
    {
        parent::setUp(FALSE);
    }

    public function test__construct()
    {
        $amount = new Amount(100, 840, 1);
        $json_string = json_encode($amount);
        $this->assertEquals('{"value":100,"currency_id":840,"conversion_rate":1}', $json_string);
    }

    public function testWrongAmount()
    {
        $additional_data = new Amount(-1, 840,1);
        $json_string = json_encode($additional_data);
        $this->assertEquals('[]', $json_string);
    }

    public function testWrongCurrency()
    {
        $additional_data = new Amount(100, -1,1);
        $json_string = json_encode($additional_data);
        $this->assertEquals('[]', $json_string);
    }

    public function testAmountWithoutConversionRate()
    {
        $additional_data = new Amount(100, 840);
        $json_string = json_encode($additional_data);
        $this->assertEquals('{"value":100,"currency_id":840}', $json_string);
    }
}