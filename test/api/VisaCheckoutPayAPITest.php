<?php
require_once __DIR__ . '/PayAPITest.php';

class VisaCheckoutPayAPITest extends PayAPITest
{
    public function testSuccessfulPay()
    {
        parent::testSuccessfulPay(Constants::iVISA_CHECKOUT_PSP, Constants::iVISA_CARD, Constants::iPROCESSOR_TYPE_WALLET,  Constants::iPROCESSOR_TYPE_WALLET, 200, 840);
        $res =  $this->queryDB('SELECT walletid from Log.Transaction_Tbl WHERE id = 1001001');
		$this->assertTrue(is_resource($res) );

		$walletid = 0;
		while ($row = pg_fetch_assoc($res) )
		{
			$walletid = (int)$row["walletid"];
		}
		$this->assertEquals(Constants::iVISA_CHECKOUT_PSP, $walletid);
	}
}