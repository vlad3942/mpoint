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
    public function initialize(PSPConfig $obj_PSPConfig = NULL, $euaid=-1, $sc=false, $card_type_id=-1, $card_token='', $obj_BillingAddress = NULL, ClientInfo $obj_ClientInfo = NULL,$authToken = NULL, $cardName='', $aWalletCardSchemes = array())
    {
        if($card_type_id !== -1)
        {
            $sql = "UPDATE Log" . sSCHEMA_POSTFIX . ".Transaction_Tbl
                SET pspid = " . $this->getPSPID() . "
                , cardid = ". intval($card_type_id) . "
                WHERE id = " . $this->getTxnInfo()->getID();
            $this->getDBConn()->query($sql);
        }
        $this->updateTxnInfoObject();
        $this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_PENDING_STATE, "");
        $args = array('amount'=>$this->getTxnInfo()->getAmount(),
							'transact'=>$this->getTxnInfo()->getExternalID(),
							'cardid'=>$card_type_id);
        $this->notifyClient(Constants::iPAYMENT_PENDING_STATE, $args, $this->getTxnInfo()->getClientConfig()->getSurePayConfig($this->getDBConn()));
        $obj_XML = simplexml_load_string("<root><hidden-fields></hidden-fields></root>");

        return $obj_XML;
    }

    /**
     * Performs a refund operation with CPM PSP for the provided transaction.
     * The method will return one the following status codes:
     *    >=1000 Refund succeeded
     *    <1000 Refund failed
     *
     * @param int $iAmount
     * @param null $iStatus
     * @return int
     * @throws mPointException
     */
    public function refund($iAmount=-1,$iStatus=null)
    {
        $this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_REFUNDED_STATE, "");
        $txnPassbookObj = TxnPassbook::Get($this->getDBConn(), $this->getTxnInfo()->getID(), $this->getTxnInfo()->getClientConfig()->getID());
        $txnPassbookObj->updateInProgressOperations($iAmount, Constants::iPAYMENT_REFUNDED_STATE, Constants::sPassbookStatusDone);
        //Update Refund amount in txn table
        if((int)$iAmount === -1)
        {
            //get auth amount
            $iAmount = $this->getTxnInfo()->getAmount();
        }
        $this->getTxnInfo()->updateRefundedAmount($this->getDBConn(), $iAmount);
        return 1000;
    }
}
