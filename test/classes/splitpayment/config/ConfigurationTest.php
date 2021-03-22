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
        $combinations = new Combination(array($paymentType,$paymentType1));
        $this->configuration = new Configuration(2, array($combinations));

    }

    public function testGetSplitCount(): void
    {
        $this->assertEquals(2, $this->configuration->getSplitCount());
    }

    public function testSetSplitCount(): void
    {
        $this->configuration->setSplitCount(3);
        $this->assertEquals(3, $this->configuration->getSplitCount());
    }

    public function testSetCombination(): void
    {
        $paymentType = new PaymentType(1, 1);
        $paymentType1 = new PaymentType(3, 2);
        $combinations = new Combination(array($paymentType,$paymentType1));
        $this->configuration->setCombination($combinations);
        $this->assertCount(2, $this->configuration->getCombinations());
    }

    public function testToXML(): void
    {
        $this->assertEquals('<configuration><split_count>2</split_count><combinations><combination><payment_type><id>1</id><index>1</index><is_clubbable>false</is_clubbable></payment_type><payment_type><id>3</id><index>2</index><is_clubbable>false</is_clubbable></payment_type></combination></combinations></configuration>', $this->configuration->toXML());
    }

    public function test__construct(): void
    {
        $configuration = new Configuration(2);
        $this->assertInstanceOf(Configuration::class,$configuration);
        $this->assertCount(0, $configuration->getCombinations());
    }

    public function testGetCombinations(): void
    {
        $this->assertCount(1, $this->configuration->getCombinations());
    }

    public function testProduceConfig(): void
    {
        $configuration =  Configuration::ProduceConfig('{"split_count":2,"combinations":[{"combination":[{"index":1,"id":3,"is_clubbable":false},{"index":2,"id":4,"is_clubbable":false}]},{"combination":[{"index":1,"id":5,"is_clubbable":false},{"index":2,"id":6,"is_clubbable":false}]}]}');
        $this->assertEquals('<configuration><split_count>2</split_count><combinations><combination><payment_type><id>1</id><index>3</index><is_clubbable>false</is_clubbable></payment_type><payment_type><id>2</id><index>4</index><is_clubbable>false</is_clubbable></payment_type></combination><combination><payment_type><id>1</id><index>5</index><is_clubbable>false</is_clubbable></payment_type><payment_type><id>2</id><index>6</index><is_clubbable>false</is_clubbable></payment_type></combination></combinations></configuration>', $configuration->toXML());

    }


}