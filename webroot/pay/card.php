<?php
/**
 * This file contains the Controller for mPoint's Card Selection component.
 * The component will generate a page using the Client Configuration listing the credit cards available to the Customer.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Payment
 * @subpackage CreditCard
 * @version 1.10
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
// Instantiate main special object in order to pass all relevant data for the Accept Payment page through DIBS: Custom Pages
$obj_Accept = new Accept($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_UA']);

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/'. General::getMarkupLanguage($_SESSION['obj_UA'], $_SESSION['obj_TxnInfo']) .'/pay/card.xsl"?>';
?>
<root>
	<title><?= $_OBJ_TXT->_("Select Payment Method"); ?></title>

	<?= $obj_mPoint->getSystemInfo(); ?>

	<?= $_SESSION['obj_TxnInfo']->getClientConfig()->getCountryConfig()->toXML(); ?>
	<?= $_SESSION['obj_TxnInfo']->getClientConfig()->getAccountConfig()->toXML(); ?>
	<?= $_SESSION['obj_TxnInfo']->getClientConfig()->toXML(); ?>

	<?= $_SESSION['obj_TxnInfo']->toXML($_SESSION['obj_UA']); ?>
	
	<?= $_SESSION['obj_UA']->toXML(); ?>
	
	<labels>
		<progress><?= $_OBJ_TXT->_("Step 1 of 2"); ?></progress>
		<info><?= $_OBJ_TXT->_("Please select your Payment Method"); ?></info>
	</labels>

	<?= $obj_mPoint->getCards($_SESSION['obj_TxnInfo']->getAmount() ); ?>
	
	<?= $obj_mPoint->getStoredCards($_SESSION['obj_TxnInfo']->getAccountID(), $_SESSION['obj_UA']); ?>

	<!-- DIBS Custom Pages: Payment Accepted -->
	<accept>
		<?= $obj_Accept->getmPointLogoInfo(); ?>

		<?= $obj_Accept->getClientVars($_SESSION['obj_TxnInfo']->getID() ); ?>
	</accept>

	<?= $obj_mPoint->getMessages("Select Card"); ?>
	
	<?php
	// Current transaction is an Account Top-Up and a previous transaction is in progress
	if ($_SESSION['obj_TxnInfo']->getTypeID() >= 100 && $_SESSION['obj_TxnInfo']->getTypeID() <= 109 && array_key_exists("obj_OrgTxnInfo", $_SESSION) === true)
	{
		echo '<original-transaction-id>'. $_SESSION['obj_OrgTxnInfo']->getID() .'</original-transaction-id>';
	}
	?>
</root>