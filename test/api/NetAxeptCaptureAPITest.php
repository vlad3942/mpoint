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
        $sDIBSCallbackURL = $this->_aMPOINT_CONN_INFO["protocol"] ."://". $this->_aMPOINT_CONN_INFO["host"]. "/callback/netaxept.php";

        parent::testSuccessfulCapture($sDIBSCallbackURL);
    }

}