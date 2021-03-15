<?php
/**
 * User: jot
 * Date: 24-03-15
 * Time: 19:46
 */

require_once __DIR__. '/authorizeAPITest.php';

class DIBSAuthorizeAPITest extends AuthorizeAPITest
{
    public function testSuccessfulAuthorize()
    {
        $this->bIgnoreErrors = true;
        parent::testSuccessfulAuthorize(Constants::iDIBS_PSP);
    }

    public function testSuccessfulAuthorizeWithCurrency()
    {
        $this->bIgnoreErrors = true;
        parent::testSuccessfulAuthorizeWithCurrency(Constants::iDIBS_PSP);
    }

	public function testSuccessfulAuthorizeIncludingAutoCapture()
	{
        $this->bIgnoreErrors = true;
		parent::testSuccessfulAuthorizeIncludingAutoCapture(Constants::iDIBS_PSP);
	}

}