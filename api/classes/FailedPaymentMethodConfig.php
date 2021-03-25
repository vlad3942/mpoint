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
     * Payment statu ID for the transaction
     *
     * @var integer
     */
    private $_iTransactionStateId;

    /**
     * Default constructor
     *
     * @param integer $cardId		The unique ID for the Payment Method (Card)
     * @param integer $paymentType	The unique ID for the psp category
     * @param integer $stateid      The unique ID for transaction status
     */
	public function __construct($cardId, $paymentType, $stateid)
	{
		$this->_iCardID = $cardId;
        $this->_iCardCategoryId = $paymentType;
        $this->_iTransactionStateId = $stateid;
	}

    public function getCardID() { return $this->_iCardID; }
    public function getSystemType() { return $this->_iCardCategoryId; }
    public function getPaymentState() { return $this->_iTransactionStateId; }


	public function toAttributeLessXML()
	{
        $xml = '<retry_attempt>';
        $xml .= '<card_id>'. $this->getCardID() .'</card_id>';
        $xml .= '<transaction_state_id>'. $this->getPaymentState() .'</transaction_state_id>';
        $xml .= '<card_category_id>'. $this->getSystemType() .'</card_category_id>';
        $xml .= '</retry_attempt>';
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
        $aObj_Configurations = array();
        if($obj instanceof RDB && $sessionId > 0 && $clientId > 0) {
            $aStateIDs = array(Constants::iPAYMENT_REJECTED_STATE);
            $sql = "SELECT Txn.cardid, C.paymenttype, p2.st AS stateid
                FROM Log" . sSCHEMA_POSTFIX . ".Transaction_Tbl Txn
                INNER JOIN Log" . sSCHEMA_POSTFIX . ".Session_Tbl S ON Txn.sessionid = S.id AND S.stateid != " . Constants::iSESSION_COMPLETED . ".
                INNER JOIN System" . sSCHEMA_POSTFIX . ".PSP_Tbl PSP ON Txn.pspid = PSP.id
				INNER JOIN System" . sSCHEMA_POSTFIX . ".Card_Tbl C ON Txn.cardid = C.id
				INNER JOIN (select transactionid,max(requestedopt) as st from log.txnpassbook_tbl where clientid = $clientId group by transactionid) p2 ON (Txn.id = p2.transactionid)
				WHERE Txn.sessionid = " . $sessionId . " AND p2.st IN (" . implode(",", $aStateIDs) . ")";

            $res = $obj->query($sql);
            while ($RS = $obj->fetchName($res)) {
                if (empty($RS["CARDID"]) === false && empty($RS["PAYMENTTYPE"]) === false && empty($RS["STATEID"]) === false) {
                    $aObj_Configurations[] = new FailedPaymentMethodConfig($RS["CARDID"], $RS["PAYMENTTYPE"], $RS["STATEID"]);
                }
            }
        }
        return $aObj_Configurations;
    }
	
}
?>