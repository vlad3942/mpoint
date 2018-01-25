<?php
/**
 * This file contains the Controller for a part of mPoint's Payment Completed component.
 * The component will generate a page using the Client Configuration offering the user the opportunity to name the card just stored.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Payment
 * @subpackage SaveCardName
 * @version 1.01
 */

// Require Global Include File
require_once("../inc/include.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require Business logic for the Select Credit Card component
require_once(sCLASS_PATH ."/credit_card.php");

// Re-Build HTTP GET super global to support arrays
rebuild_get();

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );

// Re-initialization of Session required
if (array_key_exists("mpoint-id", $_REQUEST) === true
&& (array_key_exists("obj_TxnInfo", $_SESSION) === false || ($_SESSION['obj_TxnInfo'] instanceof TxnInfo) === false) )
{
	$_SESSION['obj_TxnInfo'] = TxnInfo::produceInfo($_REQUEST['mpoint-id'], $_OBJ_DB);
}
$obj_mPoint = new CreditCard($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo'], $_SESSION['obj_UA']);

if (array_key_exists("cardid", $_REQUEST) === true) { $_SESSION['temp']['cardid'] = $_REQUEST['cardid']; }

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/'. General::getMarkupLanguage($_SESSION['obj_UA'], $_SESSION['obj_TxnInfo']) .'/pay/name.xsl"?>';
?>
<root>
	<title><?= $_OBJ_TXT->_("Save Card"); ?></title>

	<?= $obj_mPoint->getSystemInfo($aHTTP_CONN_INFO["hpp"]["protocol"]); ?>

	<?= $_SESSION['obj_TxnInfo']->getClientConfig()->toXML(); ?>

	<?= $_SESSION['obj_TxnInfo']->toXML($_SESSION['obj_UA']); ?>

	<labels>
		<selected-card><?= $_OBJ_TXT->_("Selected Card"); ?></selected-card>
		<info><?= $_OBJ_TXT->_("Please enter a name for the stored card below?"); ?></info>
		<name><?= $_OBJ_TXT->_("Card Name"); ?></name>
		<help><?= $_OBJ_TXT->_("Card Name - Help"); ?></help>
		<submit><?= $_OBJ_TXT->_("Save"); ?></submit>
	</labels>
	
	<?= $obj_mPoint->getCards($_SESSION['obj_TxnInfo']->getAmount() ); ?>

	<?= $obj_mPoint->getMessages("Create Account"); ?>
	
	<?= $obj_mPoint->getSession(); ?>
</root>