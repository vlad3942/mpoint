<?php
require_once __DIR__. '/payAPITest.php';

class VisaCheckoutPayAPITest extends PayAPITest
{
    public function testSuccessfulPay()
    {
        parent::testSuccessfulPay(Constants::iVISA_CHECKOUT_PSP, Constants::iVISA_CARD);
	}
}