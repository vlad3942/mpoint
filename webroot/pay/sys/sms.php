<?php
/**
 * This files contains the Controller for mPoint's SMS Purchase API.
 * The Controller will receive an MO-SMS from GoMobile and check if the the End-User has accepted the purchase.
 * A callback is generated if the if the purchase is accepted and the amount charged to the End-User's prepaid E-Money Account.   
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Payment
 * @subpackage SMS
 * @version 1.10
 */

// Require Global Include File
require_once("../../inc/include.php");

// Require the PHP API for handling the connection to GoMobile
require_once(sAPI_CLASS_PATH ."/gomobile.php");

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require Business logic for the Mobile Web module
require_once(sCLASS_PATH ."/mobile_web.php");
// Require Business logic for the SMS Purchase module
require_once(sCLASS_PATH ."/sms_purchase.php");

// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require specific Business logic for the DIBS component
require_once(sCLASS_PATH ."/dibs.php");
// Require general Business logic for the Cellpoint Mobile module
require_once(sCLASS_PATH ."/cpm.php");

header("content-type: text/plain");

// Parse received MO-SMS
$obj_MsgInfo = GoMobileMessage::produceMessage($HTTP_RAW_POST_DATA);

// Instantiate mPoint object to handle the transaction
$obj_mPoint = SMS_Purchase::produceSMS_Purchase($_OBJ_DB, $obj_MsgInfo);

$obj_TxnInfo = TxnInfo::produceInfo($obj_mPoint->findTxnIDFromSMS($obj_MsgInfo), $_OBJ_DB);

