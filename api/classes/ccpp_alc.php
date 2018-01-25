<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol.
 *
 * @author Amar
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage 2C2P-ALC
 * @version 1.00
 */

/* ==================== 2C2P-ALC Exception Classes Start ==================== */
/**
 * Super class for all 2C2P-ALC Exceptions
 */
class CCPPALCException extends CallbackException { }
/* ==================== 2C2P-ALC Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: 2C2P-ALC
 *
 */
class CCPPALC extends CPMPSP
{
	public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL) { throw new CCPPALCException("Method: getPaymentData is not supported by 2C2P-ALC"); }
	public function getPSPID() { return Constants::i2C2P_ALC_PSP; }
}