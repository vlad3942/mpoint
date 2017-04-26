<?php
/**
 * This file contains the Controller for mPoint's Administration API.
* The Controller will ensure that all input is validated and the desired country is updated.
* If the input provided was determined to be invalid, an error status will be returned.
*
* @author Manish Dewani
* @copyright Cellpoint Mobile
* @link http://www.cellpointmobile.com
* @package API
* @subpackage Admin
* @version 1.10
*/

// Require Global Include File
require_once("../../inc/include.php");

// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");

// Require Business logic for the Failed Transactions for Status Input
require_once(sCLASS_PATH ."/admin.php");

// Require Business logic for the mConsole Module
require_once(sCLASS_PATH ."/mConsole.php");


// Require data data class for Transaction Log Information
require_once(sCLASS_PATH ."/transaction_log_info.php");
// Require data data class for Customer Information
require_once(sCLASS_PATH ."/customer_info.php");
// Require data data class for Message Information about each State a Transaction has passed through
require_once(sCLASS_PATH ."/message_info.php");
// Require data data class for Information about an Amount
require_once(sCLASS_PATH ."/amount_info.php");


// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );

$_SERVER['PHP_AUTH_USER'] = "CPMDemo";
$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<get-failed-transactions>';
$HTTP_RAW_POST_DATA .= '<clients>';
$HTTP_RAW_POST_DATA .= '<client-id>10007</client-id>';
$HTTP_RAW_POST_DATA .= '<client-id>10001</client-id>';
$HTTP_RAW_POST_DATA .= '</clients>';
$HTTP_RAW_POST_DATA .= '<transaction>';
$HTTP_RAW_POST_DATA .= '<customer>';
$HTTP_RAW_POST_DATA .= '<mobile country-id="100">28882861</mobile>';
$HTTP_RAW_POST_DATA .= '<email>jona@oismail.com</email>';
$HTTP_RAW_POST_DATA .= '</customer>';
$HTTP_RAW_POST_DATA .= '</transaction>';
$HTTP_RAW_POST_DATA .= '</get-failed-transactions>';
$HTTP_RAW_POST_DATA .= '</root>';

$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

$obj_mPoint = new mConsole($_OBJ_DB, $_OBJ_TXT);

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
	$xml = "<failed-transactions>";
	
	if ( ($obj_DOM instanceof SimpleDOMElement) === true && count($obj_DOM->{'get-failed-transactions'}->clients->{'client-id'}) > 0)
	{
		$clients = array();
		$aStateIDs = array('1001','1009');


		for($i = 0;$i<count($obj_DOM->{'get-failed-transactions'}->clients->{'client-id'});$i++)
		{
			$clients[] .= $obj_DOM->{'get-failed-transactions'}->clients->{'client-id'}[$i];
		}


		$aObj_Logs = $obj_mPoint->getFailedTransactions($clients, $aStateIDs);
		foreach ($aObj_Logs as $obj_Log)
		{
			$xml .= $obj_Log->toXML();
		}
		
	$xml .="</failed-transactions>";


	}

}

header("Content-Type: text/xml; charset=\"UTF-8\"");

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<root>';
echo $xml;
echo '</root>';
?>