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

    $sql = "SELECT id from log".sSCHEMA_POSTFIX.".Transaction_tbl where orderid ='".$OrderNo."'";
    $res = $_OBJ_DB->query($sql);
    $iStatusId = 0;

    while ($RS = $_OBJ_DB->fetchName($res))
    {
        $txnId = (int)$RS['ID'];
        $sqlA = "SELECT name, value FROM log" . sSCHEMA_POSTFIX . ".additional_data_tbl WHERE type='Transaction' and externalid=" . $txnId." and value = '".$externalId."'";
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