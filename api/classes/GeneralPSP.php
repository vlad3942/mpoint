<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package:
 * File Name: GeneralPSP
 */

final class GeneralPSP extends CPMACQUIRER
{
    public function __construct(RDB $oDB, TranslateText $oTxt, ?TxnInfo $oTI = NULL, ?array $aConnInfo = NULL, ?PSPConfig $obj_PSPConfig = NULL, ClientInfo $oClientInfo = NULL)
    {
        parent::__construct($oDB, $oTxt, $oTI, $aConnInfo, $obj_PSPConfig, $oClientInfo);
    }

    public function getPSPID()
    {
        return -1;
    }

    public function notifyClient($iStateId, array $vars, $surePay)
    {
        throw new BadMethodCallException('Method notifyClient is not supported by GeneralPSP class');
    }

    public function capture($iAmount = -1)
    {
        throw new BadMethodCallException('Method capture is not supported by GeneralPSP class');
    }

    public function refund($iAmount = -1, $iStatus = NULL)
    {
        throw new BadMethodCallException('Method refund is not supported by GeneralPSP class');
    }

    public function void($iAmount = -1)
    {
        throw new BadMethodCallException('Method void is not supported by GeneralPSP class');
    }

    public function cancel($amount = -1)
    {
        throw new BadMethodCallException('Method cancel is not supported by GeneralPSP class');
    }

    public function status()
    {
        throw new BadMethodCallException('Method status is not supported by GeneralPSP class');
    }

    public function initialize(PSPConfig $obj_PSPConfig, $euaid = -1, $sc = FALSE, $card_type_id = -1, $card_token = '', $obj_BillingAddress = NULL, ClientInfo $obj_ClientInfo = NULL, $authToken = NULL)
    {
        throw new BadMethodCallException('Method initialize is not supported by GeneralPSP class');
    }

    public function authorize(PSPConfig $obj_PSPConfig, $obj_Card, $clientInfo = NULL)
    {
        throw new BadMethodCallException('Method authorize is not supported by GeneralPSP class');
    }

    public function tokenize(array $aConnInfo, PSPConfig $obj_PSPConfig, $obj_Card)
    {
        throw new BadMethodCallException('Method tokenize is not supported by GeneralPSP class');
    }

    public function redeem($iVoucherID, $iAmount = -1)
    {
        throw new BadMethodCallException('Method redeem is not supported by GeneralPSP class');
    }

    public function initCallback(PSPConfig $obj_PSPConfig, TxnInfo $obj_TxnInfo, $iStateID, $sStateName, $iCardid)
    {
        throw new BadMethodCallException('Method initCallback is not supported by GeneralPSP class');
    }

    public function getPaymentData(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, $mode = Constants::sPAYMENT_DATA_FULL)
    {
        throw new BadMethodCallException('Method getPaymentData is not supported by GeneralPSP class');
    }

    public function callback(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Card, SimpleXMLElement $obj_Status, $purchaseDate = NULL)
    {
        throw new BadMethodCallException('Method callback is not supported by GeneralPSP class');
    }

    public function processCallback(PSPConfig $obj_PSPConfig, SimpleXMLElement $obj_Request)
    {
        throw new BadMethodCallException('Method processCallback is not supported by GeneralPSP class');
    }

    public function getExternalPaymentMethods($sCards)
    {
        throw new BadMethodCallException('Method getExternalPaymentMethods is not supported by GeneralPSP class');
    }

    public function invoice($sMsg = "", $iAmount = -1)
    {
        throw new BadMethodCallException('Method invoice is not supported by GeneralPSP class');
    }

    public function postStatus($obj_Elem)
    {
        throw new BadMethodCallException('Method postStatus is not supported by GeneralPSP class');
    }

    public function getPaymentMethods(PSPConfig $obj_PSPConfig)
    {
        throw new BadMethodCallException('Method getPaymentMethods is not supported by GeneralPSP class');
    }

    public function authenticate($xml, $obj_Card, $obj_ClientInfo = NULL)
    {
        throw new BadMethodCallException('Method authenticate is not supported by GeneralPSP class');
    }

    public function notifyForeignExchange(array $aStateId, $aCI)
    {
        throw new BadMethodCallException('Method notifyForeignExchange is not supported by GeneralPSP class');
    }

    public function voidTransaction(int $amount, ?string $orderReference = null, ?string $orderReferenceIdentifier = null): array
    {
        try {
            $aMsgCds = [];

            global $_OBJ_TXT;
            global $aHTTP_CONN_INFO;

            $code = 0;
            $txnPassbookObj = TxnPassbook::Get($this->getDBConn(), $this->getTxnInfo()->getID(), $this->getTxnInfo()->getClientConfig()->getID());

            if (empty($orderReference) === TRUE) {
                $orderReference = '';
            }
            if (empty($orderReferenceIdentifier)) {
                $orderReferenceIdentifier = '';
            }

            $passbookEntry = new PassbookEntry
            (
                NULL,
                $amount,
                $this->getTxnInfo()->getCurrencyConfig()->getID(),
                Constants::iVoidRequested,
                $orderReference,
                $orderReferenceIdentifier
            );
            if ($txnPassbookObj instanceof TxnPassbook) {
                try {
                    $txnPassbookObj->addEntry($passbookEntry);
                    $codes = $txnPassbookObj->performPendingOperations($_OBJ_TXT, $aHTTP_CONN_INFO);
                    $code = reset($codes);
                }
                catch (Exception $e) {
                    trigger_error($e, E_USER_WARNING);
                }
            }
            $this->updateTxnInfoObject();
            // Refund operation succeeded
            if ($code === 1000 || $code === 1001) {
                $aMsgCds[$code] = "Success";
                // Perform callback to Client
                if ($this->getTxnInfo()->hasEitherState($this->getDBConn(), Constants::iPAYMENT_REFUNDED_STATE) === TRUE) {
                    if ($this->getTxnInfo()->getCallbackURL() != '') {
                        $args = ["transact" => $this->getTxnInfo()->getExternalID(),
                                 "amount"   => $_REQUEST['amount']];
                        parent::notifyClient(Constants::iPAYMENT_REFUNDED_STATE, $args, $this->getTxnInfo()->getClientConfig()->getSurePayConfig($this->getDBConn()));
                    }

                    parent::notifyForeignExchange([Constants::iPAYMENT_REFUNDED_STATE], $aHTTP_CONN_INFO['foreign-exchange']);
                } elseif ($this->getTxnInfo()->hasEitherState($this->getDBConn(), Constants::iPAYMENT_CANCELLED_STATE) === TRUE) {
                    parent::notifyForeignExchange([Constants::iPAYMENT_CANCELLED_STATE], $aHTTP_CONN_INFO['foreign-exchange']);
                }
            } elseif ($code === 1100) {
         //       header("HTTP/1.0 200 OK");
                $aMsgCds[$code] = "Success";
            } else {
           //     header("HTTP/1.0 502 Bad Gateway");

                $aMsgCds[999] = "Declined";
            }
        }
        catch (HTTPException $e) {
            //header("HTTP/1.0 502 Bad Gateway");

            $aMsgCds[998] = "Error while communicating with PSP";
        }
            // Internal Error
        catch (mPointException $e) {
            //header("HTTP/1.0 500 Internal Error");

            $aMsgCds[$e->getCode()] = $e->getMessage();
        }
        return $aMsgCds;
    }

    public function setTxnInfo(int $id): void
    {
        $this->updateTxnInfoObjectUsingId($id);
    }

}