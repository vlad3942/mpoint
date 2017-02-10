<?php

/**
 * This files contains the Controller for mPoint's Mobile Web API.
 * The Controller will ensure that all input from a Mobile Internet Site or Mobile Application is validated and a new payment transaction is started.
 * Finally, assuming the Client Input is valid, the Controller will redirect the Customer to one of the following flow start pages:
 * 	- Payment Flow: /pay/card.php
 * 	- Shop Flow: /shop/delivery.php
 * If the input provided was determined to be invalid, an error page will be generated.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package API
 * @subpackage MobileWeb
 * @version 1.10
 */

// Require Global Include File
require_once ("../inc/include.php");

// Require the PHP API for handling the connection to GoMobile
require_once (sAPI_CLASS_PATH . "/gomobile.php");

// Require Business logic for the End-User Account Component
require_once (sCLASS_PATH . "/enduser_account.php");
// Require Business logic for the Mobile Web module
require_once (sCLASS_PATH . "/mobile_web.php");
// Require Business logic for the Select Credit Card component
require_once (sCLASS_PATH . "/credit_card.php");

// Require Business logic for the validating client Input
require_once (sCLASS_PATH . "/validate.php");

session_start ();
if ($_REQUEST ["return"]) {
	$_SESSION ["return"] = $_REQUEST ["return"];
}
$aMsgCds = array ();
header ( 'Content-Type: text/xml; charset="UTF-8"' );
// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants ( array (
		"AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH,
		"AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH 
) );

// Set Global Defaults
if (array_key_exists ( "account", $_REQUEST ) === false || intval ( $_REQUEST ['account'] ) <= 0) {
	$_REQUEST ['account'] = - 1;
}
if (array_key_exists ( "orderid", $_REQUEST ) === false) {
	$_REQUEST ['orderid'] = null;
}
if (array_key_exists ( "email", $_REQUEST ) === false) {
	$_REQUEST ['email'] = "";
}

$obj_mPoint = new General ( $_OBJ_DB, $_OBJ_TXT );

