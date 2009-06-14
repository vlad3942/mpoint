<?php
/**
 *
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Admin
 * @subpackage General
 */

// Require include file for including all Shared and General APIs
require_once("../inc/include.php");

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<root type="multipart">
	<document type="command" msg="internal">
		<content>
			<url>/login/content.php</url>
		</content>
	</document>
	<document type="status">
		<internal id="1001"><?= $_OBJ_TXT->_("Unauthorized system access"); ?></internal>
	</document>
</root>