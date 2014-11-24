<?php
/**
 * The Callback Package provide methods for informing the Client of the Transaction status automatically.
 * Callbacks can be performed either using mPoint's own Callback protocol or the PSP's native protocol.
 * The NetAxept subpackage is a specific implementation capable of imitating NetAxept's own protocol.
 *
 * @author Jacob Emil Baung�rd Hansen & Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Callback
 * @subpackage Authorize.Net
 * @version 1.00
 */

/**
 * Model Class containing all the Business Logic for handling interaction with NetAxept
 *
 */
class NetAxept extends Callback
{

	/**
	 * Initialize an transaction with NetAxept.
	 * The method will return XML with information to the client on how to submit card details to the server.
	 *
	 * @param	HTTPConnInfo $oCI		Information on how to connect to NetAxept
	 * @param	integer $merchant		The merchant ID to identify us to NetAxept
	 * @param 	integer $account
	 * @param	String $currency		The currency to use in ISO 4217 format
	 * @param	inteter $cardid			mPoints card ID
	 * @return	String					XML information of how the client should submit card details to NetAxepts server
	 * @throws	E_USER_WARNING
	 */
	public function initialize(HTTPConnInfo &$oCI, $merchant, $account, $currency, $cardid, $storecard)
	{
		$obj_SOAP = new SOAPClient("https://". $oCI->getHost() . $oCI->getPath(), array("trace" => true,
																						"exceptions" => true) );
		$sOrderNo = $this->getTxnInfo()->getOrderID();
		if (empty($sOrderNo) === true) { $sOrderNo = $this->getTxnInfo()->getID(); }

		$request = array("Description" => "mPoint Transaction: ". $this->getTxnInfo()->getID() ." for Order: ". $this->getTxnInfo()->getOrderID(),
						 "Environment" => array("WebServicePlatform" => "PHP5"),
						 "Order" => array("Amount" => $this->getTxnInfo()->getAmount(),
						 				  "CurrencyCode" => $currency,
						 				  "OrderNumber" => $sOrderNo),
						 "ServiceType" => "M",
						 "Terminal" => array("Language" => "en_GB",
										  	 "RedirectUrl" => "http://". $_SERVER['HTTP_HOST'] ."/netaxept/accept.php?mpoint-id=". $this->getTxnInfo()->getID(),
										  	 "SinglePage" => "true"),
										  	 "TransactionId" => $this->getTxnInfo()->getID() ."-". time() );

		// check if we need to store the card
		if ($storecard == true)
		{
			$request['Recurring'] = array("Type" => "S");
		}

		$aParams = array("merchantId" => $merchant,
						 "token" => $oCI->getPassword(),
						 "request" => $request);

		try
		{
			$obj_Std = $obj_SOAP->Register($aParams);

			if (intval($obj_Std->RegisterResult->TransactionId) == $this->getTxnInfo()->getID() )
			{
				$xml = '<?xml version="1.0" encoding="UTF-8"?>';
				$xml .= '<root>';
				$xml .= '<url method="post" content-type="application/x-www-form-urlencoded">https://'. $oCI->getHost() .'/Terminal/default.aspx</url>';
				$xml .= '<card-number>pan</card-number>';
				$xml .= '<expiry-month>expiryDate</expiry-month>';
				$xml .= '<expiry-year>expiryDate</expiry-year>';
				$xml .= '<cvc>securityCode</cvc>';
				$xml .= '<hidden-fields>';
				$xml .= '<merchantId>'. $merchant .'</merchantId>';
				$xml .= '<transactionId>'. $obj_Std->RegisterResult->TransactionId .'</transactionId>';
				$xml .= '</hidden-fields>';
				$xml .= '</root>';

				$data = array("psp-id" => Constants::iNETAXEPT_PSP, "url" => var_export($obj_Std, true) );
				$this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_INIT_WITH_PSP_STATE, serialize($data) );

				$obj_XML = simplexml_load_string($xml);

				// save ext id in database
						$sql = "UPDATE Log".sSCHEMA_POSTFIX.".Transaction_Tbl
								SET pspid = ". Constants::iNETAXEPT_PSP .", extid = '".$obj_Std->RegisterResult->TransactionId."'
								WHERE id = ". $this->getTxnInfo()->getID();
//					echo $sql ."\n";
						$this->getDBConn()->query($sql);
			}
			// Error: Unable to initialize payment transaction
			else
			{
				trigger_error("Unable to initialize payment transaction with NetAxept. HTTP Response Code: ". $code ."\n". var_export($obj_HTTP, true), E_USER_WARNING);

				throw new mPointException("NetAxept returned HTTP Code: ". $code, 1100);
			}
		}
		catch (Exception $e)
		{
			if ($e->detail->BBSException->Result->ResponseCode != NULL)
			{
				// Transaction already processed
				if (intval($e->detail->BBSException->Result->ResponseCode) == 98)
				{
					return "OK";
				}
				else { return $e->detail->BBSException->Result->ResponseCode; }
			}
			else { return $e->getMessage();	}
		}

