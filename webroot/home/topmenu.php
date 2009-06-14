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
	echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/ajax/home/topmenu.xsl"?>';
?>
	<root type="page">
		<top-menu>
			<link>
				<name><?= $_OBJ_TXT->_("My Account"); ?></name>
				<url>/home/my_account.php</url>
			</link>
			<link>
				<name><?= $_OBJ_TXT->_("Top-Up Account"); ?></name>
				<url>/home/topup.php</url>
			</link>
			<link>
				<name><?= $_OBJ_TXT->_("Transaction History"); ?></name>
				<url>/home/history.php</url>
			</link>
			<link>
				<name><?= $_OBJ_TXT->_("Transfer"); ?></name>
				<url>/home/transfer.php</url>
			</link>

			<info>
				<balance><?= $_OBJ_TXT->_("Balance"); ?></balance>
			</info>

			<?= $obj_mPoint->getAccountInfo($_SESSION['obj_Info']->getInfo("accountid") ); ?>
		</top-menu>
	</root>
<?php
}	// Access validation end
?>