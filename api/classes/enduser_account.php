<?php
/**
 * The End-User Account Package provide methods for informing storing information about the End-User, including:
 * 	- Credit Card Information in the form of a Ticket ID
 * 	- User information
 * 	- Prepaid Balance
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Payment
 * @subpackage EndUserAccount
 * @version 1.01
 */

/* ==================== End-User Account Exception Classes Start ==================== */
/**
 * Exception class for all End-User Account exceptions
 */
class EndUserAccountException extends mPointException { }
/* ==================== End-User Account Exception Classes End ==================== */

/**
 * Model Class containing all the Business Logic for handling an End-User's Account
 * The class contains methods that creates and updates a User Account.
 *
 */
class EndUserAccount extends Home
{
	/**
	 * Data object with the Client Configuration
	 *
	 * @var ClientConfig
	 */
	private $_obj_ClientConfig;

	/**
	 * Default Constructor.
	 *
	 * @param	RDB $oDB				Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param	TranslateText $oTxt 	Reference to the Text Translation Object for translating any text into a specific language
	 * @param 	ClientConfig $oCI 		Reference to the data object with the Client Configuration
	 */
	public function __construct(RDB &$oDB, TranslateText &$oTxt, ClientConfig &$oCI)
	{
		parent::__construct($oDB, $oTxt, $oCI->getCountryConfig() );

		$this->_obj_ClientConfig = $oCI;
	}
	
	/**
	 * Returns a reference to the data object with the Client's configuration
	 *
	 * @return ClientConfig
	 */
	public function &getClientConfig() { return $this->_obj_ClientConfig; }

	/**
	 * Sends an SMS with information about the newly created account
	 *
	 * @see 	GoMobileClient
	 * @see 	Constants::iMT_SMS_TYPE
	 * @see 	Constants::iMT_PRICE
	 *
	 * @param 	GoMobileConnInfo $oCI 	Reference to the data object with the Connection Info required to communicate with GoMobile
	 * @param 	TxnInfo $oTI 			Reference to the data object holding the Transaction for which an MT should be send out
	 */
	public function sendAccountInfo(GoMobileConnInfo &$oCI, TxnInfo &$oTI)
	{
		$sBody = $this->getText()->_("mPoint - Account Info");
		$sBody = str_replace("{URL}", "http://". sDEFAULT_MPOINT_DOMAIN, $sBody);
		$sBody = str_replace("{CLIENT}", $this->_obj_ClientConfig->getName(), $sBody);
		
		// Instantiate Message Object for holding the message data which will be sent to GoMobile
		$obj_MsgInfo = GoMobileMessage::produceMessage(Constants::iMT_SMS_TYPE, $this->_obj_ClientConfig->getCountryConfig()->getID(), $oTI->getOperator(), $this->_obj_ClientConfig->getCountryConfig()->getChannel(), $this->_obj_ClientConfig->getKeywordConfig()->getKeyword(), Constants::iMT_PRICE, $oTI->getMobile(), utf8_decode($sBody) );
		$obj_MsgInfo->setDescription("mPoint - Account Inf");
		if ($this->getCountryConfig()->getID() != 200) { $obj_MsgInfo->setSender(substr($this->_obj_ClientConfig->getName(), 0, 11) ); }
		
		// Send MT with Account Info
		$this->sendMT($oCI, $obj_MsgInfo, $oTI);
	}
	
