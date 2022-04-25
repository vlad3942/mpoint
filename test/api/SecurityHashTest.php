<?php
use api\classes\SecurityHash;

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once __DIR__ . '/../../api/classes/core/card.php';


class SecurityHashTest extends baseAPITest
{
    private SecurityHash $SecurityHash;
    
    public function setUp() : void
    {
        parent::setUp(FALSE);
    }

    public function testGenerate512Hash()
    {
        $clientId = 10099; 
        $salt = "salt";
        
    
        $securityHash = new SecurityHash($clientId, $salt);
        $securityHash->_hashString = "123456";

        $hash = $securityHash->generate512Hash();
        $this->assertEquals('ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', $hash);
    }
    
    
    
}