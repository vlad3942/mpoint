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
require_once("../../inc/include.php");

$_SESSION['temp']['countryid'] = $_SESSION['obj_CountryConfig']->getID();

// Clear session object
unset($_SESSION['obj_Info']);
unset($_SESSION['obj_CountryConfig']);

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<root type="multipart" cache="false">
	<document type="command">
		<delcache />
	</document>
	<document type="command">
		<recache>
			<url>/login/topmenu.php</url>
			<url>/login/content.php</url>
		</recache>
	</document>
	<document type="command" msg="internal">
		<top-menu>
			<url>/login/topmenu.php</url>
		</top-menu>
		<content>
			<url>/login/content.php</url>
		</content>
	</document>
	<document type="status">
		<internal id="1000"><?= $_OBJ_TXT->_("You've successfully been logged out"); ?></internal>
	</document>
</root>