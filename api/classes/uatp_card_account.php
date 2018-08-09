<?php
/* ==================== UATP Exception Classes Start ==================== */
/**
 * Super class for all UATP Exceptions
 */
class UATPCarfdAccountException extends CallbackException { }
/* ==================== UATP Exception Classes End ==================== */

class UATPCardAccount extends CPMPSP
{

    public function getPSPID()
    {
       return Constants::iUATP_CARD_ACCOUNT;
    }
}