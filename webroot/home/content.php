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
	echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/ajax/home/content.xsl"?>';
?>
	<root type="page">
		<content>
			<headline><?= $_OBJ_TXT->_("mPoint - User Admin"); ?></headline>

			<labels>
				<my-account><?= $_OBJ_TXT->_("My Account"); ?></my-account>
				<top-up><?= $_OBJ_TXT->_("Top-Up Account"); ?></top-up>
				<stored-cards><?= $_OBJ_TXT->_("Stored Cards"); ?></stored-cards>
			</labels>

			<overview><?= $_OBJ_TXT->_("mPoint - User Admin - Overview"); ?></overview>

			<help>
				<my-account><?= $_OBJ_TXT->_("My Account - Help"); ?></my-account>
				<top-up><?= $_OBJ_TXT->_("Top-Up Account - Help"); ?></top-up>
				<stored-cards><?= $_OBJ_TXT->_("Stored Cards - Help"); ?></stored-cards>
			</help>
		</content>
	</root>
<?php
}	// Access validation end
?>