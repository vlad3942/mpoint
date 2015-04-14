<?php
/**
 * User: jot
 * Date: 24-03-15
 * Time: 19:46
 */

require_once __DIR__. '/captureAPITest.php';

class DIBSCaptureAPITest extends CaptureAPITest
{
    public function testSuccessfulCapture()
    {
        parent::testSuccessfulCapture(Constants::iDIBS_PSP);
    }

}