<?php
/**
 * User: Priya Alamwar
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package:
 * File Name:MobileMoneyPayAPITest.php
 */

require_once __DIR__ . '/PayAPITest.php';

class MobileMoneyPayAPITest extends PayAPITest
{
    public function testSuccessfulMobileMoneyPay()
    {
        parent::testSuccessfulPay(Constants::iCellulant_PSP, Constants::iCELLULANT, 7, 7, 325,404 );
    }

}