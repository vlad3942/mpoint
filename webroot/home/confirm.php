<?php
/**
 *
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Admin
 * @subpackage Transfer
 */

// Require include file for including all Shared and General APIs
require_once("../inc/include.php");

// Require Business logic for the E-Money Transfer component
require_once(sCLASS_PATH ."/transfer.php");

// Initialize Standard content Object
$obj_mPoint = new Transfer($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_CountryConfig']);

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
	echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/ajax/home/confirm.xsl"?>';
?>
	<root type="page">
		<confirm-transfer>
			<headline><?= $_OBJ_TXT->_("Confirm Transfer"); ?></headline>
	
			<labels>
				<recipient><?= $_OBJ_TXT->_("Recipient"); ?></recipient>
				<total><?= $_OBJ_TXT->_("Total"); ?></total>
				<password><?= $_OBJ_TXT->_("Password"); ?></password>
				<confirmation-code><?= $_OBJ_TXT->_("Confirmation Code"); ?></confirmation-code>
				<submit><?= $_OBJ_TXT->_("Transfer"); ?></submit>
			</labels>
			
			<guide>
				<confirmation-code><?= $_OBJ_TXT->_("Confirm Transfer Guide - Confirmation Code"); ?></confirmation-code>
				<password-only><?= $_OBJ_TXT->_("Confirm Transfer Guide - Password Only"); ?></password-only>
			</guide>
			
			<?= $_SESSION['obj_CountryConfig']->toXML(); ?>
			
			<?= $obj_mPoint->getAccountInfo($_SESSION['obj_Info']->getInfo("accountid") ); ?>
			
			<amount><?= $_GET['amount']; ?></amount>
			<code><?= $_GET['code']; ?></code>
		</confirm-transfer>
	</root>
<?php
}	// Access validation end
?>