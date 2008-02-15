<?php
/**
 * This files contains the Controller for fetching and image and resize it to fit the screen resolution
 * of the customer's Mobile Device using its User Agent Profile.
 * Additionally the image will be converted into a format supported by the device in the following order:
 * 	1. JPEG
 * 	2. PNG
 * 	3. GIF
 * The Controller currently provides access to the following images:
 * 	- Client Logo
 * 	- Credit Card Logos
 * 	- mPoint Logo
 * 
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Payment
 * @subpackage Images
 * @version 1.0
 */

// Retrieve Session ID from Image URL
$_REQUEST[session_name()] = substr($_SERVER['REDIRECT_URL'], strrpos($_SERVER['REDIRECT_URL'], "_")+1);
$_REQUEST[session_name()] = substr($_REQUEST[session_name()], 0, strlen($_REQUEST[session_name()])-4);

// Require Global Include File
require_once("../inc/include.php");

// Require Business logic for the Image Component
require_once(sCLASS_PATH ."/retrieve_image.php");

// Re-Create the URL
$url = General::rebuildURL();

$obj_mPoint = new RetrieveImage($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_UA']);

// Determine Image type to be retrieved
switch (true)
{
case (strstr($url, "client") ):	// Retrieve Client Logo
	// Client logo not previously returned
	if ($_SESSION['obj_Info']->getInfo("client_logo") === false)
	{
		$obj_Image = $obj_mPoint->getClientLogo($_SESSION['obj_TxnInfo']->getLogoURL() );
	}
	$etag = "client";
	break;
case (strstr($url, "product") ):
	$etag = "product";
	break;
case (strstr($url, "card") ):	// Retrieve Credit Card Logo
	$aTmp = explode("_", $url);
	$id = $aTmp[count($aTmp)-2];
	// Credit Card logo not previously returned
	if ($_SESSION['obj_Info']->getInfo("card_". $id ."_logo") === false)
	{
		$obj_Image = $obj_mPoint->getCardLogo($id);
	}
	$etag = "card_". $id;
	break;
case (strstr($url, "/mpoint") ):// Retrieve mPoint Logo
	// mPoint logo not previously returned
	if ($_SESSION['obj_Info']->getInfo("mpoint_logo") === false)
	{
		$obj_Image = $obj_mPoint->getmPointLogo();
	}
	$etag = "mpoint";
	break;
default:					// Error: Unknown Image Type
	trigger_error("Unknown Image Type {TRACE URL: ".$url ."}", E_USER_ERROR);
	break;
}

// Image has previously been returned
if ($_SESSION['obj_Info']->getInfo($etag ."_logo") !== false)
{
	// Set HTTP Headers
	header("HTTP/1.1 304 Not Modified");
	header("Date: ". gmdate("D, d M Y H:i:s T", time() ) );
	header("Expires: ". gmdate("D, d M Y H:i:s T", time() + 24*60*60) );
	header("Etag: ".  $_SESSION['obj_Info']->getInfo($etag ."_logo") );
}
else
{
	// Convert image into format supported by the Mobile Device
	switch (true)
	{
	case ($_SESSION['obj_UA']->hasJPG() ):	// Device supports JPEG Images
		$sImage = $obj_Image->getTgtImage("jpg");
		break;
	case ($_SESSION['obj_UA']->hasPNG() ):	// Device supports PNG Images
		$sImage = $obj_Image->getTgtImage("png");
		break;
	case ($_SESSION['obj_UA']->hasGIF() ):	// Device supports GIF Images
		$sImage = $obj_Image->getTgtImage("gif");
		break;
	default:					// Error: Image formats not supported by Device
		trigger_error("Image formats not supported by Device {TRACE {OBJ_UA} }", E_USER_ERROR);
		break;
	}
	$_SESSION['obj_Info']->setInfo($etag ."_logo", $etag ."-". base_convert(strlen($sImage), 10, 32) ."-". base_convert(date("YmdHis"), 10, 32) );
	
	// Set HTTP Headers
	header("HTTP/1.1 200 OK");
	header("Content-Type: ". $obj_Image->getTgtMimeType() );
	header("Content-Length: ". strlen($sImage) );
	header("Cache-Control: max-age=". (24*60*60) .", public");
	header("Pragma: cache");
	header("Last-Modified: ". gmdate("D, d M Y H:i:s T", time() ) );
	header("Expires: ". gmdate("D, d M Y H:i:s T", time() + 24*60*60) );
	header("Etag: ".  $_SESSION['obj_Info']->getInfo($etag ."_logo") );
	
	echo $sImage;
}
?>