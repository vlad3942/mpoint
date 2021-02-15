<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package: classes.splitpayment.config
 * File Name: PaymentTypeTest
 */


use api\classes\splitpayment\config\PaymentType;

require_once __DIR__ . '/../../../../webroot/inc/include.php';
require_once __DIR__ . '/../../../inc/testinclude.php';

class PaymentTypeTest extends baseAPITest
{

    public function setUp(): void
    {
        parent::setUp(FALSE);
    }

    public function testGetIndex(): void
    {
        $paymentType = new PaymentType(1, 2);
        $this->assertEquals(2, $paymentType->getIndex());
    }

    public function test__construct(): void
    {
        $paymentType = new PaymentType(1, 2);
        $this->assertEquals(2, $paymentType->getIndex());
        $this->assertEquals(1, $paymentType->getId());
    }

    public function testToXML(): void
    {
        $paymentType = new PaymentType(1, 2);
        $this->assertEquals('<paymentType><id>1</id><index>2</index></paymentType>', $paymentType->toXML());
    }

    public function testSetId(): void
    {
        $paymentType = new PaymentType(1, 2);
        $paymentType->setId(3);
        $this->assertEquals(3, $paymentType->getId());
    }

    public function testGetId(): void
    {
        $paymentType = new PaymentType(1, 2);
        $this->assertEquals(1, $paymentType->getId());
    }

    public function testSetIndex(): void
    {
        $paymentType = new PaymentType(1, 2);
        $paymentType->setIndex(1);
        $this->assertEquals(1, $paymentType->getIndex());
    }
}