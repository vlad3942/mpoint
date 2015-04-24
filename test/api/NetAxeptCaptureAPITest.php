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
        parent::testSuccessfulCapture(Constants::iNETAXEPT_PSP);

		/* Test that Netaxept card fee is included in capture amount, and handled correctly */
		$res =  $this->queryDB("SELECT fee FROM Log.Transaction_Tbl WHERE id = 1001001");
		$this->assertTrue(is_resource($res) );
		$row = pg_fetch_assoc($res);

		$this->assertEquals(147, intval($row["fee"]) );
    }

}