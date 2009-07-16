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
	echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/ajax/home/my_account.xsl"?>';
?>
	<root type="page">
		<content>
			<headline><?= $_OBJ_TXT->_("My Account"); ?></headline>

			<?= $_SESSION['obj_CountryConfig']->toXML(); ?>

			<labels>
				<account-info><?= $_OBJ_TXT->_("Account Details"); ?></account-info>
				<stored-card><?= $_OBJ_TXT->_("Stored Card"); ?></stored-card>
				<multiple-stored-cards><?= $_OBJ_TXT->_("Stored Cards"); ?></multiple-stored-cards>

				<id><?= $_OBJ_TXT->_("Account ID"); ?></id>
				<country><?= $_OBJ_TXT->_("Country"); ?></country>
				<firstname><?= $_OBJ_TXT->_("Firstname"); ?></firstname>
				<lastname><?= $_OBJ_TXT->_("Lastname"); ?></lastname>
				<mobile><?= $_OBJ_TXT->_("Mobile"); ?></mobile>
				<email><?= $_OBJ_TXT->_("E-Mail"); ?></email>
				<password><?= $_OBJ_TXT->_("Password"); ?></password>
				<repeat-password><?= $_OBJ_TXT->_("Repeat Password"); ?></repeat-password>
				<submit><?= $_OBJ_TXT->_("Save"); ?></submit>
				<preferred><?= $_OBJ_TXT->_("Preferred"); ?></preferred>
				<other><?= $_OBJ_TXT->_("Other"); ?></other>
				
				<edit><?= $_OBJ_TXT->_("Edit"); ?></edit>
			</labels>

			<?= $obj_mPoint->getAccountInfo($_SESSION['obj_Info']->getInfo("accountid") ); ?>

			<?= $obj_mPoint->getStoredCards($_SESSION['obj_Info']->getInfo("accountid") ); ?>
		</content>
	</root>
<?php
}	// Access validation end
?>