<?php
/**
 * This file contains the Controller for a part of mPoint's Payment Completed component.
 * The component will generate a page using the Client Configuration to allow the user to provide a password for the newly created account.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Payment
 * @subpackage SavePassword
 * @version 1.10
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

$obj_mPoint = new CreditCard($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo'], $_SESSION['obj_UA']);
$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB,$_SESSION['obj_Info']->getInfo("countryid"), -1);
if (array_key_exists("cardid", $_REQUEST) === true) { $_SESSION['temp']['cardid'] = $_REQUEST['cardid']; }

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/'. General::getMarkupLanguage($_SESSION['obj_UA'], $_SESSION['obj_TxnInfo']) .'/pay/pwd.xsl"?>';
?>
<root>
	<title><?= $_OBJ_TXT->_("Create Account"); ?></title>

	<?= $obj_mPoint->getSystemInfo(); ?>

	<?= $_SESSION['obj_TxnInfo']->getClientConfig()->toXML(); ?>
		
	<?= $_SESSION['obj_TxnInfo']->toXML($_SESSION['obj_UA']); ?>

	<labels>
		<info><?= $_OBJ_TXT->_("Please complete the form below to finnish creating your account"); ?></info>
		<selected-card><?= $_OBJ_TXT->_("Selected Card"); ?></selected-card>
		<password><?= $_OBJ_TXT->_("Password"); ?></password>
		<repeat-password><?= $_OBJ_TXT->_("Repeat Password"); ?></repeat-password>
		<card-name><?= $_OBJ_TXT->_("Card Name"); ?></card-name>
		<card-name-help><?= $_OBJ_TXT->_("Card Name - Help"); ?></card-name-help>
		<submit><?= $_OBJ_TXT->_("Save"); ?></submit>
<?php  	if($obj_ClientConfig->getStoreCard()==2 ||$obj_ClientConfig->getStoreCard()==4){?>	
		<full-name><?= $_OBJ_TXT->_("fullname"); ?></full-name>
		 <cpr><?= $_OBJ_TXT->_("cpr"); ?></cpr>

<?php }?>
	</labels>
	
	<?= $obj_mPoint->getCards($_SESSION['obj_TxnInfo']->getAmount() ); ?>

	<?= $obj_mPoint->getMessages("Create Account"); ?>
	
	<?= $obj_mPoint->getSession(); ?>
</root>