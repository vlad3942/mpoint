<?php
/**
 * User: jot
 * Date: 24-03-15
 * Time: 19:46
 */

require_once __DIR__. '/refundAPITest.php';

class DIBSRefundAPITest extends RefundAPITest
{
    public function testSuccessfulRefund()
    {
        parent::testSuccessfulRefund(Constants::iDIBS_PSP);
    }

	public function testSuccessfulCancelTriggeredByRefund()
	{
		$this->assertTrue(true);
		//TODO: Implement this test for DIBS
		//parent::testSuccessfulCancelTriggeredByRefund(Constants::iDIBS_PSP);
	}

}