// Transaction found
if ($obj_TxnInfo instanceof TxnInfo === true)
{
	// Associate End-User Account (if exists) with Transaction
	$iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_TxnInfo->getClientConfig(), $obj_TxnInfo->getMobile() );
	if ($iAccountID == -1 && trim($obj_TxnInfo->getEMail() ) != "") { $iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_TxnInfo->getClientConfig(), $obj_TxnInfo->getEMail() ); }
	$obj_TxnInfo->setAccountID($iAccountID);
	
	// Transfer GoMobile Username / Password global array of GoMobile Connection Information
	$aGM_CONN_INFO["username"] = $obj_TxnInfo->getClientConfig()->getUsername();
	$aGM_CONN_INFO["password"] = $obj_TxnInfo->getClientConfig()->getPassword();
	// Confirm to GoMobile that the MO-SMS has been received
	$obj_ConnInfo = GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO);
	$obj_GoMobile = new GoMobileClient($obj_ConnInfo);
	$obj_GoMobile->communicate($obj_MsgInfo);
	
	// Determine End-User Response
	switch (true)
	{
	case (in_array(strtoupper($obj_MsgInfo->getBody() ), $aACCEPT_WORDS) ):	// Payment Accepted by End-User
		$obj_PSP = new CellpointMobile($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo);
		// End-User has an account
		if ($obj_TxnInfo->getAccountID() > 0)
		{
			$obj_XML = simplexml_load_string($obj_mPoint->getAccountInfo($obj_TxnInfo->getAccountID() ) );
			// Pay using prepaid E-Money Account
			if (intval($obj_XML->balance) >= $obj_TxnInfo->getAmount() )
			{
				$obj_mPoint->purchase($obj_TxnInfo->getAccountID(), $obj_TxnInfo->getID(), $obj_TxnInfo->getAmount() );
				// Initialise Callback to Client
				$obj_PSP->initCallback(HTTPConnInfo::produceConnInfo($aCPM_CONN_INFO), Constants::iEMONEY_CARD, Constants::iPAYMENT_ACCEPTED_STATE);			
			}
			else
			{
				$iAccountBalance = (integer) $obj_XML->balance;
				$obj_XML = simplexml_load_string($obj_mPoint->getStoredCards($obj_TxnInfo->getAccountID() ) );
				
				$mExternalID = -1;
				// Pay using Stored Card
				if (count($obj_XML->xpath("/stored-cards/card[client/@id = ". $obj_TxnInfo->getClientConfig()->getID() ."]") ) > 0)
				{
					// Get Stored Cards for Client with Preferred Card first
					$aObj_XML = $obj_XML->xpath("/stored-cards/card[@preferred = 'true' and client/@id = ". $obj_TxnInfo->getClientConfig()->getID() ."]");
					$aObj_XML = array_merge($aObj_XML, $obj_XML->xpath("/stored-cards/card[@preferred = 'false' and client/@id = ". $obj_TxnInfo->getClientConfig()->getID() ."]") );
					
					// Loop through sorted cards
					for ($i=0; $i<count($aObj_XML); $i++)
					{
						// Determine PSP
						switch (intval($aObj_XML[$i]["pspid"]) )
						{
						case (Constants::iDIBS_PSP):	// DIBS
							// Authorise payment with PSP based on Ticket
							$obj_PSP = new DIBS($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo);
							$mExternalID = $obj_PSP->authTicket( (integer) $aObj_XML[$i]->ticket);
							// Payment successfully authorised
							if ($mExternalID > 0)
							{
								// Initialise Callback to Client
								$aCPM_CONN_INFO["path"] = "/callback/dibs.php";
								$obj_PSP->initCallback(HTTPConnInfo::produceConnInfo($aCPM_CONN_INFO), intval($aObj_XML[$i]->type["id"]), $mExternalID);
								$i = count($aObj_XML);
							}
							break;
						default:	// Error: Unkown PSP
							break;
						}
					}
				}
				// Auto Top-Up Account
				if ($mExternalID == -1 && count($obj_XML->xpath("/stored-cards/card[client/@id = ". $obj_TxnInfo->getClientConfig()->getCountryConfig()->getID() ."]") ) > 0)
				{
					/*
					 * Create new Top-Up Transaction:
					 * 	- Set the Transaction Type as a Top-Up Purchase
					 * 	- End-User's Account ID
					 * 	- Calculate the amount that the account needs to be topped up with
					 */
					$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getCountryConfig()->getID(), -1);
					$obj_General = new General($_OBJ_DB, $_OBJ_TXT);
					$iTxnID = $obj_General->newTransaction($obj_ClientConfig, Constants::iTOPUP_PURCHASE_TYPE);
					$aTxnInfo = array("typeid" => Constants::iTOPUP_PURCHASE_TYPE, "accountid" => $iAccountID,
									  "amount" => $obj_TxnInfo->getAmount() - $iAccountBalance);
					$oTI = TxnInfo::produceInfo($iTxnID, $obj_TxnInfo, $aTxnInfo);
					// Update Transaction Log
					$obj_mPoint->logTransaction($oTI);
					
					// Get Stored Cards for System User with Preferred Card first
					$aObj_XML = $obj_XML->xpath("/stored-cards/card[@preferred = 'true' and client/@id = ". $obj_TxnInfo->getClientConfig()->getCountryConfig()->getID() ."]");
					$aObj_XML = array_merge($aObj_XML, $obj_XML->xpath("/stored-cards/card[@preferred = 'false' and client/@id = ". $obj_TxnInfo->getClientConfig()->getCountryConfig()->getID() ."]") );
					
					// Loop through sorted cards
					for ($i=0; $i<count($aObj_XML); $i++)
					{
						// Determine PSP
						switch (intval($aObj_XML[$i]["pspid"]) )
						{
						case (Constants::iDIBS_PSP):	// DIBS
							// Authorise payment with PSP based on Ticket
							$obj_PSP = new DIBS($_OBJ_DB, $_OBJ_TXT, $oTI);
							$mExternalID = $obj_PSP->authTicket( (integer) $aObj_XML[$i]->ticket);
							// Payment successfully authorised
							if ($mExternalID > 0)
							{
								// Initialise Callback to Client
								$aCPM_CONN_INFO["path"] = "/callback/dibs.php";
								$obj_PSP->initCallback(HTTPConnInfo::produceConnInfo($aCPM_CONN_INFO), intval($aObj_XML[$i]->type["id"]), $mExternalID);
								$i = count($aObj_XML);
							}
							break;
						default:	// Error: Unkown PSP
							break;
						}
					}
					// Unable to Auto Top-Up account - Payment Transaction rejected by PSP
					if ($mExternalID != -1)
					{
						$obj_mPoint->purchase($obj_TxnInfo->getAccountID(), $obj_TxnInfo->getID(), $obj_TxnInfo->getAmount() );
						// Initialise Callback to Client
						$obj_PSP = new CellpointMobile($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo);
						$obj_PSP->initCallback(HTTPConnInfo::produceConnInfo($aCPM_CONN_INFO), Constants::iEMONEY_CARD, Constants::iPAYMENT_ACCEPTED_STATE);
					}
				}
				// Pay using Premium SMS
				if ($mExternalID == -1 && $obj_mPoint->psmsAvailable($obj_TxnInfo->getAmount() ) === true)
				{
					// Send Billing SMS through GoMobile
					$obj_MsgInfo = $obj_PSP->sendBillingSMS(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO) );
					$mExternalID = $obj_MsgInfo->getGoMobileID();
					// Initialise Callback to Client
					$obj_PSP->initCallback(HTTPConnInfo::produceConnInfo($aCPM_CONN_INFO), Constants::iPSMS_CARD, $obj_MsgInfo->getReturnCodes(), $obj_MsgInfo->getGoMobileID() );
				}
				// All Payment Attempts have failed
				if ($mExternalID == -1)
				{
					// Initialise Callback to Client
					$obj_PSP->initCallback(HTTPConnInfo::produceConnInfo($aCPM_CONN_INFO), Constants::iEMONEY_CARD, Constants::iPAYMENT_REJECTED_STATE);
				}
			}
		}
		// Pay using Premium SMS
		elseif ($obj_mPoint->psmsAvailable($obj_TxnInfo->getAmount() ) === true)
		{
			// Send Billing SMS through GoMobile
			$obj_MsgInfo = $obj_PSP->sendBillingSMS(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO) );
			// Initialise Callback to Client
			$obj_PSP->initCallback(HTTPConnInfo::produceConnInfo($aCPM_CONN_INFO), Constants::iPSMS_CARD, $obj_MsgInfo->getReturnCodes(), $obj_MsgInfo->getGoMobileID() );
		}
		// No payment method available
		else
		{
		// Initialise Callback to Client
			$obj_PSP->initCallback(HTTPConnInfo::produceConnInfo($aCPM_CONN_INFO), Constants::iEMONEY_CARD, Constants::iPAYMENT_REJECTED_STATE);
		}
		break;
	case (in_array(strtoupper($obj_MsgInfo->getBody() ), $aREJECT_WORDS) ):	// Payment Rejected by End-User
		$obj_PSP = new CellpointMobile($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo);
		// Initialise Callback to Client
		$obj_PSP->initCallback(HTTPConnInfo::produceConnInfo($aCPM_CONN_INFO), Constants::iEMONEY_CARD, Constants::iPAYMENT_REJECTED_STATE);
		break;
	default:	// Unknown Response
		$obj_MsgInfo = GoMobileMessage::produceMessage(Constants::iMT_SMS_TYPE, $obj_MsgInfo->getCountry(), $obj_MsgInfo->getOperator(), $obj_MsgInfo->getChannel(), $obj_MsgInfo->getKeyword(), Constants::iMT_PRICE, $obj_MsgInfo->getSender(), $_OBJ_TXT->_("SMS Purchase - Unknown Reponse"), $obj_MsgInfo->getGoMobileID() );
		$obj_MsgInfo->setDescription("mPoint - SMS Purchase");
		$obj_MsgInfo->enableConcatenation();
		$obj_GoMobile->communicate($obj_MsgInfo);
		break;
	}
}
// Transaction not found
else
{
?>
	<root>
		<message type="2" id="<?= $obj_MsgInfo->getGoMobileID(); ?>">
			<transaction><?= $obj_MsgInfo->getGoMobileID(); ?></transaction>
			<country><?= $obj_MsgInfo->getCountry(); ?></country>
			<operator><?= $obj_MsgInfo->getOperator(); ?></operator>
			<channel><?= $obj_MsgInfo->getChannel(); ?></channel>
			<keyword><?= $obj_MsgInfo->getKeyword(); ?></keyword>
			<recipient><?= $obj_MsgInfo->getSender(); ?></recipient>
			<body type="200" concat="false"><?= htmlspecialchars($_OBJ_TXT->_("SMS Purchase - Transaction not found"), ENT_NOQUOTES); ?></body>
			<price><?= Constants::iMT_PRICE; ?></price>
			<description>mPoint - SMS Purchase</description>
		</message>
	</root>
<?php
}
?>