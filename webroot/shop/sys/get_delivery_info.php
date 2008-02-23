<?php
/**
 * This files contains the Controller for doing an Address Lookup using the provided MSISDN.
 * The file will ensure that the provided MSISDN is valid in country and make a lookup using the Address Lookup Service available in the Country.
 * 
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Shop
 * @subpackage Delivery
 * @version 1.0
 */

// Require Global Include File
require_once("../../inc/include.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
// Require Business logic for the Delivery Component
require_once(sCLASS_PATH ."/delivery.php");

$_SESSION['temp'] = array_merge($_SESSION['temp'], $_POST);

$obj_mPoint = new Delivery($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo'], HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO[$_SESSION['obj_TxnInfo']->getClientConfig()->getCountryConfig()->getID()]) );
$obj_Validator = new Validate($_SESSION['obj_TxnInfo']->getClientConfig() );

$aMsgCds = array();
	
if ($obj_Validator->valAddress($_POST['address']) != 10) { $aMsgCds[] = $obj_mPoint->valAddress($_POST['address']) + 10; }

// Success: Input Valid
if (count($aMsgCds) == 0)
{
	$aDeliveryAddress = $obj_mPoint->getDeliveryAddressFromMSISDN($_POST['address']);
	var_dump($_SESSION['temp']);
	// Address found
	if (count($aDeliveryAddress) > 0)
	{
		$_SESSION['temp'] = array_merge($_SESSION['temp'], $aDeliveryAddress);	
	}
	// Unable to find Address using the provided MSISDN
	else
	{
		$aMsgCds[] = 19;
		// Remove old Session Data
		unset($_SESSION['temp']['name'], $_SESSION['temp']['company'], $_SESSION['temp']['street'], $_SESSION['temp']['zipcode'], $_SESSION['temp']['city']);
	}
}
// Error occurred
if (count($aMsgCds) > 0)
{
	$msg = "&msg=". $aMsgCds[0];
}
else { $msg = ""; }

header("content-type: text/plain");
header("content-length: 0");

header("location: http://". $_SERVER['HTTP_HOST'] ."/shop/delivery.php?". session_name() ."=". session_id() . $msg);
?>