<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package:
 * File Name: nets.php
 */

/* ==================== Nets Exception Classes Start ==================== */
/**
 * Super class for all Data Cash Exceptions
 */
class NetsException extends CallbackException { }
/* ==================== Nets Exception Classes End ==================== */

class Nets extends CPMACQUIRER
{

    public function getPSPID()
    {
       return Constants::iNETS_ACQUIRER;
    }
}