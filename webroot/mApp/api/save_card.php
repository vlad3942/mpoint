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
 * @subpackage MobileApp
 * @version 1.10
 */

// Require Global Include File
require_once("../../inc/include.php");

// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require Data Class for Client Information
require_once(sCLASS_PATH ."/clientinfo.php");
// Require data class for Customer Information
require_once(sCLASS_PATH ."/customer_info.php");

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );
/*
$_SERVER['PHP_AUTH_USER'] = "CPMDemo";
$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<save-card client-id="100">';
$HTTP_RAW_POST_DATA .= '<card type-id="6" psp-id="9" preferred="true" charge-type-id="2">';
$HTTP_RAW_POST_DATA .= '<name>My VISA</name>';
$HTTP_RAW_POST_DATA .= '<card-number-mask>540287******5344</card-number-mask>';
$HTTP_RAW_POST_DATA .= '<expiry-month>10</expiry-month>';
$HTTP_RAW_POST_DATA .= '<expiry-year>14</expiry-year>';
$HTTP_RAW_POST_DATA .= '<token>123456-ABCD</token>';
$HTTP_RAW_POST_DATA .= '<card-holder-name>Jonatan Evad Buus</card-holder-name>';
$HTTP_RAW_POST_DATA .= '<address country-id="100">';
$HTTP_RAW_POST_DATA .= '<first-name>Jonatan Evald</first-name>';
$HTTP_RAW_POST_DATA .= '<last-name>Buus</last-name>';
$HTTP_RAW_POST_DATA .= '<street>Dexter Gordons Vej 3, 6.tv</street>';
$HTTP_RAW_POST_DATA .= '<postal-code>2450</postal-code>';
$HTTP_RAW_POST_DATA .= '<city>'. utf8_encode("København SV") .'</city>';
$HTTP_RAW_POST_DATA .= '<state>N/A</state>';
$HTTP_RAW_POST_DATA .= '</address>';
$HTTP_RAW_POST_DATA .= '</card>';
$HTTP_RAW_POST_DATA .= '<client-info platform="iOS" version="1.00" language="da">';
$HTTP_RAW_POST_DATA .= '<customer-ref>ABC-123</customer-ref>';
$HTTP_RAW_POST_DATA .= '<mobile country-id="100" operator-id="10000">28882861</mobile>';
$HTTP_RAW_POST_DATA .= '<email>jona@oismail.com</email>';
$HTTP_RAW_POST_DATA .= '<device-id>23lkhfgjh24qsdfkjh</device-id>';
$HTTP_RAW_POST_DATA .= '</client-info>';
$HTTP_RAW_POST_DATA .= '</save-card>';
$HTTP_RAW_POST_DATA .= '</root>';
*/
$obj_DOM = simpledom_load_string(file_get_contents('php://input'));


