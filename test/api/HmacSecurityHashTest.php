<?php
use api\classes\HmacSecurityHash;

require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once __DIR__ . '/../../api/classes/core/card.php';


class HmacSecurityHashTest extends baseAPITest
{
    private HmacSecurityHash $HmacSecurityHash;
    
    public function setUp() : void
    {
        parent::setUp(FALSE);
    }

    public function testRegularHmac()
    {
        $clientId = 10099; 
        $orderId = 'CY988'; 
        $amount = 2000;
        $countryid = 640;
        $salt = "salt";
        $mobile = 9898989898;
        $mobileCountry = 640;
        $email = "demo@cellpointDigital.com";
        $device = "";
        $hmacType = "";
    
        $hmacSecurityHash = new HmacSecurityHash($clientId, $orderId, $amount, $countryid, $salt);

        $hmacSecurityHash->setHmacType($hmacType);
        $hmacSecurityHash->setMobile($mobile);
        $hmacSecurityHash->setMobileCountry($mobileCountry);
        $hmacSecurityHash->setEMail($email);
        $hmacSecurityHash->setDeviceId($device);
        $hmac = $hmacSecurityHash->generate512Hash();
        $this->assertEquals('314003aa0fdea3680d3cf375269aceb1a24fe332aa5cc5a15906bcc368b9c57ff82efcef4aa9a7edc04e67343d9eed139264a2dface21b334049cc2f551d8ad8', $hmac);
    }
    
    
    public function testFxHmac()
    {
        $clientId = 10099; 
        $orderId = 'CY988'; 
        $amount = 2000;
        $countryid = 640;
        $salt = "salt";
        $mobile = 9898989898;
        $mobileCountry = 640;
        $email = "demo@cellpointDigital.com";
        $device = "";
        $hmacType = "FX";
        $saleAmount = "3000";
        $saleCurrency = "30";
        $cfxId = "100";
    
        $hmacSecurityHash = new HmacSecurityHash($clientId, $orderId, $amount, $countryid, $salt);

        $hmacSecurityHash->setHmacType($hmacType);
        $hmacSecurityHash->setMobile($mobile);
        $hmacSecurityHash->setMobileCountry($mobileCountry);
        $hmacSecurityHash->setEMail($email);
        $hmacSecurityHash->setDeviceId($device);
        $hmacSecurityHash->setSaleAmount($saleAmount);
        $hmacSecurityHash->setSaleCurrency($saleCurrency);
        $hmacSecurityHash->setCfxID($cfxId);
        
        $fxhmac = $hmacSecurityHash->generate512Hash();
        $this->assertEquals('0d6a6dcedec93a36706a29f1aeae65afef4f7422e755daf4d6e2a1e4a33a1b89afc3d8a07e486b75747fa8e85d6d87f00677d881bdf1f3e4869aa56f86718548', $fxhmac);
        
    }
}