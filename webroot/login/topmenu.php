<?php
/**
 *
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Admin
 * @subpackage Login
 */

// Require include file for including all Shared and General APIs
require_once("../inc/include.php");

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/ajax/home/topmenu.xsl"?>';
?>
<root type="page">
	<top-menu>
		<link id="login">
			<name><?= $_OBJ_TXT->_("Login"); ?></name>
			<url>/login/content.php</url>
		</link>
		<link id="sign-up">
			<name><?= $_OBJ_TXT->_("Create Account"); ?></name>
			<url>/new/step1.php</url>
		</link>
	</top-menu>
</root>