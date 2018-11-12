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
}