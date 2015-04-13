<?php
/**
 * User: jot
 * Date: 24-03-15
 * Time: 19:46
 */

require_once __DIR__. '/captureAPITest.php';

class NetAxeptCaptureAPITest extends CaptureAPITest
{
    public function testSuccessfulCapture()
    {
        $sCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/_test/simulators/mticket/callback.php";

        //parent::testSuccessfulCapture($sCallbackURL, Constants::iNETAXEPT_PSP);
    }

}