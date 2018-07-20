<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:amex.php
 */

/* ==================== Amex Exception Classes Start ==================== */
/**
 * Super class for all Data Cash Exceptions
 */
class AmexException extends CallbackException { }
/* ==================== Amex Exception Classes End ==================== */

class Amex extends CPMACQUIRER
{

    public function getPSPID()
    {
        return Constants::iAMEX_ACQUIRER;
    }

    public function capture($iAmount = -1)
    {
        if($this->getTxnInfo()->hasEitherState($this->getDBConn(), Constants::iPAYMENT_CAPTURE_INITIATED_STATE) === false)
        {
            $this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_CAPTURE_INITIATED_STATE, "");
        }
        return 1000;
    }

    public function refund($iAmount = -1, $iStatus = null)
    {
        if($this->getTxnInfo()->hasEitherState($this->getDBConn(), Constants::iPAYMENT_REFUND_INITIATED_STATE) === false)
        {
            $this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_REFUND_INITIATED_STATE, "");
        }
        return 1000;
    }
}