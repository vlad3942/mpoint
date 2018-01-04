<?php
/**
 * User: jot
 * Date: 24-03-15
 * Time: 19:46
 */

require_once __DIR__. '/payAPITest.php';

class MobilePayAPITest extends PayAPITest
{
    public function testSuccessfulPay()
    {
        $sReplyBody = parent::testSuccessfulPay(Constants::iMOBILEPAY_PSP, Constants::iMOBILEPAY, $typeId=3);
		$this->assertContains('<url method="app"/>', $sReplyBody);
	}

}