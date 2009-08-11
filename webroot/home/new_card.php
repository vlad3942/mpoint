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

$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, $_SESSION['obj_CountryConfig']->getID(), -1);

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
	echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/ajax/home/new_card.xsl"?>';
?>
	<root type="page">
		<new-card>
			<headline><?= $_OBJ_TXT->_("Add New Card"); ?></headline>

			<labels>
				<submit><?= $_OBJ_TXT->_("OK"); ?></submit>
			</labels>
			
			<guide><?= str_replace("{AMOUNT}", General::formatAmount($_SESSION['obj_CountryConfig'], $_SESSION['obj_CountryConfig']->getAddCardAmount() ), $_OBJ_TXT->_("Add New Card Guide") ); ?></guide>
			
			<?= $obj_mPoint->getAccountInfo($_SESSION['obj_Info']->getInfo("accountid") ); ?>
			<?= $obj_ClientConfig->toXML(); ?>
			<?= $obj_ClientConfig->getAccountConfig()->toXML(); ?>
			<?= $_SESSION['obj_CountryConfig']->toXML(); ?>
			
		</new-card>
	</root>
<?php
}	// Access validation end
?>