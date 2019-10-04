<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:txn_passbook.php
 */

final class TxnPassbook
{
    private static $instances = array();

    private $_obj_Db;

    private $_transactionId;

    private $_passbookEntries = array();

    private $_isPartialCaptureSupported = FALSE;

    private $_isPartialRefundSupported = FALSE;

    private $_isPartialCancelSupported = FALSE;

    private $_pspSupportedPartialOperation = -1;

    private $_merchantSupportedPartialOperation = -1;

    private $_initializedAmount = 0;

    private $_authorizedAmount = 0;

    private $_capturedAmount = 0;

    private $_refundedAmount = 0;

    private $_cancelledAmount = 0;

    private $_initializeAmount = 0;

    private $_authorizeAmount = 0;

    private $_captureAmount = 0;

    private $_refundAmount = 0;

    private $_cancelAmount = 0;

    /**
     * TxnPassbook constructor.
     */
    public function __construct()
    {
        $args = func_get_args();
        $aArgs = $args[0];
        $this->_obj_Db = $aArgs[0];
        $this->_transactionId = (int)$aArgs[1];
    }

    /**
     * @param object
     * @param int
     * @return mixed|TxnPassbook|null
     */
    public static function Get()
    {
        $txnPassbookInstance = NULL;
        $aArgs = func_get_args();
        if (count($aArgs) === 2) {
            $requestedTxnId = (int)$aArgs[1];
            if (empty(self::$instances) === false) {
                foreach (self::$instances as $txnid => $instance) {
                    if (($instance instanceof self) === TRUE && $txnid === $requestedTxnId) {
                        $txnPassbookInstance = $instance;
                    }
                }
            }
            if ($txnPassbookInstance === NULL) {
                $txnPassbookInstance = new TxnPassbook(func_get_args());
                self::$instances[$requestedTxnId] = $txnPassbookInstance;
            }
        }
        return $txnPassbookInstance;
    }

    /**
     * @param bool $getUpdatedEntries
     *
     * @return array
     * @throws Exception
     */
    public function getEntries($getUpdatedEntries = FALSE)
    {
        if ($getUpdatedEntries || count($this->_passbookEntries) === 0) {
            $this->_passbookEntries = array();
            $this->_getUpdatedPassbookEntries();
        }
        return $this->_passbookEntries;
    }

    /**
     * Fetch passbook entries from database
     * @throws Exception
     */
    private function _getUpdatedPassbookEntries()
    {
        $sql = 'SELECT * FROM log.' . sSCHEMA_POSTFIX . 'TxnPassbook_tbl WHERE transactionid = $1 ORDER BY id ASC';
        $res = $this->getDBConn()->prepare($sql);
        if (is_resource($res) === TRUE) {
            $aParams = array(
                $this->getTransactionId()
            );

            $result = $this->getDBConn()->execute($res, $aParams);

            if ($result === FALSE) {
                throw new Exception('Fail to fetch passbook entries for transaction id :' . $this->_transactionId, E_USER_ERROR);
            } else {
                while ($RS = $this->getDBConn()->fetchName($result)) {

                    $passbookEntry = new PassbookEntry
                    (
                        $RS['ID'],
                        $RS['AMOUNT'],
                        $RS['CURRENCYID'],
                        $RS['REQUESTEDOPT'],
                        $RS['EXTREF'],
                        $RS['EXTREFIDENTIFIER'],
                        $RS['PERFORMEDOPT'],
                        $RS['STATUS'],
                        $RS['ENABLED'],
                        $RS['CREATED'],
                        $RS['MODIFIED']
                    );
                    array_push($this->_passbookEntries, $passbookEntry);
                }
            }
        }
    }

    /**
     * @return mixed
     */
    public function getTransactionId()
    {
        return $this->_transactionId;
    }

