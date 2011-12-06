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
 * @version 1.0
 */

// Require Global Include File
require_once("inc/include.php");

// Require Business logic for the Product Overview Component
require_once(sCLASS_PATH ."/overview.php");

// Instantiate data object with the User Agent Profile for the customer's mobile device.
$_SESSION['obj_UA'] = UAProfile::produceUAProfile();

$obj_mPoint = new Overview($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo']);

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/'. General::getMarkupLanguage($_SESSION['obj_UA'], $_SESSION['obj_TxnInfo']) .'/terms.xsl"?>';
?>
<root>
	<title><?= htmlspecialchars($_OBJ_TXT->_("Terms & Conditions") ); ?></title>
	
	<?= $obj_mPoint->getSystemInfo(); ?>
	
	<?= $_SESSION['obj_TxnInfo']->getClientConfig()->toXML(); ?>
	
	<?= $_SESSION['obj_TxnInfo']->toXML($_SESSION['obj_UA']); ?>
	
	<back><?= htmlspecialchars($_OBJ_TXT->_("<< Back"), ENT_NOQUOTES); ?></back>
	<terms><?= nl2br(utf8_encode($_SESSION['obj_TxnInfo']->getClientConfig()->getTerms() ) ); ?></terms>
</root>