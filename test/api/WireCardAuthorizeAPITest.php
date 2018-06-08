<?php
/**
 * User: Rohit M
 * Date: 04-06-16
 * Time: 19:46
 */

require_once __DIR__. '/authorizeAPITest.php';

class WireCardAuthorizeAPITest extends AuthorizeAPITest
{
    public function testSuccessfulAuthorize()
    {
        parent::testSuccessfulAuthorize(Constants::iWIRE_CARD_PSP);
    }

    public function testSuccessfulAuthorizeWithCurrency()
    {
        parent::testSuccessfulAuthorizeWithCurrency(Constants::iWIRE_CARD_PSP);
    }

	public function testSuccessfulAuthorizeIncludingAutoCapture()
	{
		parent::testSuccessfulAuthorizeIncludingAutoCapture(Constants::iWIRE_CARD_PSP);
	}

}