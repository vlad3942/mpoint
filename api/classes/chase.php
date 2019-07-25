<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Badave
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:chase.php
 */

/* ==================== Chase Exception Classes Start ==================== */
/**
 * Super class for all Data Cash Exceptions
 */
class ChaseException extends CallbackException { }
/* ==================== Chase Exception Classes End ==================== */

class Chase extends CPMACQUIRER
{

    public function getPSPID()
    {
        return Constants::iCHASE_ACQUIRER;
    }

    public function capture($iAmount = -1)
    {
        $this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_CAPTURE_INITIATED_STATE, $iAmount);
        return 1000;
    }

    public function refund($iAmount = -1, $iStatus = null)
    {
        $this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_REFUND_INITIATED_STATE, $iAmount);
        return 1000;
    }
}