<?php
/**
 * User: jot
 * Date: 24-03-15
 * Time: 19:46
 */

require_once __DIR__. '/payAPITest.php';

class SafetyPayAPITest extends PayAPITest
{
    public function testSuccessfulSafetyPayPay()
    {
        parent::testSuccessfulPay(Constants::iSAFETYPAY_AGGREGATOR, 63, 4);
	}
	public function testSuccessfulPSEPay()
	{
	    parent::testSuccessfulPay(Constants::iSAFETYPAY_AGGREGATOR, 97, 4);
	}

}