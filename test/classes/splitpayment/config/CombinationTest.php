<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package: classes.splitpayment.config
 * File Name: CombinationTest
 */

use api\classes\splitpayment\config\Combination;
use api\classes\splitpayment\config\PaymentType;

require_once __DIR__ . '/../../../../webroot/inc/include.php';
require_once __DIR__ . '/../../../inc/testinclude.php';

class CombinationTest extends baseAPITest
{

    public function setUp(): void
    {
        parent::setUp(FALSE);
    }

    public function testSetPaymentType(): void
    {
        $paymentType = new PaymentType(1, 1);
        $paymentType1 = new PaymentType(3, 2);
        $paymentType2 = new PaymentType(4, 3);
        $combinations = new Combination(array($paymentType,$paymentType1));
        $combinations->setPaymentType($paymentType2);
        $this->assertCount(3, $combinations->getPaymentTypes());
    }

    public function testGetPaymentTypes(): void
    {
        $paymentType = new PaymentType(1, 1);
        $combinations = new Combination();
        $combinations->setPaymentType($paymentType);
        $this->assertCount(1, $combinations->getPaymentTypes());
    }

    public function testToXML(): void
    {
        $paymentType = new PaymentType(1, 1);
        $paymentType1 = new PaymentType(3, 2);
        $combinations = new Combination(array($paymentType,$paymentType1),false);
        $this->assertEquals('<combination><payment_type><id>1</id><sequence>1</sequence></payment_type><payment_type><id>3</id><sequence>2</sequence></payment_type><is_one_step_authorization>false</is_one_step_authorization></combination>',$combinations->toXML());
    }

    public function test__construct(): void
    {
        $combinations = new Combination();
        $this->assertIsArray($combinations->getPaymentTypes());
        $this->assertCount(0, $combinations->getPaymentTypes());
    }
}