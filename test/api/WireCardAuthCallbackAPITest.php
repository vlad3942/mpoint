<?php

require_once __DIR__ . '/CallbackAPITest.php';

class WireCardAuthCallbackAPITest extends CallbackAPITest
{
    public function testSuccessfulNoAutoCapture()
    {
        $this->successfulNoAutoCapture(Constants::iWIRE_CARD_PSP, Constants::iPAYMENT_ACCEPTED_STATE);
    }

    public function testSuccessfulOneStepAuthorizationAutoCapture()
    {
		$this->successfulOneStepAuthorizationAutoCapture(Constants::iWIRE_CARD_PSP, Constants::iPAYMENT_ACCEPTED_STATE);
    }

    public function testSuccessfulCapture()
    {
        $this->successfulCallbackAccepted(Constants::iWIRE_CARD_PSP, Constants::iPAYMENT_CAPTURED_STATE);
    }

    public function testCallbackAttempt()
    {
        parent::callbackAttemptTest(Constants::iWIRE_CARD_PSP, Constants::iPAYMENT_ACCEPTED_STATE);
    }

    public function testSuccessfulCancel()
    {
        $this->successfulCallbackAccepted(Constants::iWIRE_CARD_PSP, Constants::iPAYMENT_CANCELLED_STATE);
    }

    public function testSuccessfulRefund()
    {
        $this->successfulCallbackAccepted(Constants::iWIRE_CARD_PSP, Constants::iPAYMENT_REFUNDED_STATE);
    }

    public function testSuccessfulAutoCapture()
    {
        $this->successfulAutoCapture(Constants::iWIRE_CARD_PSP, Constants::iPAYMENT_ACCEPTED_STATE);
    }
    public function testSuccessfulAutoCaptureUnionpay()
    {
        $this->successfulAutoCaptureUnionpay(Constants::iWIRE_CARD_PSP, Constants::iPAYMENT_ACCEPTED_STATE);
    }
    public function testSuccessfulPartialCapture()
    {
        $this->successfulPartialCapture(Constants::iWIRE_CARD_PSP, Constants::iPAYMENT_CAPTURED_STATE);
    }
    public function testSuccessfulPartialRefund()
    {
        $this->successfulPartialRefund(Constants::iWIRE_CARD_PSP, Constants::iPAYMENT_REFUNDED_STATE);
    }
    public function testSuccessfulPartialCancel()
    {
        $this->successfulPartialCancel(Constants::iWIRE_CARD_PSP, Constants::iPAYMENT_CANCELLED_STATE);
    }
}