<?php
/**
 * User: jot
 * Date: 24-03-15
 * Time: 19:46
 */

require_once __DIR__ . '/CaptureAPITest.php';

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

    public function testSuccessfulCaptureWithTicketNo()
    {
        parent::testSuccessfulCaptureWithTicketNo(Constants::iMOBILEPAY_PSP);
    }

    public function testCaptureWithoutTicketNo()
    {
        parent::testCaptureWithoutTicketNo(Constants::iMOBILEPAY_PSP);
    }
}