<?php
/**
 *
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Admin
 * @subpackage TopUp
 */

// Require include file for including all Shared and General APIs
require_once("../inc/include.php");

// Require Business logic for the Top-Up Component
require_once(sCLASS_PATH ."/topup.php");

// Initialize Standard content Object
$obj_mPoint = new TopUp($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_CountryConfig']);

$xml = $obj_mPoint->getAccountInfo($_SESSION['obj_Info']->getInfo("accountid") );
$obj_XML = simplexml_load_string($xml);
$aObj_XML = $obj_XML->xpath("/clients/client[@store-card = 2]");
// End-User account is only linked to a single Client, which uses an e-money or loyalty account
if (count($aObj_XML) == 1)
{
	$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, (integer) $aObj_XML[0]["id"]);
}
else { $obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, $_SESSION['obj_CountryConfig']->getID(), -1); }

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
	echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/ajax/home/topup.xsl"?>';
?>
	<root type="page">
		<content>
			<headline><?= $_OBJ_TXT->_("Top-Up Account"); ?></headline>

			<labels>
				<balance><?= $_OBJ_TXT->_("Balance"); ?></balance>
				<amount><?= $_OBJ_TXT->_("Amount"); ?></amount>
				<price><?= $_OBJ_TXT->_("Price"); ?></price>
				<points-topup><?= $_OBJ_TXT->_("Top-Up Points"); ?></points-topup>
				<emoney-topup><?= $_OBJ_TXT->_("Top-Up e-Money"); ?></emoney-topup>
			</labels>
			
			<?= $xml; ?>
			
			<?= $obj_mPoint->getDepositOptions( (integer) $obj_XML->balance); ?>
			
			<?= $obj_ClientConfig->toXML(); ?>
			<?= $obj_ClientConfig->getAccountConfig()->toXML(); ?>
			<?= $_SESSION['obj_CountryConfig']->toXML(); ?>
		</content>
	</root>
<?php
}	// Access validation end
?>