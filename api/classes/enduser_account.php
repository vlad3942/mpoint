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
 * @version 1.00
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
	 * @param	TranslateText $oTxt 	Text Translation Object for translating any text into a specific language
	 * @param 	ClientConfig $oCI 		Data object with the Client Configuration
	 */
	public function __construct(RDB &$oDB, TranslateText &$oTxt, ClientConfig &$oCI)
	{
		parent::__construct($oDB, $oTxt, $oCI->getCountryConfig() );

		$this->_obj_ClientConfig = $oCI;
	}

	/**
	 * Creates a new End-User Account.
	 * Depending on the Client Configuration the End-User account may be linked to the specific Client
	 *
	 * @param	integer $cid 	ID of the country the End-User Account should be created in
	 * @param	string $mob 	End-User's mobile number
	 * @param 	string $pwd 	Password for the created End-User Account (optional)
	 * @param 	integer $ticket Ticket ID representing the End-User's stored Credit Card which should be associated with the account (optional)
	 * @param 	string $email	End-User's e-mail address (optional)
	 * @return	integer 		The unique ID of the created End-User Account
	 */
	public function newAccount($cid, $mob, $pwd="", $email="")
	{
		$iAccountID = parent::newAccount($cid, $mob, $pwd, $email);

		// Created account should only be available to Client
		if ($iAccountID > 0 && $this->_obj_ClientConfig->getStoreCard() == 1)
		{
			$sql = "INSERT INTO EndUser.CLAccess_Tbl
						(clientid, accountid)
					VALUES
						(". $this->_obj_ClientConfig->getID() .", ". $iAccountID .")";
//			echo $sql ."\n";
			$res = $this->getDBConn()->query($sql);
		}

		return $iAccountID;
	}

	/**
	 * Saves a credit card to an End-User Account.
	 * If no account can be found for the End-User a new account will automatically be created.
	 * The method will aupdated the Ticket ID if the card has been saved previously.
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
	 * @return	integer			1 if a new account was created, otherwise 0
	 */
	public function saveCard($addr, $cardid, $pspid, $ticket, $mask, $exp)
	{
		$iAccountID = self::getAccountID($this->getDBConn(), $this->_obj_ClientConfig, $addr);
		$iStatus = 0;

		// End-User Account already exists
		if ($iAccountID > 0) { $bPreferred = "false"; }
		// End-User Account doesn't exist, create new account
		else
		{
			$mob = "";
			$email = "";
			if (floatval($addr) > $this->_obj_ClientConfig->getCountryConfig()->getMinMobile() ) { $mob = $addr; }
			else { $email = $addr; }

			$iAccountID = $this->newAccount($this->_obj_ClientConfig->getCountryConfig()->getID(), $mob, "", $email);
			$bPreferred = "true";
			$iStatus = 1;
		}

		// Check of card has already been saved
		$sql = "SELECT id, ticket, pspid
				FROM EndUser.Card_Tbl
				WHERE accountid = ". $iAccountID ." AND cardid = ". intval($cardid) ." AND id = ". intval($cardid) ." AND mask = '". $this->getDBConn()->escStr($mask) ."' AND expiry = '". $this->getDBConn()->escStr($exp) ."'";
//		echo $sql ."\n";
		$RS = $this->getDBConn()->getName($sql);

		// Card not previously saved, add card info to database
		if (is_array($RS) === false)
		{
			$sql = "INSERT INTO EndUser.Card_Tbl
						(accountid, cardid, pspid, ticket, mask, expiry, preferred)
					VALUES
						(". $iAccountID .", ". intval($cardid) .", ". intval($pspid) .", ". intval($ticket) .", '". $this->getDBConn()->escStr($mask) ."', '". $this->getDBConn()->escStr($exp) ."', ". $bPreferred .")";
//			echo $sql ."\n";
			$res = $this->getDBConn()->query($sql);
		}
		// Card previously saved by End-User
		else
		{
			$sql = "UPDATE EndUser.Card_Tbl
					SET pspid ". intval($pspid) .", ticket = ". intval($ticket) ."
					WHERE id = ". $RS["ID"];
//			echo $sql ."\n";
			$res = $this->getDBConn()->query($sql);

			$this->delTicket($RS["PSPID"], $RS["TICKET"]);
		}

		return $iStatus;
	}

	/**
	 * Saves the specified Password for the End-User Account.
	 * If no account can be found for the End-User a new account will automatically be created
	 * otherwise method will set the password for account overwriting the old password.
	 *
	 * @see		EndUserAccount::getAccountID()
	 * @see		EndUserAccount::newAccount()
	 *
	 * @param	string $addr 	End-User's mobile number or E-Mail address
	 * @param 	string $pwd 	Password for the created End-User Account
	 * @return	integer			1 if a new account was created, otherwise 0
	 */
	public function savePassword($addr, $pwd)
	{
		$iAccountID = self::getAccountID($this->getDBConn(), $this->_obj_ClientConfig, $addr);
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
	 *
	 * @param	RDB $oDB			Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param 	ClientConfig $oCI 	Data object with the Client Configuration
	 * @param	string $addr 		End-User's mobile number or E-Mail address
	 * @return	integer				Unqiue ID of the End-User's Account or -1 if no account was found
	 */
	public static function getAccountID(RDB &$oDB, ClientConfig &$oCI, $addr)
	{
		if (floatval($addr) > $oCI->getCountryConfig()->getMinMobile() ) { $sql = "mobile = '". floatval($addr) ."'"; }
		else { $sql = "Upper(email) = Upper('". $oDB->escStr($addr) ."')"; }

		$sql = "SELECT DISTINCT EUA.id
				FROM EndUser.Account_Tbl EUA
				LEFT OUTER JOIN EndUser.CLAccess_Tbl CLA ON EUA.id = CLA.accountid
				WHERE EUA.countryid = ". $oCI->getCountryConfig()->getID() ."
					AND ". $sql ." AND EUA.enabled = true
					AND (CLA.clientid = ". $oCI->getID() ."
						 OR NOT EXISTS (SELECT id
									 	FROM EndUser.CLAccess_Tbl
									 	WHERE accountid = EUA.id) )";
//		echo $sql ."\n";
		$RS = $oDB->getName($sql);

		return is_array($RS)===true?$RS["ID"]:-1;
	}

	/**
	 * Tops an End-User's  e-money based prepaid account up with the specified amount
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
					(accountid, typeid, txnid, amount)
				VALUES
					(". intval($id) .", ". Constants::iEMONEY_TOPUP_TYPE .", ". intval($txnid) .", ". abs(intval($amount) ) .")";
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
		if ($amount > 0) { $amount = $amount * -1; }

		$sql = "INSERT INTO EndUser.Transaction_Tbl
					(accountid, typeid, txnid, amount)
				VALUES
					(". intval($id) .", ". Constants::iEMONEY_PURCHASE_TYPE .", ". intval($txnid) .", ". intval($amount) .")";
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
					(accountid, typeid, txnid)
				VALUES
					(". intval($id) .", ". Constants::iCARD_PURCHASE_TYPE .", ". intval($txnid) .")";
//		echo $sql ."\n";

		return is_resource($this->getDBConn()->query($sql) );
	}

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
		$obj_MsgInfo = GoMobileMessage::produceMessage(Constants::iMT_SMS_TYPE, $this->_obj_ClientConfig->getCountryConfig()->getID(), $this->_obj_TxnInfo->getOperator(), $this->_obj_ClientConfig->getCountryConfig()->getChannel(), $this->_obj_ClientConfig->getKeywordConfig()->getKeyword(), Constants::iMT_PRICE, $oTI->getMobile(), $sBody);
		
		// Send MT with Account Info
		$this->sendMT($oCI, $obj_MsgInfo, $oTI);
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