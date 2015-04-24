<?php
/**
 * This files contains the Controller for initializing a payment through WannaFindt and presenting the
 * customer with the payment form
 * The file will construct the HTML form used to authorize the payment by enabling the customer to enter his / her credit card details.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Payment
 * @subpackage WannaFind
 * @version 1.00
 */

// Require Global Include File
require_once("../inc/include.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");

// Require Business logic for the Select Credit Card component
require_once(sCLASS_PATH ."/credit_card.php");

// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require specific Business logic for the WannaFind component
require_once(sCLASS_PATH ."/wannafind.php");


// Instantiate main mPoint object for handling the component's functionality
$obj_mPoint = new CreditCard($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo'], $_SESSION['obj_UA']);

$obj_XML = simplexml_load_string($obj_mPoint->getCards($_SESSION['obj_TxnInfo']->getAmount() ) );
$obj_XML = $obj_XML->xpath("item[@id = ". $_REQUEST['cardid'] ." and @pspid = 7]");
$obj_XML = $obj_XML[0];

// Instantiate main mPoint object for handling the component's functionality
$obj_mPoint = new WannaFind($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo'], $aHTTP_CONN_INFO["wannafind"]);

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/'. General::getMarkupLanguage($_SESSION['obj_UA'], $_SESSION['obj_TxnInfo']) .'/wannafind/postform.xsl"?>';
?>
<root>
	<title><?= $_OBJ_TXT->_("Card Info"); ?></title>

	<?= $obj_mPoint->getSystemInfo(); ?>
	<?= $_SESSION['obj_TxnInfo']->getClientConfig()->toXML(); ?>
	<?= $_SESSION['obj_TxnInfo']->toXML($_SESSION['obj_UA']); ?>
	
	<labels>
		<progress><?= $_OBJ_TXT->_("Step 2 of 2"); ?></progress>
		<info><?= $_OBJ_TXT->_("Please enter your card information below"); ?></info>
		<selected-card><?= $_OBJ_TXT->_("Selected Card"); ?></selected-card>
		<price><?= $_OBJ_TXT->_("Price"); ?></price>
		<card-number><?= $_OBJ_TXT->_("Card Number"); ?></card-number>
		<expiry-date><?= $_OBJ_TXT->_("Expiry Date"); ?></expiry-date>
		<expiry-month><?= $_OBJ_TXT->_("mm"); ?></expiry-month>
		<expiry-year><?= $_OBJ_TXT->_("yy"); ?></expiry-year>
		<cvc><?= $_OBJ_TXT->_("CVC / CVS"); ?></cvc>
		<cvc-3-help><?= $_OBJ_TXT->_("3 digits (printed on the back of the card)"); ?></cvc-3-help>
		<cvc-4-help><?= $_OBJ_TXT->_("4 digits (printed on the back of the card)"); ?></cvc-4-help>
		<store-card><?= $_OBJ_TXT->_("Save Card Info"); ?></store-card>
		<submit><?= $_OBJ_TXT->_("Complete Payment"); ?></submit>
	</labels>
	
	<wannafind shop-id="<?= htmlspecialchars($obj_XML->account, ENT_NOQUOTES); ?>">
		<url><?= htmlspecialchars($aHTTP_CONN_INFO["wannafind"]["protocol"] ."://". $aHTTP_CONN_INFO["wannafind"]["host"] . $aHTTP_CONN_INFO["wannafind"]["path"], ENT_NOQUOTES); ?></url>
	</wannafind>
	
	<card id="<?= intval($obj_XML["id"]); ?>">
		<name><?= htmlspecialchars($obj_XML->name, ENT_NOQUOTES); ?></name>
		<width><?= intval($obj_XML->{'logo-width'}); ?></width>
		<height><?= intval($obj_XML->{'logo-height'}); ?></height>
		<currency><?= intval($obj_XML->currency); ?></currency>
	</card>	
</root>