	/**
	 * Sends an SMS with information about the account that has been linked
	 *
	 * @see 	GoMobileClient
	 * @see 	Constants::iMT_SMS_TYPE
	 * @see 	Constants::iMT_PRICE
	 *
	 * @param 	GoMobileConnInfo $oCI 	Reference to the data object with the Connection Info required to communicate with GoMobile
	 * @param 	TxnInfo $oTI 			Reference to the data object holding the Transaction for which an MT should be send out
	 */
	public function sendLinkedInfo(GoMobileConnInfo &$oCI, TxnInfo &$oTI)
	{
		$iAccountID = self::getAccountID($this->getDBConn(), $this->_obj_ClientConfig, $oTI->getMobile() );
		$obj_XML = simplexml_load_string($this->getAccountInfo($iAccountID) );
		
		$sBody = $this->getText()->_("mPoint - Linked Info");
		$sBody = str_replace("{PASSWORD}", (string) $obj_XML->password, $sBody);
		$sBody = str_replace("{CLIENT}", $this->_obj_ClientConfig->getName(), $sBody);
		
		// Instantiate Message Object for holding the message data which will be sent to GoMobile
		$obj_MsgInfo = GoMobileMessage::produceMessage(Constants::iMT_SMS_TYPE, $this->_obj_ClientConfig->getCountryConfig()->getID(), $oTI->getOperator(), $this->_obj_ClientConfig->getCountryConfig()->getChannel(), $this->_obj_ClientConfig->getKeywordConfig()->getKeyword(), Constants::iMT_PRICE, $oTI->getMobile(), utf8_decode($sBody) );
		$obj_MsgInfo->setDescription("mPoint - Linked Info");
		if ($this->getCountryConfig()->getID() != 200) { $obj_MsgInfo->setSender(substr($this->_obj_ClientConfig->getName(), 0, 11) ); }
		
		// Send MT with Account Info
		$this->sendMT($oCI, $obj_MsgInfo, $oTI);
	}

	/**
	 * Creates a new End-User Account.
	 * Depending on the Client Configuration the End-User account may be linked to the specific Client
	 *
	 * @param	integer $cid 	ID of the country the End-User Account should be created in
	 * @param	string $mob 	End-User's mobile number
	 * @param 	string $pwd 	Password for the created End-User Account (optional)
	 * @param 	string $email	End-User's e-mail address (optional)
	 * @return	integer 		The unique ID of the created End-User Account
	 */
	public function newAccount($cid, $mob, $pwd="", $email="")
	{
		$iAccountID = parent::newAccount($cid, $mob, $pwd, $email);

		// Created account should only be available to Client
		if ($iAccountID > 0 && ($this->_obj_ClientConfig->getStoreCard()&2) == 2)
		{
			$this->link($iAccountID);
		}

		return $iAccountID;
	}

	/**
	 * Saves a credit card to an End-User Account.
	 * If no account can be found for the End-User a new account will automatically be created.
	 * The method will aupdated the Ticket ID if the card has been saved previously and return the following status codes:
	 * 	0. Card stored
	 * 	1. Card stored and Existing account linked
	 * 	2. Card stored and New account created
	 *
	 * @see		EndUserAccount::getAccountID()
	 * @see		EndUserAccount::newAccount()
	 *
	 * @param	string $addr 	End-User's mobile number or E-Mail address
	 * @param 	integer $cardid ID of the Card Type
	 * @param 	integer $pspid 	ID of the Payment Service Provider (PSP) that the ticket is valid through
	 * @param 	integer $ticket Ticket ID representing the End-User's stored Credit Card which should be associated with the account
	 * @param	string $mask 	Masked card number in the fomat {CARD PREFIX}******{LAST 4 DIGITS}
	 * @param	string $exp 	Expiry date for the Card in the format MM/YY
	 * @return	integer
	 */
	public function saveCard($addr, $cardid, $pspid, $ticket, $mask, $exp)
	{
		$iAccountID = self::getAccountID($this->getDBConn(), $this->_obj_ClientConfig, $addr);
		if ($iAccountID == -1)
		{
			$iAccountID = self::getAccountID($this->getDBConn(), $this->_obj_ClientConfig, $addr, false);
			$this->link($iAccountID);
			$iStatus = 1;
			$bPreferred = "true";
		} 
		else
		{
			$bPreferred = "false";
			$iStatus = 0;
		}

		// End-User Account doesn't exist, create new account
		if ($iAccountID == -1)
		{
			$mob = "";
			$email = "";
			if (floatval($addr) > $this->_obj_ClientConfig->getCountryConfig()->getMinMobile() ) { $mob = $addr; }
			else { $email = $addr; }

			$iAccountID = $this->newAccount($this->_obj_ClientConfig->getCountryConfig()->getID(), $mob, "", $email);
			$bPreferred = "true";
			$iStatus = 2;
		}

		// Check of card has already been saved
		$sql = "SELECT id, ticket, pspid
				FROM EndUser.Card_Tbl
				WHERE accountid = ". $iAccountID ." AND clientid = ". $this->_obj_ClientConfig->getID() ." AND cardid = ". intval($cardid) ."
					AND ( (mask = '". $this->getDBConn()->escStr($mask) ."' AND expiry = '". $this->getDBConn()->escStr($exp) ."') OR (mask IS NULL AND expiry IS NULL) )";
//		echo $sql ."\n";
		$RS = $this->getDBConn()->getName($sql);

		// Card not previously saved, add card info to database
		if (is_array($RS) === false)
		{
			$sql = "INSERT INTO EndUser.Card_Tbl
						(accountid, clientid, cardid, pspid, ticket, mask, expiry, preferred)
					VALUES
						(". $iAccountID .", ". $this->_obj_ClientConfig->getID() .", ". intval($cardid) .", ". intval($pspid) .", ". intval($ticket) .", '". $this->getDBConn()->escStr($mask) ."', '". $this->getDBConn()->escStr($exp) ."', ". $bPreferred .")";
//			echo $sql ."\n";
			$res = $this->getDBConn()->query($sql);
			
			$sql = "SELECT id
					FROM EndUser.CLAccess_Tbl
					WHERE clientid = ". $this->_obj_ClientConfig->getID() ." AND accountid = ". $iAccountID;
//			echo $sql ."\n";
			$RS = $this->getDBConn()->getName($sql);
			// Link between End-User Account and Client doesn't exist
			if (is_array($RS) === false) { $this->link($iAccountID); }
		}
		// Card previously saved by End-User
		else
		{
			$sql = "UPDATE EndUser.Card_Tbl
					SET pspid = ". intval($pspid) .", ticket = ". intval($ticket) .",
						mask = '". $this->getDBConn()->escStr($mask) ."', expiry = '". $this->getDBConn()->escStr($exp) ."',
						enabled = true
					WHERE id = ". $RS["ID"];
//			echo $sql ."\n";
			$res = $this->getDBConn()->query($sql);

			$this->delTicket($RS["PSPID"], $RS["TICKET"]);
		}

		return $iStatus;
	}
	
