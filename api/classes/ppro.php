<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:ppro.php
 */

/* ==================== PPRO Exception Classes Start ==================== */
/**
 * Super class for all PPRO Exceptions
 */
class PPROException extends CallbackException { }
/* ==================== PPRO Exception Classes End ==================== */

class PPRO extends CPMPSP
{
    public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL) { throw new PPROException("Method: getPaymentData is not supported by PPRO"); }
    public function authorize(PSPConfig $obj_PSPConfig, $obj_Card) { throw new PPROException("Method: authorize is not supported by PPRO"); }
    public function cancel($iStatus = null) { throw new PPROException("Method: cancel is not supported by PPRO"); }
    public function capture($iAmount = -1) { throw new PPROException("Method: cancel is not supported by PPRO"); }

    public  function getPSPID()
    {
        Constants::iPPRO_PSP;
    }


}