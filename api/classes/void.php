<?php
/**
 * The Void Package provide methods for Refunding or cancelling a payment depending on the status on the PSP side.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package API
 * @subpackage Refund
 * @version 1.00
 */

/* ==================== Capture Exception Classes Start ==================== */
/**
 * Exception class for all Void exceptions
 */
class VoidException extends mPointException { }
/* ==================== Capture Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for handling the Callback request from the Payment Service Provider (PSP).
 * The class contains methods that refunding or cancelling the payment on the PSP server, notifies the Client.
 * 
 *
 */
class Void extends General
{
	/**
	 * Data object with the Transaction Information
	 *
	 * @var TxnInfo
	 */
	private $_obj_TxnInfo;
	/**
	 * Model object for the PSP to which the capture operation should be executed
	 *
	 * @var Callback
	 */
	private $_obj_PSP;

	/**
	 * Default Constructor.
	 *
	 * @param	RDB $oDB				Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param	TranslateText $oTxt 	Text Translation Object for translating any text into a specific language
	 * @param 	TxnInfo $oTI 			Data object with the Transaction Information
	 * @param 	Callback $oPSP 			Model for the PSP to which the VOID operation should be executed
	 */
	public function __construct(RDB $oDB, TranslateText $oTxt, TxnInfo $oTI, Callback $oPSP)
	{
		parent::__construct($oDB, $oTxt, $oTI->getClientConfig() );

		$this->_obj_TxnInfo = $oTI;
		$this->_obj_PSP = $oPSP;
	}

	/**
	 * Returns the Data object with the Transaction Information.
	 *
	 * @return TxnInfo
	 */
	public function getTxnInfo() { return $this->_obj_TxnInfo; }
	/**
	 * Returns the Model for the PSP to which the VOID operation should be executed
	 *
	 * @return object
	 */
	public function getPSP() { return $this->_obj_PSP; }



	public function void($iAmount = -1)
	{
		if ($iAmount <= 0) { $iAmount = $this->_obj_TxnInfo->getAmount(); }

		// If PSP supports the Refund operation, perform the refund
		if ( ($this->_obj_PSP instanceof Refundable) === true) { $code = $this->_obj_PSP->void($iAmount); }
		else { throw new BadMethodCallException("Void not supported by PSP: ". get_class($this->_obj_PSP) ); }

		if ($code === 1000)
		{
			$sql = "UPDATE Log".sSCHEMA_POSTFIX.".Transaction_Tbl
					SET refund = refund + ". intval($iAmount) ."
					WHERE id = ". $this->_obj_TxnInfo->getID();
//			echo $sql ."\n";
			$res = $this->getDBConn()->query($sql);
			if (is_resource($res) === false) { trigger_error("Failed to update refunded amount for transaction: ". $this->getTxnInfo()->getID(), E_USER_WARNING); }

			$aArgs = array("amount" => $iAmount);
			$this->_obj_TxnInfo = TxnInfo::produceInfo($this->_obj_TxnInfo->getID(), $this->_obj_TxnInfo, $aArgs);
		}
		return $code;
	}
}
?>