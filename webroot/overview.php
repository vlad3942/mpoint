<?php
/**
 * This file contains the Controller for the Order Overview component in mPoint's payment flow.
 * The component will generate a page using the transaction data, which lists the ordered products and their total price
 * 
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Payment
 * @subpackage Overview
 * @version 1.00
 */

// Require Global Include File
require_once("inc/include.php");

// Require Business logic for the Product Overview Component
require_once(sCLASS_PATH ."/overview.php");

// Instantiate data object with the User Agent Profile for the customer's mobile device.
$_SESSION['obj_UA'] = UAProfile::produceUAProfile();

$obj_mPoint = new Overview($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo']);

// Use Physical Product Flow
if ($_SESSION['obj_TxnInfo']->getClientConfig()->getFlowID() == Constants::iPHYSICAL_FLOW)
{
	// Instantiate Data Object for Holding the Shop's Configuration
	$_SESSION['obj_ShopConfig'] = ShopConfig::produceConfig($_OBJ_DB, $_SESSION['obj_TxnInfo']->getClientConfig() );
	// Ensure Total Order Amount is available for Calculating Shipping Cost
	$_SESSION['obj_Info']->setInfo("order_cost", $_SESSION['obj_TxnInfo']->getAmount() );
}

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/'. General::getMarkupLanguage($_SESSION['obj_UA'], $_SESSION['obj_TxnInfo']) .'/overview.xsl"?>';
?>
<root>
	<title><?= $_OBJ_TXT->_("Order Overview"); ?></title>
	
	<?= $obj_mPoint->getSystemInfo($aHTTP_CONN_INFO["hpp"]["protocol"]); ?>
	
	<?= $_SESSION['obj_TxnInfo']->getClientConfig()->toXML(); ?>
	
	<?= $_SESSION['obj_TxnInfo']->toXML($_SESSION['obj_UA']); ?>
	
	<labels>
		<info><?= $_OBJ_TXT->_("Product - Info"); ?></info>
		<product><?= $_OBJ_TXT->_("Product"); ?></product>
		<quantity><?= $_OBJ_TXT->_("Quantity"); ?></quantity>
		<price><?= $_OBJ_TXT->_("Price"); ?></price>
		<total><?= $_OBJ_TXT->_("Total"); ?></total>
		<terms><?= htmlspecialchars(str_replace("{CLIENT}", $_SESSION['obj_TxnInfo']->getClientConfig()->getName(), $_OBJ_TXT->_("Terms - Info") ), ENT_NOQUOTES); ?></terms>
		<delivery-info>
			<info><?= htmlspecialchars($_OBJ_TXT->_("Delivery - Info"), ENT_NOQUOTES); ?></info>
			<name><?= htmlspecialchars($_OBJ_TXT->_("Name"), ENT_NOQUOTES); ?></name>
			<company><?= htmlspecialchars($_OBJ_TXT->_("Company / CO"), ENT_NOQUOTES); ?></company>
			<street><?= htmlspecialchars($_OBJ_TXT->_("Street"), ENT_NOQUOTES); ?></street>
			<zipcode><?= htmlspecialchars($_OBJ_TXT->_("Zip Code"), ENT_NOQUOTES); ?></zipcode>
			<city><?= htmlspecialchars($_OBJ_TXT->_("City"), ENT_NOQUOTES); ?></city>
			<delivery-date><?= htmlspecialchars($_OBJ_TXT->_("Delivery Date"), ENT_NOQUOTES); ?></delivery-date>
		</delivery-info>
		<next><?= htmlspecialchars($_OBJ_TXT->_("Next >>"), ENT_NOQUOTES); ?></next>
		<payment><?= htmlspecialchars($_OBJ_TXT->_("Go to Payment >>"), ENT_NOQUOTES); ?></payment>
	</labels>
	
	<?= $obj_mPoint->getProducts(); ?>
	
	<?= $obj_mPoint->getDeliveryInfo(); ?>
	
	<?= $obj_mPoint->getShippingInfo(); ?>
	
	<?= $obj_mPoint->getMessages("Overview"); ?>
</root>