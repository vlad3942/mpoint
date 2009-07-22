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
$_REQUEST[session_name()] = substr($_GET['file'], strrpos($_GET['file'], "_") + 1);

// Require Global Include File
require_once("../inc/include.php");

// Require Business logic for the Image Component
require_once(sCLASS_PATH ."/retrieve_image.php");

// Image has previously been returned
if (array_key_exists("HTTP_IF_NONE_MATCH", $_SERVER) === true && 1 == 2)
{
	// Set HTTP Headers
	header("HTTP/1.1 304 Not Modified");
	header("Date: ". gmdate("D, d M Y H:i:s T", time() ) );
	header("Cache-Control: max-age=". (24*60*60) .", public");
	header("Pragma: public");
	header("Expires: ". gmdate("D, d M Y H:i:s T", time() + 24*60*60) );
	header("Etag: ".  $_SERVER['HTTP_IF_NONE_MATCH']);
}
else
{
	$obj_mPoint = new RetrieveImage($_OBJ_DB, $_OBJ_TXT);
	
	@list($w, $h) = explode("x", substr($_GET['file'], 0, strpos($_GET['file'], "_") ) );
	// Image size incuded in URL
	if (empty($w) === false && empty($h) === false) { $_GET['file'] = substr($_GET['file'], strpos($_GET['file'], "_") + 1); }

	// Determine Image type to be retrieved
	switch (true)
	{
	case (strstr($_GET['file'], "client") ):	// Retrieve Client Logo
		$obj_Image = $obj_mPoint->getClientLogo($_SESSION['obj_TxnInfo']->getLogoURL() );
		$etag = "client";
		break;
	case (strstr($_GET['file'], "product") ):
		$etag = "product";
		break;
	case (strstr($_GET['file'], "card") ):	// Retrieve Credit Card Logo
		$aTmp = explode("_", $_GET['file']);
		$id = $aTmp[count($aTmp)-2];
		$obj_Image = $obj_mPoint->getCardLogo($id);
		$etag = "card_". $id;
		break;
	case (strstr($_GET['file'], "mpoint") ):// Retrieve mPoint Logo
		$obj_Image = $obj_mPoint->getmPointLogo();
		$etag = "mpoint";
		break;
	default:					// Error: Unknown Image Type
		trigger_error("Unknown Image Type {TRACE URL: ".$_GET['file'] ."}", E_USER_ERROR);
		break;
	}
	$obj_Image->resize($w, $h);

	// Mobile Device
	if (array_key_exists("obj_UA", $_SESSION) === true)
	{
		// Convert image into format supported by the Mobile Device
		switch (true)
		{
		case ($_SESSION['obj_UA']->hasPNG() ):	// Device supports PNG Images
			$sImage = $obj_Image->getTgtImage("png");
			break;
		case ($_SESSION['obj_UA']->hasJPG() ):	// Device supports JPEG Images
			$sImage = $obj_Image->getTgtImage("jpg");
			break;
		case ($_SESSION['obj_UA']->hasGIF() ):	// Device supports GIF Images
			$sImage = $obj_Image->getTgtImage("gif");
			break;
		default:					// Image formats not supported by Device
			$sImage = $obj_Image->getTgtImage(substr($_GET['file'], -3) );
			break;
		}
	}
	// Web Browser
	else { $sImage = $obj_Image->getTgtImage(substr($_GET['file'], -3) ); }

	// Set HTTP Headers
	header("HTTP/1.1 200 OK");
	header("Content-Type: ". $obj_Image->getTgtMimeType() );
	header("Content-Length: ". strlen($sImage) );
	header("Cache-Control: max-age=". (24*60*60) .", public");
	header("Pragma: public");
	header("Last-Modified: ". gmdate("D, d M Y H:i:s T", time() ) );
	header("Expires: ". gmdate("D, d M Y H:i:s T", time() + 24*60*60) );
	header("Etag: ".  $etag ."-". base_convert(strlen($sImage), 10, 32) ."-". base_convert(date("YmdHis"), 10, 32) );

	echo $sImage;
}
?>