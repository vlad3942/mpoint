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
	echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/ajax/home/edit_password.xsl"?>';
?>
	<root type="page">
		<edit-password>
			<headline><?= $_OBJ_TXT->_("Edit Password"); ?></headline>

			<?= $_SESSION['obj_CountryConfig']->toXML(); ?>

			<labels>
				<old-password><?= $_OBJ_TXT->_("Old Password"); ?></old-password>
				<new-password><?= $_OBJ_TXT->_("New Password"); ?></new-password>
				<repeat-password><?= $_OBJ_TXT->_("Repeat Password"); ?></repeat-password>
				<submit><?= $_OBJ_TXT->_("Save"); ?></submit>
			</labels>
		</edit-password>
	</root>
<?php
}	// Access validation end
?>