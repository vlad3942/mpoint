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
require_once("../inc/include.php");

// Require Business logic for the Payment Accepted component
require_once(sCLASS_PATH ."/accept.php");

header("Cache-Control: no-cache, no-store, must-revalidate");	// HTTP 1.1.
header("Pragma: no-cache"); 									// HTTP 1.0.
header("Expires: 0");											// Proxies

// Re-initialization of Session required
if (array_key_exists("mpoint-id", $_REQUEST) === true
	&& (array_key_exists("obj_TxnInfo", $_SESSION) === false || ($_SESSION['obj_TxnInfo'] instanceof TxnInfo) === false) )
{
	$_SESSION['obj_TxnInfo'] = TxnInfo::produceInfo($_REQUEST['mpoint-id'], $_OBJ_DB);
}
//print_r($_SESSION['obj_TxnInfo']);exit;
// User is re-entering the payment flow
//echo "<pre>";print_r($_SESSION['obj_TxnInfo']);exit;
/* if ($_SESSION['obj_Info']->getInfo("payment-completed") === true)
{
	header("Location: /pay/re-enter.php?". session_name() ."=". session_id() ."&mpoint-id=". $_SESSION['obj_TxnInfo']->getID() );
}
else
{ */
	$obj_mPoint = new Accept($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_UA']);
	
	$_SESSION['obj_Info']->setInfo("payment-completed", true);
	
	$xml = '<?xml version="1.0" encoding="UTF-8"?>';
	$xml .= '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/'. General::getMarkupLanguage($_SESSION['obj_UA'], $_SESSION['obj_TxnInfo']) .'/pay/accept.xsl"?>';

	$xml .= '<root>';
		$xml .= '<title>'.$_OBJ_TXT->_("Payment Completed").'</title>';
	
		$xml .= $obj_mPoint->getSystemInfo();
	
		$xml .= $_SESSION['obj_TxnInfo']->getClientConfig()->toXML();
	
		//$xml .= $_SESSION['obj_TxnInfo']->toXML($_SESSION['obj_UA']);
	
		$xml .= $obj_mPoint->getmPointLogoInfo();
	
		$xml .= $obj_mPoint->getClientVars($_SESSION['obj_TxnInfo']->getID() );
	
		$xml .='<labels>
			<mpoint>'.$_OBJ_TXT->_("Thank you for using").'</mpoint>
			<status>'.$_OBJ_TXT->_("Status - Success").'</status>
			<txnid>'.$_OBJ_TXT->_("mPoint ID").'</txnid>
			<orderid>'.$_OBJ_TXT->_("Order No").'</orderid>
			<price>'.$_OBJ_TXT->_("Price").'</price>
			<sms-receipt>'.str_replace("{MOBILE}", $_SESSION['obj_TxnInfo']->getMobile(), $_OBJ_TXT->_("SMS Receipt - Info") ).'</sms-receipt>
			<email-receipt>'.$_OBJ_TXT->_("Send receipt via E-Mail").'</email-receipt>
			<continue>'.htmlspecialchars($_OBJ_TXT->_("Continue >>") ).'</continue>
			<resume>'.htmlspecialchars($_OBJ_TXT->_("Resume Payment >>") ).'</resume>
		</labels>';

		// Current transaction is an Account Top-Up and a previous transaction is in progress
		if ($_SESSION['obj_TxnInfo']->getTypeID() >= 100 && $_SESSION['obj_TxnInfo']->getTypeID() <= 109 && array_key_exists("obj_OrgTxnInfo", $_SESSION) === true)
		{
			$xml .= '<original-transaction-id>'. $_SESSION['obj_OrgTxnInfo']->getID() .'</original-transaction-id>';
		}
	
		$xml .= $obj_mPoint->getMessages("Accept");
		$xml .= '<transactionstatus>'.$_REQUEST['transactionStatus'].'</transactionstatus>';
		$xml .= '<transactionid>'.$_REQUEST['mpoint-id'].'</transactionid>';
		if($_SESSION['obj_TxnInfo']->getCSSURL()=="")
		{
			$cssurll="http://". $_SERVER["HTTP_HOST"] ."/css/bootstrap/styles.css";
		}
		else 
		{
			$cssurll=$_SESSION['obj_TxnInfo']->getCSSURL();
		}
		
		
		
		if($_SESSION['obj_TxnInfo']->getACCEPTURL()=="")
		{
			$accept = "#";
		}
		else
		{
			$accept = $_SESSION['obj_TxnInfo']->getACCEPTURL();
		}
		
		if($_SESSION['obj_TxnInfo']->getCSSURL()=="")
		{
			$cancel = "#";
		}
		else
		{
			$cancel = $_SESSION['obj_TxnInfo']->getCANCELURL();
		}


		$xml .= '<cssurl>'.$cssurll.'</cssurl>';
		$xml .= '<accepturl>'.$accept.'</accepturl>';
		$xml .= '<cancelurl>'.$cancel.'</cancelurl>';
	$xml .= '</root>';
	
	file_put_contents(sLOG_PATH ."/debug_accept". date("Y-m-d") .".log", $xml);
	
	echo $xml;
	exit;

//}
?>