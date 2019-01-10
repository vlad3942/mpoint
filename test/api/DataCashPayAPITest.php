<?php
require_once __DIR__. '/payAPITest.php';

class DataCashPayAPITest extends PayAPITest
{
    public function testSuccessfulPay()
    {
        parent::testSuccessfulPay(Constants::iDATA_CASH_PSP, Constants::iVISA_CARD);
	}

}