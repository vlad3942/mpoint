<?php
/**
 * User: jot
 * Date: 24-03-15
 * Time: 19:46
 */

require_once __DIR__. '/refundAPITest.php';

class NetAxeptRefundAPITest extends RefundAPITest
{
    public function testSuccessfulRefund()
    {
		/* Setup netaxept simulator, through error file mark */
		$config = new stdClass();
		$config->CardIssuer = 'Dankort';
		$config->AmountAuthorized = 5000;
		$config->AmountCaptured = 5000;
		trigger_error("NETAXEPT SIMULATOR CONFIG :: ". base64_encode(serialize($config) ) );

		parent::testSuccessfulRefund(Constants::iNETAXEPT_PSP);
    }

	public function testSuccessfulCancelTriggeredByRefund()
	{
		$this->assertTrue(true);
		//TODO: Implement this test for Netaxept
		//parent::testSuccessfulCancelTriggeredByRefund(Constants::iDIBS_PSP);
	}
}