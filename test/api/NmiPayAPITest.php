<?php
require_once __DIR__ . '/PayAPITest.php';

class StripePayAPITest extends PayAPITest
{
    public function testSuccessfulPay()
    {
        parent::testSuccessfulPay(Constants::iNMI_CREDOMATIC, Constants::iVISA_CARD);
	}

}