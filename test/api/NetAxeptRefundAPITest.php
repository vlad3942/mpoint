<?php
/**
 * User: jot
 * Date: 24-03-15
 * Time: 19:46
 */

require_once __DIR__. '/refundAPITest.php';

class NetAxeptRefundAPITest extends RefundAPITest
{
    public function testSuccessfulRefund()
    {
        parent::testSuccessfulRefund(Constants::iNETAXEPT_PSP);
    }

}