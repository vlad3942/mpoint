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

echo '<?xml version="1.0" encoding="ISO-8859-15"?>';
echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/ajax/default.xsl"?>';
?>
<root>
	<title><?= $_OBJ_TXT->_("mPoint"); ?></title>
	<unsupported><?= $_OBJ_TXT->_("Unsupported Browser"); ?></unsupported>
</root>