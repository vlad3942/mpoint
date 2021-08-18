<?php
/**
 * User: Ramesh Tiwari
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package:
 * File Name:PayUPayAPITest.php
 */

require_once __DIR__ . '/PayAPITest.php';

class PayUPayAPITest extends PayAPITest
{
    public function testSuccessfulPayUPay()
    {
        parent::testSuccessfulPay(Constants::iPAYU_PSP, 98, 4, 7, 405,170 );
    }

    public function testSuccessfulPayUPaymentPending()
    {
        parent::testSuccessfulPay(Constants::iPAYU_PSP, 98, 4, 7, 403,986, 1041 );
    }

    public function testSuccessfulPSEPay()
    {
        parent::testSuccessfulPay(Constants::iPAYU_PSP, 97, 4, 7, 405,170);
    }

}