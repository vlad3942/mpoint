<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name: mConsoleTest
 */


require_once __DIR__ . '/../../webroot/inc/include.php';
require_once __DIR__ . '/../inc/testinclude.php';
require_once(sAPI_CLASS_PATH ."simpledom.php");
require_once __DIR__ . '/../../api/classes/general.php';
require_once __DIR__ . '/../../api/classes/admin.php';
require_once __DIR__ . '/../../api/classes/core/State.php';
require_once __DIR__ . '/../../api/classes/mConsole.php';


class mConsoleTest extends baseAPITest
{
    private $_OBJ_DB;
    protected $_aHTTP_CONN_INFO;
    protected $_objMConsole;

    public function setUp() : void
    {
        parent::setUp(TRUE);
        global $aHTTP_CONN_INFO;
        global  $_OBJ_TXT;
        //$this->bIgnoreErrors = true;
        $this->_aHTTP_CONN_INFO = $aHTTP_CONN_INFO;
        $this->_OBJ_DB = RDB::produceDatabase($this->mPointDBInfo);
        $this->_objMConsole = new mConsole($this->_OBJ_DB, $_OBJ_TXT);
    }

    public function testSSOCheck(): void
    {
        global $aHTTP_CONN_INFO;
        $code = $this->_objMConsole->SSOCheck($aHTTP_CONN_INFO["mconsole"], 10099);
        $this->assertEquals(mConsole::iAUTHORIZATION_SUCCESSFUL, $code);

    }

    public function testGetSSOValidationError(): void
    {
       $this->assertEquals('HTTP/1.1 504 Gateway Timeout' ,$this->_objMConsole->getSSOValidationError(2)['http_message']);
       $this->assertEquals('<status code="2">Single Sign-On Service is unreachable</status>' ,$this->_objMConsole->getSSOValidationError(2)['response']);

       $this->assertEquals('HTTP/1.1 502 Bad Gateway' ,$this->_objMConsole->getSSOValidationError(3)['http_message']);
       $this->assertEquals('<status code="3">Single Sign-On Service is unavailable</status>' ,$this->_objMConsole->getSSOValidationError(3)['response']);

       $this->assertEquals('HTTP/1.1 401 Unauthorized' ,$this->_objMConsole->getSSOValidationError(4)['http_message']);
       $this->assertEquals('<status code="4">Unauthorized User Access</status>' ,$this->_objMConsole->getSSOValidationError(4)['response']);

       $this->assertEquals('HTTP/1.1 403 Forbidden' ,$this->_objMConsole->getSSOValidationError(5)['http_message']);
       $this->assertEquals('<status code="5">Insufficient User Permissions</status>' ,$this->_objMConsole->getSSOValidationError(5)['response']);

       $this->assertEquals('HTTP/1.1 402 Payment Required' ,$this->_objMConsole->getSSOValidationError(6)['http_message']);
       $this->assertEquals('<status code="6">Insufficient Client License</status>' ,$this->_objMConsole->getSSOValidationError(6)['response']);

       $this->assertEquals('HTTP/1.1 500 Internal Server Error' ,$this->_objMConsole->getSSOValidationError(7)['http_message']);
       $this->assertEquals('<status code="7">Internal Error</status>' ,$this->_objMConsole->getSSOValidationError(7)['response']);
    }

