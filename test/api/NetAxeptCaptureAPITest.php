<?php
/**
 * User: jot
 * Date: 24-03-15
 * Time: 19:46
 */

require_once __DIR__ . '/CaptureAPITest.php';

class NetAxeptCaptureAPITest extends CaptureAPITest
{
    public function testSuccessfulCapture()
    {
        $this->markTestIncomplete("Skipped as the test case tests an old integration which is no longer supported by NetAxept. To remedy this, the new NetAxept integration from branch: release/v2.02 needs to be merged in");
		/* Setup netaxept simulator, through error file mark */
		$config = new stdClass();
		$config->AmountCaptured = 5147;
		trigger_error("NETAXEPT SIMULATOR CONFIG :: ". base64_encode(serialize($config) ) );

        parent::testSuccessfulCapture(Constants::iNETAXEPT_PSP);

		/* Test that Netaxept card fee is included in capture amount, and handled correctly */
		$res =  $this->queryDB("SELECT fee FROM Log.Transaction_Tbl WHERE id = 1001001");
		$this->assertTrue(is_resource($res) );
		$row = pg_fetch_assoc($res);

		$this->assertEquals(147, intval($row["fee"]) );

		$bContainsCorrectFee = false;
		foreach ($this->getErrorLogContent() as $line)
		{
			if (strpos($line, 'Fee received from notify client: 147') !== false)
			{
				$bContainsCorrectFee = true;
				break;
			}
		}
		$this->assertTrue($bContainsCorrectFee);
	}

	public function testSuccessfulCaptureInitiated()
	{
        $this->markTestIncomplete("Skipped as the test case tests an old integration which is no longer supported by NetAxept. To remedy this, the new NetAxept integration from branch: release/v2.02 needs to be merged in");
	}
}