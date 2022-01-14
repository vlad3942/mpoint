<?php
use api\classes\SecurityHashResponse;

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once __DIR__ . '/../../api/classes/core/card.php';


class SecurityHashResponseTest extends baseAPITest
{
    private SecurityHashResponse $SecurityHashResponse;
    
    public function setUp() : void
    {
        parent::setUp(FALSE);
    }

    public function test__construct()
    {
        $unique_reference_identifier = 101;
        $token = 123456;
    
        $this->SecurityHashResponse = new SecurityHashResponse($token, $unique_reference_identifier);
        $x = xml_encode($this->SecurityHashResponse);
        $j = json_encode($this->SecurityHashResponse);
        
        $this->assertEquals('{"unique_reference_identifier":"101","token":"123456"}', json_encode($this->SecurityHashResponse));
        $this->assertStringContainsString("<security_token_detail><unique_reference_identifier>101</unique_reference_identifier><token>123456</token></security_token_detail>", xml_encode($this->SecurityHashResponse));
    }
}