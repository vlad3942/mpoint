<?php
/* ==================== UATP Exception Classes Start ==================== */
/**
 * Super class for all UATP Exceptions
 */
class UATPException extends CallbackException { }
/* ==================== UATP Exception Classes End ==================== */

class UATP extends CPMACQUIRER
{

    public function getPSPID()
    {
       return Constants::iUATP_ACQUIRER;
    }
}