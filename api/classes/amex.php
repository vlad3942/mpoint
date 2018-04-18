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
}