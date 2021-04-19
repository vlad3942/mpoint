<?php
/**
 * User: jot
 * Date: 24-03-15
 * Time: 19:46
 */

require_once __DIR__. '/refundAPITest.php';

class MobilePayRefundAPITest extends RefundAPITest
{
    public function testSuccessfulRefund()
    {
        parent::testSuccessfulRefund(Constants::iMOBILEPAY_PSP);
    }

    public function testSuccessfulRefundWithAID()
    {
        parent::testSuccessfulRefundWithAID(Constants::iMOBILEPAY_PSP);
    }

	public function testSuccessfulCancelTriggeredByRefund()
	{
		parent::testSuccessfulCancelTriggeredByRefund(Constants::iMOBILEPAY_PSP);
	}


}