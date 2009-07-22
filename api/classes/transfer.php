<?php
/**
 * The General package provides low level functionality that are shared accross several modules and/or pages
 * Obvious choices for functionality in this class are:
 * 	- Access Validation
 * 	- General validation methods: valEMail, valUsername, valPassword etc.
 * The Home subpackage provides general features accessible to a user that has successfully logged in as
 * well as basic navigation between the different modules in Direct Participation.
 *
 * @author Jonatan Evald Buus
 * @package Admin
 * @subpackage Transfer
 * @license Cellpoint Mobile
 */

/**
 * 
 *
 */
class Transfer extends Home
{
/**
	 * Makes a transfer between 2 End-Users' e-money based prepaid accounts.
	 * The method will credit the recipient's account amount received and debit the sender's account with the specified amount sent.
	 * For national transfers $as and $ar are equal but for international remittance $ar must have been converted into the
	 * recipient's currency
	 * All database operations are run within an ATOMIC transaction to ensure that the entire transfer either
	 * fails or succeeds.
	 * The method will return the following status codes:
	 * 	 1. Unable to debit sender's account
	 * 	 2. Unable to credit recipient's account
	 * 	10. Transfer successful
	 *
	 * @see		Constants::iEMONEY_TRANSFER_TYPE
	 *
	 * @param	integer $toid 	Unqiue ID of the recipient's account
	 * @param	integer $fromid Unqiue ID of the sender's account
	 * @param 	integer $ar 	Amount that should be credited the recipient's account
	 * @param 	integer $as 	Amount that should be debited the sender's account
	 * @return 	integer
	 */
	public function makeTransfer($toid, $fromid, $ar, $as)
	{
		// Start Transaction
		$this->getDBConn()->query("BEGIN");

		$as = abs(intval($as) );
		$ar = abs(intval($ar) );
		$sql = "INSERT INTO EndUser.Transaction_Tbl
					(accountid, typeid, toid, fromid, amount)
				VALUES
					(". intval($fromid) .", ". Constants::iEMONEY_TRANSFER_TYPE .", ". intval($toid) .", ". intval($fromid) .", ". ($as * -1) .")";
//		echo $sql ."\n";

		// Sender's account successfully debited
		if (is_resource($this->getDBConn()->query($sql) ) === true)
		{
			$sql = "INSERT INTO EndUser.Transaction_Tbl
						(accountid, typeid, toid, fromid, amount)
					VALUES
						(". intval($toid) .", ". Constants::iEMONEY_TRANSFER_TYPE .", ". intval($toid) .", ". intval($fromid) .", ". $ar .")";
//			echo $sql ."\n";

			// Recipient's account successfully credited
			if (is_resource($this->getDBConn()->query($sql) ) === true)
			{
				// Commit Transfer
				$this->getDBConn()->query("COMMIT");
				$code = 10;
			}
			// Error: Unable to credit recipient's account
			else
			{
				// Abort transaction and rollback to previous state
				$this->getDBConn()->query("ROLLBACK");
				$code = 2;
			}
		}
		// Error: Unable to debit sender's account
		else
		{
			// Abort transaction and rollback to previous state
			$this->getDBConn()->query("ROLLBACK");
			$code = 1;
		}

		return $code;
	}