    /**
     * Validate the new passbook entry and add to passbook
     *
     * @param PassbookEntry $passbookEntry
     *
     * @param bool          $isCancelPriority
     *
     * @return array|void
     * @throws \Exception
     */
    public function addEntry(PassbookEntry $passbookEntry, $isCancelPriority = FALSE)
    {
        $validateEntryResponse = null;
        $passbookEntries = array($passbookEntry);
        $this->_getUpdatedTransactionAmounts();
        if($passbookEntry->getRequestedOperation() === Constants::iVoidRequested)
        {
            $validateEntryResponse = $this->validateEntry($passbookEntry);
            if ($validateEntryResponse['Status'] === 0) {
                $passbookEntries = $this->_segregateVoidEntry($passbookEntry, $isCancelPriority);
            }
        }

        foreach ($passbookEntries as $_passbookEntry) {
            if($validateEntryResponse === null) {
                $validateEntryResponse = $this->validateEntry($_passbookEntry);
            }
            if ($validateEntryResponse['Status'] > 0) {
                $_passbookEntry->setStatus(Constants::sPassbookStatusInvalid);
            }
            $_addEntryResponse = $this->_addEntry($_passbookEntry);
            if ($_addEntryResponse['Status'] !== 0) {
                $validateEntryResponse = $_addEntryResponse;
            }

        }
        return $validateEntryResponse;
    }

    private function _segregateVoidEntry(PassbookEntry $passbookEntry, $isCancelPriority = FALSE)
    {
        $cancelAmount = 0;
        $refundAmount = 0;
        if ($isCancelPriority === TRUE) {
            if($passbookEntry->getAmount() <= $this->_getCancelableAmount()) {
                $cancelAmount = $passbookEntry->getAmount();
                $refundAmount = 0;
            }
            else
            {
                $cancelAmount = $this->_getCancelableAmount();
                $refundAmount = $passbookEntry->getAmount() - $this->_getCancelableAmount();
            }
        } else {
            if($passbookEntry->getAmount() <=  $this->_getRefundableAmount()) {
                $refundAmount = $passbookEntry->getAmount();
                $cancelAmount = 0;
            }
            else
            {
                $refundAmount = $this->_getRefundableAmount();
                $cancelAmount = $passbookEntry->getAmount() - $this->_getRefundableAmount();
            }
        }

        $newPassbookEntries = array();
        if ($cancelAmount > 0) {
            $newCancelPassbookEntry = new PassbookEntry(
                $passbookEntry->getId(),
                $cancelAmount,
                $passbookEntry->getCurrencyId(),
                Constants::iCancelRequested,
                $passbookEntry->getExternalReference(),
                $passbookEntry->getExternalReferenceIdentifier(),
                $passbookEntry->getPerformedOperation(),
                $passbookEntry->getStatus()
            );
            array_push($newPassbookEntries, $newCancelPassbookEntry);
        }
        // $refundAmount = $passbookEntry->getAmount() - $this->_getCancelableAmount();
        if ($refundAmount > 0) {
            $newRefundPassbookEntry = new PassbookEntry(
                $passbookEntry->getId(),
                $refundAmount,
                $passbookEntry->getCurrencyId(),
                Constants::iRefundRequested,
                $passbookEntry->getExternalReference(),
                $passbookEntry->getExternalReferenceIdentifier(),
                $passbookEntry->getPerformedOperation(),
                $passbookEntry->getStatus()
            );
            array_push($newPassbookEntries, $newRefundPassbookEntry);
        }
        return $newPassbookEntries;
    }

