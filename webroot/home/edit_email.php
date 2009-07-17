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
	echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/ajax/home/edit_email.xsl"?>';
?>
	<root type="page">
		<edit-email>
			<headline><?= $_OBJ_TXT->_("Edit E-Mail"); ?></headline>

			<?= $_SESSION['obj_CountryConfig']->toXML(); ?>

			<labels>
				<progress><?= $_OBJ_TXT->_("Step 1 of 2"); ?></progress>
				<old-email><?= $_OBJ_TXT->_("Old E-Mail"); ?></old-email>
				<new-email><?= $_OBJ_TXT->_("New E-Mail"); ?></new-email>
				<submit><?= $_OBJ_TXT->_("Send"); ?></submit>
			</labels>
			
			<guide><?= $_OBJ_TXT->_("Edit E-Mail Guide - Step 1"); ?></guide>
			
			<?= $obj_mPoint->getAccountInfo($_SESSION['obj_Info']->getInfo("accountid") ); ?>
		</edit-email>
	</root>
<?php
}	// Access validation end
?>