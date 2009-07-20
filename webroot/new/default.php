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

/*
 * Use Output buffering to "magically" transform the XML via XSL behind the scene
 * This means that all PHP scripts must output a wellformed XML document.
 * The XML in turn must refer to an XSL Stylesheet by using the xml-stylesheet tag
 */
ob_start(array(new Output("all", false), "transform") );

// Construct URL to load
$sURL = "/new/step1.php";
if (array_key_exists("checksum", $_GET) === true) { $sURL .= "?". "checksum=". $_GET['checksum']; }

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/ajax/default.xsl"?>';
?>
<root>
	<title><?= $_OBJ_TXT->_("mPoint"); ?></title>
	<unsupported><?= $_OBJ_TXT->_("Unsupported Browser"); ?></unsupported>
	<url><?= htmlspecialchars($sURL, ENT_NOQUOTES); ?></url>
</root>