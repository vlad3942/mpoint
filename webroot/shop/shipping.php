<?php
/**
 * This file contains the Controller for the Shipping and Terms & Conditions page in mPoint's Shopping fow.
 * The component will generate a page listing the total shipping charges as well as a link to the Shops Terms & Conditions.
 * 
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Shop
 * @subpackage Shipping
 * @version 1.0
 */

// Require Global Include File
require_once("../inc/include.php");

// Require Business logic for the Shipping Component
require_once(sCLASS_PATH ."/shipping.php");

$obj_mPoint = new Shipping($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo'], $_SESSION['obj_ShopConfig']);

$_SESSION['obj_Info']->setInfo("shipping_cost", $obj_mPoint->calcShippingCost($_SESSION['obj_Info']->getInfo("order_cost") ) );

echo '<?xml version="1.0" encoding="ISO-8859-15"?>';
echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/'. General::getMarkupLanguage($_SESSION['obj_UA']) .'/shop/shipping.xsl"?>';
?>
<root>
	<title><?= $_OBJ_TXT->_("Shipping Info"); ?></title>
	
	<?= $obj_mPoint->getSystemInfo(); ?>
	
	<?= $_SESSION['obj_TxnInfo']->getClientConfig()->getCountryConfig()->toXML(); ?>
	
	<?= $_SESSION['obj_TxnInfo']->getClientConfig()->toXML(); ?>
	
	<?= $_SESSION['obj_TxnInfo']->toXML($_SESSION['obj_UA']); ?>
	
	<?= $_SESSION['obj_ShopConfig']->toXML(); ?>
	
	<labels>
		<info><?= $_OBJ_TXT->_("Shipping - Info"); ?></info>
		<company><?= htmlspecialchars($_OBJ_TXT->_("Company"), ENT_NOQUOTES); ?></company>
		<price><?= htmlspecialchars($_OBJ_TXT->_("Price"), ENT_NOQUOTES); ?></price>
		<next><?= htmlspecialchars($_OBJ_TXT->_("Next >>"), ENT_NOQUOTES); ?></next>
	</labels>
	
	<shipping-cost><?= $_SESSION['obj_Info']->getInfo("shipping_cost")==0?$_OBJ_TXT->_("FREE"):General::formatAmount($_SESSION['obj_TxnInfo']->getClientConfig()->getCountryConfig(), $_SESSION['obj_Info']->getInfo("shipping_cost") ); ?></shipping-cost>
</root>