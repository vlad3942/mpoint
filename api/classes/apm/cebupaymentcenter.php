<?php
/**
 * This is the Cebu Payment Center PSP class
 *
 * @author Gaorav Vishnoi
 * @copyright Cellpoint Digital
 * @link http://www.cellpointdigital.com
 * @package apm
 * @version 1.00
 */

/* ==================== Cebu Payment Center Exception Classes Start ==================== */
/**
 * Super class for all Cebu Payment Center Exceptions
 */
class CebuPaymentCenterException extends CallbackException { }
/* ==================== Cebu Payment Center Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: Cebu Payment Center
 *
 */
class CebuPaymentCenter extends CPMPSP
{
    public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL) { throw new CebuPaymentCenterException("Method: getPaymentData is not supported by Cebu Payment Center"); }
	public function getPSPID() { return Constants::iCEBUPAYMENTCENTER_APM; }
}
