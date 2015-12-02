<?php
/**
 * User: jot
 * Date: 24-03-15
 * Time: 19:46
 */

require_once __DIR__. '/authorizeAPITest.php';

class NetaxeptAuthorizeAPITest extends AuthorizeAPITest
{
    public function testSuccessfulAuthorize()
    {
		/* Setup netaxept simulator, through error file mark */
		$config = new stdClass();
		$config->AmountCaptured = 0;
		trigger_error("NETAXEPT SIMULATOR CONFIG :: ". base64_encode(serialize($config) ) );

        parent::testSuccessfulAuthorize(Constants::iNETAXEPT_PSP);
    }

	public function testSuccessfulAuthorizeIncludingAutoCapture()
	{
		//TODO: Implement auto-capture test for Netaxept. This implies letting the Netaxept authorize simulator trigger a callback to mPoint
	}

}