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
	echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/ajax/home/link.xsl"?>';
?>
	<root type="page">
		<edit-email>
			<headline><?= $_OBJ_TXT->_("Edit E-Mail"); ?></headline>

			<?= $_SESSION['obj_CountryConfig']->toXML(); ?>

			<labels>
				<progress><?= $_OBJ_TXT->_("Step 2 of 3"); ?></progress>
			</labels>
			
			<guide><?= $_OBJ_TXT->_("Edit E-Mail Guide - Step 2"); ?></guide>
		</edit-email>
	</root>
<?php
}	// Access validation end
?>