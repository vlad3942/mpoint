<?php
/**
 * This files contains the Controller for constructing the user interface that allows customers to enter their e-mail addresses.
 * The file will construct a page allow customers to enter and submit their e-mail addresses.
 * 
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Payment
 * @subpackage Receipt
 * @version 1.0
 */

// Require Global Include File
require_once("../inc/include.php");

// Require Business logic for the Payment Accepted component
require_once(sCLASS_PATH ."/accept.php");

// Re-Build HTTP GET super global to support arrays
rebuild_get();

$obj_mPoint = new Accept($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_UA']);

echo '<?xml version="1.0" encoding="ISO-8859-15"?>';
echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/'. General::getMarkupLanguage($_SESSION['obj_UA']) .'/pay/email.xsl"?>';
?>
<root>
	<title><?= $_OBJ_TXT->_("E-Mail Receipt"); ?></title>
	
	<?= $obj_mPoint->getSystemInfo(); ?>
	
	<?= $_SESSION['obj_TxnInfo']->getClientConfig()->toXML(); ?>
	
	<?= $_SESSION['obj_TxnInfo']->toXML($_SESSION['obj_UA']); ?>
	
	<?= $obj_mPoint->getmPointLogoInfo(); ?>
	
	<labels>
		<mpoint><?= $_OBJ_TXT->_("Thank you for using"); ?></mpoint>
		<info><?= $_OBJ_TXT->_("Please enter your e-mail address below"); ?></info>
		<email><?= $_OBJ_TXT->_("E-Mail"); ?></email>
		<submit><?= $_OBJ_TXT->_("Send E-Mail"); ?></submit>
		<back><?= htmlspecialchars($_OBJ_TXT->_("<< Back") ); ?></back>
	</labels>
	
	<?= $obj_mPoint->getMessages("E-Mail"); ?>
	
	<?= $obj_mPoint->getSession(); ?>
</root>