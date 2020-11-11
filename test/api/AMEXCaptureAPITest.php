<?php
/**
 * User: jot
 * Date: 24-03-15
 * Time: 19:46
 */

require_once __DIR__. '/captureAPITest.php';

class AMEXCaptureAPITest extends CaptureAPITest
{
	public function testSuccessfulCapture()
	{
        $this->markTestIncomplete();
	}

	public function testSuccessfulCaptureInitiated()
    {
        parent::testSuccessfulCaptureInitiated(Constants::iAMEX_ACQUIRER);
    }
}