<?php
/**
 * This file contains the Controller for mPoint's Stored Card Prompt component.
 * The component will generate a page using the Client Configuration asking the Customer whether the card should be stored for future use.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Payment
 * @subpackage StoreCard
 * @version 1.00
 */

// Require Global Include File
require_once("../inc/include.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require Business logic for the Select Credit Card component
require_once(sCLASS_PATH ."/credit_card.php");

// Require Business logic for the Payment Accepted component
require_once(sCLASS_PATH ."/accept.php");

// Instantiate main mPoint object for handling the component's functionality
$obj_mPoint = new CreditCard($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo'], $_SESSION['obj_UA']);

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/'. General::getMarkupLanguage($_SESSION['obj_UA'], $_SESSION['obj_TxnInfo']) .'/pay/store.xsl"?>';
?>
<root>
	<title><?= $_OBJ_TXT->_("Store Card"); ?></title>

	<?= $obj_mPoint->getSystemInfo(); ?>

	<?= $_SESSION['obj_TxnInfo']->getClientConfig()->getCountryConfig()->toXML(); ?>
	<?= $_SESSION['obj_TxnInfo']->getClientConfig()->getAccountConfig()->toXML(); ?>
	<?= $_SESSION['obj_TxnInfo']->getClientConfig()->toXML(); ?>

	<?= $_SESSION['obj_TxnInfo']->toXML($_SESSION['obj_UA']); ?>
	
	<?= $_SESSION['obj_UA']->toXML(); ?>
	
	<labels>
		<progress><?= $_OBJ_TXT->_("Step 2 of 3"); ?></progress>
		<info><?= $_OBJ_TXT->_("Would you like to store your card for future use?"); ?></info>
		<yes><?= $_OBJ_TXT->_("Yes"); ?></yes>
		<no><?= $_OBJ_TXT->_("No"); ?></no>
		<cancel><?= $_OBJ_TXT->_("Cancel Payment"); ?></cancel>
	</labels>
	<psp id="<?= $_SESSION['obj_Info']->getInfo("psp-id"); ?>" card-id="<?= $_SESSION['obj_Info']->getInfo("card-id"); ?>">
		<account><?= $_SESSION['obj_Info']->getInfo("account"); ?></account>
		<sub-account><?= $_SESSION['obj_Info']->getInfo("sub-account"); ?></sub-account>
		<currency><?= $_SESSION['obj_Info']->getInfo("currency"); ?></currency>
	</psp>
</root>