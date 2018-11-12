<?php
/**
 * SDK sends in post-status request. This endpoint after detection will invoke general.php with specific error code.
 *
 * @author Arvind Halgekar
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package API
 */

// Require Global Include File
require_once("../../inc/include.php");

// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");
// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require specific Business logic for the CPM PSP component
require_once(sINTERFACE_PATH ."/cpm_psp.php");
// Require specific Business logic for the 2c2p alc component
require_once(sCLASS_PATH ."/ccpp_alc.php");
// Require specific Business logic for the WireCard component
require_once(sCLASS_PATH ."/wirecard.php");
$aMsgCds = array();
/*
$_SERVER['PHP_AUTH_USER'] = "MalindoDemo";
$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA = '<root>';
$HTTP_RAW_POST_DATA = '<callback>';
$HTTP_RAW_POST_DATA = '<psp-config id="25" >';
$HTTP_RAW_POST_DATA = '<name>wirecard</name>';
$HTTP_RAW_POST_DATA = '<transaction id="1986311" order-no="HYHJXT">';
$HTTP_RAW_POST_DATA = '<amount country-id="603" currency="INR" />';
$HTTP_RAW_POST_DATA = '<card type-id="8" />';
$HTTP_RAW_POST_DATA = '</transaction>';
$HTTP_RAW_POST_DATA = '<status code="20109" />';
$HTTP_RAW_POST_DATA = '</callback>';
$HTTP_RAW_POST_DATA = '</root>';
*/
$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);
if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
	if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mpoint.xsd") === true && count($obj_DOM->{'callback'}) > 0)
	{
		$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);
		$xml = '';

		for ($i=0; $i<count($obj_DOM->{'callback'}); $i++)
		{
            $obj_Elem = $obj_DOM->{'callback'}[$i];
            if (intval($obj_Elem->{'psp-config'}['id']) > 0) {
                $obj_TxnInfo = TxnInfo::produceInfo($obj_Elem->transaction["id"], $_OBJ_DB);
                $obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), intval($obj_Elem->{'psp-config'}['id']));
                $obj_PSP = Callback::producePSP($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO, $obj_PSPConfig);
                $code = $obj_PSP->postStatus($obj_Elem);
            }
		}
	}
	// Error: Invalid XML Document
	elseif ( ($obj_DOM instanceof SimpleDOMElement) === false)
	{
		header("HTTP/1.1 415 Unsupported Media Type");
		
		$xml = '<status code="415">Invalid XML Document</status>';
	}
	// Error: Wrong operation
	elseif (count($obj_DOM->{'callback'}) == 0)
	{
		header("HTTP/1.1 400 Bad Request");
	
		$xml = '';
		foreach ($obj_DOM->children() as $obj_Elem)
		{
			$xml .= '<status code="400">Wrong operation: '. $obj_Elem->getName() .'</status>'; 
		}
	}
	// Error: Invalid Input
	else
	{
		header("HTTP/1.1 400 Bad Request");
		$aObj_Errs = libxml_get_errors();
		
		$xml = '';
		for ($i=0; $i<count($aObj_Errs); $i++)
		{
			$xml .= '<status code="400">'. htmlspecialchars($aObj_Errs[$i]->message, ENT_NOQUOTES) .'</status>';
		}
	}
}
else
{
	header("HTTP/1.1 401 Unauthorized");
	
	$xml = '<status code="401">Authorization required</status>';
}
header("Content-Type: text/xml; charset=\"UTF-8\"");

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<root>';
echo $xml;
echo '</root>';
?>