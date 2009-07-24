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

// Re-Build HTTP GET super global to support arrays
rebuild_get();

// Initialize Standard content Object
$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/ajax/home/activate.xsl"?>';
?>
<root type="page">
	<content>
		<headline><?= $_OBJ_TXT->_("Activate E-Mail"); ?></headline>
		
		<labels>
			<progress><?= $_OBJ_TXT->_("Step 3 of 3"); ?></progress>
		</labels>

		<guide><?= $_OBJ_TXT->_("Edit E-Mail Guide - Step 3"); ?></guide>
		
		<?= $obj_mPoint->getMessages("Activate E-Mail"); ?>
	</content>
</root>