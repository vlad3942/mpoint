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
	echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/ajax/home/code.xsl"?>';
?>
	<root type="page">
		<edit-mobile>
			<headline><?= $_OBJ_TXT->_("Edit Mobile"); ?></headline>

			<?= $_SESSION['obj_CountryConfig']->toXML(); ?>

			<labels>
				<progress><?= $_OBJ_TXT->_("Step 2 of 2"); ?></progress>
				<activation-code><?= $_OBJ_TXT->_("Activation Code"); ?></activation-code>
				<submit><?= $_OBJ_TXT->_("Save Mobile"); ?></submit>
			</labels>
			
			<guide><?= $_OBJ_TXT->_("Edit Mobile Guide - Step 2"); ?></guide>
			
			<?= $obj_mPoint->getAccountInfo($_SESSION['obj_Info']->getInfo("accountid") ); ?>
		</edit-mobile>
	</root>
<?php
}	// Access validation end
?>