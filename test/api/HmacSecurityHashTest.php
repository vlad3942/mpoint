<?php
// use api\classes\Amount;
use api\classes\HmacSecurityHash;
// use api\classes\PSPData;
// use api\classes\StateInfo;
// use api\classes\TransactionData;

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once __DIR__ . '/../../api/classes/core/card.php';


class HmacSecurityHashTest extends baseAPITest
{
    // protected $_aMPOINT_CONN_INFO;
    private HmacSecurityHash $HmacSecurityHash;
    
    public function setUp() : void
    {
        parent::setUp(FALSE);
    }

    public function test__construct()
    {
        $unique_reference = 101;
        $init_token = 123456;
        $hmac = "hmac";
    
        $this->HmacSecurityHash = new HmacSecurityHash($hmac, $unique_reference, $init_token);
        $x = xml_encode($this->HmacSecurityHash);
        $j = json_encode($this->HmacSecurityHash);
        
        $this->assertEquals('{"unique_reference":"101","init_token":"123456","hmac":"hmac"}', json_encode($this->HmacSecurityHash));
        $this->assertStringContainsString("<hmac-security-hash><unique_reference>101</unique_reference><init_token>123456</init_token><hmac>hmac</hmac></hmac-security-hash>", xml_encode($this->HmacSecurityHash));
    }
}