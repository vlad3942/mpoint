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
 * @package Payment
 * @subpackage Accept
 * @version 1.10
 */

// Require Global Include File
require_once("../include.php");

// Require Business logic for the Payment Accepted component
require_once(sCLASS_PATH ."/accept.php");

// Re-initialization of Session required
if (array_key_exists("mpoint-id", $_REQUEST) === true
	&& (array_key_exists("obj_TxnInfo", $_SESSION) === false || ($_SESSION['obj_TxnInfo'] instanceof TxnInfo) === false) )
{
	$_SESSION['obj_TxnInfo'] = TxnInfo::produceInfo($_REQUEST['mpoint-id'], $_OBJ_DB);
}

$obj_mPoint = new Accept($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_UA']);

$xml = '<?xml version="1.0" encoding="UTF-8"?>';
$xml .= '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/'. General::getMarkupLanguage($_SESSION['obj_UA'], $_SESSION['obj_TxnInfo']) .'/pay/re-enter.xsl"?>';

$xml .= '<root>';
$xml .=  '<title>'.$_OBJ_TXT->_("Payment Already Completed").'</title>';
	
$xml .= $obj_mPoint->getSystemInfo(); 
	
$xml .= $_SESSION['obj_TxnInfo']->getClientConfig()->toXML(); 

$xml .= $_SESSION['obj_TxnInfo']->toXML($_SESSION['obj_UA']); 

$xml .= $obj_mPoint->getClientVars($_SESSION['obj_TxnInfo']->getID() ); 

$xml .= '<labels>';
$xml .= '		<status>'.$_OBJ_TXT->_("Status - Payment Already Completed").'</status>';
$xml .= '		<txnid>'.$_OBJ_TXT->_("mPoint ID").'</txnid>';
$xml .= '		<orderid>'.$_OBJ_TXT->_("Order No").'</orderid>';
$xml .= '		<price>'.$_OBJ_TXT->_("Price").'</price>';
$xml .= '		<note>'.$_OBJ_TXT->_("Note - Payment Already Completed").'</note>';
$xml .= '		<continue>'.htmlspecialchars($_OBJ_TXT->_("Continue >>") ).'</continue>';
$xml .= '	</labels>';
$xml .= '</root>';

echo $xml;
exit;