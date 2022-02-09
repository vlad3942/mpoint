<?php
require_once __DIR__ . '/PayAPITest.php';

class NmiPayAPITest extends PayAPITest
{
    public function testSuccessfulPay()
    {
        parent::testSuccessfulPay(Constants::iNMI_CREDOMATIC, Constants::iVISA_CARD,1,1,200,840);
	}

}