<?php
/**
 *
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Admin
 * @subpackage MyAccount
 */

// Require include file for including all Shared and General APIs
require_once("../inc/include.php");

// Initialize Standard content Object
$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<root type="multipart">
	<document type="command">
		<top-menu>
			<url><?= General::val() == 1000 ? "/home/topmenu.php" : "/login/topmenu.php"; ?></url>
		</top-menu>
	</document>
	<document type="command">
		<content>
			<url>/home/activate.php?<?= htmlspecialchars($_SERVER['QUERY_STRING'], ENT_NOQUOTES); ?></url>
		</content>
	</document>
</root>