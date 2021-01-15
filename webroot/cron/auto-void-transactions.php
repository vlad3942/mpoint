<?php

if (PHP_SAPI == "cli") {
    if ($argc < 3) {
        echo "Expected 2 arguments, but got " . ($argc - 1) . PHP_EOL;
        echo "Syntax : php auto-void-transactions.php <ClientId> <mPointHost> <optional : PSPId>" . PHP_EOL;
        die();
    }

    if ($argc === 4) {
        [$filePath, $mPointHost, $clientid, $pspid] = $argv;
    } else {
        [$filePath, $mPointHost, $clientid] = $argv;
        $pspid = NULL;
    }
    $_SERVER['HTTP_HOST'] = getenv('MPOINT_HOST');
    $_SERVER['DOCUMENT_ROOT'] = getenv('DOCUMENT_ROOT','/opt/cpm/mPoint/webroot');
} else {
    $clientid = $_REQUEST['clientid'];
    $pspid = $_REQUEST['pspid'];
}

ini_set('max_execution_time', 1200);
include $_SERVER['DOCUMENT_ROOT'].'/cron/cron-include.php';
// <editor-fold defaultstate="collapsed" desc="Required dependancies">
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/include.php');
require_once(sAPI_CLASS_PATH . "/gomobile.php");
require_once(sAPI_CLASS_PATH . "simpledom.php");
require_once(sCLASS_PATH . "/pspconfig.php");
require_once(sCLASS_PATH . "/validate.php");
require_once(sCLASS_PATH . "/enduser_account.php");
require_once(sCLASS_PATH . "/credit_card.php");
require_once(sCLASS_PATH . "/customer_info.php");
require_once(sCLASS_PATH . "/callback.php");
require_once(sINTERFACE_PATH . "/cpm_psp.php");
require_once(sINTERFACE_PATH . "/cpm_acquirer.php");
require_once(sINTERFACE_PATH . "/cpm_gateway.php");
require_once(sCLASS_PATH . "/cpm.php");
require_once(sCLASS_PATH . "/wannafind.php");
require_once(sCLASS_PATH . "/netaxept.php");
require_once(sCLASS_PATH . "/worldpay.php");
require_once(sCLASS_PATH . "/cpg.php");
require_once(sCLASS_PATH . "/dsb.php");
require_once(sCLASS_PATH . "/visacheckout.php");
require_once(sCLASS_PATH . "/stripe.php");
require_once(sCLASS_PATH . "/adyen.php");
require_once(sCLASS_PATH . "/applepay.php");
require_once(sCLASS_PATH . "/datacash.php");
require_once(sCLASS_PATH . "/mada_mpgs.php");
require_once(sCLASS_PATH . "/masterpass.php");
require_once(sCLASS_PATH . "/amexexpresscheckout.php");
require_once(sCLASS_PATH . "/wirecard.php");
require_once(sCLASS_PATH . "/globalcollect.php");
require_once(sCLASS_PATH . "/androidpay.php");
require_once(sCLASS_PATH . "/securetrading.php");
require_once(sCLASS_PATH . "/payfort.php");
require_once(sCLASS_PATH . "/paypal.php");
require_once(sCLASS_PATH . "/ccavenue.php");
require_once(sCLASS_PATH . "/ccpp.php");
require_once(sCLASS_PATH . "/maybank.php");
require_once(sCLASS_PATH . "/publicbank.php");
require_once(sCLASS_PATH . "/mobilepayonline.php");
require_once(sCLASS_PATH . "/klarna.php");
require_once(sCLASS_PATH . "/clientinfo.php");
require_once(sCLASS_PATH . "/nets.php");
require_once(sCLASS_PATH . "/ccpp_alc.php");
require_once(sCLASS_PATH . "/ppro.php");
require_once(sCLASS_PATH . "/amex.php");
require_once(sCLASS_PATH . "/chubb.php");
require_once(sCLASS_PATH . "/payment_processor.php");
require_once(sCLASS_PATH . "/uatp.php");
require_once(sCLASS_PATH . "/uatp_card_account.php");
require_once(sCLASS_PATH . "/chase.php");
require_once(sCLASS_PATH . "/payu.php");
require_once(sCLASS_PATH . "/cielo.php");
require_once(sCLASS_PATH . "/cellulant.php");
require_once(sCLASS_PATH . "/global-payments.php");
require_once(sCLASS_PATH . "/cybersource.php");
require_once(sCLASS_PATH . "/psp/veritrans4g.php");
require_once(sCLASS_PATH . "/aggregator/dragonpay.php");
require_once(sCLASS_PATH . "/apm/swish.php");
require_once(sCLASS_PATH . "/first-data.php");
require_once sCLASS_PATH . '/txn_passbook.php';
require_once sCLASS_PATH . '/passbookentry.php';
require_once(sCLASS_PATH . '/apm/paymaya.php');
require_once(sCLASS_PATH . '/GeneralPSP.php');
// </editor-fold>

global $_OBJ_DB;
global $aHTTP_CONN_INFO;

$xml ='<?xml version="1.0" encoding="UTF-8"?><root>';

$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, $clientid);
if ($obj_ClientConfig !== NULL)
{
    $obj_Home = new Home($_OBJ_DB, $_OBJ_TXT);
    $_aConfig = $obj_Home->getAutoVoidConfig($clientid, $pspid);
    foreach ($_aConfig as $config) {

        $aTransactionId = $obj_Home->getOrphanAuthorizedTransactionList($clientid, $config['EXPIRY'], $config['PSPID']);
        if (count($aTransactionId) > 0) {
            $transactionId = $aTransactionId[0]['ID'];
            $objTxnInfo = TxnInfo::produceInfo($transactionId, $_OBJ_DB);
            $objGeneralPSP = new GeneralPSP($_OBJ_DB, $_OBJ_TXT, $objTxnInfo, $aHTTP_CONN_INFO, NULL, NULL);

            foreach ($aTransactionId as $transactionId) {
                $objGeneralPSP->setTxnInfo($transactionId['ID']);

                $externalRefCancelStatus = 100;

                // Cancel SUVTP
                $externalRef = $objTxnInfo->getExternalRef(50,50);
                if(empty($externalRef) === FALSE)
                {
                    $obj_uatpPSP = PaymentProcessor::produceConfig($_OBJ_DB, $_OBJ_TXT, $objTxnInfo, 50, $aHTTP_CONN_INFO);
                    $externalRefCancelStatus = $obj_uatpPSP->cancel();
                }

                if($externalRefCancelStatus === 100)
                {
                    $response = $objGeneralPSP->voidTransaction($objGeneralPSP->getTxnInfo()->getAmount(), 'Void triggered from Auto Void Cron');
                    $statusCode = array_key_first($response);
                    $xml .= "<transaction id ='".$transactionId['ID']."'><status code='$statusCode'>". $response[$statusCode] ."</status></transaction>";
                }
                else
                {
                    $xml .= "<transaction id ='".$transactionId['ID']."'><status code='$externalRefCancelStatus'>Unable to cancel transaction for PSP : 50 </status></transaction>";
                }

            }

        }
    }
}
else {
    header("HTTP/1.1 400 Bad Request");
        $xml = '';
        $xml .= '<status code="400">Invalid Client id : '.$clientid.'</status>';
}

header("Content-Type: text/xml; charset=\"UTF-8\"");
echo $xml;
echo '</root>';