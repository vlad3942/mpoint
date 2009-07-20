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

// Initialize Standard content Object
$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/ajax/new/step2.xsl"?>';
?>
<root type="page">
	<content>
		<headline><?= $_OBJ_TXT->_("Create Account"); ?></headline>

		<labels>
			<progress><?= $_OBJ_TXT->_("Step 2 of 2"); ?></progress>
			<account-id><?= $_OBJ_TXT->_("Account ID"); ?></account-id>
			<activation-code><?= $_OBJ_TXT->_("Activation Code"); ?></activation-code>
			<submit><?= $_OBJ_TXT->_("Verify Mobile"); ?></submit>
		</labels>
		
		<guide><?= $_OBJ_TXT->_("Create Account Guide - Step 2"); ?></guide>
		
		<?= $obj_mPoint->getSession(); ?>
	</content>
</root>