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
        if($this->getTxnInfo()->hasEitherState($this->getDBConn(), Constants::iPAYMENT_CAPTURE_INITIATED_STATE) === false)
        {
            $this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_CAPTURE_INITIATED_STATE, "Capture Initiated");
        }
        return 1000;
    }

    public function refund($iAmount = -1, $iStatus = null)
    {
        if (count($this->getMessageData($this->getTxnInfo()->getID(), Constants::iPAYMENT_CAPTURED_STATE, false) ) == 0)
        {
            return parent::cancel();
        }
        else if($this->getTxnInfo()->hasEitherState($this->getDBConn(), Constants::iPAYMENT_REFUND_INITIATED_STATE) === false)
        {
            $this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_REFUND_INITIATED_STATE, "Refund Initiated");
        }
        return 1000;
    }
}