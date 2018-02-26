<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol. 
 *
 * @author Urmila Sridharan
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage AliPay Chinese
 * @version 1.00
 */

/* ==================== AliPay Exception Classes Start ==================== */
/**
 * Super class for all AliPayChinese Exceptions
 */
class AliPayChineseException extends CallbackException { }
/* ==================== AliPayChinese Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: AliPayChinese
 *
 */
class AliPayChinese extends CPMPSP
{
	public function capture($iAmount=-1) { throw new AliPayException("Method: capture is not supported by AliPay Chinese"); }
	public function void($iAmount=-1) { throw new AliPayException("Method: void is not supported by AliPay Chinese"); }
	public function cancel() { throw new AliPayException("Method: cancel is not supported by AliPay Chinese"); }
	public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL) { throw new AliPayException("Method: getPaymentData is not supported by AliPay Chinese"); }
	public function authorize(PSPConfig $obj_PSPConfig, $ticket) { throw new AliPayException("Method: authTicket is not supported by AliPay Chinese"); }
	public function getPSPID() { return Constants::iALIPAY_CHINESE_PSP; }
}
