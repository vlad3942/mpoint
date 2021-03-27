<?php
require_once __DIR__. '/payAPITest.php';

class PaymentCenterPayAPITest extends PayAPITest
{
    public function testSuccessfulPay()
    {
        parent::testSuccessfulPay(Constants::iCEBUPAYMENTCENTER_APM, Constants::iCEBUPAYMENTCENTEROFFLINE, Constants::iPAYMENT_TYPE_APM, 1, 100, 208, 1041);
	}

}