	/**
	 * Links the End-User's account to the Client thereby making the account available for use 
	 * when making purchases from the Client
	 *
	 * @param	integer $id 	Unqiue ID of the End-User's Account
	 * @return	boolean			True if the link is created, otherwise false
	 */
	public function link($id)
	{
		$sql = "INSERT INTO EndUser.CLAccess_Tbl
					(clientid, accountid)
				VALUES
					(". $this->_obj_ClientConfig->getID() .", ". intval($id) .")";
//		echo $sql ."\n";
		
		return $this->getDBConn()->query($sql);
	}

	/**
	 * Saves the specified Password for the End-User Account.
	 * If no account can be found for the End-User a new account will automatically be created
	 * otherwise method will set the password for account overwriting the old password.
	 * The method will return one of the following status codes:
	 * 	0. Password has been updated
	 * 	1. New account created
	 *
	 * @see		EndUserAccount::getAccountID()
	 * @see		EndUserAccount::newAccount()
	 *
	 * @param	string $addr 	End-User's mobile number or E-Mail address
	 * @param 	string $pwd 	Password for the created End-User Account
	 * @return	integer
	 */
	public function savePassword($addr, $pwd)
	{
		$iAccountID = self::getAccountID($this->getDBConn(), $this->_obj_ClientConfig, $addr, false);
		$iStatus = 0;
		
		// End-User Account already exists, update password
		if ($iAccountID > 0)
		{
			parent::savePassword($iAccountID, $pwd);
		}
		// End-User Account doesn't exist, create new account
		else
		{
			$mob = "";
			$email = "";
			if (floatval($addr) > $this->_obj_ClientConfig->getCountryConfig()->getMinMobile() ) { $mob = $addr; }
			else { $email = $addr; }

			$iAccountID = $this->newAccount($this->_obj_ClientConfig->getCountryConfig()->getID(), $mob, $pwd, $email);
			$iStatus = 1;
		}

		return $iStatus;
	}
	
