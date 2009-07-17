<?php
/**
 *
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Web
 * @subpackage General
 */

// Require include file for including all Shared and General APIs
require_once("inc/include.php");

// Set default URL to load
if (array_key_exists("url", $_GET) === false) { $_GET['url'] = "/login/default.php"; }

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/ajax/default.xsl"?>';
?>
<root>
	<title><?= $_OBJ_TXT->_("mPoint"); ?></title>
	<unsupported><?= $_OBJ_TXT->_("Unsupported Browser"); ?></unsupported>
	<url><?= htmlspecialchars($_GET['url'], ENT_NOQUOTES); ?></url>
</root>