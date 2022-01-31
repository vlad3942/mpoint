<?php
use api\classes\InitTokenSecurityHash;

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once __DIR__ . '/../../api/classes/core/card.php';


class InitTokenSecurityHashTest extends baseAPITest
{
    private InitTokenSecurityHash $InitTokenSecurityHash;
    
    public function setUp() : void
    {
        parent::setUp(FALSE);
    }

    public function testInitToken()
    {
        $clientId = 10099; 
        $username = "user";
        $password = "pass";
        $nonce = "123456";
        $acceptUrl = "";
    
        $InitTokenSecurityHash = new InitTokenSecurityHash($clientId, $nonce, $username, $password);
        $InitTokenSecurityHash->setAcceptUrl($acceptUrl);
        $initToken = $InitTokenSecurityHash->generate512Hash();
        $this->assertEquals('d0a7b634ffdc295c4627942b85a9f45fabfd1db0056e11e4c540db5d897122e4736da104a641dd5f9670cd2b57b222ad67678103db31184f4501145dc8e7130a', $initToken);
    }
    
    public function testInitTokenAcceptURL()
    {
        $clientId = 10099; 
        $username = "user";
        $password = "pass";
        $nonce = "123456";
        $acceptUrl = "http://www";
    
        $InitTokenSecurityHash = new InitTokenSecurityHash($clientId, $nonce, $username, $password);
        $InitTokenSecurityHash->setAcceptUrl($acceptUrl);
        $initToken = $InitTokenSecurityHash->generate512Hash();
        $this->assertEquals('ff3277e1d0e7eb2b3e4b25b08e96a4067d66811cb1f33363f5b8aa88f27095a8134a82e891311a973be2f4c179411183e567711e2df6525175f781adc75eb160', $initToken);
    }
}