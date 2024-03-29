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
 * @version 1.01
 */

// Require Global Include File
require_once("../include.php");

// Require the PHP API for handling the connection to GoMobile
require_once(sAPI_CLASS_PATH ."/gomobile.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require specific Business logic for the DIBS component
require_once(sCLASS_PATH ."/dibs.php");
// Require general Business logic for the Cellpoint Mobile module
require_once(sCLASS_PATH ."/cpm.php");

// Re-Build HTTP GET super global to support arrays
rebuild_get();

// Resume original transaction after account top-up has been completed
if (array_key_exists("resume", $_POST) === true && $_POST['resume'] == "true" && array_key_exists("obj_OrgTxnInfo", $_SESSION) === true)
{
	$_SESSION['obj_TxnInfo'] = $_SESSION['obj_OrgTxnInfo'];
	unset($_SESSION['obj_OrgTxnInfo']);
}
// Set Defaults
if (array_key_exists("cardtype", $_REQUEST) === true) { $_SESSION['temp']['cardtype'] = $_REQUEST['cardtype']; }

$obj_mPoint = new CellpointMobile($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo'], $aCPM_CONN_INFO);

try
{
	switch ($_SESSION['temp']['cardtype'])
	{
	case (Constants::iPREMIUM_SMS):	// Premium SMS
		header("content-type: text/plain");
		// Send Billing SMS through GoMobile
		$obj_MsgInfo = $obj_mPoint->sendBillingSMS(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO) );

		ignore_user_abort(true);
		// Redirect customer
		header("Content-Length: 0");
		header("location: https://". $_SERVER['HTTP_HOST'] ."/pay/accept.php" );
		header("Connection: close");
		flush();

		// Initialise Callback to Client
		$obj_mPoint->initCallback(HTTPConnInfo::produceConnInfo($aCPM_CONN_INFO), $_SESSION['temp']['cardtype'], $obj_MsgInfo->getReturnCodes(), $obj_MsgInfo->getGoMobileID() );
		break;
	case (Constants::iWALLET):	// My Account
		if ($_SESSION['obj_TxnInfo']->getAccountID() > 0) { $iAccountID = $_SESSION['obj_TxnInfo']->getAccountID(); }
		else
		{
			$obj_Home = new Home($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo']->getClientConfig()->getCountryConfig() );
			$iAccountID = $obj_Home->getAccountID($_SESSION['obj_TxnInfo']->getClientConfig()->getCountryConfig(), $_SESSION['obj_TxnInfo']->getMobile() );
			if ($iAccountID == -1 && trim($_SESSION['obj_TxnInfo']->getEMail() ) != "") { $iAccountID = $obj_Home->getAccountID($_SESSION['obj_TxnInfo']->getClientConfig()->getCountryConfig(), $_SESSION['obj_TxnInfo']->getEMail() ); }
		}
		$obj_AccountXML = simplexml_load_string($obj_mPoint->getAccountInfo($iAccountID, $_SESSION['obj_UA']) );
		$obj_CardsXML = simplexml_load_string($obj_mPoint->getStoredCards($_SESSION['obj_TxnInfo']->getAccountID(), $_SESSION['obj_TxnInfo']->getClientConfig(), false, $_SESSION['obj_UA']) );
		if (count($obj_CardsXML) > 0)
		{
			if ($_SESSION['obj_TxnInfo']->getClientConfig()->getStoreCard() <= 3) { $obj_ClientCardsXML = $obj_CardsXML->xpath("/stored-cards/card[client/@id = ". $_SESSION['obj_TxnInfo']->getClientConfig()->getID() ."]"); }
			else { $obj_ClientCardsXML = $obj_CardsXML->xpath("/stored-cards/card"); }
		}

		/*
		 * End-User does not have an account yet AND account hasn't just been disabled
		 * Automatically redirect to "Create New Account"
		 */
		if (intval($obj_AccountXML["id"]) == 0 && array_key_exists("msg", $_GET) === false)
		{
			header("Location: http://". $_SERVER['HTTP_HOST'] ."/new/?msg=2");
		}
		/*
		 * Transaction amount doesn't require Authentication
		 * AND
		 * The balance on the End-User's e-money based prepaid account is equal to or greater than the transaction amount
		 * AND
		 * No Stored Cards available AND no error has occurred
		 */
		elseif (intval($obj_AccountXML->balance) >= $_SESSION['obj_TxnInfo']->getAmount() && $_SESSION['obj_TxnInfo']->getAmount() < $_SESSION['obj_TxnInfo']->getClientConfig()->getCountryConfig()->getMaxPSMSAmount()
			&& count($obj_ClientCardsXML) == 0 && array_key_exists("msg", $_GET) === false)
		{
			$obj_mPoint->purchase($_SESSION['obj_TxnInfo']->getAccountID(), Constants::iPURCHASE_USING_EMONEY, $_SESSION['obj_TxnInfo']->getID(), $_SESSION['obj_TxnInfo']->getAmount() );

			ignore_user_abort(true);
			// Redirect customer
			header("Content-Length: 0");
			header("location: https://". $_SERVER['HTTP_HOST'] ."/pay/accept.php" );
			header("Connection: close");
			flush();

			// Initialise Callback to Client
			$obj_mPoint->initCallback(HTTPConnInfo::produceConnInfo($aCPM_CONN_INFO), Constants::iWALLET, Constants::iPAYMENT_ACCEPTED_STATE);
		}
		// Display "My Account" page
		else
		{
			/*
			 * Use Output buffering to "magically" transform the XML via XSL behind the scene
			 * This means that all PHP scripts must output a wellformed XML document.
			 * The XML in turn must refer to an XSL Stylesheet by using the xml-stylesheet tag
			 */
			ob_start(array(new Output("all", false), "transform") );

			echo '<?xml version="1.0" encoding="UTF-8"?>';
			echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/'. General::getMarkupLanguage($_SESSION['obj_UA'], $_SESSION['obj_TxnInfo']) .'/cpm/payment.xsl"?>';
	?>
			<root single-sign-on="<?= ($_SESSION['obj_Info']->getInfo("auth-token") === false ? "false" : "true"); ?>">
				<title><?= $_OBJ_TXT->_("Pay using Account"); ?></title>
				<?= $obj_mPoint->getSystemInfo($aHTTP_CONN_INFO["hpp"]["protocol"]); ?>
				<?= $_SESSION['obj_UA']->toXML(); ?>
				<?= $_SESSION['obj_TxnInfo']->getClientConfig()->getCountryConfig()->toXML(); ?>
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
					<create-account><?= $_OBJ_TXT->_("Create Account"); ?></create-account>
					<top-up><?= $_OBJ_TXT->_("Top-Up"); ?></top-up>
					<add-card><?= $_OBJ_TXT->_("Add Card"); ?></add-card>
					<delete-card><?= $_OBJ_TXT->_("Delete Card"); ?></delete-card>
					<authorize-deletion><?= htmlspecialchars($_OBJ_TXT->_("- Authorize Delete Text -"), ENT_NOQUOTES); ?></authorize-deletion>
					<confirm-delete><?= htmlspecialchars($_OBJ_TXT->_("- Confirm Delete Text -"), ENT_NOQUOTES); ?></confirm-delete>
					<yes><?= $_OBJ_TXT->_("Yes"); ?></yes>
					<no><?= $_OBJ_TXT->_("No"); ?></no>
					<single-sign-on><?= $_OBJ_TXT->_("Account - Single Sign-On"); ?></single-sign-on>
					<authorizing-payment><?= nl2br(htmlspecialchars($_OBJ_TXT->_("- Authorizing Payment Text -"), ENT_NOQUOTES) ); ?></authorizing-payment>
					<cancel><?= $_OBJ_TXT->_("Cancel Payment"); ?></cancel>
				</labels>

				<?= str_replace('<?xml version="1.0"?>', '', $obj_AccountXML->asXML() ) ?>

				<?= count($obj_CardsXML) > 0 ? str_replace('<?xml version="1.0"?>', '', $obj_CardsXML->asXML() ) : ""; ?>

				<?= $obj_mPoint->getMessages("CPM Payment"); ?>

				<?= $obj_mPoint->getSession(); ?>
			</root>
<?php
		}
		break;
	}
}
// Error: Billing SMS rejected by GoMobile
catch (mPointException  $e)
{
	header("location: https://". $_SERVER['HTTP_HOST'] ."/pay/card.php?msg[]=99");
}
?>