<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package:
 * File Name: ConfigurationTest
 */

use api\classes\splitpayment\config\Combination;
use api\classes\splitpayment\config\Configuration;
use api\classes\splitpayment\config\PaymentType;

require_once __DIR__ . '/../../../../webroot/inc/include.php';
require_once __DIR__ . '/../../../inc/testinclude.php';

class ConfigurationTest extends baseAPITest
{
    private Configuration $configuration;

    public function setUp() : void
    {
        parent::setUp(FALSE);

        $paymentType = new PaymentType(1, 1);
        $paymentType1 = new PaymentType(3, 2);
        $combinations = new Combination(array($paymentType,$paymentType1),false);
        $this->configuration = new Configuration(array($combinations));

    }

    public function testSetCombination(): void
    {
        $paymentType = new PaymentType(1, 1);
        $paymentType1 = new PaymentType(3, 2);
        $combinations = new Combination(array($paymentType,$paymentType1),false);
        $this->configuration->setCombination($combinations);
        $this->assertCount(2, $this->configuration->getCombinations());
    }

    public function testToXML(): void
    {
        $this->assertEquals('<configuration><applicable_combinations><combination><payment_type><id>1</id><sequence>1</sequence></payment_type><payment_type><id>3</id><sequence>2</sequence></payment_type><is_one_step_authorization>false</is_one_step_authorization></combination></applicable_combinations></configuration>', $this->configuration->toXML());
    }

    public function test__construct(): void
    {
        $configuration = new Configuration();
        $this->assertInstanceOf(Configuration::class,$configuration);
        $this->assertCount(0, $configuration->getCombinations());
    }

    public function testGetCombinations(): void
    {
        $this->assertCount(1, $this->configuration->getCombinations());
    }

}