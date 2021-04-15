<?php
require_once __DIR__. '/payAPITest.php';

class AlipayPayAPITest extends PayAPITest
{
    public function testSuccessfulPay()
    {
        parent::testSuccessfulPay(Constants::iALIPAY_PSP, Constants::iALIPAY_WALLET, Constants::iPROCESSOR_TYPE_APM,Constants::iPROCESSOR_TYPE_APM, 200,840);
	}

    public function testSuccessfulPayWithAID()
    {
        parent::testSuccessfulPayWithAID(Constants::iALIPAY_PSP, Constants::iALIPAY_WALLET, Constants::iPROCESSOR_TYPE_APM,Constants::iPROCESSOR_TYPE_APM, 200,840);
    }
}