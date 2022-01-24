<?php
require_once __DIR__ . '/PayAPITest.php';

class StripePayAPITest extends PayAPITest
{
    public function testSuccessfulPay()
    {
        parent::testSuccessfulPay(Constants::iSTRIPE_PSP, Constants::iVISA_CARD, 7);
	}

}