    /**
     * Validate the new passbook entry
     * @param PassbookEntry $passbookEntry
     *
     * @return array
     * @throws Exception
     */
    private function validateEntry(PassbookEntry $passbookEntry)
    {
        $requestedOperation = $passbookEntry->getRequestedOperation();
        $validateOperationResponse = array();
        if ($requestedOperation === Constants::iCaptureRequested) {
            $validateOperationResponse = $this->_validateOperation($this->_capturedAmount, $passbookEntry->getAmount(), $this->_getCapturebleAmount(), $this->isPartialCaptureSupported());
        } elseif ($requestedOperation === Constants::iCancelRequested) {
            $validateOperationResponse = $this->_validateOperation($this->_cancelledAmount, $passbookEntry->getAmount(), $this->_getCancelableAmount(), $this->isPartialCancelSupported());
        } elseif ($requestedOperation === Constants::iRefundRequested) {
            $validateOperationResponse = $this->_validateOperation($this->_refundedAmount, $passbookEntry->getAmount(), $this->_getRefundableAmount(), $this->isPartialRefundSupported());
        } elseif ($requestedOperation === Constants::iVoidRequested) {
            $validateOperationResponse = $this->_validateOperation($this->_cancelledAmount + $this->_refundedAmount, $passbookEntry->getAmount(), $this->_getCancelableAmount() + $this->_getRefundableAmount(), $this->isPartialRefundSupported());
        } elseif ($requestedOperation === Constants::iAuthorizeRequested) {
            if ($this->_authorizedAmount === 0 && $this->_initializedAmount >= $passbookEntry->getAmount()) {
                $validateOperationResponse['Status'] = 0;
                $validateOperationResponse['Message'] = '';
            }
            else {
                $validateOperationResponse['Status'] = Constants::iAmountIsHigher;
                $validateOperationResponse['Message'] = 'Operation Not Allowed - Invalid Amount';
            }
        }
        elseif ($requestedOperation === Constants::iInitializeRequested) {
            $validateOperationResponse['Status'] = 0;
            $validateOperationResponse['Message'] = '';
        }
        return $validateOperationResponse;
    }

    /**
     * @throws Exception
     */
    private function _getUpdatedTransactionAmounts()
    {
        $sql = "WITH tp
                    AS
                    (
                    SELECT 
                        transactionid, 
                        amount, 
                        performedopt,
                        requestedopt, 
                        status
                    FROM log." . sSCHEMA_POSTFIX . "TxnPassbook_tbl
                    WHERE transactionid = $1
                    )
                    SELECT 
                        q1.IntializedAmount,
                        q2.AuthorizedAmount,
                        q3.CapturedAmount, 
                        q4.CancelledAmount, 
                        q5.RefundedAmount,
                        q6.CaptureAmount, 
                        q7.CancelAmount, 
                        q8.RefundAmount
                    FROM
                        ( 
                         SELECT COALESCE(SUM(tp.amount),0) AS IntializedAmount
                                             FROM tp
                                             WHERE tp.performedopt = " . Constants::iINPUT_VALID_STATE . "
                                               AND tp.status IN ('". Constants::sPassbookStatusDone ."', '". Constants::sPassbookStatusInProgress ."')
                        ) AS q1,
                        ( 
                         SELECT COALESCE(SUM(tp.amount),0) AS AuthorizedAmount
                                             FROM tp
                                             WHERE tp.performedopt = " . Constants::iPAYMENT_ACCEPTED_STATE . "
                                               AND tp.status IN ('". Constants::sPassbookStatusDone ."', '". Constants::sPassbookStatusInProgress ."')
                        ) AS q2,
                        ( 
                         SELECT COALESCE(SUM(tp.amount),0) AS CapturedAmount
                                             FROM tp
                                             WHERE tp.performedopt = " . Constants::iPAYMENT_CAPTURED_STATE . "
                                               AND tp.status IN ('". Constants::sPassbookStatusDone ."', '". Constants::sPassbookStatusInProgress ."')
                        ) AS q3,
                        ( 
                         SELECT COALESCE(SUM(tp.amount),0) as CancelledAmount
                                             FROM tp
                                             WHERE tp.performedopt = " . Constants::iPAYMENT_CANCELLED_STATE . "
                                               AND tp.status IN ('". Constants::sPassbookStatusDone ."', '". Constants::sPassbookStatusInProgress ."')
                        ) as q4,
                        ( 
                         SELECT COALESCE(SUM(tp.amount),0) as RefundedAmount
                                             FROM tp
                                             WHERE tp.performedopt = " . Constants::iPAYMENT_REFUNDED_STATE . "
                                               AND tp.status IN ('". Constants::sPassbookStatusDone ."', '". Constants::sPassbookStatusInProgress ."')
                        ) AS q5,
                        ( 
                         SELECT COALESCE(SUM(tp.amount),0) AS CaptureAmount
                                             FROM tp
                                             WHERE tp.requestedopt =  " . Constants::iCaptureRequested . "
                                               AND tp.status = 'pending'
                        ) AS q6,
                        ( 
                         SELECT COALESCE(SUM(tp.amount),0) AS CancelAmount
                                             FROM tp
                                             WHERE tp.requestedopt =  " . Constants::iCancelRequested . "
                                               AND tp.status = 'pending'
                        ) AS q7,
                        ( 
                         SELECT COALESCE(SUM(tp.amount),0) AS RefundAmount
                                             FROM tp
                                             WHERE tp.requestedopt =  " . Constants::iRefundRequested . "
                                               AND tp.status = 'pending'
                        ) AS q8";

        $res = $this->getDBConn()->prepare($sql);
        if (is_resource($res) === TRUE) {
            $aParams = array(
                $this->getTransactionId()
            );

        }
        $result = $this->getDBConn()->execute($res, $aParams);

        if ($result === FALSE) {
            throw new Exception('Fail to fetch transaction entry for transaction id :' . $this->_transactionId, E_USER_ERROR);
        } else {
            $RS = $this->getDBConn()->fetchName($result);
            $this->_initializedAmount = (int)$RS['INTIALIZEDAMOUNT'];
            $this->_authorizedAmount = (int)$RS['AUTHORIZEDAMOUNT'];
            $this->_capturedAmount = (int)$RS['CAPTUREDAMOUNT'];
            $this->_cancelledAmount = (int)$RS['CANCELLEDAMOUNT'];
            $this->_refundedAmount = (int)$RS['REFUNDEDAMOUNT'];
            $this->_captureAmount = (int)$RS['CAPTUREAMOUNT'];
            $this->_cancelAmount = (int)$RS['CANCELAMOUNT'];
            $this->_refundAmount = (int)$RS['REFUNDAMOUNT'];

        }
    }

