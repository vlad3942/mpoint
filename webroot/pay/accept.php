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
 * @version 1.10
 */

// Require Global Include File
require_once("../inc/include.php");

// Require Business logic for the Payment Accepted component
require_once(sCLASS_PATH ."/accept.php");

$obj_mPoint = new Accept($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_UA']);

echo '<?xml version="1.0" encoding="ISO-8859-15"?>';
echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/'. General::getMarkupLanguage($_SESSION['obj_UA']) .'/pay/accept.xsl"?>';
?>
<root>
	<title><?= $_OBJ_TXT->_("Payment Completed"); ?></title>

	<?= $obj_mPoint->getSystemInfo(); ?>

	<?= $_SESSION['obj_TxnInfo']->getClientConfig()->toXML(); ?>

	<?= $_SESSION['obj_TxnInfo']->toXML($_SESSION['obj_UA']); ?>

	<?= $obj_mPoint->getmPointLogoInfo(); ?>

	<?= $obj_mPoint->getClientVars($_SESSION['obj_TxnInfo']->getID() ); ?>

	<labels>
		<mpoint><?= $_OBJ_TXT->_("Thank you for using"); ?></mpoint>
		<status><?= $_OBJ_TXT->_("Status - Success"); ?></status>
		<txnid><?= $_OBJ_TXT->_("mPoint ID"); ?></txnid>
		<orderid><?= $_OBJ_TXT->_("Order No"); ?></orderid>
		<price><?= $_OBJ_TXT->_("Price"); ?></price>
		<sms-receipt><?= str_replace("{MOBILE}", $_SESSION['obj_TxnInfo']->getMobile(), $_OBJ_TXT->_("SMS Receipt - Info") ); ?></sms-receipt>
		<email-receipt><?= $_OBJ_TXT->_("Send receipt via E-Mail"); ?></email-receipt>
		<continue><?= htmlspecialchars($_OBJ_TXT->_("Continue >>") ); ?></continue>
		<resume><?= htmlspecialchars($_OBJ_TXT->_("Resume Payment >>") ); ?></resume>
	</labels>
	
	<?php
	// Current transaction is an Account Top-Up and a previous transaction is in progress
	if ($_SESSION['obj_TxnInfo']->getTypeID() >= 100 && $_SESSION['obj_TxnInfo']->getTypeID() <= 109 && array_key_exists("obj_OrgTxnInfo", $_SESSION) === true)
	{
		echo '<original-transaction-id>'. $_SESSION['obj_OrgTxnInfo']->getID() .'</original-transaction-id>';
	}
	?>

	<?= $obj_mPoint->getMessages("Accept"); ?>
</root>