// Validate basic information
if (Validate::valBasic ( $_OBJ_DB, $_REQUEST ['clientid'], $_REQUEST ['account'] ) == 100) {
	$obj_ClientConfig = ClientConfig::produceConfig ( $_OBJ_DB, $_REQUEST ['clientid'], $_REQUEST ['account'] );
	
	$obj_Validator = new Validate ( $obj_ClientConfig->getCountryConfig () );
	if (array_key_exists ( "mac", $_REQUEST ) === true && $obj_Validator->valMAC ( $_REQUEST ['mac'], $_REQUEST, $obj_ClientConfig->getPassword () ) != 10) {
		$aMsgCds [210] = $_REQUEST ['mac'];
	}
	
	// Set Client Defaults
	if (array_key_exists ( "operator", $_REQUEST ) === false) {
		$_REQUEST ['operator'] = $obj_ClientConfig->getCountryConfig ()->getID () * 100;
	}
	if (array_key_exists ( "logo-url", $_REQUEST ) === false) {
		$_REQUEST ['logo-url'] = $obj_ClientConfig->getLogoURL ();
	}
	if (array_key_exists ( "css-url", $_REQUEST ) === false) {
		$_REQUEST ['css-url'] = $obj_ClientConfig->getCSSURL ();
	}
	if (array_key_exists ( "accept-url", $_REQUEST ) === false) {
		$_REQUEST ['accept-url'] = $obj_ClientConfig->getAcceptURL ();
	}
	if (array_key_exists ( "cancel-url", $_REQUEST ) === false) {
		$_REQUEST ['cancel-url'] = $obj_ClientConfig->getCancelURL ();
	}
	if (array_key_exists ( "callback-url", $_REQUEST ) === false) {
		$_REQUEST ['callback-url'] = $obj_ClientConfig->getCallbackURL ();
	}
	if (array_key_exists ( "icon-url", $_REQUEST ) === false) {
		$_REQUEST ['icon-url'] = $obj_ClientConfig->getIconURL ();
	}
	if (array_key_exists ( "language", $_REQUEST ) === false) {
		$_REQUEST ['language'] = $obj_ClientConfig->getLanguage ();
	}
	if (array_key_exists ( "markup", $_REQUEST ) === false) {
		$_REQUEST ['markup'] = $obj_ClientConfig->getAccountConfig ()->getMarkupLanguage ();
	}
	if (array_key_exists ( "auth-url", $_REQUEST ) === false) {
		$_REQUEST ['auth-url'] = $obj_ClientConfig->getAuthenticationURL ();
	}
	
	if (array_key_exists ( "country", $_REQUEST ) === true && empty ( $_REQUEST ['country'] ) == false) {
		$_REQUEST ['country-config'] = CountryConfig::produceConfig ( $_OBJ_DB, ( integer ) $_REQUEST ['country'] );
	}
	
	$obj_mPoint = new MobileWeb ( $_OBJ_DB, $_OBJ_TXT, $obj_ClientConfig );
	
	if(array_key_exists("txnid", $_REQUEST) === true && empty($_REQUEST["mobile"]) == false)
	{
		$iTxnID = $_REQUEST["txnid"];
	}
	else 
	{
		$iTxnID = $obj_mPoint->newTransaction ( Constants::iPURCHASE_VIA_WEB );
	}
	
	/* ========== Input Validation Start ========== */
	if(array_key_exists("mobile", $_REQUEST) === true && empty($_REQUEST["mobile"]) == false)
	{
	if ($obj_Validator->valMobile ( $_REQUEST ['mobile'] ) != 10 && $obj_ClientConfig->smsReceiptEnabled () === true) {
		$aMsgCds [$obj_Validator->valMobile ( $_REQUEST ['mobile'] ) + 30] = $_REQUEST ['mobile'];
	}
	}
	if(array_key_exists("operator", $_REQUEST) === true & $_REQUEST["operator"]!="")
	{
	if ($obj_Validator->valOperator ( $_REQUEST ['operator'] ) != 10 && $obj_ClientConfig->smsReceiptEnabled () === true) {
		$aMsgCds [$obj_Validator->valOperator ( $_REQUEST ['operator'] ) + 40] = $_REQUEST ['operator'];
	}
	}
	if(array_key_exists("amount", $_REQUEST) === true & $_REQUEST["amount"]!="")
	{
	if ($obj_Validator->valPrice ( $obj_ClientConfig->getMaxAmount (), $_REQUEST ['amount'] ) != 10) {
		$aMsgCds [$obj_Validator->valPrice ( $obj_ClientConfig->getMaxAmount (), $_REQUEST ['amount'] ) + 50] = $_REQUEST ['amount'];
	}
	}
	if(array_key_exists("language", $_REQUEST) === true & $_REQUEST["language"]!="")
	{
	if ($obj_Validator->valLanguage ( $_REQUEST ['language'] ) != 10) {
		$aMsgCds [$obj_Validator->valLanguage ( $_REQUEST ['language'] ) + 130] = $_REQUEST ['language'];
	}
	}
	if(array_key_exists("email", $_REQUEST) === true & $_REQUEST["email"]!="")
	{
	if ($obj_Validator->valEMail ( $_REQUEST ['email'] ) != 1 && $obj_Validator->valEMail ( $_REQUEST ['email'] ) != 10) {
		$aMsgCds [$obj_Validator->valEMail ( $_REQUEST ['email'] ) + 140] = $_REQUEST ['email'];
	}
	}
	if(array_key_exists("markup", $_REQUEST) === true & $_REQUEST["markup"]!="")
	{
	if ($obj_Validator->valMarkupLanguage ( $_REQUEST ['markup'] ) != 10) {
		$aMsgCds [$obj_Validator->valMarkupLanguage ( $_REQUEST ['markup'] ) + 190] = $_REQUEST ['markup'];
	}
	}
	
	
	
	// Validate URLs
	if ($obj_Validator->valURL ( $_REQUEST ['logo-url'] ) > 1 && $obj_Validator->valURL ( $_REQUEST ['logo-url'] ) != 10) {
		$aMsgCds [$obj_Validator->valURL ( $_REQUEST ['logo-url'] ) + 70] = $_REQUEST ['logo-url'];
	}
	if ($obj_Validator->valURL ( $_REQUEST ['css-url'] ) != 10) {
		$aMsgCds [$obj_Validator->valURL ( $_REQUEST ['css-url'] ) + 80] = $_REQUEST ['css-url'];
	}
	if ($obj_Validator->valURL ( $_REQUEST ['accept-url'] ) > 1 && $obj_Validator->valURL ( $_REQUEST ['accept-url'] ) != 10) {
		$aMsgCds [$obj_Validator->valURL ( $_REQUEST ['accept-url'] ) + 90] = $_REQUEST ['accept-url'];
	}
	if ($obj_Validator->valURL ( $_REQUEST ['cancel-url'] ) > 1 && $obj_Validator->valURL ( $_REQUEST ['cancel-url'] ) != 10) {
		$aMsgCds [$obj_Validator->valURL ( $_REQUEST ['cancel-url'] ) + 100] = $_REQUEST ['cancel-url'];
	}
	if ($obj_Validator->valURL ( $_REQUEST ['callback-url'] ) != 10) {
		$aMsgCds [$obj_Validator->valURL ( $_REQUEST ['callback-url'] ) + 110] = $_REQUEST ['callback-url'];
	}
	if ($obj_Validator->valURL ( $_REQUEST ['icon-url'] ) > 1 && $obj_Validator->valURL ( $_REQUEST ['icon-url'] ) != 10) {
		$aMsgCds [$obj_Validator->valURL ( $_REQUEST ['icon-url'] ) + 160] = $_REQUEST ['icon-url'];
	}
	// Security Violation: Authentication URL must be configured for Client
	if (strlen ( $_REQUEST ['auth-url'] ) > 0 && strlen ( $obj_ClientConfig->getAuthenticationURL () ) == 0) {
		$aMsgCds [209] = $_REQUEST ['auth-url'];
	} elseif ($obj_Validator->valURL ( $_REQUEST ['auth-url'], $obj_ClientConfig->getAuthenticationURL () ) > 1 && $obj_Validator->valURL ( $_REQUEST ['auth-url'], $obj_ClientConfig->getAuthenticationURL () ) != 10) {
		$aMsgCds [$obj_Validator->valURL ( $_REQUEST ['auth-url'], $obj_ClientConfig->getAuthenticationURL () ) + 200] = $_REQUEST ['auth-url'];
	}
	/* ========== Input Validation End ========== */
	
	// Success: Input Valid
	if (count ( $aMsgCds ) == 0) {
		try {
			// Update Transaction State
			$_REQUEST ['typeid'] = Constants::iPURCHASE_VIA_WEB;
			$_REQUEST ['gomobileid'] = - 1;
			$_REQUEST ['description'] = "";
			$_REQUEST ['ip'] = $_SERVER ['REMOTE_ADDR'];
			$_REQUEST ['amount'] = $_REQUEST ['amount'] * 100;
			$obj_mPoint->newMessage ( $iTxnID, Constants::iINPUT_VALID_STATE, var_export ( $_REQUEST, true ) );
			if (array_key_exists ( "auth-token", $_REQUEST ) === true) {
				$_SESSION ['obj_Info']->setInfo ( "auth-token", $_REQUEST ['auth-token'] );
			}

			if(array_key_exists("txnid", $_REQUEST) === true && empty($_REQUEST["txnid"]) == false)
			{
				$obj_TxnInfo = TxnInfo::produceInfo ($iTxnID, $_OBJ_DB);
				$_REQUEST["mobile"] = $obj_TxnInfo->getMobile();
				$_REQUEST["email"] = $obj_TxnInfo->getEMail();
				$_REQUEST["language"] = $obj_TxnInfo->getLanguage();
				$_REQUEST["markup"] = $obj_TxnInfo->getMarkupLanguage();
				
			}
			else
			{
				$obj_TxnInfo = TxnInfo::produceInfo ( $iTxnID, $obj_ClientConfig, $_REQUEST );
				
			}
			$aAirlinedata = unserialize($_REQUEST["orderdata"]);
			if(count($aAirlinedata) == 1 && count($aAirlinedata["orders"]) > 0)
			{
				for($i=0; $i<count($aAirlinedata["orders"]); $i++)
				{
					for ($j=0; $j<count($aAirlinedata["orders"][$i]["lineitem"]); $j++)
					{
						if(count($aAirlinedata["orders"][$i]["lineitem"]) > 0)
						{
							$data['orders'][$j]['product-sku'] = (string) $aAirlinedata["orders"][$i]["lineitem"][$j]['product']['sku'];
							$data['orders'][$j]['product-name'] = (string) $aAirlinedata["orders"][$i]["lineitem"][$j]['product']['name'];
							$data['orders'][$j]['product-description'] = (string) $aAirlinedata["orders"][$i]["lineitem"][$j]['product']['description'];
							$data['orders'][$j]['product-image-url'] = (string) $aAirlinedata["orders"][$i]["lineitem"][$j]['product']['imageurl'];
							$data['orders'][$j]['amount'] = (float) $aAirlinedata["orders"][$i]["lineitem"][$j]["amount"];
							if (array_key_exists ( "country", $_REQUEST ) === true && empty ( $_REQUEST ['country'] ) == false)
							{
								$country = $_REQUEST["country"];
							}
							else
							{
								$country = $obj_ClientConfig->getCountryConfig ()->getID();
							}
							$data['orders'][$j]['country-id'] = $country;
							$data['orders'][$j]['points'] = (float) $aAirlinedata["orders"][$i]["lineitem"][$j]["points"];
							$data['orders'][$j]['reward'] = (float) $aAirlinedata["orders"][$i]["lineitem"][$j]["reward"];
							$data['orders'][$j]['quantity'] = (float) $aAirlinedata["orders"][$i]["lineitem"][$j]["quantity"];
						}
					}
					$order_id = $obj_TxnInfo->setOrderDetails($_OBJ_DB, $data['orders']);
					for ($j=0; $j<count($aAirlinedata["orders"][$i]["lineitem"]); $j++)
					{
						if(count($aAirlinedata["orders"][$i]["lineitem"][$j]['product']['airlinedata']['flightdata']) > 0)
						{
							for ($k=0; $k<count($aAirlinedata["orders"][$i]["lineitem"][$j]['product']['airlinedata']['flightdata']); $k++ )
							{
								$data['flights']['service_class'] = (string) $aAirlinedata["orders"][$i]["lineitem"][$j]['product']['airlinedata']['flightdata'][$k]['serviceclass'];
								$data['flights']['departure_airport'] = (string) $aAirlinedata["orders"][$i]["lineitem"][$j]['product']['airlinedata']['flightdata'][$k]['departureairport'];
								$data['flights']['arrival_airport'] = (string) $aAirlinedata["orders"][$i]["lineitem"][$j]['product']['airlinedata']['flightdata'][$k]['arrivalairport'];
								$data['flights']['airline_code'] = (string) $aAirlinedata["orders"][$i]["lineitem"][$j]['product']['airlinedata']['flightdata'][$k]['airlinecode'];
								$data['flights']['arrival_date'] = (string) $aAirlinedata["orders"][$i]["lineitem"][$j]['product']['airlinedata']['flightdata'][$k]['arrivaldate'];
								$data['flights']['departure_date'] = (string) $aAirlinedata["orders"][$i]["lineitem"][$j]['product']['airlinedata']['flightdata'][$k]['departuredate'];
								$data['flights']['flight_number'] = (string) $aAirlinedata["orders"][$i]["lineitem"][$j]['product']['airlinedata']['flightdata'][$k]['flightnumber'];
								$data['flights']['order_id'] = $order_id;
								if(count($aAirlinedata["orders"][$i]["lineitem"][$j]['product']['airlinedata']['flightdata'][$k]['additionaldata']) > 0)
								{
									for ($l=0; $l<count($aAirlinedata["orders"][$i]["lineitem"][$j]['product']['airlinedata']['flightdata'][$k]['additionaldata']['param']); $l++)
									{
										$data['additional'][$l]['name'] = (string) $aAirlinedata["orders"][$i]["lineitem"][$j]['product']['airlinedata']['flightdata'][$k]['additionaldata']['param'][$l]['name'];
										$data['additional'][$l]['value'] = (string) $aAirlinedata["orders"][$i]["lineitem"][$j]['product']['airlinedata']['flightdata'][$k]['additionaldata']['param'][$l]['value'];
										$data['additional'][$l]['type'] = (string) "Flight";
									}
								}
								else
								{
									$data['additional'] = array();
								}
								$flight = $obj_TxnInfo->setFlightDetails($_OBJ_DB, $data['flights'], $data['additional']);
							}
						}
						if(count($aAirlinedata["orders"][$i]["lineitem"][$j]['product']['airlinedata']['passengerdata']) > 0)
						{
							for ($k=0; $k<count($aAirlinedata["orders"][$i]["lineitem"][$j]['product']['airlinedata']['passengerdata']); $k++ )
							{
								$data['passenger']['first_name'] = (string) $aAirlinedata["orders"][$i]["lineitem"][$j]['product']['airlinedata']['passengerdata'][$k]['firstname'];
								$data['passenger']['last_name'] = (string) $aAirlinedata["orders"][$i]["lineitem"][$j]['product']['airlinedata']['passengerdata'][$k]['lastname'];
								$data['passenger']['type'] = (string) $aAirlinedata["orders"][$i]["lineitem"][$j]['product']['airlinedata']['passengerdata'][$k]['type'];
								$data['passenger']['order_id'] = $order_id;
								if(count($aAirlinedata["orders"][$i]["lineitem"][$j]['product']['airlinedata']['passengerdata'][$k]['additionaldata']) > 0)
								{
									for ($l=0; $l<count($aAirlinedata["orders"][$i]["lineitem"][$j]['product']['airlinedata']['passengerdata'][$k]['additionaldata']['param']); $l++)
									{
										$data['additional'][$l]['name'] = (string) $aAirlinedata["orders"][$i]["lineitem"][$j]['product']['airlinedata']['passengerdata'][$k]['additionaldata']['param'][$l]['name'];
										$data['additional'][$l]['value'] = (string) $aAirlinedata["orders"][$i]["lineitem"][$j]['product']['airlinedata']['passengerdata'][$k]['additionaldata']['param'][$l]['value'];
										$data['additional'][$l]['type'] = (string) "Passenger";
									}
								}
								else
								{
									$data['additional'] = array();
								}
								$passenger = $obj_TxnInfo->setPassengerDetails($_OBJ_DB, $data['passenger'], $data['additional']);
							}
						}
					}
					if(count($aAirlinedata["orders"][$i]['shipping']) > 0)
					{
						for ($j=0; $j<count($aAirlinedata["orders"][$i]['shipping']); $j++ )
						{
							$data['shipping_address'][$j]['name'] = (string) $aAirlinedata["orders"][$i]['shipping'][$j]['name'];
							$data['shipping_address'][$j]['street'] = (string) $aAirlinedata["orders"][$i]['shipping'][$j]['street'];
							$data['shipping_address'][$j]['street2'] = (string) $aAirlinedata["orders"][$i]['shipping'][$j]['street2'];
							$data['shipping_address'][$j]['city'] = (string) $aAirlinedata["orders"][$i]['shipping'][$j]['city'];
							$data['shipping_address'][$j]['state'] = (string) $aAirlinedata["orders"][$i]['shipping'][$j]['state'];
							$data['shipping_address'][$j]['zip'] = (string) $aAirlinedata["orders"][$i]['shipping'][$j]['zip'];
							$data['shipping_address'][$j]['country'] = (string) $aAirlinedata["orders"][$i]['shipping'][$j]['country'];
							$data['shipping_address'][$j]['reference_type'] = (string) "order";
							if($order_id!="")
							{
								$data['shipping_address'][$j]['reference_id'] = $order_id;
							}
						}
						$shipping_id = $obj_TxnInfo->setShippingDetails($_OBJ_DB, $data['shipping_address']);
					}
				}
			}
			$_SESSION ['obj_TxnInfo'] = $obj_TxnInfo;
			
			if(array_key_exists("txnid", $_REQUEST) === true && empty($_REQUEST["txnid"]) == false)
			{
				$obj_TxnInfo = TxnInfo::produceInfo ($iTxnID, $_OBJ_DB);
				$_REQUEST["mobile"] = $obj_TxnInfo->getMobile();
				$_REQUEST["email"] = $obj_TxnInfo->getEMail();
				$_REQUEST["language"] = $obj_TxnInfo->getLanguage();
				$_REQUEST["markup"] = $obj_TxnInfo->getMarkupLanguage();
			}
			else
			{
				$obj_TxnInfo = TxnInfo::produceInfo ( $iTxnID, $obj_ClientConfig, $_REQUEST );
			}
			
			$_SESSION ['obj_TxnInfo'] = $obj_TxnInfo;
			// Associate End-User Account (if exists) with Transaction
			/*
			 * $iAccountID = -1;
			 * if (array_key_exists("customer-ref", $_REQUEST) === true) { $iAccountID = EndUserAccount::getAccountIDFromExternalID($_OBJ_DB, $obj_ClientConfig, $_REQUEST['customer-ref']); }
			 * if ($iAccountID == -1 && trim($_SESSION['obj_TxnInfo']->getMobile() ) != "") { $iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $_SESSION['obj_TxnInfo']->getMobile() ); }
			 * if ($iAccountID == -1 && trim($_SESSION['obj_TxnInfo']->getEMail() ) != "") { $iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $_SESSION['obj_TxnInfo']->getEMail() ); }
			 * // Client supports global storage of payment cards
			 * if ($iAccountID == -1 && $obj_ClientConfig->getStoreCard() > 3)
			 * {
			 * if (array_key_exists("customer-ref", $_REQUEST) === true && strlen($_REQUEST['customer-ref']) > 0) { $iAccountID = EndUserAccount::getAccountIDFromExternalID($_OBJ_DB, $obj_ClientConfig, $_REQUEST['customer-ref'], false); }
			 * if ($iAccountID == -1 && trim($_SESSION['obj_TxnInfo']->getMobile() ) != "") { $iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $_SESSION['obj_TxnInfo']->getMobile(), $_SESSION['obj_TxnInfo']->getCountryConfig(), false); }
			 * if ($iAccountID == -1 && trim($_SESSION['obj_TxnInfo']->getEMail() ) != "") { $iAccountID = EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $_SESSION['obj_TxnInfo']->getEMail(), $_SESSION['obj_TxnInfo']->getCountryConfig(), false); }
			 * }
			 */
			
			$iAccountID = EndUserAccount::getAccountID ( $_OBJ_DB, $_SESSION ['obj_TxnInfo']->getClientConfig (), $_SESSION ['obj_TxnInfo']->getCountryConfig (), $_SESSION ['obj_TxnInfo']->getCustomerRef (), $_SESSION ['obj_TxnInfo']->getMobile (), $_SESSION ['obj_TxnInfo']->getEMail () );
			
			$_SESSION ['obj_TxnInfo']->setAccountID ( $iAccountID );
			// Update Transaction Log
			$obj_mPoint->logTransaction ( $_SESSION ['obj_TxnInfo'] );
			// Log additional data
			$obj_mPoint->logClientVars ( $_REQUEST );
			
			// Client is using the Physical Product Flow, ensure Shop has been Configured
			if ($_SESSION ['obj_TxnInfo']->getClientConfig ()->getFlowID () == Constants::iPHYSICAL_FLOW) {
				$_SESSION ['obj_ShopConfig'] = ShopConfig::produceConfig ( $_OBJ_DB, $_SESSION ['obj_TxnInfo']->getClientConfig () );
			}
			
			$aMsgCds [1000] = "Success";
		}		// Internal Error
		catch ( mPointException $e ) {
			$aMsgCds [$e->getCode ()] = $e->getMessage ();
		}
	} 	// Error: Invalid Input
	else {
		// Log Errors
		foreach ( $aMsgCds as $state => $debug ) {
			$obj_mPoint->newMessage ( $iTxnID, $state, $debug );
		}
	}
} // Error: Basic information is invalid
else {
	$aMsgCds [Validate::valBasic ( $_OBJ_DB, $_REQUEST ['clientid'], $_REQUEST ['account'] ) + 10] = "Client: " . $_REQUEST ['clientid'] . ", Account: " . $_REQUEST ['account'];
}