    /**
     * @param $usedAmount
     * @param $requestedAmount
     * @param $allowableAmount
     * @param $isMultiplePartialSupported
     *
     * @return array
     */
    private function _validateOperation($usedAmount, $requestedAmount, $allowableAmount, $isMultiplePartialSupported)
    {
        $response = array();
        $response['Status'] = -1;
        $response['Message'] = '';
        if ($requestedAmount > $allowableAmount) {
            $response['Status'] = Constants::iAmountIsHigher;
            $response['Message'] = 'Operation Not Allowed - Invalid Amount';
        }
        elseif ($usedAmount === 0 && $allowableAmount === $this->_authorizedAmount) {
            $response['Status'] = 0;
        }
        elseif ($usedAmount > 0 && $isMultiplePartialSupported === FALSE) {
            $response['Status'] = Constants::iOperationNotAllowed;
            $response['Message'] = 'Operation Not Allowed - Multiple Capture';
        }
        elseif ($isMultiplePartialSupported === TRUE && $requestedAmount <= $allowableAmount) {
            $response['Status'] = 0;
        }
        return $response;
    }

    /**
     * @return int
     */
    private function _getCapturebleAmount()
    {
        return $this->_authorizedAmount - ($this->_capturedAmount + $this->_captureAmount + $this->_cancelledAmount + $this->_cancelAmount + $this->_refundedAmount);
    }

    /**
     * @return int
     */
    private function _getCancelableAmount()
    {
        return $this->_authorizedAmount - ($this->_capturedAmount + $this->_captureAmount + $this->_cancelledAmount + $this->_cancelAmount + $this->_refundedAmount + $this->_refundAmount);
    }

    /**
     * @return int
     */
    private function _getRefundableAmount()
    {
        return ($this->_capturedAmount +  $this->_captureAmount) - ($this->_refundedAmount + $this->_refundAmount);
    }

    /**
     * @return bool
     */
    private function isPartialCaptureSupported()
    {
        $this->getSupportedPartialOperation();
        return $this->_isPartialCaptureSupported;
    }

