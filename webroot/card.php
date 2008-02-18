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
 * @version 1.0
 */

// Require Global Include File
require_once("inc/include.php");

// Require Business logic for the Select Credit Card component
require_once(sCLASS_PATH ."/credit_card.php");

// Require Business logic for the Payment Accepted component
require_once(sCLASS_PATH ."/accept.php");

// Instantiate main mPoint object for handling the component's functionality
$obj_mPoint = new CreditCard($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo'], $_SESSION['obj_UA']);
// Instantiate main special object in order to pass all relevant data for the Accept Payment page through DIBS: Custom Pages
$obj_Accept = new Accept($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_UA']);

echo '<?xml version="1.0" encoding="ISO-8859-15"?>';
echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/'. General::getMarkupLanguage($_SESSION['obj_UA']) .'/card.xsl"?>';
?>
<root>
	<title><?= $_OBJ_TXT->_("Select Card"); ?></title>
	
	<?= $obj_mPoint->getSystemInfo(); ?>
	
	<?= $_SESSION['obj_TxnInfo']->getClientConfig()->toXML(); ?>
	
	<?= $_SESSION['obj_TxnInfo']->toXML($_SESSION['obj_UA']); ?>
	
	<labels>
		<progress><?= $_OBJ_TXT->_("Step 1 of 2"); ?></progress>
		<info><?= $_OBJ_TXT->_("Please select your Credit Card"); ?></info>
	</labels>
	
	<?= $obj_mPoint->getCards(); ?>
	
	<!-- DIBS Custom Pages: Credit Card Information -->
	<payment>
		<title><?= $_OBJ_TXT->_("Card Information"); ?></title>
		<progress><?= $_OBJ_TXT->_("Step 2 of 2"); ?></progress>
		<selected><?= $_OBJ_TXT->_("Selected Card"); ?></selected>
		<info><?= $_OBJ_TXT->_("Please enter your card information below"); ?></info>
		<card-number><?= $_OBJ_TXT->_("Card Number"); ?></card-number>
		<expiry><?= $_OBJ_TXT->_("Expiry date"); ?></expiry>
		<expiry-month><?= $_OBJ_TXT->_("mm"); ?></expiry-month>
		<expiry-year><?= $_OBJ_TXT->_("yy"); ?></expiry-year>
		<cvc><?= $_OBJ_TXT->_("CVC / CVS"); ?></cvc>
		<cvc-help><?= $_OBJ_TXT->_("3 digits (printed on the backside of the card)"); ?></cvc-help>
		<submit><?= $_OBJ_TXT->_("Complete Payment"); ?></submit>
	</payment>
	
	<!-- DIBS Custom Pages: Payment Accepted -->
	<accept>
		<?= $obj_Accept->getmPointLogoInfo(); ?>
	
		<?= $obj_Accept->getClientVars($_SESSION['obj_TxnInfo']->getID() ); ?>
		
		<mpoint><?= $_OBJ_TXT->_("Thank you for using"); ?></mpoint>
		<status><?= $_OBJ_TXT->_("Status - Success"); ?></status>
		<txn-id><?= $_OBJ_TXT->_("mPoint ID"); ?></txn-id>
		<order-id><?= $_OBJ_TXT->_("Order No"); ?></order-id>
		<price><?= $_OBJ_TXT->_("Price"); ?></price>
		<sms-receipt><?= str_replace("{ADDRESS}", $_SESSION['obj_TxnInfo']->getAddress(), $_OBJ_TXT->_("SMS Receipt - Info") ); ?></sms-receipt>
		<email-receipt><?= $_OBJ_TXT->_("Send receipt via E-Mail"); ?></email-receipt>
		<continue><?= htmlspecialchars($_OBJ_TXT->_("Continue >>") ); ?></continue>
	</accept>
</root>