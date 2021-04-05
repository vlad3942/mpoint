<?php
require_once __DIR__ . '/PayAPITest.php';

class CIAMpayPayAPITest extends PayAPITest
{
    public function testSuccessfulPay()
    {
        parent::testSuccessfulPay(Constants::iALIPAY_PSP, Constants::iALIPAY_WALLET, Constants::iPROCESSOR_TYPE_APM,Constants::iPROCESSOR_TYPE_APM, 200,840);
	}

	public function testCIAMSSOPreferenceNotEnabled()
	{
		parent::testSuccessfulPayWithSSO(Constants::iALIPAY_PSP, Constants::iALIPAY_WALLET, Constants::iPROCESSOR_TYPE_APM,Constants::iPROCESSOR_TYPE_APM, 200,840);
	}

	public function testCIAMSSOPreferenceLoose()
	{
		parent::testSuccessfulPayWithSSO(Constants::iALIPAY_PSP, Constants::iALIPAY_WALLET, Constants::iPROCESSOR_TYPE_APM,Constants::iPROCESSOR_TYPE_APM, 200,840,'LOOSE');
	}

	public function testSuccessfulPayWithSSOStrict_AuthToken()
	{
		parent::testSuccessfulPayWithSSOStrict_AuthToken(Constants::iALIPAY_PSP, Constants::iALIPAY_WALLET, Constants::iPROCESSOR_TYPE_APM,Constants::iPROCESSOR_TYPE_APM, 200,840,'STRICT');
	}

	public function testSuccessfulPayWithSSOStrict_Authurl()
	{
		parent::testSuccessfulPayWithSSOStrict_Authurl(Constants::iALIPAY_PSP, Constants::iALIPAY_WALLET, Constants::iPROCESSOR_TYPE_APM,Constants::iPROCESSOR_TYPE_APM, 200,840,'STRICT');
	}
	
	public function testSuccessfulPayWithSSOStrict_CustomerInfo()
	{
		parent::testSuccessfulPayWithSSOStrict_CustomerInfo(Constants::iALIPAY_PSP, Constants::iALIPAY_WALLET, Constants::iPROCESSOR_TYPE_APM,Constants::iPROCESSOR_TYPE_APM, 200,840,'STRICT');
	}

	// public function testSuccessfulPayWithSSOStrict_ValidCIAM()
	// {
	// 	parent::testSuccessfulPayWithSSOStrict_ValidCIAM(Constants::iALIPAY_PSP, Constants::iALIPAY_WALLET, Constants::iPROCESSOR_TYPE_APM,Constants::iPROCESSOR_TYPE_APM, 200,840,'STRICT');
	// }

	// public function testSuccessfulPayWithSSOStrict_InvalidCIAM()
	// {
	// 	parent::testSuccessfulPayWithSSOStrict_InvalidCIAM(Constants::iALIPAY_PSP, Constants::iALIPAY_WALLET, Constants::iPROCESSOR_TYPE_APM,Constants::iPROCESSOR_TYPE_APM, 200,840,'STRICT');
	// }

}