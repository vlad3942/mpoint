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
        $combinations = new Combination(array($paymentType,$paymentType1));
        $this->assertEquals('<combination><paymentType><id>1</id><index>1</index></paymentType><paymentType><id>3</id><index>2</index></paymentType></combination>',$combinations->toXML());
    }

    public function test__construct(): void
    {
        $combinations = new Combination();
        $this->assertIsArray($combinations->getPaymentTypes());
        $this->assertCount(0, $combinations->getPaymentTypes());
    }
}