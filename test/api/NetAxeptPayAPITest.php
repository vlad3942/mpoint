<?php
/**
 * User: jot
 * Date: 24-03-15
 * Time: 19:46
 */

require_once __DIR__. '/payAPITest.php';

class NetAxeptPayAPITest extends PayAPITest
{
    public function testSuccessfulPay()
    {
        $this->markTestIncomplete("Skipped as the test case tests an old integration which is no longer supported by NetAxept. To remedy this, the new NetAxept integration from branch: release/v2.02 needs to be merged in");
        parent::testSuccessfulPay(Constants::iNETAXEPT_PSP, 2); //DK-VISA
	}

}