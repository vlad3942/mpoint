<?php
/**
 * Fraud service provider(FSP) handles fraud related operation
 * @author Gaurav Pawar
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage MADA PPGS
 * @version 1.00
 */

/* ==================== EZY Exception Classes Start ==================== */
/**
 * Super class for all EZY Exceptions
 */
class EZYException extends CallbackException { }
/* ==================== EZY Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for the Fraud Service Provider: EZY
 *
 */
class EZY extends CPMFRAUD
{
    public function getFSPID() { return Constants::iEZY_PSP; }
}
