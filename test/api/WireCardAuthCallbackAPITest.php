<?php

require_once __DIR__. '/callbackAPITest.php';

class WireCardAuthCallbackAPITest extends CallbackAPITest
{
    public function testSuccessfulCapture()
    {
        parent::successfulCallbackAccepted(Constants::iWIRE_CARD_PSP, Constants::iPAYMENT_CAPTURED_STATE);
    }

   public function testSuccessfulCancel()
    {
        parent::successfulCallbackAccepted(Constants::iWIRE_CARD_PSP, Constants::iPAYMENT_CANCELLED_STATE);
    }

    public function testSuccessfulRefund()
    {
        parent::successfulCallbackAccepted(Constants::iWIRE_CARD_PSP, Constants::iPAYMENT_REFUNDED_STATE);
    }

    public function testSuccessfulAutoCapture()
    {
        parent::successfulAutoCapture(Constants::iWIRE_CARD_PSP, Constants::iPAYMENT_ACCEPTED_STATE);
    }
    public function testSuccessfulNoAutoCapture()
    {
        parent::successfulNoAutoCapture(Constants::iWIRE_CARD_PSP, Constants::iPAYMENT_ACCEPTED_STATE);
    }

}