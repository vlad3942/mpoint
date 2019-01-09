<?php
require_once __DIR__. '/payAPITest.php';

class AlipayPayAPITest extends PayAPITest
{
    public function testSuccessfulPay()
    {
        parent::testSuccessfulPay(Constants::iALIPAY_PSP, Constants::iVISA_CARD);
	}
}