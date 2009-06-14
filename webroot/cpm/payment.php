<?php
/**
 * This files contains the Controller for mPoint's implementation of Premium SMS Billing.
 * The Controller will construct a Premium MT-SMS and send it to the customer.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Payment
 * @subpackage CellpointMobile
 * @version 1.0
 */

// Require Global Include File
require_once("../inc/include.php");

// Require the PHP API for handling the connection to GoMobile
require_once(sAPI_CLASS_PATH ."/gomobile.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require general Business logic for the Cellpoint Mobile module
require_once(sCLASS_PATH ."/cpm.php");

// Re-Build HTTP GET super global to support arrays
rebuild_get();

// Set Defaults
if (array_key_exists("euaid", $_POST) === true) { $_SESSION['temp']['euaid'] = $_POST['euaid']; }
if (array_key_exists("cardtype", $_POST) === true) { $_SESSION['temp']['cardtype'] = $_POST['cardtype']; }

$obj_mPoint = new CellpointMobile($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo']);

try
{
	switch ($_SESSION['temp']['cardtype'])
	{
	case (Constants::iPSMS_CARD):	// Premium SMS
		header("content-type: text/plain");
		// Send Billing SMS through GoMobile
		$obj_MsgInfo = $obj_mPoint->sendBillingSMS(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO) );

		ignore_user_abort(true);
		// Re-Direct customer
		header("Content-Length: 0");
		header("location: http://". $_SERVER['HTTP_HOST'] ."/pay/accept.php?". session_name() ."=". session_id() );
		header("Connection: close");
		flush();

		// Initialise Callback to Client
		$obj_mPoint->initCallback(HTTPConnInfo::produceConnInfo($aCPM_CONN_INFO), $_SESSION['temp']['cardtype'], $obj_MsgInfo->getReturnCodes(), $obj_MsgInfo->getGoMobileID() );
		break;
	case (Constants::iEMONEY_CARD):	// My Account
		/*
		 * Use Output buffering to "magically" transform the XML via XSL behind the scene
		 * This means that all PHP scripts must output a wellformed XML document.
		 * The XML in turn must refer to an XSL Stylesheet by using the xml-stylesheet tag
		 */
		ob_start(array(new Output("all", false), "transform") );

		echo '<?xml version="1.0" encoding="ISO-8859-15"?>';
		echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/'. General::getMarkupLanguage($_SESSION['obj_UA']) .'/cpm/payment.xsl"?>';
?>
		<root>
			<title><?= $_OBJ_TXT->_("Pay using Account"); ?></title>

			<?= $obj_mPoint->getSystemInfo(); ?>

			<?= $_SESSION['obj_TxnInfo']->getClientConfig()->toXML(); ?>

			<?= $_SESSION['obj_TxnInfo']->toXML($_SESSION['obj_UA']); ?>

			<labels>
				<progress><?= $_OBJ_TXT->_("Step 2 of 2"); ?></progress>
				<price><?= $_OBJ_TXT->_("Price"); ?></price>
				<info><?= $_OBJ_TXT->_("Account - Info"); ?></info>
				<my-account><?= $_OBJ_TXT->_("My Account"); ?></my-account>
				<balance><?= $_OBJ_TXT->_("Balance"); ?></balance>
				<stored-card><?= $_OBJ_TXT->_("Stored Card"); ?></stored-card>
				<multiple-stored-cards><?= $_OBJ_TXT->_("Stored Cards"); ?></multiple-stored-cards>
				<password><?= $_OBJ_TXT->_("Password"); ?></password>
				<submit><?= $_OBJ_TXT->_("Complete Payment"); ?></submit>
			</labels>

			<?= $obj_mPoint->getAccountInfo($_SESSION['temp']['euaid'], $_SESSION['obj_UA']); ?>

			<?= $obj_mPoint->getStoredCards($_SESSION['temp']['euaid'], $_SESSION['obj_UA']); ?>

			<?= $obj_mPoint->getMessages("CPM Payment"); ?>

			<?= $obj_mPoint->getSession(); ?>
		</root>
<?php
		break;
	}
}
// Error: Billing SMS rejected by GoMobile
catch (mPointException  $e)
{
	header("location: http://". $_SERVER['HTTP_HOST'] ."/pay/card.php?". session_name() ."=". session_id() ."&msg=99");
}
?>