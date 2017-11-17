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

// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require Business logic for the Mobile Web module
require_once(sCLASS_PATH ."/mobile_web.php");
// Require Business logic for the Select Credit Card component
require_once(sCLASS_PATH ."/credit_card.php");
// Require general Business logic for the Callback module
require_once(sCLASS_PATH ."/callback.php");
// Require specific Business logic for the CPM PSP component
require_once(sINTERFACE_PATH ."/cpm_psp.php");
// Require specific Business logic for the CPM ACQUIRER component
require_once(sINTERFACE_PATH ."/cpm_acquirer.php");
// Require specific Business logic for the DSB PSP component
require_once(sCLASS_PATH ."/dsb.php");
// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
// Require Data Class for Client Information
require_once(sCLASS_PATH ."/clientinfo.php");

$aMsgCds = array();

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );
/*
$_SERVER['PHP_AUTH_USER'] = "1415";
$_SERVER['PHP_AUTH_PW'] = "Ghdy4_ah1G";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<initialize-payment client-id="10019" account="100026">';
$HTTP_RAW_POST_DATA .= '<transaction order-no="904-70158922">';
$HTTP_RAW_POST_DATA .= '<amount country-id="100">2400</amount>';
$HTTP_RAW_POST_DATA .= '<callback-url>http://cinema.mretail.localhost/mOrder/sys/mpoint.php</callback-url>';
$HTTP_RAW_POST_DATA .= '<hmac>0489be0b8439cc6543787bd722f8d8352e23fc7e</hmac>';
$HTTP_RAW_POST_DATA .= '</transaction>';
$HTTP_RAW_POST_DATA .= '<client-info platform="iOS" version="5.1.1" language="da">';
$HTTP_RAW_POST_DATA .= '<mobile country-id="100" operator-id="10000">28882861</mobile>';
$HTTP_RAW_POST_DATA .= '<email>jona@oismail.com</email>';
$HTTP_RAW_POST_DATA .= '<device-id>4615F4E94A9749D7B7BB9654EAC00ED314212383</device-id>';
$HTTP_RAW_POST_DATA .= '</client-info>';
$HTTP_RAW_POST_DATA .= '</initialize-payment>';
$HTTP_RAW_POST_DATA .= '</root>';
*/
$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
	if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mpoint.xsd") === true && count($obj_DOM->{'initialize-payment'}) > 0)
	{
		$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);

		for ($i=0; $i<count($obj_DOM->{'initialize-payment'}); $i++)
		{
			// Set Global Defaults
			if (empty($obj_DOM->{'initialize-payment'}[$i]["account"]) === true || intval($obj_DOM->{'initialize-payment'}[$i]["account"]) < 1) { $obj_DOM->{'initialize-payment'}[$i]["account"] = -1; }

			// Validate basic information
			$code = Validate::valBasic($_OBJ_DB, (integer) $obj_DOM->{'initialize-payment'}[$i]["client-id"], (integer) $obj_DOM->{'initialize-payment'}[$i]["account"]);
			if ($code == 100)
			{
				$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'initialize-payment'}[$i]["client-id"], (integer) $obj_DOM->{'initialize-payment'}[$i]["account"]);				
				if ($obj_ClientConfig->getUsername() == trim($_SERVER['PHP_AUTH_USER']) && $obj_ClientConfig->getPassword() == trim($_SERVER['PHP_AUTH_PW'])
					&& $obj_ClientConfig->hasAccess($_SERVER['REMOTE_ADDR']) === true)
				{
					$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'initialize-payment'}[$i]->transaction->amount["country-id"]);
					if ( ($obj_CountryConfig instanceof CountryConfig) === false || $obj_CountryConfig->getID() < 1) { $obj_CountryConfig = $obj_ClientConfig->getCountryConfig(); }
					
					$obj_Validator = new Validate($obj_ClientConfig->getCountryConfig() );
					$iValResult = $obj_Validator->valPrice($obj_ClientConfig->getMaxAmount(), (integer) $obj_DOM->{'initialize-payment'}[$i]->transaction->amount);
					if ($obj_ClientConfig->getMaxAmount() > 0 && $iValResult != 10) { $aMsgCds[$iValResult + 50] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->amount; }
					
					// Hash based Message Authentication Code (HMAC) enabled for client and payment transaction is not an attempt to simply save a card
					if (strlen($obj_ClientConfig->getSalt() ) > 0
						&& (strlen($obj_DOM->{'initialize-payment'}[$i]->transaction['order-no']) > 0 || intval($obj_DOM->{'initialize-payment'}[$i]->transaction->amount) > 100 || count($obj_DOM->{'initialize-payment'}[$i]->transaction->hmac) == 1) )
					{
						$obj_ClientInfo = ClientInfo::produceInfo($obj_DOM->{'initialize-payment'}[$i]->{'client-info'},
																  CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'initialize-payment'}[$i]->{'client-info'}->mobile["country-id"]),
																  $_SERVER['HTTP_X_FORWARDED_FOR']);
						if ($obj_Validator->valHMAC(trim($obj_DOM->{'initialize-payment'}[$i]->transaction->hmac), $obj_ClientConfig, $obj_ClientInfo, trim($obj_DOM->{'initialize-payment'}[$i]->transaction['order-no']), intval($obj_DOM->{'initialize-payment'}[$i]->transaction->amount), intval($obj_DOM->{'initialize-payment'}[$i]->transaction->amount["country-id"]) ) != 10) { $aMsgCds[210] = trim($obj_DOM->{'initialize-payment'}[$i]->transaction->hmac); }
					}
					
					// Success: Input Valid
					if (count($aMsgCds) == 0)
					{
					
						$obj_mPoint = new MobileWeb($_OBJ_DB, $_OBJ_TXT, $obj_ClientConfig);
						$iTxnID = $obj_mPoint->newTransaction(Constants::iPURCHASE_VIA_APP);
						try
						{
							// Update Transaction State
							$obj_mPoint->newMessage($iTxnID, Constants::iINPUT_VALID_STATE, $obj_DOM->asXML() );
	
							$data['typeid'] = $obj_DOM->{'initialize-payment'}[$i]->transaction["type-id"];
							$data['amount'] = (float) $obj_DOM->{'initialize-payment'}[$i]->transaction->amount;
							$data['country-config'] = $obj_CountryConfig;
							if (count($obj_DOM->{'initialize-payment'}[$i]->transaction->points) == 1)
							{
								$data['points'] = (integer) $obj_DOM->{'initialize-payment'}[$i]->transaction->points;
							}
							if (count($obj_DOM->{'initialize-payment'}[$i]->transaction->reward) == 1)
							{
								$data['reward'] = (integer) $obj_DOM->{'initialize-payment'}[$i]->transaction->reward;
								$data['reward-type'] = (integer) $obj_DOM->{'initialize-payment'}[$i]->transaction->reward["type-id"];
							}
	
							$data['description'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->description;
							$data['gomobileid'] = -1;
							$data['orderid'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction["order-no"];
							
							// Adding/Updating the client data in to the enduser account.
							$data['customer-ref'] = (string) $obj_DOM->{'initialize-payment'}[$i]->{'client-info'}->{'customer-ref'};
							$data['mobile'] = (float) $obj_DOM->{'initialize-payment'}[$i]->{'client-info'}->mobile;
							$data['operator'] = (integer) $obj_DOM->{'initialize-payment'}[$i]->{'client-info'}->mobile["operator-id"];
							if (intval($data['operator']) == 0) { $data['operator'] = intval($obj_DOM->{'initialize-payment'}[$i]->{'client-info'}->mobile["country-id"]) * 100; }
							$data['email'] = (string) $obj_DOM->{'initialize-payment'}[$i]->{'client-info'}->email;
							$data['device-id'] = (string) $obj_DOM->{'initialize-payment'}[$i]->{'client-info'}->{'device-id'};
							if (count($obj_DOM->{'initialize-payment'}[$i]->{'client-info'}->ip) == 1) { $data['ip'] = (string) $obj_DOM->{'initialize-payment'}[$i]->{'client-info'}->ip; }
							elseif (array_key_exists("HTTP_X_FORWARDED_FOR", $_SERVER) === true) { $data['ip'] = $_SERVER['HTTP_X_FORWARDED_FOR']; }
							else { $data['ip'] = $_SERVER['REMOTE_ADDR']; }
							$data['logo-url'] = "";
							$data['css-url'] = "";
							/*
							 *  Added capability to accept the transaction specific accept URL.
							 *  Used by Master Pass wallet for sending a callback along with the checkout URL for getting user's card details
							 * */
							if (count($obj_DOM->{'initialize-payment'}[$i]->transaction->{'accept-url'}) == 1)
							{
								$data['accept-url'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->{'accept-url'};
							}
							else { $data['accept-url'] = $obj_ClientConfig->getAcceptURL(); }
							
							if (count($obj_DOM->{'initialize-payment'}[$i]->transaction->{'cancel-url'}) == 1)
                            {
                                $data['cancel-url'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->{'cancel-url'};
                            }
                            else { $data['cancel-url'] = $obj_ClientConfig->getCancelURL(); }
							
							if (count($obj_DOM->{'initialize-payment'}[$i]->transaction->{'callback-url'}) == 1)
							{
								$data['callback-url'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->{'callback-url'};
							}
							else { $data['callback-url'] = $obj_ClientConfig->getCallbackURL(); }
							if (count($obj_DOM->{'initialize-payment'}[$i]->transaction->{'auth-url'}) == 1)
							{
								$data['auth-url'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->{'auth-url'};
							}
							else { $data['auth-url'] = $obj_ClientConfig->getAuthenticationURL(); }
							$data['icon-url'] = "";
							$data['language'] = (string) $obj_DOM->{'initialize-payment'}[$i]->{'client-info'}["language"];
							$data['markup'] = $obj_ClientConfig->getAccountConfig()->getMarkupLanguage();
							
							$obj_CurrencyConfig = CurrencyConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'initialize-payment'}[$i]->transaction->amount["currency-id"]);
							$data['currency-config']= $obj_CurrencyConfig ;
	
							$obj_TxnInfo = TxnInfo::produceInfo($iTxnID, $obj_ClientConfig, $data);
							// Associate End-User Account (if exists) with Transaction
							$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'initialize-payment'}[$i]->{'client-info'}->mobile["country-id"]);
	
							$obj_TxnInfo->setAccountID(EndUserAccount::getAccountID($_OBJ_DB, $obj_ClientConfig, $obj_CountryConfig, $obj_TxnInfo->getCustomerRef(), $obj_TxnInfo->getMobile(), $obj_TxnInfo->getEMail() ) );
							// Update Transaction Log
							$obj_mPoint->logTransaction($obj_TxnInfo);
							
							//Test if the order/cart details are passed as part of the input XML request. 
							if(count( $obj_DOM->{'initialize-payment'}[$i]->transaction->orders) == 1 && count( $obj_DOM->{'initialize-payment'}[$i]->transaction->orders->children()) > 0 )
							{
								for ($j=0; $j<count($obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'line-item'}); $j++ )
								{
									if(count($obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'line-item'}) > 0)
									{
										$data['orders'][$j]['product-sku'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'line-item'}[$j]->product["sku"];
										$data['orders'][$j]['product-name'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'line-item'}[$j]->product->name;
										$data['orders'][$j]['product-description'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'line-item'}[$j]->product->description;
										$data['orders'][$j]['product-image-url'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'line-item'}[$j]->product->{'image-url'};
										$data['orders'][$j]['amount'] = (float) $obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'line-item'}[$j]->amount;
										$data['orders'][$j]['country-id'] = $obj_CountryConfig->getID();
										$data['orders'][$j]['points'] = (float) $obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'line-item'}[$j]->points;
										$data['orders'][$j]['reward'] = (float) $obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'line-item'}[$j]->reward;
										$data['orders'][$j]['quantity'] = (float) $obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'line-item'}[$j]->quantity;
										
										$order_id = $obj_TxnInfo->setOrderDetails($_OBJ_DB, $data['orders']);
									}
									
									if(count($obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}) > 0)
									{
										for ($k=0; $k<count($obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}); $k++ )
										{
										$data['flights']['service_class'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]->{'service-class'};
										$data['flights']['departure_airport'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]->{'departure-airport'};
										$data['flights']['arrival_airport'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]->{'arrival-airport'};
										$data['flights']['airline_code'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]->{'airline-code'};
										$data['flights']['arrival_date'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]->{'arrival-date'};
										$data['flights']['departure_date'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]->{'departure-date'};
										$data['flights']['flight_number'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]->{'flight-number'};
										$data['flights']['order_id'] = $order_id;
											if(count($obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]->{'additional-data'}) > 0)
											{
												for ($l=0; $l<count($obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]->{'additional-data'}->children()); $l++)
												{
													$data['additional'][$l]['name'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]->{'additional-data'}->param[$l]['name'];
													$data['additional'][$l]['value'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'line-item'}[$j]->product->{'airline-data'}->{'flight-detail'}[$k]->{'additional-data'}->param[$l];
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
									
									if(count($obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'line-item'}[$j]->product->{'airline-data'}->{'passenger-detail'}) > 0)
									{
										for ($k=0; $k<count($obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'line-item'}[$j]->product->{'airline-data'}->{'passenger-detail'}); $k++ )
										{
											$data['passenger']['first_name'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'line-item'}[$j]->product->{'airline-data'}->{'passenger-detail'}[$k]->{'first-name'};
											$data['passenger']['last_name'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'line-item'}[$j]->product->{'airline-data'}->{'passenger-detail'}[$k]->{'last-name'};
											$data['passenger']['type'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'line-item'}[$j]->product->{'airline-data'}->{'passenger-detail'}[$k]->{'type'};
											$data['passenger']['order_id'] = $order_id;
											if(count($obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'line-item'}[$j]->product->{'airline-data'}->{'passenger-detail'}[$k]->{'additional-data'}) > 0)
											{
												for ($l=0; $l<count($obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'line-item'}[$j]->product->{'airline-data'}->{'passenger-detail'}[$k]->{'additional-data'}->children()); $l++)
												{
													$data['additionalp'][$l]['name'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'line-item'}[$j]->product->{'airline-data'}->{'passenger-detail'}[$k]->{'additional-data'}->param[$l]['name'];
													$data['additionalp'][$l]['value'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'line-item'}[$j]->product->{'airline-data'}->{'passenger-detail'}[$k]->{'additional-data'}->param[$l];
													$data['additionalp'][$l]['type'] = (string) "Passenger";
												}
											}
											else 
											{
												$data['additionalp'] = array();
											}
											$passenger = $obj_TxnInfo->setPassengerDetails($_OBJ_DB, $data['passenger'], $data['additionalp']);
										}
									}
								}
								
								if(count($obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'shipping-address'}) > 0)
								{
									for ($j=0; $j<count($obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'shipping-address'}); $j++ )
									{
								
										$data['shipping_address'][$j]['name'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'shipping-address'}[$j]->name;
										$data['shipping_address'][$j]['street'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'shipping-address'}[$j]->street;
										$data['shipping_address'][$j]['street2'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'shipping-address'}[$j]->street2;
										$data['shipping_address'][$j]['city'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'shipping-address'}[$j]->city;
										$data['shipping_address'][$j]['state'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'shipping-address'}[$j]->state;
										$data['shipping_address'][$j]['zip'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'shipping-address'}[$j]->zip;
										$data['shipping_address'][$j]['country'] = (string) $obj_DOM->{'initialize-payment'}[$i]->transaction->orders->{'shipping-address'}[$j]->country;
										$data['shipping_address'][$j]['reference_type'] = (string) "order";
										if($order_id!="")
										{
											$data['shipping_address'][$j]['reference_id'] = $order_id;
										}
									}
									$shipping_id = $obj_TxnInfo->setShippingDetails($_OBJ_DB, $data['shipping_address']);
								}
							}
							
							if (count($obj_DOM->{'initialize-payment'}[$i]->transaction->{'custom-variables'}) == 1 && count($obj_DOM->{'initialize-payment'}[$i]->transaction->{'custom-variables'}->children() ) > 0)
							{
								$aVars = array();
								foreach ($obj_DOM->{'initialize-payment'}[$i]->transaction->{'custom-variables'}->children() as $obj_Var)
								{
									if (substr($obj_Var->getName(), 0, 4) == "var_")
									{
										$aVars[$obj_Var->getName()] = (string) $obj_Var;
									}
									else { $aVars["var_". $obj_Var->getName()] = (string) $obj_Var; }
								}
								// Log additional data
								$obj_mPoint->logClientVars($aVars);
							}
							
							
							$obj_mPoint = new CreditCard($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo);
							$obj_XML = simplexml_load_string($obj_TxnInfo->toXML(), "SimpleXMLElement", LIBXML_COMPACT);
							$xml = '<client-config id="'. $obj_ClientConfig->getID() .'" account="'. $obj_ClientConfig->getAccountConfig()->getID() .'" store-card="'. $obj_ClientConfig->getStoreCard() .'" auto-capture="'. General::bool2xml($obj_ClientConfig->useAutoCapture() ) .'" mode="'. $obj_ClientConfig->getMode() .'">';
							$xml .= '<name>'. htmlspecialchars($obj_ClientConfig->getName(), ENT_NOQUOTES) .'</name>';
							$xml .= '<callback-url>'. htmlspecialchars($obj_ClientConfig->getCallbackURL(), ENT_NOQUOTES) .'</callback-url>';
							$xml .= '<accept-url>'. htmlspecialchars($obj_ClientConfig->getAcceptURL(), ENT_NOQUOTES) .'</accept-url>';
							$xml .= '<cancel-url>'. htmlspecialchars($obj_ClientConfig->getCancelURL(), ENT_NOQUOTES) .'</cancel-url>';
							$xml .= '<app-url>'. htmlspecialchars($obj_ClientConfig->getAppURL(), ENT_NOQUOTES) .'</app-url>';
							$xml .= '<css-url>'. htmlspecialchars($obj_ClientConfig->getCSSURL(), ENT_NOQUOTES) .'</css-url>';
                            $xml .= '<logo-url>'. htmlspecialchars($obj_ClientConfig->getLogoURL(), ENT_NOQUOTES) .'</logo-url>';
                            $xml .= '<additional-config>';
                            foreach ($obj_ClientConfig->getAdditionalProperties() as $aAdditionalProperty)
                            {
                                $xml .= '<property name="'.$aAdditionalProperty['key'].'">'.$aAdditionalProperty['value'].'</property>';
                            }
                            $xml .= '</additional-config>';
							$xml .= '</client-config>';
							$xml .= '<transaction id="'. $obj_TxnInfo->getID() .'" order-no="'. htmlspecialchars($obj_TxnInfo->getOrderID(), ENT_NOQUOTES) .'" type-id="'. $obj_TxnInfo->getTypeID() .'" eua-id="'. $obj_TxnInfo->getAccountID() .'" language="'. $obj_TxnInfo->getLanguage() .'" auto-capture="'. General::bool2xml($obj_TxnInfo->useAutoCapture() ) .'" mode="'. $obj_TxnInfo->getMode() .'">';
							$xml .= $obj_XML->amount->asXML();
							if ($obj_TxnInfo->getPoints() > 0) { $xml .= $obj_XML->points->asXML(); }
							if ($obj_TxnInfo->getReward() > 0) { $xml .= $obj_XML->reward->asXML(); }
							$xml .= '<mobile country-id="'. $obj_CountryConfig->getID() .'" operator-id="'. $obj_TxnInfo->getOperator() .'">'. floatval($obj_TxnInfo->getMobile() ) .'</mobile>';
							if (trim($obj_TxnInfo->getEMail() ) != "") { $xml .= $obj_XML->email->asXML(); }
							$xml .= $obj_XML->{'callback-url'}->asXML();
							$xml .= $obj_XML->{'accept-url'}->asXML();
							$xml .= '</transaction>';
							$obj_XML = simplexml_load_string($obj_mPoint->getCards($obj_TxnInfo->getAmount() ), "SimpleXMLElement", LIBXML_COMPACT);
	
							// End-User already has an account and payment with Account enabled
							if ($obj_TxnInfo->getAccountID() > 0 && count($obj_XML->xpath("/cards/item[@type-id = 11]") ) == 1)
							{
								$obj_mPoint = new CreditCard($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo);
								$aObj_XML = simplexml_load_string($obj_mPoint->getStoredCards($obj_TxnInfo->getAccountID(), $obj_ClientConfig), "SimpleXMLElement", LIBXML_COMPACT);
								if ($obj_ClientConfig->getStoreCard() <= 3) { $aObj_XML = $aObj_XML->xpath("/stored-cards/card[client/@id = ". $obj_ClientConfig->getID() ."]"); }
								else { $aObj_XML = $aObj_XML->xpath("/stored-cards/card"); }
							}
							else { $aObj_XML = array(); }
							
							$aPSPs = array();
							$cardsXML = '<cards>';
							for ($j=0; $j<count($obj_XML->item); $j++)
							{
								// Card does not represent "My Account" or the End-User has an acccount with Stored Cards or Stored Value Account is available
								if ($obj_XML->item[$j]["type-id"] != 11
									|| ($obj_TxnInfo->getAccountID() > 0 && (count($aObj_XML) > 0 || $obj_ClientConfig->getStoreCard() == 2) ) )
								{
									if (in_array((integer) $obj_XML->item[$j]["pspid"], $aPSPs) === false) { $aPSPs[] = intval($obj_XML->item[$j]["pspid"] ); } 
									$cardsXML .= '<card id="'. $obj_XML->item[$j]["id"] .'" type-id="'. $obj_XML->item[$j]["type-id"] .'" psp-id="'. $obj_XML->item[$j]["pspid"] .'" min-length="'. $obj_XML->item[$j]["min-length"] .'" max-length="'. $obj_XML->item[$j]["max-length"] .'" cvc-length="'. $obj_XML->item[$j]["cvc-length"] .'" state-id="'. $obj_XML->item[$j]["state-id"] .'">';
									$cardsXML .= '<name>'. htmlspecialchars($obj_XML->item[$j]->name, ENT_NOQUOTES) .'</name>';
									$cardsXML .= $obj_XML->item[$j]->prefixes->asXML();
									$cardsXML .= htmlspecialchars($obj_XML->item[$j]->name, ENT_NOQUOTES);	// Backward compatibility
									$cardsXML .= '</card>';
								}
							}
							$cardsXML .= '</cards>';
							
							for ($j=0; $j<count($aPSPs); $j++)
							{
								switch ($aPSPs[$j])
								{
								case (Constants::iDSB_PSP):
									$obj_PSPConfig = PSPConfig::produceConfig($_OBJ_DB, $obj_TxnInfo->getClientConfig()->getID(), $obj_TxnInfo->getClientConfig()->getAccountConfig()->getID(), Constants::iDSB_PSP);
									$obj_PSP = new DSB($_OBJ_DB, $_OBJ_TXT, $obj_TxnInfo, $aHTTP_CONN_INFO["dsb"]);
									$cardsXML =  $obj_PSP->getExternalPaymentMethods($cardsXML);
									break;
								default:
									break;
								}
							}
							$xml .= $cardsXML;
								
							// End-User has Stored Cards available
							if (is_array($aObj_XML) === true && count($aObj_XML) > 0)
							{
								$xml .= '<stored-cards>';
								for ($j=0; $j<count($aObj_XML); $j++)
								{
									$xml .= '<card id="'. $aObj_XML[$j]["id"] .'" type-id="'. $aObj_XML[$j]->type["id"] .'" psp-id="'. $aObj_XML[$j]["pspid"] .'" preferred="'. $aObj_XML[$j]["preferred"] .'" state-id="'. $aObj_XML[$j]["state-id"] .'" charge-type-id="'. $aObj_XML[$j]["charge-type-id"] .'">';
									if (strlen($aObj_XML[$j]->name) > 0) { $xml .= $aObj_XML[$j]->name->asXML(); }
									$xml .= '<card-number-mask>'. $aObj_XML[$j]->mask .'</card-number-mask>';
									$xml .= $aObj_XML[$j]->expiry->asXML();
									if (strlen($aObj_XML[$j]->{'card-holder-name'}) > 0) { $xml .= $aObj_XML[$j]->{'card-holder-name'}->asXML(); }
									if (count($aObj_XML[$j]->address) == 1) { $xml .= $aObj_XML[$j]->address->asXML(); }
									$xml .= '</card>';
								}
								$xml .= '</stored-cards>';
							}
							if ($obj_TxnInfo->getAccountID() > 0 && $obj_ClientConfig->getStoreCard() == 2)
							{
								$obj_XML = simplexml_load_string($obj_mPoint->getAccountInfo($obj_TxnInfo->getAccountID() ) );
								$xml .= '<account id="'. $obj_TxnInfo->getAccountID() .'">';
								$xml .= $obj_XML->balance->asXML();
								$xml .= $obj_XML->points->asXML();
								$xml .= '</account>';
							}
						}
						// Internal Error
						catch (mPointException $e)
						{
							trigger_error("Unknown error: ". $e->getMessage() ."(". $e->getCode() .")" ."\n". $e->getTrace(), E_USER_WARNING);
	
							header("HTTP/1.1 500 Internal Server Error");
	
							$xml = '<status code="'. $e->getCode() .'">'. htmlspecialchars($e->getMessage(), ENT_NOQUOTES) .'</status>';
						}
					}
					// Error: Invalid Input
					else
					{
						header("HTTP/1.1 400 Bad Request");
					
						foreach ($aMsgCds as $code => $data)
						{
							$xml .= '<status code="'. $code .'">'. htmlspecialchars($data, ENT_NOQUOTES) .'</status>';
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
	elseif (count($obj_DOM->{'initialize-payment'}) == 0)
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
echo preg_replace('~\s*(<([^>]*)>[^<]*</\2>|<[^>]*>)\s*~', '$1', $xml);
echo '</root>';
?>