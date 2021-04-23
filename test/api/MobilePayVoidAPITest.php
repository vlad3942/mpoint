<?php
/**
 * User: Abhinav Shaha
 * Date: 08-05-20
 * Time: 10:00
 */

require_once __DIR__ . '/VoidAPITest.php';

class MobilePayVoidAPITest extends VoidAPITest
{
	public function testSuccessfulRefund()
	{
		parent::testSuccessfulRefund(Constants::iMOBILEPAY_PSP);
	}
	public function testSuccessfulCancelTriggeredByVoid()
	{
		parent::testSuccessfulCancelTriggeredByVoid(Constants::iMOBILEPAY_PSP);
	}

    public function testSuccessfulCancelTriggeredByVoidAID()
    {
        parent::testSuccessfulCancelTriggeredByVoidAID(Constants::iMOBILEPAY_PSP);
    }
}