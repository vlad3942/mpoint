<?php
/**
 * User: jot
 * Date: 24-03-15
 * Time: 19:46
 */

require_once __DIR__ . '/AuthorizeAPITest.php';

class DIBSAuthorizeAPITest extends AuthorizeAPITest
{
    public function testSuccessfulAuthorize()
    {
        parent::testSuccessfulAuthorize(Constants::iDIBS_PSP);
    }

    public function testSuccessfulAuthorizeWithCurrency()
    {
        parent::testSuccessfulAuthorizeWithCurrency(Constants::iDIBS_PSP);
    }

	public function testSuccessfulAuthorizeIncludingAutoCapture()
	{
		parent::testSuccessfulAuthorizeIncludingAutoCapture(Constants::iDIBS_PSP);
	}

}