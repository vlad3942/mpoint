<?php
/**
 * This files contains the Controller for the SurePay component which monitors all customers in order to ensure that
 * they have successfully activated the Payment Link.
 * The controller will perform the following tasks:
 * 	- Find all Customers who haven't activated the Payment Link
 * 	- Re-Send the Payment Link embedded in an SMS for customers who have not yet activated the original link
 *	- Re-Send the Payment Link as a WAP Push for customers who have not yet activated either the original or the secondary link
 * 	- Notify Customer Service via e-mail if a customer fails to activate any of the Payment Links sent
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package SurePay
 * @version 1.10
 */

// Require Global Include File
require_once("../inc/include.php");

// Require the PHP API for handling the connection to GoMobile
require_once(sAPI_CLASS_PATH ."/gomobile.php");

// Require general Business logic for the SurePay module
require_once(sCLASS_PATH ."/surepay.php");
// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require Business logic for the E-Mail Receipt Component
require_once(sCLASS_PATH ."/email_receipt.php");

header("Content-Type: text/plain");

// Re-Send Payment link embedded in an SMS
echo "========== ". date("Y-m-d H:i:s") ." ==========" ."\n";
$aObj_mPoints = SurePay::produceSurePays($_OBJ_DB, 1);
for ($i=0; $i<count($aObj_mPoints); $i++)
{
	$aObj_mPoints[$i]->sendEmbeddedLink(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO), $aObj_mPoints[$i]->getTxnInfo(), $aObj_mPoints[$i]->getLink() );
	echo "Txn: ". $aObj_mPoints[$i]->getTxnInfo()->getID() .", Link: ". $aObj_mPoints[$i]->getLink() .", Action: Re-sent as embedded link" ."\n";
}

// Re-Send Payment link 2nd time as a WAP Push
$aObj_mPoints = SurePay::produceSurePays($_OBJ_DB, 2);
for ($i=0; $i<count($aObj_mPoints); $i++)
{
	$aObj_mPoints[$i]->sendLink(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO), $aObj_mPoints[$i]->getTxnInfo(), $aObj_mPoints[$i]->getLink() );
	echo "Txn: ". $aObj_mPoints[$i]->getTxnInfo()->getID() .", Link: ". $aObj_mPoints[$i]->getLink() .", Action: Re-sent as WAP Push" ."\n";
}

// Notify Customer Service via E-Mail
$aObj_mPoints = SurePay::produceSurePays($_OBJ_DB, 3);
for ($i=0; $i<count($aObj_mPoints); $i++)
{
	$aObj_mPoints[$i]->notifyClient();
	echo "Txn: ". $aObj_mPoints[$i]->getTxnInfo()->getID() .", Link: ". $aObj_mPoints[$i]->getLink() .", Action: Notified Client" ."\n";
}
echo "\n";
?>