	/**
	 * Converts the specified amount into the currency of the specified country.
	 * The method will automatically load the current conversion rates from the European Central Bank and 
	 * return the amount converted into the currency of the specified country on success.
	 * If an error occurs one of the following error codes will be returned:
	 * 	-1. Unable to fetch conversion rates from the European Central Bank
	 * 	-2. Unable to convert from source currency into Euro
	 * 	-3. Unable to convert from Euro into target currency
	 * 
	 * @link	http://www.ecb.int/stats/eurofxref/eurofxref-daily.xml
	 * 
	 * @param 	CountryConfig $oCC 	Reference to the data object with the Country Configuration that the amount should be converted to
	 * @param 	$amount				Amount to be converted in country's smallest currency
	 * @return 	integer				The amount converted into the currency of the specified country on success, a negative error code on error.
	 */
	public function convert(CountryConfig &$oCC, $amount)
	{
		// Get Exchange rates from the Central European Bank
		$obj_XML = simplexml_load_file("http://www.ecb.int/stats/eurofxref/eurofxref-daily.xml");
		
		// Error: Unable to fetch conversion rates from the European Central Bank
		if ($obj_XML === false)
		{
			$amount = -1;
		}
		else
		{
			// Register namespaces for use with XPath
			$aNamespaces = $obj_XML->getNamespaces(true);
			foreach ($aNamespaces as $prefix => $url)
			{
				if (empty($prefix) == true) { $prefix = "default"; }
				$obj_XML->registerXPathNamespace($prefix, $url);  
			}
			
			// Convert from sender's local currency into Euro
			if ($this->getCountryConfig()->getCurrency() != "EUR")
			{
				$obj_Elem = $obj_XML->xpath('//default:Cube/default:Cube/default:Cube[@currency = "'. $this->getCountryConfig()->getCurrency() .'"]');
				if (is_array($obj_Elem) === true && count($obj_Elem) > 0)
				{
					$obj_Elem = $obj_Elem[0];
					$amount = $amount / (float) $obj_Elem["rate"];
				}
				// Error: Unable to convert from source currency into Euro
				else { $amount = -2; }
			}
			// Convert from Euro into recipient's local currency
			if ($oCC->getCurrency() != "EUR")
			{
				$obj_Elem = $obj_XML->xpath('//default:Cube/default:Cube/default:Cube[@currency = "'. $oCC->getCurrency() .'"]');
				if (is_array($obj_Elem) === true && count($obj_Elem) > 0)
				{
					$obj_Elem = $obj_Elem[0];
					$amount = $amount * (float) $obj_Elem["rate"];
				}
				// Error: Unable to convert from Euro into target currency
				else { $amount = -3; }
			}
		}
		
		return $amount;
	}
	
	/**
	 * Constructs the message that should be sent to a recipient who receives a transfer without having an account.
	 * The method will perform the following Text Tag replacements:
	 * 	- {CODE}, the transfer code created from: base_convert({ACCOUNT CREATED}, 10, 32)Zbase_convert({ACCOUNT ID FOR RECIPIENT}, 10, 32)Zbase_convert({ACCOUNT ID FOR SENDER}, 10, 32)
	 * 	- {DOMAIN}, the domain that mPoint is currently using
	 * 	- {AMOUNT}, the amount that was transferred
	 * 	- {SENDER}, metadata about the sender of the transfer, will be replaced with either {ADDRESS} or {NAME} ({ADDRESS})
	 * 	- {NAME}, the name of the sender
	 * 	- {ADDRESS}, the sender's mobile number or e-mail address
	 * 	- {URL}, the URL that the recipient can use to create an account. The URL will include {CODE} as part of the path
	 * 
	 * @param 	SimpleXMLElement $msg	The source message which should be used for the replacement
	 * @param 	SimpleXMLElement $oRcpt	XML Document with the recipient's account information
	 * @param 	SimpleXMLElement $oSndr	XML Document with the sender's account information
	 * @param 	integer $amount			Total amount transferred
	 * @return 	string
	 */
	private function _constNewAccountMessage($msg, SimpleXMLElement &$oRcpt, SimpleXMLElement &$oSndr, $amount)
	{
		$code = base_convert( (integer) $oRcpt->created["timestamp"], 10, 32) ."Z". base_convert( (integer) $oRcpt["id"], 10, 32) ."Z". base_convert( (integer) $oSndr["id"], 10, 32);
		$msg = str_replace("{CODE}", strtoupper($code), $msg);
		$msg = str_replace("{DOMAIN}", "http://". sDEFAULT_MPOINT_DOMAIN, $msg);
		$msg = str_replace("{AMOUNT}", $amount, $msg);
		$msg = str_replace("{URL}", "http://". sDEFAULT_MPOINT_DOMAIN ."/new/". $code, $msg);
		// Insert sender information
		$sName = trim($oSndr->firstname ." ". $oSndr->lastname);
		if (empty($sName) === true) { $msg = str_replace("{SENDER}", "{ADDRESS}", $msg); }
		else { $msg = str_replace("{SENDER}", "{NAME} ({ADDRESS})", $msg); }
		$msg = str_replace("{NAME}", $sName, $msg);
		// Insert address
		$addr = (string) $oSndr->mobile;
		if (empty($addr) === true) { $addr = (string) $oSndr->email; }
		$msg = str_replace("{ADDRESS}", $addr, $msg);
		
		return $msg;	
	}
	
