<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol. 
 *
 * @author Abhishek Sawant
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage GlobalCollect
 * @version 1.00
 */

/* ==================== GlobalCollect Exception Classes Start ==================== */
/**
 * Super class for all Data Cash Exceptions
 */
class GlobalCollectException extends CallbackException { }
/* ==================== GlobalCollect Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for the Payment Service Provider: GlobalCollect
 *
 */
class GlobalCollect extends CPMPSP
{
	public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode=Constants::sPAYMENT_DATA_FULL) { throw new GlobalCollectException("Method: getPaymentData is not supported by GlobalCollect"); }

	public function getPSPID() { return Constants::iGLOBAL_COLLECT_PSP; }
	
	/***
	 * This function will override auth path defined in
	 * global.php to auth-complete depending on flag
	 * @param boolean $flag
	 */
	public function setAuthPath($flag) { if($flag === true) { $this->aCONN_INFO["paths"]["auth"] = $this->aCONN_INFO["paths"]["auth-complete"]; } }
	
}
