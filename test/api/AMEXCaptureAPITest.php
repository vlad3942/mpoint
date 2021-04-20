<?php
/**
 * User: jot
 * Date: 24-03-15
 * Time: 19:46
 */

require_once __DIR__ . '/CaptureAPITest.php';

class AMEXCaptureAPITest extends CaptureAPITest
{
	public function testSuccessfulCaptureInitiated()
    {
        parent::testSuccessfulCaptureInitiated(Constants::iAMEX_ACQUIRER);
    }
}