// Success: Construct "Select Credit Card" page
if (array_key_exists ( 1000, $aMsgCds ) === true) {
	$_SESSION ['obj_Info']->delInfo ( "payment-completed" );
	// Instantiate data object with the User Agent Profile for the customer's mobile device.
	
	$_SESSION ['obj_UA'] = UAProfile::produceUAProfile ( HTTPConnInfo::produceConnInfo ( $aUA_CONN_INFO ) );
	
	unset ( $_SESSION ['temp'] );
	// Start Shop Flow
	if ($_SESSION ['obj_TxnInfo']->getClientConfig ()->getFlowID () == Constants::iPHYSICAL_FLOW) {
		$_SESSION ['obj_Info']->setInfo ( "order_cost", $_SESSION ['obj_TxnInfo']->getAmount () );
		
		header ( "Location: /shop/delivery.php?" . session_name () . "=" . session_id () );
	} 	// Start Payment Flow
	else {
		
		if (strlen ( $_SESSION ['obj_TxnInfo']->getOrderID () ) > 0 && $obj_mPoint->orderAlreadyAuthorized ( $_SESSION ['obj_TxnInfo']->getOrderID () ) === true) {
			$obj_mPoint->newMessage ( $_SESSION ['obj_TxnInfo']->getID (), Constants::iPAYMENT_DUPLICATED_STATE, "Order: " . $_SESSION ['obj_TxnInfo']->getOrderID () . " already authorized" );
			
			header ( "Location: /pay/accept.php?" . session_name () . "=" . session_id () . "&mpoint-id=" . $_SESSION ['obj_TxnInfo']->getID () );
		}		// End-User already has an account that is linked to the Client
		elseif ($_SESSION ['obj_TxnInfo']->getAccountID () > 0) {
			
			$obj_mPoint = new CreditCard ( $_OBJ_DB, $_OBJ_TXT, $_SESSION ['obj_TxnInfo'], $_SESSION ['obj_UA'] );
			$obj_XML = simplexml_load_string ( $obj_mPoint->getCards ( $_SESSION ['obj_TxnInfo']->getAmount () ) );
			$obj_CardsXML = simplexml_load_string ( $obj_mPoint->getStoredCards ( $_SESSION ['obj_TxnInfo']->getAccountID (), $obj_ClientConfig ) );
			
			/*
			 * Only prepaid account available or End-User already has an e-money based prepaid account or a stored card
			 * Go to step 2: My Account
			 */
			if (count ( $obj_XML->xpath ( "/cards[item/@id = 11]" ) ) > 0 && (count ( $obj_XML->item ) == 1 || count ( $obj_CardsXML->xpath ( "/stored-cards/card[client/@id = " . $_SESSION ['obj_TxnInfo']->getClientConfig ()->getID () . "]" ) ) > 0 || ($_SESSION ['obj_TxnInfo']->getClientConfig ()->getStoreCard () > 3 && count ( $obj_CardsXML->card ) > 0))) {
				header ( "Location: /pay/card.php?" . session_name () . "=" . session_id () . "&cardtype=11" );
			} 			// Go to step 1: Select payment method
			else {
				header ( "Location: /pay/card.php?" . session_name () . "=" . session_id () );
			}
		} 		// Go to step 1: Select payment method
		else {
			header ( "Location: /pay/card.php?" . session_name () . "=" . session_id () );
		}
	}
} // Error: Construct Status Page
else {
	$s = date ( "Y-m-d H:i:s" ) . "\n";
	$s .= "REQUEST: " . "\n" . var_export ( $_REQUEST, true ) . "\n";
	$s .= "ERRORS: " . "\n" . var_export ( $aMsgCds, true ) . "\n";
	file_put_contents ( sLOG_PATH . "/debug_" . date ( "Y-m-d" ) . ".log", $s );
	
	$_GET ['msg'] = array_keys ( $aMsgCds );
	
	$xml = '<?xml version="1.0" encoding="UTF-8"?>';
	$xml .= '<?xml-stylesheet type="text/xsl" href="/templates/' . sTEMPLATE . '/html5/status.xsl"?>';
	$xml .= '<root>';
	$xml .= $obj_mPoint->getMessages ( "Status" );
	$xml .= '</root>';
	
	// Display page
	echo $xml;
}
?>