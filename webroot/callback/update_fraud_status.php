<?php
/**
 *
 * @author Anna Lagad
 * @copyright Cellpoint Digital
 * @link http://www.cellpointdigital.com
 * @package mConsole
 * @version 1.0
 */

// Require Global Include File
require_once("../inc/include.php");
// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");
// Require Business logic for General Administration of mPoint
require_once(sCLASS_PATH ."admin.php");
// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
// Require data class for Payment Service Provider Configurations
require_once(sCLASS_PATH ."/pspconfig.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require Business logic for the Mobile Web module
require_once(sCLASS_PATH ."/mobile_web.php");
// Require Business logic for the Select Credit Card component
require_once(sCLASS_PATH ."/credit_card.php");

// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require specific Business logic for the CPM PSP component
require_once(sINTERFACE_PATH ."/cpm_psp.php");
// Require specific Business logic for the CPM ACQUIRER component
require_once(sINTERFACE_PATH ."/cpm_acquirer.php");
// Require specific Business logic for the CPM GATEWAY component
require_once(sINTERFACE_PATH ."/cpm_gateway.php");
// Require specific Business logic for the DIBS component
require_once(sCLASS_PATH ."/dibs.php");
// Require specific Business logic for the PayEx component
require_once(sCLASS_PATH ."/payex.php");
// Require specific Business logic for the NetAxept component
require_once(sCLASS_PATH ."/netaxept.php");
// Require specific Business logic for the MobilePay component
require_once(sCLASS_PATH ."/mobilepay.php");
// Require specific Business logic for the WireCard component
require_once(sCLASS_PATH ."/wirecard.php");
// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
// Require specific Business logic for the Nets component
require_once(sCLASS_PATH ."/nets.php");
// Require specific Business logic for the mVault component
require_once(sCLASS_PATH ."/mvault.php");
// Require specific Business logic for the Amex component
require_once(sCLASS_PATH ."/amex.php");
// Require specific Business logic for the CHUBB component
require_once(sCLASS_PATH ."/chubb.php");
// Require Data Class for Client Information
require_once(sCLASS_PATH ."/clientinfo.php");
// Require specific Business logic for the UATP component
require_once(sCLASS_PATH . "/uatp.php");
// Require specific Business logic for the Chase component
require_once(sCLASS_PATH ."/chase.php");

require_once(sCLASS_PATH ."/payment_processor.php");
require_once(sCLASS_PATH ."/wallet_processor.php");
require_once(sCLASS_PATH ."/voucher/TravelFund.php");

/*
$_SERVER['PHP_AUTH_USER'] = "CPMDemo";
$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<update-fraud-status>';
$HTTP_RAW_POST_DATA .= '<client-id>123</client-id>';
$HTTP_RAW_POST_DATA .= '<transaction-id>123</transaction-id>';
$HTTP_RAW_POST_DATA .= '<status_id>3117</status_id>';
$HTTP_RAW_POST_DATA .= '<comment>hdfy28abdl</comment>';
$HTTP_RAW_POST_DATA .= '</update-fraud-status>';
$HTTP_RAW_POST_DATA .= '</root>';
*/
$obj_DOM = simpledom_load_string(file_get_contents('php://input'));

    $OrderNo = (string) $obj_DOM->{'order_no'};
    $externalId = (string) $obj_DOM->{'externalId'};
    $StatusId = (string) $obj_DOM->{'status_id'};
    $sComment = (string) $obj_DOM->comment;
    $obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);

    $sql = "SELECT id, created from log".sSCHEMA_POSTFIX.".Transaction_tbl where";
    if(empty($obj_DOM->{'transaction_id'}) === false)
    { $sql .= " id =".$obj_DOM->{'transaction_id'}.""; }
    else
    {
        $sql .= " orderid ='".$OrderNo."'";
    }
    $res = $_OBJ_DB->query($sql);
    $iStatusId = 0;

    while ($RS = $_OBJ_DB->fetchName($res))
    {
        $txnId = (int)$RS['ID'];
        $sqlA = "SELECT name, value FROM log" . sSCHEMA_POSTFIX . ".additional_data_tbl WHERE type='Transaction' and created >= '" . $RS["CREATED"]  . "'::timestamp  - interval '60 seconds' and externalid=" . $txnId." and value = '".$externalId."'";
        $rsa = $_OBJ_DB->getAllNames ( $sqlA );
        if (empty($rsa) === false )
        {
            foreach ($rsa as $rs)
            {
              if($rs['NAME'] === 'pre_auth_ext_id') { $iStatusId = (int) '30'.$StatusId; }
              else if($rs['NAME'] === 'post_auth_ext_id') { $iStatusId = (int) '31'.$StatusId; }
              break;
            }
        }
        if($iStatusId>0)
        {
            $obj_mPoint->newMessage($txnId, $iStatusId, file_get_contents('php://input'));

            $obj_TxnInfo = TxnInfo::produceInfo( $txnId, $_OBJ_DB);
            $obj_Processor = PaymentProcessor::produceConfig($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $obj_TxnInfo->getPSPID(), $aHTTP_CONN_INFO);
            $args = array('amount'=>$obj_TxnInfo->getAmount(),
                'transact'=>$externalId,
                'cardid'=>$obj_TxnInfo->getCardID());
            $obj_Processor->notifyClient($iStatusId, $args, $obj_TxnInfo->getClientConfig()->getSurePayConfig($_OBJ_DB));

            break;
        }
    }

    if($iStatusId ===0) header("HTTP/1.1 400 Bad Request");


header("Content-Type: text/xml; charset=\"UTF-8\"");

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<root>';
echo $xml;
echo '</root>';
?>