    /**
     *
     */
    private function getSupportedPartialOperation()
    {
        if ($this->_merchantSupportedPartialOperation === -1 || $this->_pspSupportedPartialOperation === -1) {
            $sql = 'SELECT psp.SupportedPartialOperations      as PSPSupportedPartialOperations,
                           merchant.SupportedPartialOperations as MerchantSupportedPartialOperations
                    FROM system.' . sSCHEMA_POSTFIX . 'psp_tbl psp
                             INNER JOIN client.' . sSCHEMA_POSTFIX . 'merchantaccount_tbl merchant ON psp.id = merchant.pspid
                             INNER JOIN log.' . sSCHEMA_POSTFIX . 'transaction_tbl transaction
                                        ON psp.id = transaction.pspid AND transaction.clientid = merchant.clientid
                    WHERE transaction.id = $1';
            $res = $this->getDBConn()->prepare($sql);
            if (is_resource($res) === TRUE) {
                $aParams = array(
                    $this->getTransactionId()
                );
            }

            $result = $this->getDBConn()->execute($res, $aParams);
            while ($RS = $this->getDBConn()->fetchName($result)) {
                $this->_pspSupportedPartialOperation = (int)$RS['PSPSUPPORTEDPARTIALOPERATIONS'];
                $this->_merchantSupportedPartialOperation = (int)$RS['MERCHANTSUPPORTEDPARTIALOPERATIONS'];
            }

            if ($this->_pspSupportedPartialOperation % 2 === 0 && $this->_merchantSupportedPartialOperation % 2 === 0) {
                $this->_isPartialCaptureSupported = TRUE;
            }
            if ($this->_pspSupportedPartialOperation % 3 === 0 && $this->_merchantSupportedPartialOperation % 3 === 0) {
                $this->_isPartialRefundSupported = TRUE;
            }
            if ($this->_pspSupportedPartialOperation % 5 === 0 && $this->_merchantSupportedPartialOperation % 5 === 0) {
                $this->_isPartialCancelSupported = TRUE;
            }
        }
    }

    /**
     * @return bool
     */
    private function isPartialCancelSupported()
    {
        $this->getSupportedPartialOperation();
        return $this->_isPartialCancelSupported;
    }

    /**
     * @return bool
     */
    private function isPartialRefundSupported()
    {
        $this->getSupportedPartialOperation();
        return $this->_isPartialRefundSupported;
    }

    /**
     * @return mixed
     */
    private function getDBConn()
    {
        return $this->_obj_Db;
    }

    /**
     * @param      $_OBJ_TXT
     * @param      $aHTTP_CONN_INFO
     * @param bool $isConsolidate
     * @param bool $isMutualExclusive
     * @param bool $isRetryRequest
     *
     * @return array
     * @throws \Exception
     */
    public function performPendingOperations($_OBJ_TXT = NULL, $aHTTP_CONN_INFO = NULL, $isConsolidate = FALSE, $isMutualExclusive = FALSE, $isRetryRequest = FALSE)
    {
        $codes = array();
        if ($isRetryRequest === FALSE) {
            $this->getDBConn()->query('START TRANSACTION');
            $status = $this->_createPerformingOperations($isConsolidate, $isMutualExclusive);
            if ($status === TRUE) {
                $this->getDBConn()->query('COMMIT');
            } else {
                $this->getDBConn()->query('ROLLBACK');
            }
        }

        $this->_getUpdatedPassbookEntries();
        foreach ($this->_passbookEntries as $passbookEntry) {
            if ($passbookEntry instanceof PassbookEntry && $passbookEntry->getPerformedOperation() != '' && $passbookEntry->getStatus() === Constants::sPassbookStatusPending) {
                if($passbookEntry->getPerformedOperation() === Constants::iINPUT_VALID_STATE)
                {
                    $passbookEntry->setStatus(Constants::sPassbookStatusDone);
                    $this->_updatePassbookEntries(array($passbookEntry));
                }
                elseif($passbookEntry->getPerformedOperation() === Constants::iPAYMENT_ACCEPTED_STATE)
                {
                    $passbookEntry->setStatus(Constants::sPassbookStatusInProgress);
                    $this->_updatePassbookEntries(array($passbookEntry));
                }
                else
                {
                    if(empty($_OBJ_TXT) || empty($aHTTP_CONN_INFO))
                    {
                        $passbookEntry->setStatus(Constants::sPassbookStatusError);
                        $this->_updatePassbookEntries(array($passbookEntry));
                    }
                    else
                    {
                        $txnInfoObj = TxnInfo::produceInfo($this->getTransactionId(), $this->getDBConn());
                        $obj_PSP = Callback::producePSP($this->getDBConn(), $_OBJ_TXT, $txnInfoObj, $aHTTP_CONN_INFO);
                        $code = -1;
                        switch ($passbookEntry->getPerformedOperation())
                        {
                            case Constants::iPAYMENT_CAPTURED_STATE;
                                $code = $obj_PSP->capture($passbookEntry->getAmount());
                                break;
                            case Constants::iPAYMENT_CANCELLED_STATE;
                                $code = $obj_PSP->cancel(null,$passbookEntry->getAmount());
                                break;
                            case Constants::iPAYMENT_REFUNDED_STATE;
                                $code = $obj_PSP->refund($passbookEntry->getAmount());
                                break;
                        }
                        $codes[$passbookEntry->getPerformedOperation()] = $code;
                        if($code === 100 ||$code === 1000 || $code === 1001)
                        {
                            $passbookEntry->setStatus(Constants::sPassbookStatusInProgress);
                        }
                        else
                        {
                            $passbookEntry->setStatus(Constants::sPassbookStatusError);
                        }
                        $this->_updatePassbookEntries(array($passbookEntry));
                    }
                }
            }
        }
        return $codes;
    }

