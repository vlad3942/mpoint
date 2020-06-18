<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anna Lagad
 * Copyright: Cellpoint Digital
 * Link: http://www.cellpointdigital.com
 * Project: server
 * File Name:failed_payment_method_config.php
 */

/**
 * Data class for holding Failed Payment Method Configurations
 *
 */
class FailedPaymentMethodConfig
{
    /**
     * Unique ID for the Transaction
     *
     * @var integer
     */
    private $_iID;
    /**
     * Unique ID for the The PSP used for the transaction
     *
     * @var integer
     */
    private $_iPSPID;
    /**
     * Card-id used for the payment
     *
     * @var integer
     */
    private $_iCardID;
    /**
     * Unique ID for the payment session
     *
     * @var integer
     */
    private $_iSessionId;

    /**
     * System type ID for the card
     *
     * @var integer
     */
    private $_iPSPCategoryId;
    /**
     * Payment type ID for the psp
     *
     * @var integer
     */
    private $_iCardCategoryId;
    /**
     * Payment statu ID for the transaction
     *
     * @var integer
     */
    private $_iTransactionStateId;

    /**
     * Default constructor
     *
     * @param integer $id			The unique ID for the transaction
     * @param integer $pspId		The unique ID for the PSP
     * @param integer $cardId		The unique ID for the Payment Method (Card)
     * @param integer $sessionId	The unique ID for the Payment session
     * @param integer $systemType	The unique ID for the card category
     * @param integer $paymentType	The unique ID for the psp category
     */
	public function __construct($id, $pspId, $cardId, $sessionId, $systemType, $paymentType, $stateid)
	{
		$this->_iID = $id;
		$this->_iPSPID = $pspId;
		$this->_iCardID = $cardId;
        $this->_iSessionId = $sessionId;
        $this->_iPSPCategoryId = $systemType;
        $this->_iCardCategoryId = $paymentType;
        $this->_iTransactionStateId = $stateid;
	}

	public function getID() { return $this->_iID; }
    public function getPSPID() { return $this->_iPSPID; }
    public function getCardID() { return $this->_iCardID; }
    public function getSessionID() { return $this->_iSessionId; }
    public function getSystemType() { return $this->_iCardCategoryId; }
    public function getPaymentType() { return $this->_iPSPCategoryId; }
    public function getPaymentState() { return $this->_iTransactionStateId; }


	public function toAttributeLessXML()
	{
        $xml = '<failed_payment_method>';
        $xml .= '<session_id>'. $this->getSessionID() .'</session_id>';
        $xml .= '<transaction_id>'. $this->getID() .'</transaction_id>';
        $xml .= '<card_id>'. $this->getCardID() .'</card_id>';
        $xml .= '<psp_id>'. $this->getPSPID() .'</psp_id>';
        $xml .= '<transaction_state_id>'. $this->getPaymentState() .'</transaction_state_id>';
        $xml .= '<card_category_id>'. $this->getSystemType() .'</card_category_id>';
        $xml .= '<psp_category_id>'. $this->getPaymentType() .'</psp_category_id>';
        $xml .= '</failed_payment_method>';
        return $xml;
	}

    /**
     * Produces a failed payment method Configuration object.
     *
     * @param 	RDB $oDB 		     Reference to the Database Object that holds the active connection to the mPoint Database
     * @param 	integer $sessionId   Unique session ID for payment transaction
     * @param 	integer $clientId    Hold unique client ID
     * @return  FailedPaymentMethodConfig $aObj_Configurations  Data object with the failed payment method information
     */
    public static function produceFailedTxnInfoFromSession(RDB $obj, $sessionId, $clientId)
    {
        $aStateIDs = array( Constants::iInitializeRequested, Constants::iRefundRequested, Constants::iCancelRequested, Constants::iCaptureRequested, Constants::iAuthorizeRequested );
        $sql = "SELECT Txn.id, Txn.pspid, Txn.cardid, Txn.sessionid, PSP.system_type, C.paymenttype, p2.st AS stateid
                FROM Log".sSCHEMA_POSTFIX.".Transaction_Tbl Txn
                INNER JOIN Log".sSCHEMA_POSTFIX.".Session_Tbl S ON Txn.sessionid = S.id AND S.stateid != ".Constants::iSESSION_COMPLETED.".
                INNER JOIN System".sSCHEMA_POSTFIX.".PSP_Tbl PSP ON Txn.pspid = PSP.id
				INNER JOIN System".sSCHEMA_POSTFIX.".Card_Tbl C ON Txn.cardid = C.id
				INNER JOIN (select transactionid,max(requestedopt) as st from log.txnpassbook_tbl where clientid = $clientId group by transactionid) p2 ON (Txn.id = p2.transactionid)
				WHERE Txn.sessionid = ".$sessionId." AND p2.st IN (".implode(",",$aStateIDs).")";

        $res  = $obj->query($sql);
        $aObj_Configurations = array();
        while ($RS = $obj->fetchName($res) ){
            if(empty($RS["PSPID"])===false && empty($RS["CARDID"])===false && empty($RS["SESSIONID"])===false && empty($RS["SYSTEM_TYPE"])===false && empty($RS["PAYMENTTYPE"])===false && empty($RS["STATEID"])===false) {
                $aObj_Configurations[] =  new FailedPaymentMethodConfig($RS["ID"], $RS["PSPID"], $RS["CARDID"], $RS["SESSIONID"], $RS["SYSTEM_TYPE"], $RS["PAYMENTTYPE"], $RS["STATEID"]);
            }
        }
        return $aObj_Configurations;
    }
	
}
?>