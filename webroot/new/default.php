<?php
/**
 *
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Admin
 * @subpackage CreateAccount
 */

// Require include file for including all Shared and General APIs
require_once("../inc/include.php");

// Require Business logic for the using Ericsson IPX's WAP Identification API
require_once(sCLASS_PATH ."/ipx.php");

// Mobile Device
if (General::getBrowserType() == "mobile")
{
	// Instantiate data object with the User Agent Profile for the customer's mobile device.
	$_SESSION['obj_UA'] = UAProfile::produceUAProfile();
	if (array_key_exists("checksum", $_GET) === true) { $_SESSION['temp']['checksum'] = strtoupper($_GET['checksum']); }

	$obj_mPoint = new IPX("cellpoint", "KMs3M6rt36");
	// Initiate new user identification via Ericsson IPX's WAP Identification API 
	if ($_SESSION['obj_Info']->getInfo("ipx-session-id") === false)
	{
		$_SESSION['temp']['query_string'] = str_replace("&". session_name() ."=". session_id(), "", $_SERVER['QUERY_STRING']);
		$_SESSION['temp']['query_string'] = str_replace(session_name() ."=". session_id() ."&", "", $_SESSION['temp']['query_string']);
		$_SESSION['temp']['query_string'] = str_replace(session_name() ."=". session_id(), "", $_SESSION['temp']['query_string']);
		
		$obj_XML = $obj_mPoint->start("http://". $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] ."?". session_name() ."=". session_id() );
		$_SESSION['obj_Info']->setInfo("ipx-session-id", (string) $obj_XML->sessionId);
		
		header("Location: ". $obj_XML->redirectURL);
	}
	// Complete user identification via Ericsson IPX's WAP Identification API
	else
	{
		$obj_XML = $obj_mPoint->identify($_SESSION['obj_Info']->getInfo("ipx-session-id") );
		
		$_SESSION['obj_Info']->setInfo("countryid", $obj_mPoint->getCountryID( (string) $obj_XML->consumerId) );
		$_SESSION['obj_Info']->setInfo("mobile", $obj_mPoint->getMobile( (string) $obj_XML->consumerId) );
		$_SESSION['obj_Info']->delInfo("ipx-session-id");
		
		$msg = "";
		if ( floatval($_SESSION['obj_Info']->getInfo("mobile") ) == 0) { $msg .= "&msg=1"; }
		if (empty($_SESSION['temp']['query_string']) === false) { $msg .= "&". $_SESSION['temp']['query_string']; }
		
		header("Location: http://". $_SERVER['HTTP_HOST'] ."/new/step1.php?". session_name() ."=". session_id() . $msg);
		unset($_SESSION['temp']['query_string']);
	}
}
// Web Browser
else
{
	// Construct URL to load
	$sURL = "/new/step1.php";
	if (array_key_exists("checksum", $_GET) === true)
	{
		$sURL .= "?". "checksum=". $_GET['checksum'];
		$_SESSION['temp']['checksum'] = strtoupper($_GET['checksum']);
	}
	if (array_key_exists("email", $_GET) === true) { $_SESSION['temp']['email'] = strtolower($_GET['email']); }
	
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/ajax/default.xsl"?>';
	?>
	<root>
		<title><?= $_OBJ_TXT->_("mPoint"); ?></title>
		<unsupported><?= $_OBJ_TXT->_("Unsupported Browser"); ?></unsupported>
		<url><?= htmlspecialchars($sURL, ENT_NOQUOTES); ?></url>
	</root>
<?php
}
?>