    /**
     * @param \PassbookEntry $passbookEntry
     *
     * @throws \Exception
     */
    private function _addEntry(PassbookEntry $passbookEntry)
    {
        $validateEntryResponse['Status'] = 0;

        $sql = 'INSERT INTO Log' . sSCHEMA_POSTFIX . '.TxnPassbook_tbl 
                    (transactionid, amount, currencyid, requestedopt, performedopt , status, ExtRef, ExtRefIdentifier)                                                         
                VALUES 
                    ($1, $2, $3, $4, $5, $6, $7, $8)';

        $res = $this->_obj_Db->prepare($sql);
        if (is_resource($res) === TRUE) {
            $aParams = array(
                $this->getTransactionId(),
                $passbookEntry->getAmount(),
                $passbookEntry->getCurrencyId(),
                $passbookEntry->getRequestedOperation(),
                $passbookEntry->getPerformedOperation(),
                $passbookEntry->getStatus(),
                $passbookEntry->getExternalReference(),
                $passbookEntry->getExternalReferenceIdentifier()
            );

            $result = $this->getDBConn()->execute($res, $aParams);

            if ($result === FALSE) {
                $validateEntryResponse['Status'] = 999;
                $validateEntryResponse['Message'] = 'Fail to create passbook entry for transaction id ' . $this->_transactionId;
                throw new Exception('Fail to create passbook entry for transaction id ' . $this->_transactionId, E_USER_ERROR);
            }
        }
        return $validateEntryResponse;
    }

    /**
     * @param array $newEntries
     *
     * @throws \Exception
     */
    private function _addPerformingEntries(array $newEntries)
    {
        /*Full cancellation scenario wherein the
        *   Auth amount = capture amount = cancel amount
         *
         */
        $iAuthAmount = $this->_authorizedAmount;
        $iCaptureAmount = 0;
        $iCancelAmount = 0;
        $iRefundAmount = 0;
        $iCurrency = 0;
        $sEntries = array();
        foreach ($newEntries as $entry) {
            if($entry->state === Constants::iPAYMENT_CAPTURED_STATE)
            {
                $iCaptureAmount += $entry->amount;
            }
            elseif ($entry->state === Constants::iPAYMENT_CANCELLED_STATE)
            {
                $iCancelAmount += $entry->amount;

            }
            elseif ($entry->state === Constants::iPAYMENT_REFUNDED_STATE)
            {
                $iRefundAmount += $entry->amount;
            }
            $iCurrency = $entry->currency;
            array_push($sEntries,$entry->externalId);
        }
        $sEntries = array_unique($sEntries);
        if(($iCaptureAmount > 0) && ((($iCaptureAmount === $iCancelAmount) && ($iCancelAmount === $iAuthAmount)) || (($iCaptureAmount === $iRefundAmount) && ($iRefundAmount === $iAuthAmount))))
        {
            unset($newEntries);
            $newEntry = new stdClass();
            $newEntry->amount = $iCancelAmount + $iRefundAmount;
            $newEntry->currency = $iCurrency;
            $newEntry->state = Constants::iPAYMENT_CANCELLED_STATE;
            $newEntry->externalId = implode(',', $sEntries);
            $newEntries = array();
            array_push($newEntries, $newEntry);

        }
        foreach ($newEntries as $newEntry) {
            $data = new PassbookEntry(
                NULL,
                $newEntry->amount,
                $newEntry->currency,
                NULL,
                $newEntry->externalId,
                'log.txnpassbook_tbl',
                $newEntry->state,
                Constants::sPassbookStatusPending,
                TRUE,
                NULL,
                NULL
            );
            $this->_addEntry($data);
        }

    }