		return $obj_XML;
	}

	/**
	 * Performs a AUTH operation with NetAxept for the provided transaction.
	 * The method will return 'OK' if the operation suceeded.
	 *
	 * On errors a NetAxept error code will be provided.
	 *
	 * The operation wil also notify the client and log in the database if the operation suceeded.
	 *
	 * @param	HTTPConnInfo $oCI		Information on how to connect to NetAxept
	 * @param	integer $merchant		The merchant ID to identify us to NetAxept
	 * @param 	integer $transactionID	Transaction ID previously returned by NetAxept during authorisation
	 * @return	String
	 * @throws	E_USER_WARNING
	 */
	public function auth(HTTPConnInfo &$oCI, $merchant, $transactionID)
	{
		$obj_SOAP = new SOAPClient("https://". $oCI->getHost() . $oCI->getPath(), array("trace" => true,
																						"exceptions" => true) );
		$aParams = array("merchantId" => $merchant,
						 "token" => $oCI->getPassword(),
						 "request" => array("Operation" => "AUTH",
						 					"TransactionId" => $transactionID) );

		try
		{
			$obj_Std = $obj_SOAP->Process($aParams);

			// log and notify the client of the new status of the transaction if it suceeded
			if ($obj_Std->ProcessResult->ResponseCode == 'OK')
			{
				// make a query response to NetAxept to make sure everything is ok
				$queryResponse = $this->query($oCI, $merchant, $transactionID);
								
				$fee = 0;
				if (intval($queryResponse->OrderInformation->Fee) > 0) {$fee = intval($queryResponse->OrderInformation->Fee); }
				// finalize transaction in mPoint
				if ($queryResponse->Summary->Authorized == "true")
				{
					if ($queryResponse->Recurring->PanHash != null)
					{
						$ticket = $queryResponse->Recurring->PanHash;
						$this->newMessage($this->getTxnInfo()->getID(), Constants::iTICKET_CREATED_STATE, "Ticket: ". $ticket);
						$sMask = $queryResponse->CardInformation->MaskedPAN;
						$sExpiry = substr($queryResponse->CardInformation->ExpiryDate, -2) . "/" . substr($queryResponse->CardInformation->ExpiryDate, 0, 2);
						$iStatus = $this->saveCard($this->getTxnInfo(), $this->getTxnInfo()->getMobile(), $this->getCardID($queryResponse->CardInformation->Issuer), Constants::iNETAXEPT_PSP, $ticket, $sMask, $sExpiry);
						if ($iStatus == 1)
						{
							$iMobileAccountID = -1;
							$iEMailAccountID = -1;
							if (strlen($this->getTxnInfo()->getCustomerRef() ) == 0)
							{
								if (floatval($this->getTxnInfo()->getMobile() ) > 0) { $iMobileAccountID = EndUserAccount::getAccountID($this->getDBConn(), $this->getTxnInfo()->getClientConfig(), $this->getTxnInfo()->getMobile(), $this->getTxnInfo()->getCountryConfig(), ($this->getTxnInfo()->getClientConfig()->getStoreCard() <= 3) ); }
								if (trim($this->getTxnInfo()->getEMail() ) != "") { $iEMailAccountID = EndUserAccount::getAccountID($this->getDBConn(), $obj_TxnInfo->getClientConfig(), $this->getTxnInfo()->getEMail(), $this->getTxnInfo()->getCountryConfig(), ($this->getTxnInfo()->getClientConfig()->getStoreCard() <= 3) ); }
								if ($iMobileAccountID != $iEMailAccountID && $iEMailAccountID > 0)
								{
									$this->getTxnInfo()->setAccountID(-1);
								}
							}
							$obj_mPoint->sendLinkedInfo(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO), $this->getTxnInfo());
						}
						// New Account automatically created when Card was saved
						else if ($iStatus == 2)
						{
							$iMobileAccountID = -1;
							$iEMailAccountID = -1;
							if (strlen($this->getTxnInfo()->getCustomerRef() ) == 0)
							{
								if (floatval($this->getTxnInfo()->getMobile() ) > 0) { $iMobileAccountID = EndUserAccount::getAccountID($this->getDBConn(), $this->getTxnInfo()->getClientConfig(), $this->getTxnInfo()->getMobile(), $this->getTxnInfo()->getCountryConfig(), false); }
								if (trim($this->getTxnInfo()->getEMail() ) != "") { $iEMailAccountID = EndUserAccount::getAccountID($this->getDBConn(), $this->getTxnInfo()->getClientConfig(), $this->getTxnInfo()->getEMail(), $this->getTxnInfo()->getCountryConfig(), false); }
								if ($iMobileAccountID != $iEMailAccountID && $iEMailAccountID > 0)
								{
									$this->getTxnInfo()->setAccountID(-1);
								}
							}
							// SMS communication enabled
							if ($this->getTxnInfo()->getClientConfig()->smsReceiptEnabled() === true)
							{
								$this->sendAccountInfo(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO), $this->getTxnInfo() );
							}
						}
						
						if ($this->getTxnInfo()->getEMail() != "") { $this->saveEMail($obj_TxnInfo->getMobile(), $this->getTxnInfo()->getEMail() ); }
					}
					$iStateID = $this->completeTransaction(Constants::iNETAXEPT_PSP, $transactionID , $this->getCardID($queryResponse->CardInformation->Issuer), Constants::iPAYMENT_ACCEPTED_STATE, $fee, array('0' => var_export($obj_Std->ProcessResult, true) ) );
				}
				else
				{
					$iStateID = $this->completeTransaction(Constants::iNETAXEPT_PSP, $transactionID, $this->getCardID($queryResponse->CardInformation->Issuer), Constants::iPAYMENT_REJECTED_STATE, $fee, array('0' => var_export($obj_Std->ProcessResult, true) ) );
				}
			}

			return $obj_Std->ProcessResult->ResponseCode;
		}
		catch (Exception $e)
		{
			if ($e->detail->BBSException->Result->ResponseCode != NULL)
			{
				// Transaction already processed
				if (intval($e->detail->BBSException->Result->ResponseCode) == 98)
				{
					return "OK";
				}
				else { return $e->detail->BBSException->Result->ResponseCode; }
			}
			else { return $e->getMessage();	}
		}
	}

	/**
	 * Performs a capture operation with NetAxept for the provided transaction.
	 * The method will return 'OK' if the operation suceeded.
	 *
	 * Exceptions will be raised on errors.
	 *
	 * @param	HTTPConnInfo $oCI		Information on how to connect to NetAxept
	 * @param	integer $merchant		The merchant ID to identify us to NetAxept
	 * @param 	integer $transactionID	Transaction ID previously returned by NetAxept during authorisation
	 * @param 	integer $txn	Transaction ID previously returned by WannaFind during authorisation
	 * @return	String
	 * @throws	E_USER_WARNING
	 */
	public function capture(HTTPConnInfo &$oCI, $merchant,$transactionID, $txn)
	{
		$obj_SOAP = new SOAPClient("https://". $oCI->getHost() . $oCI->getPath(), array("trace" => true, "exceptions" => true) );
		$aParams = array("merchantId" => $merchant,
						 "token" => $oCI->getPassword(),
						 "request" => array("Operation" => "CAPTURE",
						 				  	"TransactionId" => $transactionID,
						 					"TransactionAmount" => $txn->getAmount() ) );
		try
		{
			$obj_Std = $obj_SOAP->Process($aParams);

			return $obj_Std->ProcessResult->ResponseCode;
		}
		catch (Exception $e)
		{
			if ($e->detail->BBSException->Result->ResponseCode != NULL)
			{
				return $e->detail->BBSException->Result->ResponseCode;
			}
			else { return $e->getMessage();	}
		}
	}

	/**
	 * Performs a query operation with NetAxept for the provided transaction.
	 * The method will return an object containing information about the given transaction.
	 * Please see below link for information on what the object contains.
	 *
	 * Exceptions will be raised on errors, unfortunently no errors codes are set by NetAxept, but only a String.
	 * As such this methods will return a string different from 'OK' on errors. P
	 *
	 * @link 	http://www.betalingsterminal.no/Netthandel-forside/Teknisk-veiledning/API/Query/
	 * @param	HTTPConnInfo $oCI		Information on how to connect to NetAxept
	 * @param	integer $merchant		The merchant ID to identify us to NetAxept
	 * @param 	integer $transactionID	Transaction ID previously returned by NetAxept during authorisation
	 * @return	String
	 * @throws	E_USER_WARNING
	 */
	public function query(HTTPConnInfo &$oCI, $merchant,$transactionID)
	{

		$obj_SOAP = new SOAPClient("https://". $oCI->getHost() . $oCI->getPath(), array("trace" => true, "exceptions" => true) );
		$aParams = array("merchantId" => $merchant, 
						 "token" => $oCI->getPassword(), 
						 "request" => array("TransactionId" => $transactionID ) );

		try
		{
			$obj_Std = $obj_SOAP->Query($aParams);

			return $obj_Std->QueryResult;
		}
		catch (Exception $e)
		{
			if ($e->detail->BBSException->Result->ResponseCode != NULL)
			{
				return $e->detail->BBSException->Result->ResponseCode;
			}
			else { return $e->getMessage();	}
		}
	}

	/**
	 * Translates NetAxept card names into mPoint specific card IDs.
	 *
	 * @link 	http://www.betalingsterminal.no/Netthandel-forside/Teknisk-veiledning/API/Query/
	 * @param 	String $name	Transaction ID previously returned by WannaFind during authorisation
	 * @return	integer			mPoint Card ID.
	 * @throws	E_USER_WARNING
	 */
	public function getCardID($name)
	{
		switch ($name)
		{
		case "AmericanExpress":	// American Express
			$id = 1;
			break;
		case "Dankort":	// Dankort
			$id = 2;
			break;
		case "DinersClubInternational":	// Diners Club
			$id = 3;
			break;
//		case "ECMC-SSL":	// EuroCard
//			$id = 4;
//			break;
		case "JCB":		// JCB
			$id = 5;
			break;
		case "Maestro":	// Maestro
			$id = 6;
			break;
		case "MasterCard":	// MasterCard
		case "SwedishDebitMasterCard":
			$id = 7;
			break;
		case "Visa":	// VISA
		case "SwedishDebitVisa":
			$id = 8;
			break;
//		case "VISA_ELECTRON-SSL":	// VISA Electron
//			$id = 9;
//			break;
//		case "SWITCH-SSL":	// Switch
//			$id = 12;
//			break;
//		case "SOLO_GB-SSL":	// Solo
//			$id = 13;
//			break;
//		case "DELTA-SSL":	// Delta
//			$id = 14;
//			break;
		default:	// Unknown
			break;
		}

		return $id;
	}

	/**
	 * Notifies the Client of the Payment Status by performing a callback via HTTP.
	 * The method will re-construct the data received from NetAxept after having removed the following mPoint specific fields:
	 * 	- width
	 * 	- height
	 * 	- format
	 * 	- PHPSESSID (found using PHP's session_name() function)
	 * 	- language
	 * 	- cardid
	 * Additionally the method will add mPoint's Unique ID for the Transaction.
	 *
	 * @see 	Callback::notifyClient()
	 * @see 	Callback::send()
	 * @see 	Callback::getVariables()
	 *
	 * @param 	integer			$sid 			Unique ID of the State that the Transaction terminated in
	 * @param 	Standard		$obj_Std 	Response retrived from NetAxept.
	 * @param 	SurePayConfig	$obj_SurePay 	SurePay Configuration Object. Default value null

	 */
	public function notifyClient($sid, stdClass $obj_Std, SurePayConfig &$obj_SurePay=null)
	{
		parent::notifyClient($sid, $this->getTxnInfo()->getPSPID(), $obj_Std->OrderInformation->Amount, $this->getCardID($obj_Std->CardInformation->Issuer), str_replace("X", "*",$obj_Std->CardInformation->MaskedPAN), $obj_SurePay, $obj_Std->OrderInformation->Fee);
	}
	
	/**
	 * Authorises a payment with NetAxept for the transaction using the provided ticket.
	 * The ticket represents a previously stored card.
	 * This method will return either a NetAxept transaction id or on failures a negated NetAxept error code.
	 *
	 * @param 	integer $ticket		Valid ticket which references a previously stored card
	 * @return 	integer
	 * @throws	E_USER_WARNING
	 */
	public function authTicket($ticket, HTTPConnInfo &$oCI, $merchant)
	{
		$obj_SOAP = new SOAPClient("https://". $oCI->getHost() . $oCI->getPath(), array("trace" => true, "exceptions" => true) );

		$sOrderNo = $this->getTxnInfo()->getOrderID();
		if (empty($sOrderNo) === true) { $sOrderNo = $this->getTxnInfo()->getID(); }

		$request = array("Description" => "mPoint Transaction: ". $this->getTxnInfo()->getID() .
										  "for Order: ". $this->getTxnInfo()->getOrderID(),
										  "Environment" => array("WebServicePlatform" => "PHP5"),
										  "Order" => array("Amount" => $this->getTxnInfo()->getAmount(),
														   "CurrencyCode" => $this->getTxnInfo()->getCountryConfig()->getCurrency(),
														   "OrderNumber" => $sOrderNo ),
										  "ServiceType" => "C",
										  "Recurring" => array("Type" => "S",
										  					   "PanHash" => $ticket),
										  "TransactionId" => $this->getTxnInfo()->getID() ."-". time() );

		$aParams = array("merchantId" => $merchant,
						 "token" => $oCI->getPassword(),
						 "request" => $request);

		try
		{
	 		$obj_Std = $obj_SOAP->Register($aParams);
	 		file_put_contents(sLOG_PATH ."/netA.log", "\n". var_export($obj_Std,true), FILE_APPEND);
	 		if (intval($obj_Std->RegisterResult->TransactionId) == $this->getTxnInfo()->getID() )
	 		{
	 			$data = array("psp-id" => Constants::iNETAXEPT_PSP,
	 						  "url" => var_export($obj_Std, true) );

	 			$this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_INIT_WITH_PSP_STATE, serialize($data) );

	 			// save ext id in database
				$sql = "UPDATE Log".sSCHEMA_POSTFIX.".Transaction_Tbl
						SET pspid = ". Constants::iNETAXEPT_PSP .", extid = '". $obj_Std->RegisterResult->TransactionId ."'
						WHERE id = ". $this->getTxnInfo()->getID();
//				echo $sql ."\n";
				$this->getDBConn()->query($sql);

				$code = $this->auth($oCI, $merchant, $obj_Std->RegisterResult->TransactionId);
				if ($code == "OK")
				{
					$code = $this->auth($oCI, $merchant, $obj_Std->RegisterResult->TransactionId);

					return $code;
				}
				else { return -abs($code); }
			}
	 		// Error: Unable to initialize payment transaction
	 		else
	 		{
	 			trigger_error("Unable to initialize payment transaction with NetAxept. \n". var_export($obj_Std, true), E_USER_WARNING);

	 			throw new mPointException("NetAxept returned an error", 1100);

				return -1;
	 		}
		}
		catch (Exception $e)
		{
			if ($e->detail->BBSException->Result->ResponseCode != NULL)
			{
				return -abs($e->detail->BBSException->Result->ResponseCode);
			}
			elseif ($e->getMessage() != null) { return $e->getMessage(); }
			else { return -1; }
		}
	}

	
	/**
	 * Performs a refund operation with NetAxept for the provided transaction. 
	 * The method will log one the following status codes from NetAxept:
	 * 
	 * 0. Refund succeeded
	 * -1.Will also return the error message provided by NetAxept
	 * @link	http://www.betalingsterminal.no/Netthandel-forside/Teknisk-veiledning/Response-codes/
	 *
	 * @param integer		$transactionID	ID previously returned by NetAxept during authorisation
	 * @param integer		$amount			full amount that needed to be refunded
	 * @param HTTPConnInfo	$oCI			on how to connect to NetAxept
	 * @param integer 		$merchant		merchant ID to identify us to NetAxept
	 * @param String  		$sType			If the amount should be refunded or released 
	 * 
	 * @throws E_USER_WARNING
	 * 
	 * @return	integer 
	 */
	public function refund($transactionID, $amount, HTTPConnInfo &$oCI, $merchant, $sType) 
	{
		$obj_SOAP = new SOAPClient("https://" . $oCI->getHost () . $oCI->getPath (), 
									array("trace" => true,
										  "exceptions" => true ) );

		$aParams = array("merchantId" => $merchant,
						 "token" => $oCI->getPassword (),
						 "request" => array (
						 "Operation" => $sType,
						 "TransactionId" => $transactionID,
						 "transactionAmount" => $amount) );
		try 
		{
			$obj_Std = $obj_SOAP->Process ($aParams);

			// log and notify the client of the new status of the transaction if it suceeded
			if ($obj_Std->ProcessResult->ResponseCode == 'OK') 
			{
				// Payment successfully refunded
				$data = array("psp-id" => Constants::iNETAXEPT_PSP,
							  "url" => var_export ( $obj_Std, true ) );
				
				$this->newMessage ( $this->getTxnInfo ()->getID (), Constants::iPAYMENT_REFUNDED_STATE, serialize( $data ) );
				
				return 0;
			}
		}
		catch (SoapFault $e) 
		{	
			trigger_error("Transaction: ". $this->getTxnInfo()->getID() ."(". $transactionID .") Could not be  Refunded, NetAxept returned : ". $e->getMessage(), E_USER_WARNING);
			return -1;
		}
	}
}
?>