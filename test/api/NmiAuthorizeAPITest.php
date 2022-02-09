<?php
/**
 * User: Amar
 * Date: 08-02-2022
 */

require_once __DIR__ . '/AuthorizeAPITest.php';

class NmiAuthorizeAPITest extends AuthorizeAPITest
{
    public function testSuccessfulAuthorize()
    {
        parent::testSuccessfulAuthorize(Constants::iNMI_CREDOMATIC);
    }

    public function testSuccessfulAuthorizeWithCurrency()
    {
        parent::testSuccessfulAuthorizeWithCurrency(Constants::iNMI_CREDOMATIC);
    }

	public function testSuccessfulAuthorizeIncludingAutoCapture()
	{
		parent::testSuccessfulAuthorizeIncludingAutoCapture(Constants::iNMI_CREDOMATIC);
	}

}