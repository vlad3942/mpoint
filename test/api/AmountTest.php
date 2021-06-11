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
        $amount = new Amount(100, 840, 2,"PHP",1);
        $json_string = json_encode($amount);
        $this->assertEquals('{"value":100,"currency_id":840,"decimals":2,"alpha3code":"PHP","conversion_rate":1}', $json_string);
    }

    public function testWrongAmount()
    {
        $additional_data = new Amount(-1, 840,2,"PHP",1);
        $json_string = json_encode($additional_data);
        $this->assertEquals('[]', $json_string);
    }

    public function testWrongCurrency()
    {
        $additional_data = new Amount(100, -1,2,"PHP",1);
        $json_string = json_encode($additional_data);
        $this->assertEquals('[]', $json_string);
    }

    public function testAmountWithoutConversionRate()
    {
        $additional_data = new Amount(100, 840,2,"PHP");
        $json_string = json_encode($additional_data);
        $this->assertEquals('{"value":100,"currency_id":840,"decimals":2,"alpha3code":"PHP"}', $json_string);
    }

    public function testWrongDecimals()
    {
        $additional_data = new Amount(100, 840,-1,"PHP");
        $json_string = json_encode($additional_data);
        $this->assertEquals('[]', $json_string);
    }

    public function testWrongCode()
    {
        $additional_data = new Amount(100, 840,-1,"");
        $json_string = json_encode($additional_data);
        $this->assertEquals('[]', $json_string);
    }

}