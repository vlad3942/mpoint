<?php
/**
 * User: jot
 * Date: 24-03-15
 * Time: 19:46
 */

require_once __DIR__. '/captureAPITest.php';

class MobilePayCaptureAPITest extends CaptureAPITest
{
    public function testSuccessfulCapture()
    {
        parent::testSuccessfulCapture(Constants::iMOBILEPAY_PSP);
    }

    public function testSuccessfulCaptureWithAID()
    {
        parent::testSuccessfulCaptureWithAID(Constants::iMOBILEPAY_PSP);
    }

    public function testSuccessfulCaptureInitiated()
    {
        $this->markTestIncomplete();
    }
}