    /**
     * @param bool $isConsolidate
     *
     * @return bool
     * @throws \Exception
     */
    private function _createPerformingOperations($isConsolidate = FALSE, $isMutualExclusive = FALSE)
    {
        $this->_getUpdatedTransactionAmounts();
        $aUpdatedEntries = array();
        $aPerformingData = array();

        $initIds = array();
        $authIds = array();
        $captureIds = array();
        $refundIds = array();
        $cancelIds = array();
        $currency = 0;
        foreach ($this->getEntries(TRUE) as $entry) {

            if ($entry instanceof PassbookEntry && $entry->getStatus() === Constants::sPassbookStatusPending) {

                $entry->setStatus(Constants::sPassbookStatusDone);
                $currency = $entry->getCurrencyId();
                array_push($aUpdatedEntries, $entry);

                $newEntry = new stdClass();
                $newEntry->amount = $entry->getAmount();
                $newEntry->currency = $entry->getCurrencyId();
                $newEntry->externalId = $entry->getId();

                if ($entry->getRequestedOperation() === Constants::iInitializeRequested) {
                    $newEntry->state = Constants::iINPUT_VALID_STATE;
                    array_push($initIds, $entry->getId());
                } elseif ($entry->getRequestedOperation() === Constants::iAuthorizeRequested) {
                    $newEntry->state = Constants::iPAYMENT_ACCEPTED_STATE;
                    array_push($authIds, $entry->getId());
                } elseif ($entry->getRequestedOperation() === Constants::iCaptureRequested) {
                    $newEntry->state = Constants::iPAYMENT_CAPTURED_STATE;
                    array_push($captureIds, $entry->getId());
                } elseif ($entry->getRequestedOperation() === Constants::iRefundRequested) {
                    $newEntry->state = Constants::iPAYMENT_REFUNDED_STATE;
                    array_push($refundIds, $entry->getId());
                } elseif ($entry->getRequestedOperation() === Constants::iCancelRequested) {
                    $newEntry->state = Constants::iPAYMENT_CANCELLED_STATE;
                    array_push($cancelIds, $entry->getId());
                }
                array_push($aPerformingData, $newEntry);
            }
        }

        if ($isConsolidate === TRUE) {
            $aPerformingData = array();

            $initializing = $this->_initializeAmount;

            if ($initializing > 0) {
                $newEntry = new stdClass();
                $newEntry->amount = $initializing;
                $newEntry->currency = $currency;
                $newEntry->state = Constants::iINPUT_VALID_STATE;
                $newEntry->externalId = implode(',', $initIds);
                array_push($aPerformingData, $newEntry);
            }

            $authorizing = $this->_authorizeAmount;

            if ($authorizing > 0) {
                $newEntry = new stdClass();
                $newEntry->amount = $authorizing;
                $newEntry->currency = $currency;
                $newEntry->state = Constants::iPAYMENT_ACCEPTED_STATE;
                $newEntry->externalId = implode(',', $authIds);
                array_push($aPerformingData, $newEntry);
            }

            if ($isMutualExclusive === TRUE) {
                $capturing = $this->_captureAmount;
                $refunding = $this->_refundAmount;
                $cancelling = $this->_cancelAmount;
            } else {
                $capturing = $this->_captureAmount - $this->_refundAmount;

                $refunding = $this->_refundAmount - $this->_captureAmount;
                $diff = 0;
                if ($refunding < 0) {
                    $refunding = 0;
                } else {
                    $diff = $this->_refundAmount - $refunding;
                    array_merge($cancelIds, $refundIds);
                }

                $cancelling = $diff + $this->_cancelAmount;
                array_merge($captureIds, $refundIds);
                $refundIds = $captureIds;
                /*if ($capturing === 0) {
                    $cancelling += $this->_captureAmount;
                    $cancelIds = array_merge($cancelIds, $captureIds);
                }*/
            }

            if ($capturing > 0) {
                $newEntry = new stdClass();
                $newEntry->amount = $capturing;
                $newEntry->currency = $currency;
                $newEntry->state = Constants::iPAYMENT_CAPTURED_STATE;
                $newEntry->externalId = implode(',', $captureIds);
                 array_push($aPerformingData, $newEntry);
            }

            if ($refunding > 0) {
                $newEntry = new stdClass();
                $newEntry->amount = $refunding;
                $newEntry->currency = $currency;
                $newEntry->state = Constants::iPAYMENT_REFUNDED_STATE;
                $newEntry->externalId = implode(',', $refundIds);
                 array_push($aPerformingData, $newEntry);
            }

            if ($cancelling > 0) {
                $newEntry = new stdClass();
                $newEntry->amount = $cancelling;
                $newEntry->currency = $currency;
                $newEntry->state = Constants::iPAYMENT_CANCELLED_STATE;
                $newEntry->externalId = implode(',', $cancelIds);
                 array_push($aPerformingData, $newEntry);
            }
        }
        try {
            $status = $this->_updatePassbookEntries($aUpdatedEntries);
            if ($status === TRUE) {
                $this->_addPerformingEntries($aPerformingData);
                return TRUE;
            }
        } catch (Exception $e) {
        }
        return FALSE;
    }