if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
	if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mpoint.xsd") === true && count($obj_DOM->{'save-card'}) > 0)
	{
		$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);

		for ($i=0; $i<count($obj_DOM->{'save-card'}); $i++)
		{
			// Set Global Defaults
			if (empty($obj_DOM->{'save-card'}[$i]["account"]) === true || (int)($obj_DOM->{'save-card'}[$i]["account"]) < 1) { $obj_DOM->{'save-card'}[$i]["account"] = -1; }

			// Validate basic information
			$code = Validate::valBasic($_OBJ_DB, (integer) $obj_DOM->{'save-card'}[$i]["client-id"], (integer) $obj_DOM->{'save-card'}[$i]["account"]);
			if ($code == 100)
			{
				$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'save-card'}[$i]["client-id"], (integer) $obj_DOM->{'save-card'}[$i]["account"]);

				// Client successfully authenticated
				if ($obj_ClientConfig->getUsername() == trim($_SERVER['PHP_AUTH_USER']) && $obj_ClientConfig->getPassword() == trim($_SERVER['PHP_AUTH_PW']) )
				{
					$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'save-card'}[$i]->{'client-info'}->mobile["country-id"]);
					if ( ($obj_CountryConfig instanceof CountryConfig) === false || $obj_CountryConfig->getID() < 1) { $obj_CountryConfig = $obj_ClientConfig->getCountryConfig(); }

					for ($j=0; $j<count($obj_DOM->{'save-card'}[$i]->card); $j++)
					{
						$obj_mPoint = new EndUserAccount($_OBJ_DB, $_OBJ_TXT, $obj_ClientConfig);
						$obj_Validator = new Validate($obj_CountryConfig);
						$aMsgCds = array();

						if (empty($obj_DOM->{'save-card'}[$i]->card[$j]->name) === false && $obj_Validator->valName( (string) $obj_DOM->{'save-card'}[$i]->card[$j]->name) != 10) { $aMsgCds[] = $obj_Validator->valName( (string) $obj_DOM->{'save-card'}[$i]->card[$j]->name) + 50; }
						if ((int)($obj_DOM->{'save-card'}[$i]->card[$j]["type-id"]) == 0 && (int)($obj_DOM->{'save-card'}[$i]->card[$j]["id"]) == 0)
						{
							$aMsgCds[] = 31;
						}
						if ((int)($obj_DOM->{'save-card'}[$i]->card[$j]["type-id"]) > 0)
						{
							if ($obj_Validator->valCardTypeID($_OBJ_DB, (integer) $obj_DOM->{'save-card'}[$i]->card[$j]["type-id"])  != 10) { $aMsgCds[] = $obj_Validator->valCardTypeId($_OBJ_DB, (integer) $obj_DOM->{'save-card'}[$i]->card[$j]["type-id"]) + 40; }
						}

						$iAccountID = EndUserAccount::getAccountID_Static($_OBJ_DB, $obj_ClientConfig, $obj_CountryConfig, $obj_DOM->{'save-card'}[$i]->{'client-info'}->{'customer-ref'}, $obj_DOM->{'save-card'}[$i]->{'client-info'}->mobile, $obj_DOM->{'save-card'}[$i]->{'client-info'}->email);
						
						// Modifying an existing Stored Card
						if ((int)($obj_DOM->{'save-card'}[$i]->card[$j]["id"]) > 0)
						{
							if ($obj_Validator->valStoredCard($_OBJ_DB, $iAccountID, (integer) $obj_DOM->{'save-card'}[$i]->card[$j]["id"]) != 10) { $aMsgCds[] = $obj_Validator->valStoredCard($_OBJ_DB, $iAccountID, (integer) $obj_DOM->{'save-card'}[$i]->card[$j]["id"]) + 20; }
						}
						// Saving Masked Card Details
						if (empty($obj_DOM->{'save-card'}[$i]->card[$j]->token) === false)
						{
							if ($obj_Validator->valPSPID($_OBJ_DB, (integer) $obj_DOM->{'save-card'}[$i]->card[$j]["psp-id"]) != 10) { $aMsgCds[] = $obj_Validator->valPSPID($_OBJ_DB, (integer) $obj_DOM->{'save-card'}[$i]->card[$j]["psp-id"]) + 60; }
							if ($obj_Validator->valMaxCards($_OBJ_DB, $iAccountID, $obj_ClientConfig->getMaxCards(), (integer) $obj_DOM->{'save-card'}[$i]["client-id"] ) != 10) { $aMsgCds[] = $obj_Validator->valMaxCards($_OBJ_DB, $iAccountID, $obj_ClientConfig->getMaxCards(), (integer) $obj_DOM->{'save-card'}[$i]["client-id"]) + 70; }
						}
						//if (count($obj_DOM->{'save-card'}[$i]->card[$j]->{'address'}) == 1)
						//{
						//	if ($obj_Validator->valState($_OBJ_DB, utf8_decode( (string) $obj_DOM->{'save-card'}[$i]->card[$j]->{'address'}->state) ) != 10) { $aMsgCds[] = $obj_Validator->valState($_OBJ_DB, utf8_decode( (string) $obj_DOM->{'save-card'}[$i]->card[$j]->{'address'}->state) ) + 80; }
						//}
                        $chkName = $obj_Validator->valCardFullname((string) $obj_DOM->{'save-card'}[$i]->card[$j]->name);
                        if($chkName != 10){
                            $aMsgCds[] = 62;
                        }
						// Success: Input Valid
						if (count($aMsgCds) == 0)
						{
						    //get enduseraccount
                                $iProfileID = -1;
                                if ($iAccountID < 0) {
                                    if ((is_array($obj_ClientConfig->getAdditionalProperties(Constants::iInternalProperty, "ENABLE_PROFILE_ANONYMIZATION"))
                                        && count($obj_ClientConfig->getAdditionalProperties(Constants::iInternalProperty, "ENABLE_PROFILE_ANONYMIZATION")) == 0 )
                                        || $obj_ClientConfig->getAdditionalProperties(Constants::iInternalProperty, "ENABLE_PROFILE_ANONYMIZATION") === "false") {
                                        if (empty($obj_DOM->{'save-card'}[$i]->{'client-info'}->{'customer-ref'}) === false) {
                                            $iAccountID = EndUserAccount::getAccountIDFromExternalID($_OBJ_DB, $obj_ClientConfig, $obj_DOM->{'save-card'}[$i]->{'client-info'}->{'customer-ref'}, ($obj_ClientConfig->getStoreCard() <= 3));
                                        }
                                        if ($iAccountID < 0 && empty($obj_DOM->{'save-card'}[$i]->{'client-info'}->mobile) === false) {
                                            $iAccountID = EndUserAccount::getAccountID_Static($_OBJ_DB, $obj_ClientConfig, $obj_DOM->{'save-card'}[$i]->{'client-info'}->mobile, $obj_CountryConfig, ($obj_ClientConfig->getStoreCard() <= 3));
                                        }
                                        if ($iAccountID < 0 && empty($obj_DOM->{'save-card'}[$i]->{'client-info'}->email) === false) {
                                            $iAccountID = EndUserAccount::getAccountID_Static($_OBJ_DB, $obj_ClientConfig, $obj_DOM->{'save-card'}[$i]->{'client-info'}->email, $obj_CountryConfig, ($obj_ClientConfig->getStoreCard() <= 3));
                                        }
                                        if ($iAccountID < 0) {
                                            $iAccountID = $obj_mPoint->getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_DOM->{'save-card'}[$i]->{'client-info'}->mobile, $obj_CountryConfig);
                                        }
                                        if ($iAccountID < 0) {
                                            $iAccountID = $obj_mPoint->getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_DOM->{'save-card'}[$i]->{'client-info'}->email, $obj_CountryConfig);
                                        }
                                    } else {
                                        //If data anonymization is enabled for the client  -- the profile id will always be present for registered user trying to store card
                                        if ($obj_ClientConfig->getAdditionalProperties(Constants::iInternalProperty, "ENABLE_PROFILE_ANONYMIZATION") == "true") {
                                            //if request does not contain clientinfo/@profileid - registered user profile id then
                                            if (empty($obj_DOM->{'save-card'}[$i]->{'client-info'}["profileid"]) === true) {
                                                //Get profile from mProfile based on client info details
                                                $obj_mProfile = new Home($_OBJ_DB, $_OBJ_TXT);
                                                $iProfileID = $obj_mProfile->getProfile($obj_ClientConfig, $obj_CountryConfig->getID(), $obj_DOM->{'save-card'}[$i]->{'client-info'}->mobile, $obj_DOM->{'save-card'}[$i]->{'client-info'}->email, $obj_DOM->{'save-card'}[$i]->{'client-info'}->{'customer-ref'});
                                                if ($iProfileID < 0) {
                                                    //if not found save profile as validated registered profile
                                                    $iProfileID = $obj_mProfile->saveProfile($obj_ClientConfig, $obj_CountryConfig->getID(), $obj_DOM->{'save-card'}[$i]->{'client-info'}->mobile, $obj_DOM->{'save-card'}[$i]->{'client-info'}->email, $obj_DOM->{'save-card'}[$i]->{'client-info'}->{'customer-ref'}, "false");
                                                    if ($iProfileID < 0) {
                                                        header("HTTP/1.1 500 Internal Server Error");

                                                        $xml = '<status code="90">Unable to create new account</status>';
                                                    }
                                                }
                                            } else {
                                                $iProfileID = (integer)$obj_DOM->{'save-card'}[$i]->{'client-info'}["profileid"];
                                            }
                                        }
                                        $iAccountID = EndUserAccount::getAccountID_Static($_OBJ_DB, $obj_ClientConfig, $obj_CountryConfig, $obj_DOM->{'save-card'}[$i]->{'client-info'}->{'customer-ref'}, $obj_DOM->{'save-card'}[$i]->{'client-info'}->mobile, $obj_DOM->{'save-card'}[$i]->{'client-info'}->email, $iProfileID);
                                    }
                                }

                            // New End-User - For backward compatibility
                            if ($iAccountID < 0)
                            {
                                $iAccountID = $obj_mPoint->newAccount($obj_CountryConfig->getID(), (float) $obj_DOM->{'save-card'}[$i]->{'client-info'}->mobile, (string) $obj_DOM->{'save-card'}[$i]->password, (string) $obj_DOM->{'save-card'}[$i]->{'client-info'}->email, (string) $obj_DOM->{'save-card'}[$i]->{'client-info'}->{'customer-ref'},$obj_DOM->{'save-card'}[$i]->{'client-info'}["pushid"], true, $iProfileID);
                            }

                                // Single Sign-On
                                if (empty($obj_DOM->{'save-card'}[$i]->{'auth-token'}) === false
                                    && (empty($obj_DOM->{'save-card'}[$i]->{'auth-url'}) === false || strlen($obj_ClientConfig->getAuthenticationURL() ) > 0) )
                                {
                                    $url = $obj_ClientConfig->getAuthenticationURL();
                                    if (empty($obj_DOM->{'save-card'}[$i]->{'auth-url'}) === false)
                                    {
                                        $url = (string) $obj_DOM->{'save-card'}[$i]->{'auth-url'};
                                    }
                                    if ($obj_Validator->valURL($url, $obj_ClientConfig->getAuthenticationURL() ) == 10)
                                    {
                                        $obj_CustomerInfo = CustomerInfo::produceInfo($_OBJ_DB, $iAccountID);
                                        $obj_Customer = simplexml_load_string($obj_CustomerInfo->toXML());
                                        //for existing accounts
                                        if (empty($obj_Customer["customer-ref"]) === true && empty($obj_DOM->{'save-card'}[$i]->{'client-info'}->{'customer-ref'}) === false) {
                                            $obj_Customer["customer-ref"] = (string) $obj_DOM->{'save-card'}[$i]->{'client-info'}->{'customer-ref'};
                                        }

                                        $obj_CustomerInfo = CustomerInfo::produceInfo($obj_Customer);
                                        $auth_val_code = $obj_mPoint->auth($obj_ClientConfig, $obj_CustomerInfo, trim($obj_DOM->{'save-card'}[$i]->{'auth-token'}) , (int)($obj_DOM->{'save-card'}[$i]["client-id"]));
                                    }
                                    else { $auth_val_code = 8; }
                                }
                                else { $auth_val_code = $obj_mPoint->auth($iAccountID, (string) $obj_DOM->{'save-card'}[$i]->password); }

                                // Authentication succeeded
                                if ($auth_val_code === 10 OR $auth_val_code === 11)
                                {
                                    // Start Transaction
                                    $_OBJ_DB->query("START TRANSACTION");
                                    // Modifying an existing Stored Card
                                    if ((int)($obj_DOM->{'save-card'}[$i]->card[$j]["id"]) > 0)
                                    {
                                        if(empty($obj_DOM->{'save-card'}[$i]->card[$j]->{'card-holder-name'}) === false)
                                        {
                                            $code = $obj_mPoint->saveCardName($obj_DOM->{'save-card'}[$i]->card[$j]["id"], (string) $obj_DOM->{'save-card'}[$i]->card[$j]->name, General::xml2bool($obj_DOM->{'save-card'}[$i]->card[$j]["preferred"]),(string) $obj_DOM->{'save-card'}[$i]->card[$j]->{'card-holder-name'} );
                                        }
                                        else
                                        {
                                            $code = $obj_mPoint->saveCardName($obj_DOM->{'save-card'}[$i]->card[$j]["id"], (string) $obj_DOM->{'save-card'}[$i]->card[$j]->name, General::xml2bool($obj_DOM->{'save-card'}[$i]->card[$j]["preferred"]) );
                                        }
                                    }

                                    // Saving Masked Card Details
                                    if (empty($obj_DOM->{'save-card'}[$i]->card[$j]->token) === false)
                                    {
                                        if ((int)($obj_DOM->{'save-card'}[$i]->card[$j]->{'expiry-month'}) < 10) { $obj_DOM->{'save-card'}[$i]->card[$j]->{'expiry-month'} = "0". (int)($obj_DOM->{'save-card'}[$i]->card[$j]->{'expiry-month'}); }

                                        // The preferred attribute could be omitted as it is optional.
                                        $bPreferred = NULL;
                                        if (strlen($obj_DOM->{'save-card'}[$i]->card[$j]["preferred"]) > 0)
                                        {
                                            $bPreferred = General::xml2bool($obj_DOM->{'save-card'}[$i]->card[$j]["preferred"]);
                                        }

                                        $code = $obj_mPoint->saveCard($iAccountID,
                                                $obj_DOM->{'save-card'}[$i]->card[$j]["type-id"],
                                                $obj_DOM->{'save-card'}[$i]->card[$j]["psp-id"],
                                                (string) $obj_DOM->{'save-card'}[$i]->card[$j]->token,
                                                (string) $obj_DOM->{'save-card'}[$i]->card[$j]->{'card-number-mask'},
                                                (string) $obj_DOM->{'save-card'}[$i]->card[$j]->{'expiry-month'} ."/". substr($obj_DOM->{'save-card'}[$i]->card[$j]->{'expiry-year'}, -2),
                                                (string) $obj_DOM->{'save-card'}[$i]->card[$j]->{'card-holder-name'},
                                                (string) $obj_DOM->{'save-card'}[$i]->card[$j]->name,
                                                $bPreferred,
                                                (integer) $obj_DOM->{'save-card'}[$i]->card[$j]["charge-type-id"]) + 1;

                                    }
                                    // Naming a Stored Card
                                    else { $code = $obj_mPoint->saveCardName($iAccountID, $obj_DOM->{'save-card'}[$i]->card[$j]["type-id"], (string) $obj_DOM->{'save-card'}[$i]->card[$j]->name, General::xml2bool($obj_DOM->{'save-card'}[$i]->card[$j]["preferred"]) ); }


                                    // Save Address if passed and cards successfuly saved
                                    if ($code > 0 && empty($obj_DOM->{'save-card'}[$i]->card[$j]->{'address'}) === false)
                                    {
                                        //update or insert address for stored card
                                        if ((int)($obj_DOM->{'save-card'}[$i]->card[$j]["id"]) > 0)
                                        {
                                            $id = (int)($obj_DOM->{'save-card'}[$i]->card[$j]["id"]);
                                        }
                                        //Saving a new card with address details
                                        else
                                        {
                                            $id = $obj_mPoint->getCardIDFromCardDetails($iAccountID,
                                                $obj_DOM->{'save-card'}[$i]->card[$j]["type-id"],
                                                (string)$obj_DOM->{'save-card'}[$i]->card[$j]->{'card-number-mask'},
                                                (string)$obj_DOM->{'save-card'}[$i]->card[$j]->{'expiry-month'} . "/" . substr($obj_DOM->{'save-card'}[$i]->card[$j]->{'expiry-year'}, -2),
                                                (string)$obj_DOM->{'save-card'}[$i]->card[$j]->token);
                                        }

                                        //$sid = $obj_mPoint->getStateID( (integer) $obj_DOM->{'save-card'}[$i]->card[$j]->address["country-id"], (string) $obj_DOM->{'save-card'}[$i]->card[$j]->address->state);
                                        //if ($sid == 0) { $sid = $obj_mPoint->saveState( (integer) $obj_DOM->{'save-card'}[$i]->card[$j]->address["country-id"], (string) $obj_DOM->{'save-card'}[$i]->card[$j]->address->state); }
                                        $code = $obj_mPoint->saveAddress($id, (integer) $obj_DOM->{'save-card'}[$i]->card[$j]->address["country-id"], (string) $obj_DOM->{'save-card'}[$i]->card[$j]->address->state, (string) $obj_DOM->{'save-card'}[$i]->card[$j]->address->{'first-name'}, (string) $obj_DOM->{'save-card'}[$i]->card[$j]->address->{"last-name"}, (string) $obj_DOM->{'save-card'}[$i]->card[$j]->address->company, (string) $obj_DOM->{'save-card'}[$i]->card[$j]->address->street, (string) $obj_DOM->{'save-card'}[$i]->card[$j]->address->{"postal-code"}, (string) $obj_DOM->{'save-card'}[$i]->card[$j]->address->city,(string) $obj_DOM->{'save-card'}[$i]->card[$j]->address->{'full-name'});
                                        //if saveAddress is successful or not commit changes
                                        // Commit Saved Card
                                        if ($obj_ClientConfig->getNotificationURL() == "" || empty($obj_DOM->{'save-card'}[$i]->{'auth-token'}) === true)
                                        {
                                            $_OBJ_DB->query("COMMIT");
                                        }
                                    }
                                    // Success: Card Saved
                                    elseif ($code > 0)
                                    {
                                        // Commit Saved Card
                                        if ($obj_ClientConfig->getNotificationURL() == "" || empty($obj_DOM->{'save-card'}[$i]->{'auth-token'}) === true)
                                        {
                                            $_OBJ_DB->query("COMMIT");
                                        }
                                    }
                                    else
                                    {
                                        // Abort transaction and rollback to previous state
                                        $_OBJ_DB->query("ROLLBACK");
                                    }
                                    // Success: Card saved
                                    if ($code > 0 && $obj_ClientConfig->getNotificationURL() != "" && empty($obj_DOM->{'save-card'}[$i]->{'auth-token'}) === false)
                                    {
                                        try
                                        {
                                            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                                            $ips = array_map('trim', $ips);
                                            $ip = $ips[0];
                                            $obj_ClientInfo = ClientInfo::produceInfo($obj_DOM->{'save-card'}[$i]->{'client-info'}, $obj_CountryConfig, $ip);

                                            $aObj_XML = simplexml_load_string($obj_mPoint->getStoredCards($iAccountID, $obj_ClientConfig, true) );
                                            $aObj_XML = $aObj_XML->xpath("/stored-cards/card[client/@id = ". $obj_ClientConfig->getID() ."]");

                                            $aURL_Info = parse_url($obj_mPoint->getClientConfig()->getNotificationURL() );
                                            $aHTTP_CONN_INFO["mesb"]["protocol"] = $aURL_Info["scheme"];
                                            $aHTTP_CONN_INFO["mesb"]["host"] = $aURL_Info["host"];
                                            $aHTTP_CONN_INFO["mesb"]["port"] = $aURL_Info["port"];
                                            $aHTTP_CONN_INFO["mesb"]["path"] = $aURL_Info["path"];
                                            if (array_key_exists("query", $aURL_Info) === true) { $aHTTP_CONN_INFO["mesb"]["path"] .= "?". $aURL_Info["query"]; }
                                            $obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["mesb"]);

                                            switch ($obj_mPoint->notify($obj_ConnInfo, $obj_ClientInfo, $iAccountID, $obj_DOM->{'save-card'}[$i]->{'auth-token'}, count($aObj_XML) ) )
                                            {
                                                case (1):	// Error: Unknown response from CRM System
                                                    // Abort transaction and rollback to previous state
                                                    $_OBJ_DB->query("ROLLBACK");
                                                    header("HTTP/1.1 502 Bad Gateway");

                                                    $xml = '<status code="98">Invalid response from CRM System</status>';
                                                    break;
                                                case (2):	// Error: Notification Rejected by CRM System
                                                    // Abort transaction and rollback to previous state
                                                    $_OBJ_DB->query("ROLLBACK");
                                                    header("HTTP/1.1 502 Bad Gateway");

                                                    $xml = '<status code="97">Notification rejected by CRM System</status>';
                                                    break;
                                                case (10):	// Success: Card successfully saved
                                                    // Commit Saved Card
                                                    $_OBJ_DB->query("COMMIT");

                                                    if (empty($obj_DOM->{'save-card'}[$i]->card[$j]->token) === false)
                                                    {
                                                        if (isset($id) === false) { $id = $obj_mPoint->getCardIDFromCardDetails($iAccountID,
                                                            $obj_DOM->{'save-card'}[$i]->card[$j]["type-id"],
                                                            (string) $obj_DOM->{'save-card'}[$i]->card[$j]->{'card-number-mask'},
                                                            (string) $obj_DOM->{'save-card'}[$i]->card[$j]->{'expiry-month'} ."/". substr($obj_DOM->{'save-card'}[$i]->card[$j]->{'expiry-year'}, -2),
                                                            (string) $obj_DOM->{'save-card'}[$i]->card[$j]->token ); }
                                                        $xml = '<status code="'. ($code+99) .'" card-id="'. (int)($id) .'">Card successfully saved and CRM system notified</status>';
                                                    }
                                                    else { $xml = '<status code="'. ($code+99) .'">Card successfully saved and CRM system notified</status>'; }
                                                    break;
                                                default:	// Error: Unknown response from CRM System
                                                    // Abort transaction and rollback to previous state
                                                    $_OBJ_DB->query("ROLLBACK");
                                                    header("HTTP/1.1 502 Bad Gateway");

                                                    $xml = '<status code="99">Unknown response from CRM System</status>';
                                                    break;
                                            }
                                        }
                                            // Error: Unable to connect to CRM System
                                        catch (HTTPConnectionException $e)
                                        {
                                            // Abort transaction and rollback to previous state
                                            $_OBJ_DB->query("ROLLBACK");
                                            header("HTTP/1.1 504 Gateway Timeout");

                                            $xml = '<?xml version="1.0" encoding="UTF-8"?>';
                                            $xml .= '<root>';
                                            $xml .= '<status code="91">Unable to connect to CRM System</status>';
                                            $xml .= '</root>';
                                        }
                                            // Error: No response received from CRM System
                                        catch (HTTPSendException $e)
                                        {
                                            // Abort transaction and rollback to previous state
                                            $_OBJ_DB->query("ROLLBACK");
                                            header("HTTP/1.1 504 Gateway Timeout");

                                            $xml = '<?xml version="1.0" encoding="UTF-8"?>';
                                            $xml .= '<root>';
                                            $xml .= '<status code="92">No response received from CRM System</status>';
                                            $xml .= '</root>';
                                        }
                                    }
                                    // Success: Card successfully saved
                                    elseif ($code > 0)
                                    {
                                        if (empty($obj_DOM->{'save-card'}[$i]->card[$j]->token) === false)
                                        {
                                            if (isset($id) === false)
                                            {

                                                $id = $obj_mPoint->getCardIDFromCardDetails($iAccountID,
                                                    $obj_DOM->{'save-card'}[$i]->card[$j]["type-id"],
                                                    (string) $obj_DOM->{'save-card'}[$i]->card[$j]->{'card-number-mask'},
                                                    (string) $obj_DOM->{'save-card'}[$i]->card[$j]->{'expiry-month'} ."/". substr($obj_DOM->{'save-card'}[$i]->card[$j]->{'expiry-year'}, -2),
                                                    (string) $obj_DOM->{'save-card'}[$i]->card[$j]->token); }
                                            $xml = '<status code="'. ($code+99) .'" card-id="'. (int)($id) .'">Card successfully saved</status>';
                                        }
                                        else { $xml = '<status code="'. ($code+99) .'">Card successfully saved</status>'; }
                                    }
                                    // Internal Error: Unable to save Card
                                    else
                                    {
                                        if ($code == 60)
                                        {
                                            header("HTTP/1.1 400 Bad Request");
                                            $xml = '<status code="61">psp-id not found on Client </status>';
                                        }
                                        else if ($code == 61)
                                        {
                                            header("HTTP/1.1 400 Bad Request");
                                            $xml = '<status code="60">Missing psp-id </status>';
                                        }
                                        else
                                        {
                                            header("HTTP/1.1 500 Internal Server Error");
                                            $xml = '<status code="90">Unable to save Card ('. $code .')</status>';
                                        }
                                    }
                                } else {
                                    header("HTTP/1.1 403 Forbidden");
                                    $xml = '<status code="'. ($auth_val_code+30) .'">Authentication failed</status>';
                                }
						}
						// Invalid Input
						else
						{
							header("HTTP/1.1 400 Bad Request");

							$xml = '<status code="'. $aMsgCds[0] .'" />';
						}
					}
				}
				else
				{
					header("HTTP/1.1 401 Unauthorized");

					$xml = '<status code="401">Username / Password doesn\'t match</status>';
				}
			}
			else
			{
				header("HTTP/1.1 400 Bad Request");

				$xml = '<status code="'. $code .'">Client ID / Account doesn\'t match</status>';
			}
		}
	}
	// Error: Invalid XML Document
	elseif ( ($obj_DOM instanceof SimpleDOMElement) === false)
	{
		header("HTTP/1.1 415 Unsupported Media Type");

		$xml = '<status code="415">Invalid XML Document</status>';
	}
	// Error: Wrong operation
	elseif (empty($obj_DOM->{'save-card'}) === true)
	{
		header("HTTP/1.1 400 Bad Request");

		$xml = '';

		foreach ($obj_DOM->children() as $obj_Elem)
		{
			$xml .= '<status code="400">Wrong operation: '. $obj_Elem->getName() .'</status>';
		}
	}
	// Error: Invalid Input
	else
	{
		header("HTTP/1.1 400 Bad Request");
		$aObj_Errs = libxml_get_errors();

		$xml = '';
		for ($i=0; $i<count($aObj_Errs); $i++)
		{
			$xml .= '<status code="400">'. htmlspecialchars($aObj_Errs[$i]->message, ENT_NOQUOTES) .'</status>';
		}
	}
}
else
{
	header("HTTP/1.1 401 Unauthorized");

	$xml = '<status code="401">Authorization required</status>';
}
header("Content-Type: text/xml; charset=\"UTF-8\"");

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<root>';
echo $xml;
echo '</root>';

$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);
$obj_XML = simplexml_load_string('<root>'. $xml .'</root>');
$obj_mPoint->newAuditMessage(Constants::iOPERATION_CARD_SAVED, $obj_DOM->{'save-card'}[0]->{'client-info'}->mobile, $obj_DOM->{'save-card'}[0]->{'client-info'}->email, $obj_DOM->{'save-card'}[0]->{'client-info'}->{'customer-ref'}, $obj_XML->status["code"], (string) $obj_XML->status);
?>