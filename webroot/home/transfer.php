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

// Set Defaults
if (array_key_exists("temp", $_SESSION) === false) { $_SESSION['temp'] = array(); }
if (array_key_exists("countryid", $_SESSION['temp']) === false || $_SESSION['temp']['countryid'] == 0) { $_SESSION['temp']['countryid'] = $_SESSION['obj_CountryConfig']->getID(); }

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
	echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/ajax/home/transfer.xsl"?>';
?>
	<root type="page">
		<content>
			<headline><?= $_OBJ_TXT->_("Transfer"); ?></headline>
			
			<?= $_SESSION['obj_CountryConfig']->toXML(); ?>

			<labels>
				<balance><?= $_OBJ_TXT->_("Balance"); ?></balance>
				<country><?= $_OBJ_TXT->_("Country"); ?></country>
				<select><?= $_OBJ_TXT->_("( Select )"); ?></select>
				<recipient>
					<label><?= $_OBJ_TXT->_("Recipient"); ?></label>
					<help><?= $_OBJ_TXT->_("Mobile or E-Mail"); ?></help>
				</recipient>
				<amount>
					<label><?= $_OBJ_TXT->_("Amount"); ?></label>
					<help><?= $_OBJ_TXT->_("Min") ." ". General::formatAmount($_SESSION['obj_CountryConfig'], $_SESSION['obj_CountryConfig']->getMinTransfer() ); ?></help>
				</amount>
				<fee><?= $_OBJ_TXT->_("Transfer Fee"); ?></fee>
				<exchange-rate><?= $_OBJ_TXT->_("Exchange Rate"); ?></exchange-rate>
				<local-amount><?= $_OBJ_TXT->_("Local Amount"); ?></local-amount>
				<total><?= $_OBJ_TXT->_("Total"); ?></total>
				<submit><?= $_OBJ_TXT->_("Transfer"); ?></submit>
			</labels>
			
			<overview><?= $_OBJ_TXT->_("Transfer - Overview"); ?></overview>
			
			<?= $obj_mPoint->getAccountInfo($_SESSION['obj_Info']->getInfo("accountid") ); ?>
			
			<?= $obj_mPoint->getCountryConfigs(); ?>
			
			<?= $obj_mPoint->getExchangeRates(); ?>
			
			<?= $obj_mPoint->getFees(Constants::iTRANSFER_FEE, $_SESSION['obj_CountryConfig']->getID() ); ?>
			
			<?= $obj_mPoint->getSession(); ?>
		</content>
	</root>
<?php
}	// Access validation end
?>