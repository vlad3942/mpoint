<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol. 
 *
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
 * Model Class containing all the Business Logic for the Payment Service Provider: EZY
 *
 */
class EZY extends CPMFRAUD
{
    public function getFSPID() { return Constants::iEZY_PSP; }
}