	/**
	 * Saves the specified Card Name for the newest card without a name which has been created recently (within the last 5 minutes).
	 * The method will automatically create a new card and set it as inactive if no card has been created recently.
	 * For this to work it's assumed that the card info will be filled out and the card enabled by a callback from the PSP,
	 * which is used to clear the transaction.
	 * The method will return the following status codes:
	 * 	0. Error - Unable to store card name
	 * 	1. Card name successfully set for card
	 * 	2. New card created with name
	 *
	 * @see		EndUserAccount::getAccountID()
	 *
	 * @param	string $addr 	End-User's mobile number or E-Mail address
	 * @param 	integer $cardid ID of the Card Type
	 * @param 	string $name	Card name entered by the end-user
	 * @param 	boolean $pref	Boolean flag indicating whether a new card should be set as preferred (defaults to false)
	 * @return	integer
	 */
	public function saveCardName($addr, $cardid, $name, $pref=false)
	{
		$iAccountID = self::getAccountID($this->getDBConn(), $this->_obj_ClientConfig, $addr, false);
		$iStatus = 0;
		
		// Set name for card
		$sql = "UPDATE EndUser.Card_Tbl
				SET name = '". $this->getDBConn()->escStr(utf8_encode($name) ) ."'
				WHERE id = (SELECT Max(id)
							FROM EndUser.Card_Tbl
							WHERE accountid = ". $iAccountID ." AND clientid = ". $this->_obj_ClientConfig->getID() ." AND cardid = ". intval($cardid) ."
								AND (name IS NULL OR name = '') AND enabled = true)
					AND created > NOW() - interval '5 minutes'";
//		echo $sql ."\n";
		$res = $this->getDBConn()->query($sql);
		if (is_resource($res) === true) { $iStatus++; }
		// Card doesn't exist, create card setting it as inactive
		if ($this->getDBConn()->countAffectedRows($res) == 0)
		{
			$sql = "INSERT INTO EndUser.Card_Tbl
						(accountid, clientid, pspid, cardid, name, preferred, enabled)
					VALUES
						(". $iAccountID .", ". $this->_obj_ClientConfig->getID() .", 0, ". intval($cardid) .", '". $this->getDBConn()->escStr(utf8_encode($name) ) ."', '". General::bool2xml($pref) ."', false)";
//			echo $sql ."\n";
			$res = $this->getDBConn()->query($sql);
			
			if (is_resource($res) === true) { $iStatus++; }
			else { $iStatus = 0; }
		}

		return $iStatus;
	}

	/**
	 * Saves the specified E-Mail address for the End-User Account
	 *
	 * @param	string $mob 	End-User's mobile number
	 * @param 	string $email	End-User's e-mail address
	 * @return	boolean
	 */
	public function saveEmail($mob, $email)
	{
		$sql = "UPDATE EndUser.Account_Tbl
				SET email = '". $this->getDBConn()->escStr($email) ."'
				WHERE countryid = ". $this->_obj_ClientConfig->getCountryConfig()->getID() ." AND mobile = '". floatval($mob) ."'
					AND (email IS NULL OR email = '') AND enabled = true";
//		echo $sql ."\n";

		return is_resource($this->getDBConn()->query($sql) );
	}

	/**
	 * Fetches the unique ID of the End-User's account from the database.
	 * The account must either be available to the specific clients or globally available to all clients
	 * as defined by the entries in database table: EndUser.CLAccess_Tbl.
	 * This method may be called as a static method but is not defined as such because PHP doesn't support
	 * a static function overriding a non-static method.
	 * 
	 * @static
	 *
	 * @param	RDB $oDB			Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param 	ClientConfig $oCC 	Data object with the Client Configuration
	 * @param	string $addr 		End-User's mobile number or E-Mail address
	 * @param	boolean $strict 	Only check for an account associated with the specific client
	 * @return	integer				Unqiue ID of the End-User's Account or -1 if no account was found
	 */
	public function getAccountID(RDB &$oDB, ClientConfig &$oCC, $addr, $strict=true)
	{
		if (floatval($addr) > $oCC->getCountryConfig()->getMinMobile() ) { $sql = "EUA.mobile = '". floatval($addr) ."'"; }
		else { $sql = "Upper(EUA.email) = Upper('". $oDB->escStr($addr) ."')"; }

		$sql = "SELECT DISTINCT EUA.id
				FROM EndUser.Account_Tbl EUA
				LEFT OUTER JOIN EndUser.CLAccess_Tbl CLA ON EUA.id = CLA.accountid
				WHERE EUA.countryid = ". $oCC->getCountryConfig()->getID() ."
					AND ". $sql ." AND EUA.enabled = true";
				// Not a System Client
				if ($oCC->getCountryConfig()->getID() != $oCC->getID() && $strict === true)
				{
					$sql .= "
							AND (CLA.clientid = ". $oCC->getID() ." OR EUA.countryid = CLA.clientid 
							OR NOT EXISTS (SELECT id
										   FROM EndUser.CLAccess_Tbl
										   WHERE accountid = EUA.id) )";
				}
//		echo $sql ."\n";
		$RS = $oDB->getName($sql);
	
		return is_array($RS)===true?$RS["ID"]:-1;
	}

