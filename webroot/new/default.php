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