    /**
     * @param array $passbookEntry
     *
     * @return bool
     * @throws \Exception
     */
    private function _updatePassbookEntries(array $passbookEntry)
    {
        $aParams = array($this->getTransactionId());
        $queryResult = FALSE;
        $sqlQuery = 'UPDATE log.' . sSCHEMA_POSTFIX . 'TxnPassbook_tbl SET status = $2 WHERE id = $3 and transactionid = $1;';

        if ($sqlQuery != '') {
            $res = $this->getDBConn()->prepare($sqlQuery);
            if (is_resource($res) === TRUE) {
                foreach ($passbookEntry as $entry) {
                    if ($entry instanceof PassbookEntry) {
                        $aParams = array(
                            $this->getTransactionId(),
                            $entry->getStatus(),
                            $entry->getId()
                        );
                        $result = $this->getDBConn()->execute($res, $aParams);
                        $queryResult = (bool)$result;
                        if ($result === FALSE) {
                            throw new Exception('Fail to fetch passbook entries for transaction id :' . $this->_transactionId, E_USER_ERROR);
                            return FALSE;
                        }

                    }
                }
                return $queryResult;
            }
        }
        return FALSE;
    }

    public function updateInProgressOperations($amount, $state, $status)
    {
        $amount  = (int)$amount;
        $sqlQuery = 'UPDATE log.' . sSCHEMA_POSTFIX . 'TxnPassbook_tbl SET status = $1 WHERE transactionid = $2 and amount = $3 and performedopt = $4;';

        $res = $this->getDBConn()->prepare($sqlQuery);
        if (is_resource($res) === TRUE) {
            $aParams = array(
                $status,
                $this->getTransactionId(),
                $amount,
                $state
            );
            $result = $this->getDBConn()->execute($res, $aParams);
            if ($result === FALSE) {
                throw new Exception('Fail to fetch passbook entries for transaction id :' . $this->_transactionId, E_USER_ERROR);
                return FALSE;
            }
        }
        return TRUE;


    }

}