	/**
	 * Tops an End-User's e-money based prepaid account up with the specified amount
	 *
	 * @see		Constants::iEMONEY_TOPUP_TYPE
	 *
	 * @param	integer $id 	Unqiue ID of the End-User's Account
	 * @param	integer $txnid 	Unqiue ID of the mPoint Transaction that was used for the Top-Up
	 * @param 	integer $amount Amount that the End-User's prepaid account should be topped up with
	 * @return 	boolean
	 */
	public function topup($id, $txnid, $amount)
	{
		$sql = "INSERT INTO EndUser.Transaction_Tbl
					(accountid, typeid, txnid, amount, ip, address)
				SELECT ". intval($id) .", ". Constants::iEMONEY_TOPUP_TYPE .", ". intval($txnid) .", ". abs(intval($amount) ) .", ip, mobile
				FROM Log.Transaction_Tbl
				WHERE id = ". intval($txnid);
//		echo $sql ."\n";

		return is_resource($this->getDBConn()->query($sql) );
	}

	/**
	 * Makes an e-money based purchase using the End-User's prepaid account.
	 *
	 * @see		Constants::iEMONEY_PURCHASE_TYPE
	 *
	 * @param	integer $id 	Unqiue ID of the End-User's Account
	 * @param	integer $txnid 	Unqiue ID of the mPoint Transaction that the purchase is for
	 * @param 	integer $amount Amount that should be charged to the End-User's prepaid account
	 * @return 	boolean
	 */
	public function purchase($id, $txnid, $amount)
	{
		$amount = abs($amount) * -1;

		$sql = "INSERT INTO EndUser.Transaction_Tbl
					(accountid, typeid, txnid, amount, ip, address)
				SELECT ". intval($id) .", ". Constants::iEMONEY_PURCHASE_TYPE .", ". intval($txnid) .", ". intval($amount) .", ip, mobile
				FROM Log.Transaction_Tbl
				WHERE id = ". intval($txnid);
//		echo $sql ."\n";

		return is_resource($this->getDBConn()->query($sql) );
	}

	/**
	 * Associates a Card / Premium SMS based transaction with an End-User's account
	 *
	 * @see		Constants::iCARD_PURCHASE_TYPE
	 *
	 * @param	integer $id 	Unqiue ID of the End-User's Account
	 * @param	integer $txnid 	Unqiue ID of the mPoint Transaction that should be associated with the account
	 * @return 	boolean
	 */
	public function associate($id, $txnid)
	{
		$sql = "INSERT INTO EndUser.Transaction_Tbl
					(accountid, typeid, txnid, ip, address)
				SELECT ". intval($id) .", ". Constants::iCARD_PURCHASE_TYPE .", ". intval($txnid) .", ip, mobile
				FROM Log.Transaction_Tbl
				WHERE id = ". intval($txnid);
//		echo $sql ."\n";

		return is_resource($this->getDBConn()->query($sql) );
	}

	/**
	 * Dummy function
	 *
	 * @param 	integer $pspid	ID of the Payment Service Provider (PSP) that the ticket is valid through
	 * @param 	integer $ticket Ticket ID representing the End-User's stored Credit Card which should be associated with the account
	 */
	public function delTicket($pspid, $ticket) { }
}
?>