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
$obj_mPoint = new Home($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_CountryConfig']);

echo '<?xml version="1.0" encoding="UTF-8"?>';
// Error: Unauthorized access
if (General::val() != 1000)
{
?>
	<root type="command">
		<redirect>
			<url>/internal/unauthorized.php?code=<?= General::val(); ?></url>
		</redirect>
	</root>
<?php
}
// Success: Access granted
else
{
	echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/ajax/home/edit_mobile.xsl"?>';
?>
	<root type="page">
		<edit-mobile>
			<headline><?= $_OBJ_TXT->_("Edit Mobile"); ?></headline>

			<?= $_SESSION['obj_CountryConfig']->toXML(); ?>

			<labels>
				<progress><?= $_OBJ_TXT->_("Step 1 of 2"); ?></progress>
				<old-mobile><?= $_OBJ_TXT->_("Old Mobile"); ?></old-mobile>
				<new-mobile><?= $_OBJ_TXT->_("New Mobile"); ?></new-mobile>
				<submit><?= $_OBJ_TXT->_("Send"); ?></submit>
			</labels>
			
			<guide><?= $_OBJ_TXT->_("Edit Mobile Guide - Step 1"); ?></guide>
			
			<?= $obj_mPoint->getAccountInfo($_SESSION['obj_Info']->getInfo("accountid") ); ?>
		</edit-mobile>
	</root>
<?php
}	// Access validation end
?>