<?php
/**
 * This file contains the Controller for mPoint's Payment Completed compont.
 * The component will generate a page using the Client Configuration providing Post Payment options:
 * 	- Send E-Mail Receipt
 * 	- Go to Client's Accept URL
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package NetAxept
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

file_put_contents(sLOG_PATH ."/error.log", "\n". "I'M HERE! ", FILE_APPEND  | LOCK_EX );

// we need to all auth in this case
$obj_TxnInfo = TxnInfo::produceInfo($_GET['mpoint-id'] , $_OBJ_DB);
$_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH . $obj_TxnInfo->getLanguage() ."/global.txt", sLANGUAGE_PATH . $obj_TxnInfo->getLanguage() ."/custom.txt"), sSYSTEM_PATH, 0, "UTF-8");

$obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), Constants::iPAYEX_PSP); 
$aHTTP_CONN_INFO["payex"]["username"] = $obj_PSPConfig->getUsername();
$aHTTP_CONN_INFO["payex"]["password"] = $obj_PSPConfig->getPassword();
$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["payex"]);

$obj_mPoint = new Payex($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo);

$obj_XML = $obj_mPoint->complete($obj_ConnInfo, $obj_PSPConfig->getMerchantAccount(), $_GET['transactionId']);

file_put_contents(sLOG_PATH ."/error.log", "\n". "accept : ".var_export($obj_XML, true), FILE_APPEND  | LOCK_EX );
	


echo '<?xml version="1.0" encoding="UTF-8"?>';
	
?>
<root>
	<status code="<?php echo $statuscode; ?>"><?php echo $message; ?></status>
</root>