    public function testGetStates(): void
    {
        $aState = $this->_objMConsole->getStates();
        $this->assertIsArray($aState);
        $this->assertArrayHasKey(2000, $aState);
        $this->assertArrayHasKey(2001, $aState);
        $this->assertArrayHasKey(2002, $aState);
        $this->assertArrayHasKey(2003, $aState);
        $this->assertArrayHasKey(2010, $aState);
        $this->assertArrayHasKey(2011, $aState);
        $this->assertArrayHasKey(2019, $aState);
        $this->assertArrayHasKey(4010, $aState);
        $this->assertArrayHasKey(4020, $aState);
        $this->assertArrayHasKey(4030, $aState);
        $this->assertArrayHasKey(4031, $aState);

        $this->assertIsArray($aState[2010]->getSubStates());
        $this->assertEquals('<state><id>2010</id><name>Payment rejected by PSP</name><sub_states><state><id>2010101</id><name>Failed during Capture</name></state><state><id>2010102</id><name>Card Number is invalid.</name></state><state><id>2010103</id><name>Installment field value is invalid</name></state><state><id>2010104</id><name>Invalid Order Number value</name></state><state><id>2010105</id><name>Missing Mandatory Fields / Data not present / invalid data field (general error code when any field is invalid)</name></state><state><id>2010106</id><name>Invalid MerchantID</name></state><state><id>2010107</id><name>Invalid TransactionID</name></state><state><id>2010108</id><name>Invalid Transaction date</name></state><state><id>2010109</id><name>Invalid CVC</name></state><state><id>2010110</id><name>Invalid Payment Type</name></state><state><id>2010112</id><name>Invalid 3DS Secure values</name></state><state><id>2010113</id><name>Invalid Card type</name></state><state><id>2010114</id><name>Invalid Request version</name></state><state><id>2010115</id><name>Return URL is not set.</name></state><state><id>2010116</id><name>Invalid currency code.</name></state><state><id>2010117</id><name>Invalid Promotion.</name></state><state><id>2010201</id><name>Failed during Cancel</name></state><state><id>2010202</id><name>Invalid PIN OR OTP</name></state><state><id>2010203</id><name>Insufficient funds / over credit limit</name></state><state><id>2010204</id><name>Expired Card</name></state><state><id>2010205</id><name>Unable to authorize</name></state><state><id>2010206</id><name>Exceeds withdrawal count limit OR Authentication requested</name></state><state><id>2010207</id><name>Do Not Honor</name></state><state><id>2010208</id><name>Transaction not permitted to user</name></state><state><id>2010209</id><name>Transaction Aborted by user / Card Holder Abandoned 3DS/Wallet</name></state><state><id>2010210</id><name>User Inactive or Session Expired</name></state><state><id>2010211</id><name>Only a partial amount was approved</name></state><state><id>2010301</id><name>Internal error / general system error</name></state><state><id>2010302</id><name>Parse error / invalid Request</name></state><state><id>2010303</id><name>Service not available.</name></state><state><id>2010304</id><name>Time out</name></state><state><id>2010305</id><name>Payment is cancelled / Payment reversed</name></state><state><id>2010306</id><name>Waiting for upstream response</name></state><state><id>2010307</id><name>No Routing Available</name></state><state><id>2010308</id><name>System DB Error</name></state><state><id>2010309</id><name>Invalid Operation / Operation Rejected</name></state><state><id>2010310</id><name>Transaction already in progress /  Duplicate Transaction / Duplicate Order Number</name></state><state><id>2010311</id><name>Endpoint not supported</name></state><state><id>2010312</id><name>Transaction not permitted to terminal</name></state><state><id>2010313</id><name>Invalid merchant account / configuration / API permission missing</name></state><state><id>2010314</id><name>Transaction rejected by Issuer / Authorization failed /Transaction Failed</name></state><state><id>2010315</id><name>EMI not available</name></state><state><id>2010316</id><name>Void not supported</name></state><state><id>2010317</id><name>Already Captured</name></state><state><id>2010318</id><name>Retry limit exceeded</name></state><state><id>2010319</id><name>Invalid Capture attempted</name></state><state><id>2010320</id><name>Transaction Not Posted</name></state><state><id>2010321</id><name>Recurring Payment Not Supported</name></state><state><id>2010322</id><name>Stored card option is disabled.</name></state><state><id>2010323</id><name>Request Authentication Failed.</name></state><state><id>2010324</id><name>Unable to decrypt request.</name></state><state><id>2010325</id><name>Transaction ID / EP Generation Failed</name></state><state><id>2010326</id><name>Installment Payment is disabled.</name></state><state><id>2010327</id><name>Ticket issue failed</name></state><state><id>2010328</id><name>China Union Pay sign failed</name></state><state><id>2010329</id><name>Card type is not allowed.</name></state><state><id>2010330</id><name>Issuing bank unavailable. </name></state><state><id>2010331</id><name>Transaction exceeds the approved limit </name></state><state><id>2010332</id><name>Cannot void as capture or credit is submitted </name></state><state><id>2010333</id><name>Cannot Refund / You requested a credit for a capture that was previously voided.</name></state><state><id>2010334</id><name>Credit amount exceeds maximum allowed for your Merchant account.</name></state><state><id>2010335</id><name>No Response</name></state><state><id>2010401</id><name>FRAUD Suspicion / Rejected</name></state><state><id>2010402</id><name>Address verification failed</name></state><state><id>2010403</id><name>Card Acceptor should contact accquirer</name></state><state><id>2010404</id><name>Security Voilation</name></state><state><id>2010405</id><name>Card is Blocked due to fraud</name></state><state><id>2010406</id><name>3D Secure authentication failed</name></state><state><id>2010407</id><name>Fraud Stolen Card</name></state><state><id>2010408</id><name>Compliance ERROR</name></state><state><id>2010409</id><name>Transaction Previously declined</name></state><state><id>2010410</id><name>E-commerce declined</name></state><state><id>2010411</id><name>Card restricted</name></state><state><id>2010412</id><name>Card Function Not Supported</name></state><state><id>2010413</id><name>Physical Card Error</name></state><state><id>2010414</id><name>BIN check failed</name></state><state><id>2010415</id><name>Validation Check Failed.</name></state><state><id>2010416</id><name>CVN did not match </name></state><state><id>2010417</id><name>The customer matched an entry on the processor’s negative file.</name></state><state><id>2010418</id><name>Strong customer authentication (SCA) is required for this transaction.</name></state><state><id>2010419</id><name>authorization request was approved by the issuing bank but declined by Gateway/processor</name></state></sub_states></state>',$aState[2010]->asXML());
    }

    public function testSSOForDeleteRouteConfig(): void
    {
        $permissionCode = "mpoint.delete-route-configuration.x";
        global $aHTTP_CONN_INFO;
        $code = $this->_objMConsole->SSOCheck($aHTTP_CONN_INFO["mconsole"], 10099, $permissionCode);
        $this->assertEquals(mConsole::iAUTHORIZATION_SUCCESSFUL, $code);

    }


    public function tearDown() : void
    {
        $this->_OBJ_DB->disConnect();
        parent::tearDown();
    }
}