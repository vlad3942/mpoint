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
 * @subpackage Products
 * @version 1.0
 */

// Require Global Include File
require_once("../inc/include.php");

// Require Business logic for the Order Overview Component
require_once(sCLASS_PATH ."/overview.php");
// Require Business logic for the Product List Component
require_once(sCLASS_PATH ."/products.php");

// Instantiate data object with the User Agent Profile for the customer's mobile device.
$_SESSION['obj_UA'] = UAProfile::produceUAProfile();
// Instantiate Data Object for Holding the Shop's Configuration
$_SESSION['obj_ShopConfig'] = ShopConfig::produceConfig($_OBJ_DB, $_SESSION['obj_TxnInfo']->getClientConfig() );

$obj_mPoint = new Products($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo']);

echo '<?xml version="1.0" encoding="ISO-8859-15"?>';
echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/'. General::getMarkupLanguage($_SESSION['obj_UA']) .'/shop/products.xsl"?>';
?>
<root>
	<title><?= $_OBJ_TXT->_("Mobile Shop"); ?></title>
	
	<?= $obj_mPoint->getSystemInfo(); ?>
	
	<?= $_SESSION['obj_TxnInfo']->getClientConfig()->toXML(); ?>
	
	<?= $_SESSION['obj_TxnInfo']->toXML($_SESSION['obj_UA']); ?>
	
	<labels>
		<info><?= $_OBJ_TXT->_("Product - Info"); ?></info>
		<name><?= $_OBJ_TXT->_("Name"); ?></name>
		<quantity><?= $_OBJ_TXT->_("Quantity"); ?></quantity>
		<price><?= $_OBJ_TXT->_("Price"); ?></price>
		<total><?= $_OBJ_TXT->_("Total"); ?></total>
		<next><?= htmlspecialchars($_OBJ_TXT->_("Next >>"), ENT_NOQUOTES); ?></next>
	</labels>
	
	<?= $obj_mPoint->getAllProducts(); ?>
	
	<?= $obj_mPoint->getMessages("Products"); ?>
	
	<?= $obj_mPoint->getSession(); ?>
</root>