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
class NetAxept extends Callback implements Captureable, Refundable
{

	public function __construct(RDB $oDB, TranslateText $oTxt, TxnInfo $oTI, array $aConnInfo, PSPConfig $oPSPConfig = null)
	{
		parent::__construct($oDB, $oTxt, $oTI, $aConnInfo, $oPSPConfig);

		if ($oTI->getMode() > 0) { $this->aCONN_INFO["host"] = str_replace("epayment.", "epayment-test.", $this->aCONN_INFO["host"]); }
		$this->aCONN_INFO["username"] = $this->getPSPConfig()->getUsername();
		$this->aCONN_INFO["password"] = $this->getPSPConfig()->getPassword();
	}

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
		$obj_SOAP = new SOAPClient($this->aCONN_INFO["protocol"] ."://". $oCI->getHost() . $oCI->getPath(), array("trace" => true,
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
										  	 "RedirectUrl" => "https://". $_SERVER['HTTP_HOST'] ."/netaxept/accept.php?mpoint-id=". $this->getTxnInfo()->getID(),
										  	 "SinglePage" => "true"),
										  	 "TransactionId" => $this->getTxnInfo()->getID() ."-". time() );

		// check if we need to store the card
		if ($storecard == true)
		{
			$request['Recurring'] = array("Type" => "R",
										  "Frequency" =>"0",
										  "ExpiryDate" => "20380119");	// We have to set the ExpiryDate of the Recurring as we dont have the ExpiryDate of the card, so we set it to the end of unix Epoch
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
//				echo $sql ."\n";
				$this->getDBConn()->query($sql);
			}
			// Error: Unable to initialize payment transaction
			else
			{
				trigger_error("Unable to initialize payment transaction with NetAxept - unexpected TxnId. Got result from Netaxept: ". var_export($obj_Std, true), E_USER_WARNING);
				throw new mPointException("unexpected TxnId. Got result from Netaxept: ". var_export($obj_Std, true), 1100);
			}
		}
		catch (Exception $e)
		{
			$msg = "";
			if ($e->detail->BBSException->Result->ResponseCode != NULL)
			{
				// Transaction already processed
				if (intval($e->detail->BBSException->Result->ResponseCode) == 98)
				{
					$msg = "Transaction already processed";
				}
				else { $msg = $e->detail->BBSException->Result->ResponseCode; }
			}
			else { $msg = $e->getMessage();	}
			
			$obj_XML = simplexml_load_string('<status code="92">'. htmlspecialchars($msg, ENT_NOQUOTES) .'</status>');
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
		$obj_SOAP = new SOAPClient($this->aCONN_INFO["protocol"] ."://". $oCI->getHost(). $oCI->getPath(), array("trace" => true,
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
						$this->saveCard($this->getTxnInfo(), $this->getTxnInfo()->getMobile(), $this->getCardID($queryResponse->CardInformation->Issuer), Constants::iNETAXEPT_PSP, $ticket, $sMask, $sExpiry);

						if (strlen($this->getTxnInfo()->getCustomerRef() ) == 0)
						{
							$iMobileAccountID = -1;
							$iEMailAccountID = -1;
							if (floatval($this->getTxnInfo()->getMobile() ) > 0) { $iMobileAccountID = EndUserAccount::getAccountID($this->getDBConn(), $this->getTxnInfo()->getClientConfig(), $this->getTxnInfo()->getMobile(), $this->getTxnInfo()->getCountryConfig(), 2); }
							if (trim($this->getTxnInfo()->getEMail() ) != "") { $iEMailAccountID = EndUserAccount::getAccountID($this->getDBConn(), $this->getTxnInfo()->getClientConfig(), $this->getTxnInfo()->getEMail(), $this->getTxnInfo()->getCountryConfig(), 2); }

							if ($iMobileAccountID != $iEMailAccountID && $iEMailAccountID > 0 && $iMobileAccountID > 0)
							{
								$this->getTxnInfo()->setAccountID(-1);
							}
							else if ($iMobileAccountID > 0) { $this->getTxnInfo()->setAccountID($iMobileAccountID); }
							else if ($iEMailAccountID > 0) { $this->getTxnInfo()->setAccountID($iEMailAccountID); }
						}

						if ($this->getTxnInfo()->getAccountID() > 0) { $this->associate($this->getTxnInfo()->getAccountID(), $this->getTxnInfo()->getID() ); }
						//if ($this->getTxnInfo()->getEMail() != "") { $this->saveEMail($this->getTxnInfo()->$obj_TxnInfo->getMobile(), $this->getTxnInfo()->getEMail() ); }
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
	 * @param	integer $iAmount	Transaction amount to capture
	 * @return	integer
	 */
	public function capture($iAmount = -1)
	{
		$obj_PSPConfig = $this->getPSPConfig();
		$obj_TxnInfo = $this->getTxnInfo();
		$transactionID = $obj_TxnInfo->getExternalID();
		$merchant = $obj_PSPConfig->getMerchantAccount();
		if ($iAmount == -1) { $this->getTxnInfo()->getAmount(); }

		$oCI = HTTPConnInfo::produceConnInfo($this->aCONN_INFO);
		// Error suppression here to avoid warnings triggered by buggy ssl implementation affecting some older PHP versions
		// fatal errors, like connection or parsing errors while reading WSDL files will yield exceptions and thus error suppression has no effect on this
		$obj_SOAP = @new SOAPClient($this->aCONN_INFO["protocol"] ."://". $oCI->getHost() . $oCI->getPath(), array("trace" => true, "exceptions" => true, 'encoding' => 'UTF-8', 'cache_wsdl' => WSDL_CACHE_NONE) );

		$aParams = array("merchantId" => $merchant,
						 "token" => $oCI->getPassword(),
						 "request" => array("Operation" => "CAPTURE",
						 				  	"TransactionId" => $transactionID,
						 					"TransactionAmount" => $iAmount ) );
		try
		{
			$obj_Std = $obj_SOAP->Process($aParams);

			if ($obj_Std->ProcessResult->ResponseCode == 'OK')
			{
				$data = array("psp-id" => Constants::iNETAXEPT_PSP,
							  "request" => var_export($aParams, true),
							  "response" => var_export($obj_Std, true) );

				$queryResponse = $this->query($oCI, $merchant, $transactionID);

				$this->completeCapture($iAmount ,intval($queryResponse->Summary->AmountCaptured) - intval($iAmount), $data);
				return 1000;
			}

			return $obj_Std->ProcessResult->ResponseCode;
		}
		catch (Exception $e)
		{
			$this->newMessage($obj_TxnInfo->getID(), Constants::iPAYMENT_DECLINED_STATE, var_export($e, true) );
			trigger_error("Netaxept capture failed with exception: ". $e->getMessage() ." (". $e->getCode() .")\n". $e->getTraceAsString(), E_USER_ERROR);
			/*
			if ($e->detail->BBSException->Result->ResponseCode != NULL)
			{
				return $e->detail->BBSException->Result->ResponseCode;
			}
			else { return $e->getMessage();	}
			*/
			return -1;
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
		// Error suppression here to avoid warnings triggered by buggy ssl implementation affecting some older PHP versions
		// fatal errors, like connection or parsing errors while reading WSDL files will yield exceptions and thus error suppression has no effect on this
		$obj_SOAP = @new SOAPClient($this->aCONN_INFO["protocol"]. '://'. $oCI->getHost() . $oCI->getPath(), array("trace" => true, "exceptions" => true) );
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
	 * @param 	array			$_post 			Response retrived from NetAxept.
	 * @param 	SurePayConfig	$obj_SurePay 	SurePay Configuration Object. Default value null

	 */
	public function notifyClient($sid, array $_post, SurePayConfig &$obj_SurePay=null)
	{		
		parent::notifyClient($sid, $_post["transact"],$_post["amount"], $_post['cardid'], $_post['cardnomask'], $obj_SurePay, intval($_post['fee'] ) );
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
		$obj_SOAP = new SOAPClient($this->aCONN_INFO["protocol"] ."://". $oCI->getHost(). $oCI->getPath(), array("trace" => true, "exceptions" => true) );

		$sOrderNo = $this->getTxnInfo()->getOrderID();
		if (empty($sOrderNo) === true) { $sOrderNo = $this->getTxnInfo()->getID(); }

		$request = array("Description" => "mPoint Transaction: ". $this->getTxnInfo()->getID() .
										  "for Order: ". $this->getTxnInfo()->getOrderID(),
										  "Environment" => array("WebServicePlatform" => "PHP5"),
										  "Order" => array("Amount" => $this->getTxnInfo()->getAmount(),
														   "CurrencyCode" => $this->getTxnInfo()->getCountryConfig()->getCurrency(),
														   "OrderNumber" => $sOrderNo ),
										  "ServiceType" => "C",
										  "Recurring" => array("Type" => "R",
										  					   "PanHash" => $ticket),
										  "TransactionId" => $this->getTxnInfo()->getID() ."-". time() );

		$aParams = array("merchantId" => $merchant,
						 "token" => $oCI->getPassword(),
						 "request" => $request);

		try
		{
	 		$obj_Std = $obj_SOAP->Register($aParams);
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
	 * @param integer		$iAmount			full amount that needed to be refunded
	 * @param integer 		$code	allows to control from the outside whether to cancel or refund the transaction
	 *
	 * @throws E_USER_WARNING
	 * 
	 * @return	integer 
	 */
	public function refund($iAmount = -1, $code = -1)
	{
		$oCI = HTTPConnInfo::produceConnInfo($this->aCONN_INFO);
		$extID = $this->getTxnInfo()->getExternalID();

		if ($code == -1)
		{
			$res = $this->query($oCI, $this->getPSPConfig()->getMerchantAccount(), $extID);
			if ($res->Summary->AmountCaptured > 0) { $code = 5; }
			else { $code = 2; }
		}

		switch ($code)
		{
		case 2:
			$operation = "ANNUL";
			break;
		case 5:
			$operation = "CREDIT";
			break;
		}

		if ($iAmount <= 0) { $this->getTxnInfo()->getAmount(); }

		$obj_SOAP = new SOAPClient($this->aCONN_INFO["protocol"] ."://". $oCI->getHost () . $oCI->getPath (),
									array("trace" => true,
										  "exceptions" => true ) );

		$aParams = array("merchantId" => $this->getPSPConfig()->getMerchantAccount(),
						 "token" => $oCI->getPassword (),
						 "request" => array (
						 "Operation" => $operation,
						 "TransactionId" => $extID,
						 "TransactionAmount" => $iAmount) );
		try 
		{
			$obj_Std = $obj_SOAP->Process ($aParams);

			// log and notify the client of the new status of the transaction if it suceeded
			if ($obj_Std->ProcessResult->ResponseCode == 'OK') 
			{
				// Payment successfully refunded
				$data = array("psp-id" => Constants::iNETAXEPT_PSP,
							  "url" => var_export ( $obj_Std, true ) );

				if ($code == 2)
				{
					$this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_CANCELLED_STATE, serialize($data) );
					return 1001;
				}
				else
				{
					$this->newMessage($this->getTxnInfo()->getID(), Constants::iPAYMENT_REFUNDED_STATE, serialize($data) );
					return 1000;
				}
			}
		}
		catch (SoapFault $e) 
		{	
			trigger_error("Transaction: ". $this->getTxnInfo()->getID() ."(". $extID .") Could not be  Refunded, NetAxept returned : ". $e->getMessage(), E_USER_WARNING);
			return -1;
		}
	}

	public function getPSPID() { return Constants::iNETAXEPT_PSP; }
}
?>