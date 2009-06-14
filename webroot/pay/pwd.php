<?php
/**
 * This file contains the Controller for mPoint's Payment Completed compont.
 * The component will generate a page using the Client Configuration providing Post Payment options:
 * 	- Send E-Mail Receipt
 * 	- Go to Client's Accept URL
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Payment
 * @subpackage Accept
 * @version 1.0
 */

// Require Global Include File
require_once("../inc/include.php");

// Require Business logic for the Payment Accepted component
require_once(sCLASS_PATH ."/accept.php");

// Re-Build HTTP GET super global to support arrays
rebuild_get();

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );

$obj_mPoint = new Accept($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_UA']);

echo '<?xml version="1.0" encoding="ISO-8859-15"?>';
echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/'. General::getMarkupLanguage($_SESSION['obj_UA']) .'/pay/pwd.xsl"?>';
?>
<root>
	<title><?= $_OBJ_TXT->_("Create Account"); ?></title>

	<?= $obj_mPoint->getSystemInfo(); ?>

	<?= $_SESSION['obj_TxnInfo']->getClientConfig()->toXML(); ?>

	<?= $_SESSION['obj_TxnInfo']->toXML($_SESSION['obj_UA']); ?>

	<?= $obj_mPoint->getmPointLogoInfo(); ?>

	<?= $obj_mPoint->getClientVars($_SESSION['obj_TxnInfo']->getID() ); ?>

	<labels>
		<info><?= $_OBJ_TXT->_("Please complete the form below to set the password for your account"); ?></info>
		<password><?= $_OBJ_TXT->_("Password"); ?></password>
		<repeat-password><?= $_OBJ_TXT->_("Repeat Password"); ?></repeat-password>
		<submit><?= $_OBJ_TXT->_("Save"); ?></submit>
	</labels>

	<?= $obj_mPoint->getMessages("Create Account"); ?>
</root>