<?php
/**
 * This file contains the Controller for mPoint's Payment Completed componentt.
 * The component will generate a page using the Client Configuration providing Post Payment options:
 * 	- Send E-Mail Receipt
 * 	- Go to Client's Accept URL
 *
 * @author Jonatan Evald Buus, Allan Ray Jasa
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package PayEx
 * @subpackage Accept
 * @version 1.00
 */

// Require Global Include File
require_once("../inc/include.php");
//define("sLOG_PATH", "/var/log/cpm/mPoint");
// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require specific Business logic for the PayEx component
require_once(sCLASS_PATH ."/payex.php");
// Require data class for Payment Service Provider Configurations
require_once(sCLASS_PATH ."/pspconfig.php");

$id = PayEx::getIDFromExternalID($_OBJ_DB, $_GET['transactionNumber']);
if ($id < 0) { $id = PayEx::getIDFromExternalID($_OBJ_DB, $_GET['orderRef']); }
$obj_TxnInfo = TxnInfo::produceInfo($id, $_OBJ_DB);
$obj_mPoint = new PayEx($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo);

$obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getAccountID(), Constants::iPAYEX_PSP); 
if ($obj_TxnInfo->getMode() > 0) { $aHTTP_CONN_INFO["payex"]["host"] = str_replace("external.", "test-external.", $aHTTP_CONN_INFO["payex"]["host"]); }
$aHTTP_CONN_INFO["payex"]["username"] = $obj_PSPConfig->getUsername();
$aHTTP_CONN_INFO["payex"]["password"] = $obj_PSPConfig->getPassword();
$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["payex"]);

$obj_XML = $obj_mPoint->complete($obj_ConnInfo, $obj_PSPConfig->getMerchantAccount(), $_GET['orderRef'] );

$statuscode = (string) $obj_XML->status[0]['code'];
switch ($statuscode) 
{
	case '2000':
		$message = 'Authorized';
		break;
	case '2001':
		$message = 'Capture';
		break;
	case '2010':
		$message = 'Declined';
		break;
	default:
		$message = 'Unknown Authorization Error';
		break;
}

echo '<?xml version="1.0" encoding="UTF-8"?>';
	
?>
<root>
	<status code="<?php echo $statuscode; ?>"><?php echo $message; ?></status>
</root>