	/**
	 * Sends an SMS message for completing the creation of a new account.
	 * The method will return one of the following status codes:
	 * 	 1. Error while sending SMS message to GoMobile
	 * 	10. SMS successfully sent
	 * 
	 * @see		GoMobileMessage::produceMessage()
	 * @see		General::getText()
	 * @see		Home::sendMessage()
	 * @see		Home::getAccountInfo()
	 * @see		ClientConfig::produceConfig()
	 *
	 * @param 	GoMobileConnInfo $oCI 	Reference to the data object with the Connection Info required to communicate with GoMobile
	 * @param	integer $id 			Unqiue ID of the Recipient's Account
	 * @param	SimpleXMLElement $oSndr	XML Document with the sender's account information
	 * @param	integer $amount			Total amount received
	 * @return	integer
	 */
	public function sendNewAccountSMS(GoMobileConfig &$oCI, $id, SimpleXMLElement &$oSndr, $amount)
	{	
		$oRcpt = simplexml_load_string($this->getAccountInfo($id) );
		$obj_ClientConfig = ClientConfig::produceConfig($this->getDBConn(), (integer) $oRcpt["countryid"], -1);
		// Construct Message Body
		$sBody = $this->getText()->_("mPoint - New Account SMS");
		$sBody = $this->_constNewAccountMessage($sBody, $oRcpt, $oSndr, General::formatAmount($obj_ClientConfig->getCountryConfig(), $amount) );
		// Create data object with the Message Information
		$obj_MsgInfo = GoMobileMessage::produceMessage(Constants::iMT_SMS_TYPE, $obj_ClientConfig->getCountryConfig()->getID(), $obj_ClientConfig->getCountryConfig()->getID()*100, $obj_ClientConfig->getCountryConfig()->getChannel(), $obj_ClientConfig->getKeywordConfig()->getKeyword(), Constants::iMT_PRICE, (float) $oRcpt->mobile, $sBody);
		$obj_MsgInfo->enableConcatenation();
		$obj_MsgInfo->setDescription("mPoint - New Account");
//		if ($obj_ClientConfig->getCountryConfig()->getID() != 200) { $obj_MsgInfo->setSender("mPoint"); }
		
		// Send SMS to GoMobile with information about how a new account may be created
		if ($this->sendMessage($oCI, $obj_ClientConfig, $obj_MsgInfo) == 200) { $iCode = 10; }
		else { $iCode = 1; }
		
		return $iCode;
	}
	
	/**
	 * Sends an E-Mail message for completing the creation of a new account.
	 * The method will return one of the following status codes:
	 * 	 1. Error while sending E-Mail
	 * 	10. E-Mail successfully sent
	 * 
	 * @see		General::getText()
	 * @see		Home::sendMessage()
	 * @see		Home::getAccountInfo()
	 * @see		ClientConfig::produceConfig()
	 *
	 * @param	integer $id 			Unqiue ID of the Recipient's Account
	 * @param	SimpleXMLElement $oSndr	XML Document with the sender's account information
	 * @param	integer $amount			Total amount received
	 * @return	integer
	 */
	public function sendNewAccountEMail($id, SimpleXMLElement &$oSndr, $amount)
	{	
		$oRcpt = simplexml_load_string($this->getAccountInfo($id) );
		$obj_ClientConfig = ClientConfig::produceConfig($this->getDBConn(), (integer) $oRcpt["countryid"], -1);
		// Construct E-Mail Subject
		$sSubject = $this->getText()->_("mPoint - New Account E-Mail Subject");
		$sSubject = $this->_constNewAccountMessage($sSubject, $oRcpt, $oSndr, General::formatAmount($obj_ClientConfig->getCountryConfig(), $amount) );
		// Construct E-Mail Body
		$sBody = $this->getText()->_("mPoint - New Account E-Mail Body");
		$sBody = $this->_constNewAccountMessage($sBody, $oRcpt, $oSndr, General::formatAmount($obj_ClientConfig->getCountryConfig(), $amount) );
		
		// Send E-Mail with information about how a new account may be created
		if (mail( (string) $oRcpt->email, $sSubject, $sBody, $this->constSMTPHeaders() ) === true)
		{
			$iCode = 10;
		}
		else { $iCode = 1; }
		
		return $iCode;
	}
}
?>