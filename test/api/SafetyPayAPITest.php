<?php
/**
 * User: Nitin Gaikwad
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package:
 * File Name:SafetyPayAPITest.php
 */

require_once __DIR__ . '/PayAPITest.php';

class SafetyPayAPITest extends PayAPITest
{
    public function testSuccessfulSafetyPayPay()
    {
        parent::testSuccessfulPay(Constants::iSAFETYPAY_AGGREGATOR, 63, 4, 7, 405,170 );
	}

	public function testSuccessfulSafetyPayPaymentPending()
    {
        parent::testSuccessfulPay(Constants::iSAFETYPAY_AGGREGATOR, 63, 4, 7, 403,986, 1041 );
	}

	public function testSuccessfulPSEPay()
	{
	    parent::testSuccessfulPay(Constants::iSAFETYPAY_AGGREGATOR, 97, 4, 7, 405,170);
	}

}