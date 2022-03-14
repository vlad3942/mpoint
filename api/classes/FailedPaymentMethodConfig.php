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
     * Card-id used for the payment
     *
     * @var integer
     */
    private $_iCardID;
    /**
     * Payment type ID for the psp
     *
     * @var integer
     */
    private $_iCardCategoryId;
    /**
     * Payment status IDs for the transaction
     *
     * @var array
     */
    private $_aTransactionStateId;

    /**
     * ID of transaction
     *
     * @var int
     */
    private $_iTransactionID;

    /**
     * Default constructor
     *
     * @param integer $cardId		The unique ID for the Payment Method (Card)
     * @param integer $paymentType	The unique ID for the psp category
     * @param array $aStateid      The unique ID for transaction status
     * @param integer $iTransactionID      The unique ID for transaction status
     */
	public function __construct($cardId, $paymentType, $aStateid, $iTransactionID)
	{
		$this->_iCardID = $cardId;
        $this->_iCardCategoryId = $paymentType;
        $this->_aTransactionStateId = $aStateid;
        $this->_iTransactionID = $iTransactionID;
	}

    public function getCardID() { return $this->_iCardID; }
    public function getSystemType() { return $this->_iCardCategoryId; }
    public function getPaymentState() { return $this->_aTransactionStateId; }
    public function getTransactionID() { return $this->_iTransactionID; }


	public function toAttributeLessXML()
	{
        $xml = '<retry_attempt>';
        $xml .= '<transaction_id>'. $this->getTransactionID() .'</transaction_id>';
        $xml .= '<card_id>'. $this->getCardID() .'</card_id>';
        $xml .= '<payment_states>';
        foreach ( $this->getPaymentState()  as $iStateID )
        {
            $xml .= '<payment_state>'.$iStateID . '</payment_state>';

        }
        $xml .= '</payment_states>';
        $xml .= '<card_category_id>'. $this->getSystemType() .'</card_category_id>';
        $xml .= '</retry_attempt>';
        return $xml;
	}

    /**
     * Produces a failed payment method Configuration object.
     *
     * @param 	RDB $obj 		     Reference to the Database Object that holds the active connection to the mPoint Database
     * @param 	integer $sessionId   Unique session ID for payment transaction
     * @param 	integer $clientId    Hold unique client ID
     * @return  FailedPaymentMethodConfig $aObj_Configurations  Data object with the failed payment method information
     */
    public static function produceFailedTxnInfoFromSession(RDB $obj, $sessionId, $clientId)
    {
        $aObj_Configurations = array();
        if($obj instanceof RDB && $sessionId > 0 && $clientId > 0)
        {
            $sql = "SELECT Txn.id as txnid,Txn.cardid, C.paymenttype, txnpass.performedopt as stateid,txnpass.status AS status,msg.stateid as fraudstateId
                FROM Log" . sSCHEMA_POSTFIX . ".Transaction_Tbl Txn
				INNER JOIN System" . sSCHEMA_POSTFIX . ".Card_Tbl C ON Txn.cardid = C.id
				INNER JOIN  log" . sSCHEMA_POSTFIX . ".txnpassbook_tbl txnpass ON (Txn.id = txnpass.transactionid and txnpass.clientid=  " . $clientId . " )
				LEFT OUTER JOIN  log" . sSCHEMA_POSTFIX . ".MESSAGE_TBL msg ON (Txn.id = msg.txnid and msg.stateid in (" . Constants::iPRE_FRAUD_CHECK_REJECTED_STATE . "," . Constants::iPOST_FRAUD_CHECK_REJECTED_STATE . "," . Constants::iPAYMENT_REJECTED_FRAUD_SUSPICION_STATE . "," . Constants::iPAYMENT_REJECTED_FRAUD_CARD_BLOCKED_STATE . "," . Constants::iPAYMENT_REJECTED_FRAUD_CARD_STOLEN_STATE . "))
				WHERE Txn.sessionid = " . $sessionId . " AND txnpass.performedopt = " . Constants::iPAYMENT_ACCEPTED_STATE . "  and txn.clientid = " . $clientId;


            $aRS = $obj->getAllNames($sql);
            if (is_array($aRS) === true && count($aRS) > 0)
            {
                foreach ($aRS as $RS)
                {
                    if (empty($RS["CARDID"]) === false && empty($RS["PAYMENTTYPE"]) === false)
                    {
                        $aStates = array();
                        if (empty($RS["STATEID"]) === false && $RS["STATUS"] === Constants::sPassbookStatusError) { array_push($aStates, $RS["STATEID"]); }
                        if (empty($RS["FRAUDSTATEID"]) === false) { array_push($aStates, $RS["FRAUDSTATEID"]); }
                        if(empty($aStates) === false) { $aObj_Configurations[] = new FailedPaymentMethodConfig($RS["CARDID"], $RS["PAYMENTTYPE"], $aStates,$RS["TXNID"]); }
                    }
                }
            }

        }

        return $aObj_Configurations;
    }

    /**
     * Get failed fraud txn count from psp id
     *
     * @param 	RDB $obj 		     Reference to the Database Object that holds the active connection to the mPoint Database
     * @param 	integer $sessionId   Unique session ID for payment transaction
     * @param 	integer $clientId    Hold unique client ID
     * @param  integer $FPSPId
     * @return  integer $failTxnCount  Count of failed fraud txn
     */
    public static function getFailedFraudTxnCount(RDB $obj, int $sessionId, int $clientId, int $FPSPId):int
    {
        $cardId = array();
        if($obj instanceof RDB && $FPSPId > 0 && $clientId > 0) {
            $sql = "SELECT cardid
                FROM Client" . sSCHEMA_POSTFIX . ".Cardaccess_Tbl
				WHERE pspid = " . $FPSPId . " AND clientid= ".$clientId." AND enabled= true";
            $result = $obj->query($sql);
            while ($RS = $obj->fetchName($result)) {
                if (empty($RS["CARDID"]) === false) {
                    $cardId[] = $RS["CARDID"];
                }
            }
        }
        $failTxnCount = 0;
        if (empty($cardId) === false) {
            $cardString = implode(',',$cardId);
            $CountSql = "SELECT DISTINCT COUNT(Txn.id)
                FROM Log" . sSCHEMA_POSTFIX . ".Transaction_Tbl Txn
                INNER JOIN Log" . sSCHEMA_POSTFIX . ".Session_Tbl S ON Txn.sessionid = S.id AND S.stateid != " . Constants::iSESSION_COMPLETED . ".
				INNER JOIN (select transactionid,performedopt as st, status from log.txnpassbook_tbl where clientid = $clientId) p2 ON (Txn.id = p2.transactionid)
				WHERE Txn.sessionid = " . $sessionId . " AND Txn.cardid IN (".$cardString.") AND p2.st = ".Constants::iPAYMENT_ACCEPTED_STATE ." AND p2.status = 'error'";
            $res = $obj->query($CountSql);
            while ($RES = $obj->fetchName($res)) {
                if (empty($RES["COUNT"]) === false) {
                    $failTxnCount = $RES["COUNT"];
                }
            }
        }
        return $failTxnCount;
    }
}
?>