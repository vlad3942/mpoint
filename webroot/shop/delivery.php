<?php
/**
 * This file contains the Controller for the List Products component in mPoint's shopping flow.
 * The component will generate a page using the transaction data, which lists all of the available products and allows the customer to
 * select the quantity to purchase for each product.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Shop
 * @subpackage Delivery
 * @version 1.0
 */

// Require Global Include File
require_once("../inc/include.php");

// Require Business logic for the Delivery Component
require_once(sCLASS_PATH ."/delivery.php");
// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");

// Re-Build HTTP GET super global to support arrays
rebuild_get();

// Country has Address Lookup Service Enabled
if ($_SESSION['obj_TxnInfo']->getClientConfig()->getCountryConfig()->hasAddressLookup() === true)
{
	$obj_mPoint = new Delivery($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo'], HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO[$_SESSION['obj_TxnInfo']->getClientConfig()->getCountryConfig()->getID()]) );
}
else { $obj_mPoint = new Delivery($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo']); }

// No data previously transmitted
if (array_key_exists("temp", $_SESSION) === false)
{
	if ($_SESSION['obj_TxnInfo']->getClientConfig()->getCountryConfig()->hasAddressLookup() === true)
	{
		$_SESSION['temp'] = $obj_mPoint->getDeliveryAddressFromMSISDN($_SESSION['obj_TxnInfo']->getMobile() );
		if (count($_SESSION['temp']) == 0)
		{
			$_GET['msg'] = 10;
			// Add Mobile Number (MSISDN) to the list of constants used for Text Tag Replacement
			$_OBJ_TXT->loadConstants(array("MOBILE" => $_SESSION['obj_TxnInfo']->getMobile() ) );
		}
	}
	$_SESSION['temp']['year'] = date("Y");
	$_SESSION['temp']['month'] = date("m");
	$_SESSION['temp']['day'] = date("d");
}

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/'. General::getMarkupLanguage($_SESSION['obj_UA'], $_SESSION['obj_TxnInfo']) .'/shop/delivery.xsl"?>';
?>
<root>
	<title><?= $_OBJ_TXT->_("Recipient"); ?></title>

	<?= $obj_mPoint->getSystemInfo($aHTTP_CONN_INFO["hpp"]["protocol"]); ?>

	<?= $_SESSION['obj_TxnInfo']->getClientConfig()->getCountryConfig()->toXML(); ?>

	<?= $_SESSION['obj_TxnInfo']->getClientConfig()->toXML(); ?>

	<?= $_SESSION['obj_TxnInfo']->toXML($_SESSION['obj_UA']); ?>

	<?= $_SESSION['obj_ShopConfig']->toXML(); ?>

	<labels>
		<info><?= $_OBJ_TXT->_("Delivery - Info"); ?></info>
		<phone-no><?= htmlspecialchars($_OBJ_TXT->_("Phone No"), ENT_NOQUOTES); ?></phone-no>
		<find><?= htmlspecialchars($_OBJ_TXT->_("Find"), ENT_NOQUOTES); ?></find>
		<name><?= $_OBJ_TXT->_("Name"); ?></name>
		<company><?= $_OBJ_TXT->_("Company / CO"); ?></company>
		<street><?= $_OBJ_TXT->_("Street"); ?></street>
		<zipcode><?= $_OBJ_TXT->_("Zip Code"); ?></zipcode>
		<city><?= $_OBJ_TXT->_("City"); ?></city>
		<delivery-date>
			<label><?= $_OBJ_TXT->_("Delivery Date"); ?></label>
			<year><?= $_OBJ_TXT->_("YYYY"); ?></year>
			<month><?= $_OBJ_TXT->_("MM"); ?></month>
			<day><?= $_OBJ_TXT->_("DD"); ?></day>
		</delivery-date>
		<next><?= htmlspecialchars($_OBJ_TXT->_("Next >>"), ENT_NOQUOTES); ?></next>
	</labels>

	<?= $obj_mPoint->getMessages("Delivery"); ?>

	<?= $obj_mPoint->getSession(); ?>
</root>