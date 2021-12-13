<?php
use api\classes\HmacSecurityHashResponse;

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once __DIR__ . '/../../api/classes/core/card.php';


class HmacSecurityHashResponseTest extends baseAPITest
{
    private HmacSecurityHashResponse $HmacSecurityHashResponse;
    
    public function setUp() : void
    {
        parent::setUp(FALSE);
    }

    public function test__construct()
    {
        $unique_reference = 101;
        $init_token = 123456;
        $hmac = "hmac";
    
        $this->HmacSecurityHashResponse = new HmacSecurityHashResponse($hmac, $unique_reference, $init_token);
        $x = xml_encode($this->HmacSecurityHashResponse);
        $j = json_encode($this->HmacSecurityHashResponse);
        
        $this->assertEquals('{"unique_reference":"101","init_token":"123456","hmac":"hmac"}', json_encode($this->HmacSecurityHashResponse));
        $this->assertStringContainsString("<hmac-security-hash><unique_reference>101</unique_reference><init_token>123456</init_token><hmac>hmac</hmac></hmac-security-hash>", xml_encode($this->HmacSecurityHashResponse));
    }
}