<?php
require_once __DIR__ . '/PayAPITest.php';

class PaymentCenterPayAPITest extends PayAPITest
{
    public function testSuccessfulPay()
    {
        parent::testSuccessfulPay(Constants::iCEBUPAYMENTCENTER_APM, Constants::iCEBUPAYMENTCENTEROFFLINE, Constants::iPAYMENT_TYPE_APM, 1, 200,840, 1041);
	}

}