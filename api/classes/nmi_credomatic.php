<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol.
 *
 * @author Amar
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage nmi_credomatic
 * @version 1.00
 */

/* ==================== nmi_credomatic Exception Classes Start ==================== */
/**
 * Super class for all nmi_credomatic Exceptions
 */
class NMI_CREDOMATICException extends CallbackException { }
/* ==================== nmi_credomatic Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: nmi_credomatic
 *
 */
class NMI_CREDOMATICE extends CPMPSP
{
	public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL) { throw new NMI_CREDOMATICException("Method: getPaymentData is not supported by nmi_credomatic"); }
	public function getPSPID() { return Constants::iNMI_CREDOMATIC; }
}