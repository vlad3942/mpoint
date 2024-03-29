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
	public function __construct(RDB &$oDB, api\classes\core\TranslateText &$oTxt, ClientConfig &$oCI)
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
	 * Sends an SMS message which notifies the end-user that the account has been disabled.
	 *
	 * @see		GoMobileMessage::produceMessage()
	 * @see		General::getText()
	 * @see		Home::sendMessage()
	 * @see		ClientConfig::produceConfig()
	 *
	 * @param 	GoMobileConnInfo $oCI 	Reference to the data object with the Connection Info required to communicate with GoMobile
	 * @param	string $mob 			End-User's mobile number
	 * @return	integer
	 * @throws 	mPointException
	 */
	public function sendAccountDisabledNotification(GoMobileConnInfo &$oCI, $mob)
	{
		$sBody = $this->getText()->_("mPoint - Account Disabled");
		$sBody = str_replace("{CLIENT}", $this->_obj_ClientConfig->getName(), $sBody);

		$obj_MsgInfo = GoMobileMessage::produceMessage(Constants::iMT_SMS_TYPE, $this->_obj_ClientConfig->getCountryConfig()->getID(), $this->_obj_ClientConfig->getCountryConfig()->getID()*100, $this->_obj_ClientConfig->getCountryConfig()->getChannel(), $this->_obj_ClientConfig->getKeywordConfig()->getKeyword(), Constants::iMT_PRICE, $mob, utf8_decode($sBody) );
		$obj_MsgInfo->setDescription("mPoint - Account Del");
		if ($this->getCountryConfig()->getID() != 200) { $obj_MsgInfo->setSender(substr($this->_obj_ClientConfig->getName(), 0, 11) ); }

		$iCode = $this->sendMessage($oCI, $this->_obj_ClientConfig, $obj_MsgInfo);
		if ($iCode != 200) { $iCode = 91; }

		return $iCode;
	}

    /**
     * Creates a new End-User Account.
     * Depending on the Client Configuration the End-User account may be linked to the specific Client
     *
     * @param integer $cid ID of the country the End-User Account should be created in
     * @param string $mob End-User's mobile number
     * @param string $pwd Password for the created End-User Account (optional)
     * @param string $email End-User's e-mail address (optional)
     * @param string $cr the Client's Reference for the Customer (optional)
     * @param string $pid
     * @param bool $enable
     * @param string $profileid
     * @return    integer        The unique ID of the created End-User Account
     */
	public function newAccount($cid, $mob = '', $pwd = '', $email = '', $cr = '', $pid = '', $enable = true, $profileid = '')
	{
		$iAccountID = parent::newAccount($cid, $mob, $pwd, $email, $cr, $pid, $enable, $profileid);

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
	 * The method will update the Ticket ID if the card has been saved previously and return the following status codes:
	 * 	0. Card stored
	 * 	1. Card stored and Existing account linked
	 * 	2. Card stored and New account created
	 *	4. Card Card previously saved by End-User
	 * @see		EndUserAccount::getAccountID()
	 * @see		EndUserAccount::newAccount()
	 *
	 * CARD SAVED DURING AUTHORIZATION:
	 * @param	TxnInfo $oTI	The transaction for which the card is being stored
	 * @param	string $addr 	End-User's mobile number or E-Mail address
	 * @param 	integer $cardid ID of the Card Type
	 * @param 	integer $pspid 	ID of the Payment Service Provider (PSP) that the ticket is valid through
	 * @param 	integer $token Ticket ID representing the End-User's stored Credit Card which should be associated with the account
	 * @param	string $mask 	Masked card number in the fomat {CARD PREFIX}******{LAST 4 DIGITS}
	 * @param	string $exp 	Expiry date for the Card in the format MM/YY
	 * CARD SAVED BY INVOKING THE "SAVE CARD" API
	 * @param	integer $accid	The End-User's unique ID
	 * @param 	integer $cardid ID of the Card Type
	 * @param 	integer $pspid 	ID of the Payment Service Provider (PSP) that the ticket is valid through
	 * @param 	integer $token Ticket ID representing the End-User's stored Credit Card which should be associated with the account
	 * @param	string $mask 	Masked card number in the fomat {CARD PREFIX}******{LAST 4 DIGITS}
	 * @param	string $exp 	Expiry date for the Card in the format MM/YY
	 * @param	string $chn 	Card Holder Name
	 * @param	string $name 	The name assigned to the stored card by the end-user (optional)
	 * @param	boolean $pref 	Boolean flag indicating whether the card is the end-user's preferred (optional), defaults to false
	 * @return	integer
	 */
	public function saveCard()
	{
		$aArgs = func_get_args();
		switch (count($aArgs) )
		{
		case (7):
		case (8):
			$chargeid = 0;
			// Card Saved during Authorization
			if ( ($aArgs[0] instanceof TxnInfo) === true)
			{
				list($oTI, $addr, $cardid, $pspid, $token, $mask, $exp) = $aArgs;
				$pid = (isset($aArgs[7]) === true)?(string)$aArgs[7]:'';
				$obj_CountryConfig = $oTI->getCountryConfig();
				$iAccountID = -1;
				if ($oTI->getAccountID() > 0) { $iAccountID = $oTI->getAccountID(); }
				elseif (strlen($oTI->getCustomerRef() ) > 0) { $iAccountID = EndUserAccount::getAccountIDFromExternalID($this->getDBConn(), $oTI->getClientConfig(), $oTI->getCustomerRef() ); }
				if ($iAccountID == -1 && floatval($addr) > $obj_CountryConfig->getMinMobile() ) { $iAccountID = self::getAccountID($this->getDBConn(), $this->_obj_ClientConfig, $addr, $obj_CountryConfig); }
				// End-User Account not found
				if ($iAccountID == -1)
				{
					if (strlen($oTI->getCustomerRef() ) > 0) { $iAccountID = EndUserAccount::getAccountIDFromExternalID($this->getDBConn(), $oTI->getClientConfig(), $oTI->getCustomerRef(), false); }
					elseif (floatval($addr) > $obj_CountryConfig->getMinMobile() ) { $iAccountID = self::getAccountID($this->getDBConn(), $this->_obj_ClientConfig, $addr, $obj_CountryConfig, false); }
					$pref = true;
					// Client supports global storage of payment cards: Link End-User Account
					if ($iAccountID > 0 && $this->getClientConfig()->getStoreCard() > 3)
					{
						$this->link($iAccountID);
						$code = 1;
					}
					// Create new End-User Account
					else
					{
						$mob = "";
						$email = "";
						if (floatval($addr) > $obj_CountryConfig->getMinMobile() ) { $mob = $addr; }
						else { $email = $addr; }
						if ( empty( $pid ) ) {
							$pushid = "";
						}
						else {
							$pushid = $pid;
						}
						$iAccountID = $this->newAccount($obj_CountryConfig->getID(), $mob, "", $email, $oTI->getCustomerRef(), $pushid );
						$code = 2;
					}
				}
				else
				{
					$pref = null;
					$code = 0;
				}
				$name = "";
				$chn = "";
			}
			// Card Saved by invoking "Save Card" API
			else
			{
				list($iAccountID, $cardid, $pspid, $token, $mask, $exp, $chn) = $aArgs;
				$name = "";
				$pref = false;
			}
			break;
		case (10):	// Card Saved by invoking "Save Card" API
			list($iAccountID, $cardid, $pspid, $token, $mask, $exp, $chn, $name, $pref, $chargeid) = $aArgs;
			$code = 0;
			break;
		}

		// Check if card has already been saved
		$id = $this->getCardIDFromCardDetails($iAccountID, $cardid, $mask, $exp, $token);

		// Stored Card should be preferred
		if ($pref === true)
		{
			$sql = "UPDATE EndUser".sSCHEMA_POSTFIX.".Card_Tbl
					SET preferred = '0'
					WHERE accountid = ". $iAccountID ." AND clientid = ". $this->_obj_ClientConfig->getID();
//			echo $sql ."\n";
			$this->getDBConn()->query($sql);
		}
		// Card previously saved by End-User
		if ($id > 0)
		{
			$sql = "UPDATE EndUser".sSCHEMA_POSTFIX.".Card_Tbl
					SET pspid = ". intval($pspid) .", ticket = '". $this->getDBConn()->escStr($token) ."',
						mask = '". $this->getDBConn()->escStr(trim($mask) ) ."', expiry = '". $this->getDBConn()->escStr($exp) ."',
						preferred = '". intval($pref) ."', enabled = '1'";
			if (empty($name) === false) { $sql .= ", name = '". $this->getDBConn()->escStr(trim($name) ) ."'"; }
			if (empty($chn) === false) { $sql .= ", card_holder_name = '". $this->getDBConn()->escStr(trim($chn) ) ."'"; }
			if ($chargeid > 0) { $sql .= ", chargetypeid = ". intval($chargeid) .""; }
				
			$sql .= "
					WHERE id = ". $id;
//			echo $sql ."\n";
			$res = $this->getDBConn()->query($sql);
			if (is_resource($res) === true) { $code = 4; }
			else { $code = -1; }
		}
		// Card not previously saved, add card info to database
		else
		{
			$sql = "INSERT INTO EndUser".sSCHEMA_POSTFIX.".Card_Tbl
						(accountid, clientid, cardid, pspid, ticket, mask, expiry, name, preferred, card_holder_name, chargetypeid)
					VALUES
						(". $iAccountID .", ". $this->_obj_ClientConfig->getID() .", ". intval($cardid) .", ". intval($pspid) .", 
						 '". $this->getDBConn()->escStr($token) ."', '". $this->getDBConn()->escStr(trim($mask) ) ."', '". $this->getDBConn()->escStr($exp) ."',
						 '". $this->getDBConn()->escStr(trim($name) ) ."', '". intval($pref) ."','". $this->getDBConn()->escStr(trim($chn) )."', ". intval($chargeid) .")";
//			echo $sql ."\n";
			$res = $this->getDBConn()->query($sql);

			if (is_resource($res) === true)
			{
				$sql = "SELECT id
						FROM EndUser".sSCHEMA_POSTFIX.".CLAccess_Tbl
						WHERE clientid = ". $this->_obj_ClientConfig->getID() ." AND accountid = ". $iAccountID;
//				echo $sql ."\n";
				$RS = $this->getDBConn()->getName($sql);
				// Link between End-User Account and Client doesn't exist
				if (is_array($RS) === false) { $this->link($iAccountID); }
			}
			else { $code = -1; }
		}

		return $code;
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
		$sql = "INSERT INTO EndUser".sSCHEMA_POSTFIX.".CLAccess_Tbl
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
	 * @param	string $addr 		End-User's mobile number or E-Mail address
	 * @param 	string $pwd 		Password for the created End-User Account
	 * @param	CountryConfig $oCC	Country Configuration, pass null to default to the Country Configuration from the Client Configuration
	 * @return	integer
	 */
	public function savePassword($addr, $pwd, CountryConfig &$oCC=null)
	{
		$iAccountID = self::_getAccountID($this->getDBConn(), $this->_obj_ClientConfig, $addr, $oCC, 2);
		$iStatus = 0;
		if ($iAccountID == -1 && $this->getClientConfig()->getStoreCard() > 3)
		{
			$iAccountID = self::_getAccountID($this->getDBConn(), $this->_obj_ClientConfig, $addr, $oCC, 0);
		}
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
			if (floatval($addr) > $oCC->getMinMobile() ) { $mob = $addr; }
			else { $email = $addr; }

			$iAccountID = $this->newAccount($oCC->getID(), $mob, $pwd, $email);
			$iStatus = 1;
		}

		return $iStatus;
	}

	/**
	 * This method have to functions, to either name a card or to rename a card.
	 * With 3 params you rename the card, with 5 params the card is saved.
	 *
	 * For this to work it's assumed that the card info will be filled out and the card enabled by a callback from the PSP,
	 * which is used to clear the transaction.
	 * The method will return the following status codes:
	 * 	0. Error - Unable to store card name
	 * 	1. Card name successfully set for card
	 * 	2. New card created with name
	 *	3. Card renamed
	 *
	 * RENAME CARD:
	 * @param 	integer $cardid ID of the Card
	 * @param 	string $name	Card name entered by the end-user
	 * @param 	boolean $pref	Boolean flag indicating whether a new card should be set as preferred (defaults to false)
	 * SAVE CARD:
	 * @param	integer $id 	Unique ID for the end-user's account
	 * @param 	integer $cardid ID of the Card Type
	 * @param 	string $name	Card name entered by the end-user
	 * @param 	boolean $pref	Boolean flag indicating whether a new card should be set as preferred (defaults to false)
	 * @return	integer
	 */
	public function saveCardName()
	{
		$aArgs = func_get_args();
		switch (count($aArgs) )
		{
		case (2):	// Rename Card
			return $this->_renameCard($aArgs[0], $aArgs[1]);
			break;
		case (3):
			if ($aArgs[2] === false || $aArgs[2] === true)
			{
				return $this->_renameCard($aArgs[0], $aArgs[1], $aArgs[2]);
			}
			else { return $this->_saveCardName($aArgs[0], $aArgs[1], $aArgs[2]); }
			break;
		case (4):
            if ($aArgs[3] === false || $aArgs[3] === true) {// Save Card Name and status (preferred)
                return $this->_saveCardName($aArgs[0], $aArgs[1], $aArgs[2], $aArgs[3]);
            }
            else
            {
                return $this->_renameCard($aArgs[0], $aArgs[1], $aArgs[2], $aArgs[3]);
            }
			break;
		default: 	// Error: Invalid number of arguments
			trigger_error("Invalid number of arguments: ". count($aArgs), E_USER_WARNING);
			return -1;
			break;
		}
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
	 * @param	integer $id 	Unique ID for the end-user's account
	 * @param 	integer $cardid ID of the Card Type
	 * @param 	string $name	Card name entered by the end-user
	 * @param 	boolean $pref	Boolean flag indicating whether a new card should be set as preferred (defaults to false)
	 * @return	integer
	 */
	private function _saveCardName($id, $cardid, $name, $pref=false)
	{
		$code = 0;
		// Set name for card
		$sql = "UPDATE EndUser".sSCHEMA_POSTFIX.".Card_Tbl
				SET name = '". $this->getDBConn()->escStr($name) ."'
				WHERE id = (SELECT Max(id)
							FROM EndUser".sSCHEMA_POSTFIX.".Card_Tbl
							WHERE accountid = ". intval($id) ." AND clientid = ". $this->_obj_ClientConfig->getID() ." AND cardid = ". intval($cardid) ."
								AND (name IS NULL OR name = '') AND enabled = '1')
					AND modified > NOW() - interval '5 minutes'";
//		echo $sql ."\n";
		$res = $this->getDBConn()->query($sql);
		if (is_resource($res) === true) { $code++; }
		// Card doesn't exist, create card setting it as inactive
		if ($this->getDBConn()->countAffectedRows($res) == 0)
		{
			$sql = "INSERT INTO EndUser".sSCHEMA_POSTFIX.".Card_Tbl
						(accountid, clientid, pspid, cardid, name, preferred, enabled)
					VALUES
						(". intval($id) .", ". $this->_obj_ClientConfig->getID() .", 0, ". intval($cardid) .", '". $this->getDBConn()->escStr($name) ."', '". intval($pref) ."', '0')";
//			echo $sql ."\n";
			$res = $this->getDBConn()->query($sql);

			if (is_resource($res) === true) { $code++; }
			else { $code = 0; }
		}

		return $code;
	}

	public function saveState($cid, $name, $code="")
	{
		$sql = "SELECT Nextvalue('System".sSCHEMA_POSTFIX.".State_Tbl_id_seq') AS id FROM DUAL";
		$RS = $this->getDBConn()->getName($sql);

		$sql = "INSERT INTO System".sSCHEMA_POSTFIX.".State_Tbl
					(id, countryid, name, code)
				VALUES
				(". $RS["ID"] .", ". intval($cid) .", '". $this->getDBConn()->escStr(trim($name) ) ."', Upper('". $this->getDBConn()->escStr(trim($code) ) ."') )";
//		echo $sql ."\n";

		return is_resource($this->getDBConn()->query($sql) ) === true ? $RS["ID"] : -1;
	}

	/**
	 * Returns the ID of the state in the specified country using either the state 2-digit code or the state name to find the state.
	 * The method will return the default state in the country (identified by code: N/A) if no state is passed to the method.
	 *
	 * @param integer $cid		ID of the Country the state must be located in
	 * @param string $state		The 2-digit code or name of the state
	 * @return integer
	 */
	public function getStateID($cid, $state="")
	{
		if (empty($state) === true) { $state = "N/A"; }

		$sql = "SELECT id
				FROM System".sSCHEMA_POSTFIX.".State_Tbl
				WHERE countryid = ". intval($cid) ." AND Upper(code) = Upper('". $this->getDBConn()->escStr(trim($state) ) ."')";
//		echo $sql ."\n";
		$RS = $this->getDBConn()->getName($sql);

		if (is_array($RS) === false || intval($RS["ID"]) == 0)
		{
			$sql = "SELECT id
					FROM System".sSCHEMA_POSTFIX.".State_Tbl
					WHERE countryid = ". intval($cid) ." AND Upper(name) = Upper('". $this->getDBConn()->escStr(trim($state) ) ."')";
//			echo $sql ."\n";
			$RS = $this->getDBConn()->getName($sql);
		}

		return is_array($RS) === true ? intval($RS["ID"]) : 0;
	}
	/**
	 * Saves Billing Address for the newest card which has been created recently (within the last 5 minutes).
	 * The method will return the following status codes:
	 * 	 1. State not found
	 * 	 2. Address Update failed
	 * 	 3. Address Insert failed
	 * 	10. Success
	 *
	 * @param 	integer $cid 	ID of the Country
	 * @param 	string $state	Address field - State
	 * @param 	string $fn 		Address field - First Name
	 * @param   string $ln		Address field - Last Name
	 * @param 	string $cmp		Address field - Company
	 * @param	string $st		Address field - Street
	 * @param	string $pc		Address field - Postal Code
	 * @param 	string $ct		Address field - City
	 * @return	integer
	 */
	public function saveAddress($cardid, $cid, $state, $fn, $ln, $cmp, $st, $pc, $ct,$fullName = "")
    {
        if(empty($fullName) === false) {
            $name = explode(' ', $fullName);
            $fn = $name[0];
            if(count($name) > 1)
                $ln = $name[1];
        }

		$sql = "UPDATE EndUser".sSCHEMA_POSTFIX.".Address_Tbl
				SET countryid = ". intval($cid) .", state = '". $this->getDBConn()->escStr($state) ."',
					firstname = '". $this->getDBConn()->escStr($fn) ."', lastname = '". $this->getDBConn()->escStr($ln) ."', company = '". $this->getDBConn()->escStr($cmp) ."',
					street = '". $this->getDBConn()->escStr($st) ."', postalcode = '". $this->getDBConn()->escStr($pc) ."', city = '". $this->getDBConn()->escStr($ct) ."'
				WHERE cardid = ". intval($cardid);
//		echo $sql ."\n";
		$res = $this->getDBConn()->query($sql);
		if (is_resource($this->getDBConn()->query($sql) ) === true)
		{
			if ($this->getDBConn()->countAffectedRows($res) == 0)
			{
				$sql = "INSERT INTO EndUser".sSCHEMA_POSTFIX.".Address_Tbl
							(cardid, countryid, state, firstname, lastname, company, street, postalcode, city)
						VALUES
							(". intval($cardid) .", ". intval($cid) .", '". $this->getDBConn()->escStr($state) ."' , '". $this->getDBConn()->escStr($fn) ."', '". $this->getDBConn()->escStr($ln) ."', '". $this->getDBConn()->escStr($cmp) ."', '". $this->getDBConn()->escStr($st) ."', '". $this->getDBConn()->escStr($pc) ."', '". $this->getDBConn()->escStr($ct) ."')";
//				echo $sql ."\n";
				if (is_resource($this->getDBConn()->query($sql) ) === true) { $code = 10; }
				else { $code = 2; }
			}
			else { $code = 10; }
		}
		else { $code = 1; }

		return $code;
	}

	/**
	 * Renames the specified card.
	 * For this to work it's assumed that the card info will be filled out and the card enabled by a callback from the PSP,
	 * which is used to clear the transaction.
	 * The method will return the following status codes:
	 * 	0. Error - Unable to store card name
	 * 	3. Card successfully renamed
	 *
	 * @param 	integer $cardid ID of the Card
	 * @param 	string $name	Card name entered by the end-user
	 * @param 	boolean $pref	Boolean flag indicating whether a new card should be set as preferred (defaults to false)
	 * @param   string $cardholdername Card holder name
     *
	 * @return	integer
	 */
	private function _renameCard($cardid, $name, $pref=false, $cardholdername)
	{
		// Reset preferred flag on all cards
		if ($pref === true)
		{
			$sql = "UPDATE EndUser".sSCHEMA_POSTFIX.".Card_Tbl
					SET preferred = '0'
					WHERE preferred = '1' AND accountid = (SELECT accountid
														   FROM EndUser".sSCHEMA_POSTFIX.".Card_Tbl
														   WHERE id = ". intval($cardid) .")";
//			echo $sql ."\n";
			$this->getDBConn()->query($sql);
		}

		// Set name for card
		$sql = "UPDATE EndUser".sSCHEMA_POSTFIX.".Card_Tbl
				SET name = '". $this->getDBConn()->escStr($name) ."', preferred = '" . intval($pref) . "'";
        if(empty($cardholdername) === false) {
            $sql .= " ,card_holder_name = '". $this->getDBConn()->escStr($cardholdername) ."'";
        }
        $sql .= " WHERE id = ". intval($cardid) ." AND enabled = '1'";

//		echo $sql ."\n";
		$res = $this->getDBConn()->query($sql);

		return $this->getDBConn()->countAffectedRows($res) > 0 ? 3 : 0;
	}

	/**
	 * Saves the specified E-Mail address for the End-User Account
	 *
	 * @param	string $mob 	End-User's mobile number
	 * @param 	string $email	End-User's e-mail address
	 * @return	boolean
	 */
	public function saveEmail($mob, $email, CountryConfig &$oCC=null)
	{
		if ( ($oCC instanceof CountryConfig) === false) { $oCC = $this->_obj_ClientConfig->getCountryConfig(); }
		$sql = "UPDATE EndUser".sSCHEMA_POSTFIX.".Account_Tbl
				SET email = '". $this->getDBConn()->escStr($email) ."'
				WHERE countryid = ". $oCC->getID() ." AND mobile = '". floatval($mob) ."'
					AND (email IS NULL OR email = '') AND enabled = '1'";
//		echo $sql ."\n";

		return is_resource($this->getDBConn()->query($sql) );
	}

	/**
	 * Retrieves the ID of the card from EndUser.Card_Tbl
	 *
	 * @param integer 	$iAccountID		Account ID
	 * @param integer 	$cardid 		ID from System.Card_Tbl
	 * @param string 	$mask			Masked credit card number
	 * @param string 	$exp			Expiry date
	 * @param string	$ticket			Token of the card at the PSP 
	 * @return integer
	 */
	public function getCardIDFromCardDetails($id, $cardid, $mask, $exp, $ticket="")
	{
		$sql = "SELECT id
				FROM EndUser".sSCHEMA_POSTFIX.".Card_Tbl
				WHERE accountid = ". $id ." AND clientid = ". $this->_obj_ClientConfig->getID() ." AND (cardid = ". intval($cardid);
		if (strlen($ticket) > 0 ) { $sql .=																" OR ticket = '". $this->getDBConn()->escStr($ticket) ."')"; }
		else { $sql .=																				  ")"; }
		$sql .=		"AND ( ( (mask = '". $this->getDBConn()->escStr(trim($mask) ) ."' AND expiry = '". $this->getDBConn()->escStr($exp) ."') OR (mask IS NULL AND expiry IS NULL) )
						 	 OR (mask = '". $this->getDBConn()->escStr(trim($mask) ) ."' AND ticket = '". $this->getDBConn()->escStr($ticket) ."') )";
//		echo $sql ."\n";
		$RS = $this->getDBConn()->getName($sql);

		return is_array($RS) === true ? $RS["ID"] : -1;
	}

	/**
	 * Fetches the unique ID of the End-User's account from the database using the provided external id.
	 * The account must either be available to the specific clients or globally available to all clients
	 * as defined by the entries in database table: EndUser.CLAccess_Tbl.
	 * This method may be called as a static method but is not defined as such because PHP doesn't support
	 * a static function overriding a non-static method.
	 *
	 * @static
	 *
	 * @param	RDB $oDB			Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param 	ClientConfig $oClC 	Data object with the Client Configuration
	 * @param	string $id 			The external ID
	 * @param	boolean $strict 	Only check for an account associated with the specific client
	 * @return	integer				Unqiue ID of the End-User's Account or -1 if no account was found
	 */
	public static function getAccountIDFromExternalID(RDB &$oDB, ClientConfig &$oClC, $id, $strict=true)
	{
		$sql = "SELECT DISTINCT EUA.id
				FROM EndUser".sSCHEMA_POSTFIX.".Account_Tbl EUA
				LEFT OUTER JOIN EndUser".sSCHEMA_POSTFIX.".CLAccess_Tbl CLA ON EUA.id = CLA.accountid
				WHERE EUA.externalid = '". $oDB->escStr($id) ."' AND length(EUA.externalid) > 1 AND EUA.enabled = '1'";
		// Not a System Client
		if ($oClC->getCountryConfig()->getID() != $oClC->getID() && $strict === true)
		{
			$sql .= "
					AND (CLA.clientid = ". $oClC->getID() ." /* OR EUA.countryid = CLA.clientid */
						 OR NOT EXISTS (SELECT id
									    FROM EndUser".sSCHEMA_POSTFIX.".CLAccess_Tbl
									    WHERE accountid = EUA.id) )";
		}
//		echo $sql ."\n";
		$RS = $oDB->getName($sql);

		return is_array($RS) === true ? $RS["ID"] : -1;
	}
	/**
	 * Fetches the unique ID of the End-User's account from the database.
	 * The account must either be available to the specific clients or globally available to all clients
	 * as defined by the entries in database table: EndUser.CLAccess_Tbl.
	 * This method may be called as a static method but is not defined as such because PHP doesn't support
	 * a static function overriding a non-static method.
	 * The method uses virtual overloading to provide different behaviour depending on the number of arguments passed as
	 * described below.
	 *
	 * @static
	 *
	 * @param	RDB $oDB			Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param 	ClientConfig $oClC 	Data object with the Client Configuration
	 * @param	string $addr 		End-User's mobile number or E-Mail address
	 * @param	CountryConfig $oCC	Country Configuration, pass null to default to the Country Configuration from the Client Configuration
	 * @param	boolean $strict 	Only check for an account associated with the specific client
	 * 		OR
	 * @param	RDB $oDB			Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param 	ClientConfig $oClC 	Data object with the Client Configuration
	 * @param	CountryConfig $oCC	Country Configuration identifying the end-user's country
	 * @param	string $cref 		The client's customer reference for the end-user, pass empty string: "" to bypass
	 * @param	long $mob 			End-User's mobile number, pass -1 to bypass
	 * @param	string $email 		End-User's E-Mail address, pass empty string: "" to bypass
     * @param	string $profile 	End-User's registered profile id
	 * @return	integer				Unqiue ID of the End-User's Account or -1 if no account was found
	 */
//	public function getAccountID(RDB &$oDB, ClientConfig &$oClC, $addr, CountryConfig &$oCC=null, $strict=true)
//	public function getAccountID(RDB &$oDB, ClientConfig &$oClC, $addr, CountryConfig &$oCC=null, $mode)
//	public function getAccountID(RDB &$oDB, ClientConfig &$oClC, $cref, $mob, $email, CountryConfig &$oCC)
    public function getAccountID(){
        return forward_static_call_array ( "self::getAccountID_Static", func_get_args());
    }
	public static function getAccountID_Static()
	{
		$aArgs = func_get_args();
		switch (count($aArgs) )
		{
		case (3):
			return self::_getAccountID($aArgs[0], $aArgs[1], $aArgs[2]);
			break;
		case (4):
			return self::_getAccountID($aArgs[0], $aArgs[1], $aArgs[2], $aArgs[3]);
			break;
		case (5):
			if ($aArgs[4] === true) { $mode = 3; }
			else if ($aArgs[4] === false) { $mode = 1; }
			else { $mode = $aArgs[4]; }

			return self::_getAccountID($aArgs[0], $aArgs[1], $aArgs[2], $aArgs[3], $mode);
			break;
		case (6):	//
			list($obj_DB, $obj_ClientConfig, $obj_CountryConfig, $sCustomerRef, $lMobile, $sEMail) = $aArgs;
			$iAccountID = -1;
			if (strlen($sCustomerRef ) > 0 && ($obj_ClientConfig->getIdentification() & 1) == 1) { $iAccountID = EndUserAccount::getAccountIDFromExternalID($obj_DB, $obj_ClientConfig, $sCustomerRef, ($obj_ClientConfig->getStoreCard() <= 3) ); }
			if ($iAccountID == -1 && floatval($lMobile ) > 0 && ($obj_ClientConfig->getIdentification() & 2) == 2) { $iAccountID = EndUserAccount::getAccountID_Static($obj_DB, $obj_ClientConfig, $lMobile, $obj_CountryConfig, ($obj_ClientConfig->getStoreCard() <= 3) ); }
			if ($iAccountID == -1 && trim($sEMail ) != "" && ($obj_ClientConfig->getIdentification() & 4) == 4) { $iAccountID = EndUserAccount::getAccountID_Static($obj_DB, $obj_ClientConfig, $sEMail, $obj_CountryConfig, ($obj_ClientConfig->getStoreCard() <= 3) ); }
			// Both Mobile No. and E-Mail address must match
			if ( ($obj_ClientConfig->getIdentification() & 8) == 8)
			{
				if ((float)$lMobile > 0) { $iMobileAccountID = EndUserAccount::getAccountID_Static($obj_DB, $obj_ClientConfig, $lMobile, $obj_CountryConfig, ($obj_ClientConfig->getStoreCard() <= 3) ); }
				if (trim($sEMail ) != "") { $iEMailAccountID = EndUserAccount::getAccountID_Static($obj_DB, $obj_ClientConfig, $sEMail, $obj_CountryConfig, ($obj_ClientConfig->getStoreCard() <= 3) ); }
				if ($iMobileAccountID == $iEMailAccountID) { $iAccountID = $iMobileAccountID; }
			}
			// Client supports global storage of payment cards
			if ($iAccountID == -1 && $obj_ClientConfig->getStoreCard() > 3)
			{
				if ((float)$lMobile > 0 && ($obj_ClientConfig->getIdentification() & 2) == 2) { $iAccountID = EndUserAccount::getAccountID_Static($obj_DB, $obj_ClientConfig, $lMobile, $obj_CountryConfig, false); }
				if ($iAccountID == -1 && trim($sEMail ) != "" && ($obj_ClientConfig->getIdentification() & 4) == 4) { $iAccountID = EndUserAccount::getAccountID_Static($obj_DB, $obj_ClientConfig, $sEMail, $obj_CountryConfig, false); }
				// Both Mobile No. and E-Mail address must match
				if ( ($obj_ClientConfig->getIdentification() & 8) == 8)
				{
					if ((float)$lMobile > 0) { $iMobileAccountID = EndUserAccount::getAccountID_Static($obj_DB, $obj_ClientConfig, $lMobile, $obj_CountryConfig, false); }
					if (trim($sEMail ) != "") { $iEMailAccountID = EndUserAccount::getAccountID_Static($obj_DB, $obj_ClientConfig, $sEMail, $obj_CountryConfig, false); }
					if ($iMobileAccountID == $iEMailAccountID) { $iAccountID = $iMobileAccountID; }
				}
			}
			return $iAccountID;
			break;
            case (7):
                list($obj_DB, $obj_ClientConfig, $obj_CountryConfig, $sCustomerRef, $lMobile, $sEMail, $iProfileID) = $aArgs;
                $iAccountID = -1;
                if (isset($iProfileID) === false || $iProfileID === '')
                {
                    $iAccountID = EndUserAccount::getAccountID_Static($obj_DB, $obj_ClientConfig, $obj_CountryConfig, $sCustomerRef, $lMobile, $sEMail);
                } else {
                    $iAccountID = self::getAccountIdFromProfileId($obj_DB, $iProfileID);
                }
                return $iAccountID;
                break;
			default:
			trigger_error("Invalid number of arguments: ". count($aArgs), E_USER_ERROR);
			return -1;
			break;
		}
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
	 * @param 	ClientConfig $oClC 	Data object with the Client Configuration
	 * @param	string $addr 		End-User's mobile number or E-Mail address
	 * @param	CountryConfig $oCC	Country Configuration, pass null to default to the Country Configuration from the Client Configuration
	 * @param	integer $mode	 	Integer flag specifying mode that is used to find the end-user account. May be one of the following:
	 * 									0. Find all accounts
	 * 									1. Find only accounds with a password defined or that has a balance > 0
	 * 									2. Find only accounds that has been linked to the client
	 * 									3. Find only accounds with a password defined or that has a balance > 0 and that has been linked to the client
	 * @return	integer				Unqiue ID of the End-User's Account or -1 if no account was found
	 */
	private static function _getAccountID(RDB &$oDB, ClientConfig &$oClC, $addr, CountryConfig &$oCC=null, $mode=3)
	{
		if (is_null($oCC) === true) { $oCC = $oClC->getCountryConfig(); }
		if (floatval($addr) > $oCC->getMinMobile() ) { $sql = "EUA.mobile = '". floatval($addr) ."'"; }
		else { $sql = "Upper(EUA.email) = Upper('". $oDB->escStr($addr) ."')"; }

		$sql = "SELECT DISTINCT EUA.id
				FROM EndUser".sSCHEMA_POSTFIX.".Account_Tbl EUA
				LEFT OUTER JOIN EndUser".sSCHEMA_POSTFIX.".CLAccess_Tbl CLA ON EUA.id = CLA.accountid
				WHERE EUA.countryid = ". $oCC->getID() ."
					AND ". $sql ." AND EUA.enabled = '1'";
		if ( ($mode & 1) == 1 && strlen($oClC->getAuthenticationURL()) == 0) { $sql .= " AND ( (EUA.passwd IS NOT NULL AND length(EUA.passwd) > 0) OR EUA.balance > 0)"; }
		// Not a System Client
		if ($oClC->getCountryConfig()->getID() != $oClC->getID() && ($mode & 2) == 2)
		{
			$sql .= "
					AND (CLA.clientid = ". $oClC->getID() ." /* OR EUA.countryid = CLA.clientid */
					OR NOT EXISTS (SELECT id
								   FROM EndUser".sSCHEMA_POSTFIX.".CLAccess_Tbl
								   WHERE accountid = EUA.id) )";
		}
		$sql .= "
				ORDER BY EUA.id DESC
				LIMIT 1";
//		echo $sql ."\n";
		$RS = $oDB->getName($sql);

		return is_array($RS) === true ? $RS["ID"] : -1;
	}

	/**
	 * Tops an End-User's e-money based prepaid account up with the specified amount
	 *
	 * @see		Constants::iTOPUP_OF_EMONEY
	 * @see		Constants::iTOPUP_OF_POINTS
	 * @see		Constants::iREWARD_OF_POINTS
	 *
	 * @param	integer $id 		Unqiue ID of the End-User's Account
	 * @param	integer $typeid		Unqiue ID of the Top-Up type
	 * @param	integer $txnid 		Unqiue ID of the mPoint Transaction that was used for the Top-Up
	 * @param 	integer $amount 	Amount that the End-User's prepaid account should be topped up with
	 * @return 	boolean
	 */
	public function topup($id, $typeid, $txnid, $amount)
	{
		$sql = "INSERT INTO EndUser".sSCHEMA_POSTFIX.".Transaction_Tbl
					(accountid, typeid, txnid, amount, ip, address)
				SELECT ". intval($id) .", ". intval($typeid) .", ". intval($txnid) .", ". abs(intval($amount) ) .", ip, mobile
				FROM Log".sSCHEMA_POSTFIX.".Transaction_Tbl
				WHERE id = ". intval($txnid);
//		echo $sql ."\n";

		return is_resource($this->getDBConn()->query($sql) );
	}

	/**
	 * Makes a purchase using the End-User's Stored Value Account.
	 *
	 * @see		Constants::iPURCHASE_USING_EMONEY
	 * @see		Constants::iPURCHASE_USING_POINTS
	 *
	 * @param	integer $id 		Unqiue ID of the End-User's Account
	 * @param	integer $typeid		Unqiue ID of the Purchase type
	 * @param	integer $txnid 		Unqiue ID of the mPoint Transaction that the purchase is for
	 * @param 	integer $amount 	Amount that should be charged to the End-User's prepaid account
	 * @return 	boolean
	 */
	public function purchase($id, $typeid, $txnid, $amount)
	{
		$amount = abs($amount) * -1;

		$sql = "INSERT INTO EndUser".sSCHEMA_POSTFIX.".Transaction_Tbl
					(accountid, typeid, txnid, amount, ip, address)
				SELECT ". intval($id) .", ". intval($typeid) .", ". intval($txnid) .", ". intval($amount) .", ip, mobile
				FROM Log".sSCHEMA_POSTFIX.".Transaction_Tbl
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
		$sql = "INSERT INTO EndUser".sSCHEMA_POSTFIX.".Transaction_Tbl
					(accountid, typeid, txnid, ip, address)
				SELECT ". intval($id) .", ". Constants::iCARD_PURCHASE_TYPE .", ". intval($txnid) .", ip, mobile
				FROM Log".sSCHEMA_POSTFIX.".Transaction_Tbl
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

	/**
	 *
	 * Return codes:
	 * 200. Customer found
	 * 404. Customer not found
	 * 500. Internal Error
	 * 502. An error occurred while communicating with the external system
	 *
	 * @param	HTTPConnInfo $obj_ConnInfo
	 * @param	ClientInfo $obj_ClientInfo
	 * @param	integer $id
	 * @param	long $ssno
	 */
	public function import(HTTPConnInfo &$obj_ConnInfo, ClientInfo &$obj_ClientInfo, $id, $ssno=-1)
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml .= '<root>';
		$xml .= '<import>';
		$xml .= '<customer id="'. intval($id) .'">';
		$ssno = (float) $ssno;
		if ($ssno > 0) { $xml .= '<social-security-number>'. str_repeat("0", 10 - strlen($ssno) ) . $ssno .'</social-security-number>'; }
		$xml .= '</customer>';
		$xml .= $obj_ClientInfo->toXML();
		$xml .= '</import>';
		$xml .= '</root>';

		$obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
		$obj_HTTP->connect();
		$code = $obj_HTTP->send($this->constHTTPHeaders(), $xml);
		$obj_HTTP->disconnect();
		if ($code == 200 || $code == 206)
		{
			if (stristr($obj_HTTP->getReplyHeader(), "UTF-8") == true)
			{
				$obj_DOM = simpledom_load_string(trim($obj_HTTP->getReplyBody() ) );
			}
			else { $obj_DOM = simpledom_load_string(utf8_encode(trim($obj_HTTP->getReplyBody() ) ) ); }

			// Success: Customer data retrieved
			if (count($obj_DOM->children() ) > 0)
			{
				if (count($obj_DOM->customer) > 0)
				{
					// Update Customer Info
					$extid = (string) $obj_DOM->customer["external-id"];
					$fn = (string) $obj_DOM->customer->profile->{'first-name'};
					$ln = (string) $obj_DOM->customer->profile->{'last-name'};
					if (empty($extid) === false)
					{
						$sql = "UPDATE EndUser".sSCHEMA_POSTFIX.".Account_Tbl
								SET externalid = '". $this->getDBConn()->escStr($extid) ."'
								WHERE id = ". intval($id);
//						echo $sql ."\n";
						if (is_resource($this->getDBConn()->query($sql) ) === false) { $code = 500; }
					}
/*
					// Update Addresses
					for ($i=0; $i<count($obj_DOM->customer->addresses->address); $i++)
					{
						$obj_XML = simplexml_load_string($this->getAddresses($obj_CustomerInfo->getID() ) );
						// Assume address doesn't exist
						$bExists = false;
						for ($j=0; $j<count($obj_XML->address); $j++)
						{
							// Check whether address already exists
							if (intval($obj_DOM->customer->addresses->address[$i]->country["id"]) == intval($obj_XML->address[$j]->country["id"])
								&& strtolower($obj_DOM->customer->addresses->address[$i]->{'first-name'}) == strtolower($obj_XML->address[$j]->{'first-name'})
								&& strtolower($obj_DOM->customer->addresses->address[$i]->{'last-name'}) == strtolower($obj_XML->address[$j]->{'last-name'})
								&& strtolower($obj_DOM->customer->addresses->address[$i]->street) == strtolower($obj_XML->address[$j]->street)
								&& strtolower($obj_DOM->customer->addresses->address[$i]->{'postal-code'}) == strtolower($obj_XML->address[$j]->{'postal-code'})
								&& strtolower($obj_DOM->customer->addresses->address[$i]->city) == strtolower($obj_XML->address[$j]->city)
								&& strtolower($obj_DOM->customer->addresses->address[$i]->state) == strtolower($obj_XML->address[$j]->state) )
							{
								$bExists = true;
								// Break out of loop as match has been found
								$j = count($obj_XML->address);
							}
						}
						// Address doesn't exist, add to profile
						if ($bExists === false)
						{
							$this->saveAddress($obj_CustomerInfo->getID(), $obj_DOM->customer->addresses->address[$i]->country["id"], (string) $obj_DOM->customer->addresses->address[$i]->{'first-name'},  (string) $obj_DOM->customer->addresses->address[$i]->{'last-name'}, (string) $obj_DOM->customer->addresses->address[$i]->company, (string) $obj_DOM->customer->addresses->address[$i]->street, (string) $obj_DOM->customer->addresses->address[$i]->{'postal-code'}, (string) $obj_DOM->customer->addresses->address[$i]->city, (string) $obj_DOM->customer->addresses->address[$i]->state);
						}
					}
*/
				}
			}
		}
		return $code;
	}

	public function notify(HTTPConnInfo &$obj_ConnInfo, ClientInfo &$obj_ClientInfo, $id, $at, $num)
	{
		$obj_XML = simplexml_load_string($this->getAccountInfo($id) );
		$xml = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml .= '<root>';
		$xml .= '<notify>';
		$xml .= '<customer id="'. intval($id) .'">';
		$xml .= '<stored-cards>'. intval($num) .'</stored-cards>';
		$xml .= '</customer>';
		$xml .= $obj_XML->password->asXML();
		$xml .= '<auth-token>'. htmlspecialchars($at, ENT_NOQUOTES) .'</auth-token>';
		$xml .= $obj_ClientInfo->toXML();
		$xml .= '</notify>';
		$xml .= '</root>';

		$obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
		$obj_HTTP->connect();
		$code = $obj_HTTP->send($this->constHeader(), $xml);
		$obj_HTTP->disconnect();
		if (stristr($obj_HTTP->getReplyHeader(), "UTF-8") == true)
		{
			$obj_XML = simpledom_load_string(trim($obj_HTTP->getReplyBody() ) );
		}
		else { $obj_XML = simpledom_load_string(utf8_encode(trim($obj_HTTP->getReplyBody() ) ) ); }

		if ( ($obj_XML instanceof SimpleDOMElement) === true)
		{
			// Notification succeeded
			if ($code == 200)
			{
				$code = 10;
			}
			else { $code = 2; }
		}
		else { $code = 1; }

		return $code;
	}

    /**
     * Retrieves the Ticket of the card from EndUser.Card_Tbl
     *
     * @param integer 	$cardid 		ID from System.Card_Tbl
     * @return string	$ticket			Token of the card at the PSP
     */
    public function getTicket($cardid)
    {
        $sql = "SELECT ticket
				FROM EndUser".sSCHEMA_POSTFIX.".Card_Tbl
				WHERE clientid = ". $this->_obj_ClientConfig->getID() ." AND id = ". intval($cardid);

//		echo $sql ."\n";
        $RS = $this->getDBConn()->getName($sql);

        return is_array($RS) === true ? $RS["TICKET"] : NULL;
    }

    /**
     * Retrieves the psp-id of the card from EndUser.Card_Tbl
     *
     * @param integer 	$id 		ID from Enduser.Card_Tbl
     * @return string	$psp-id		pspid of the card from which token was returned
     */
    public function getCardPSPId($id)
    {
        $sql = "SELECT pspid
				FROM EndUser".sSCHEMA_POSTFIX.".Card_Tbl
				WHERE id = ". intval($id);

		//echo $sql ."\n";
        $RS = $this->getDBConn()->getName($sql);

        return is_array($RS) === true ? $RS["PSPID"] : NULL;
    }

    /**
     * Retrieves the billing address of the card from EndUser.Address_tbl
     *
     * @param integer 	$cardid 		ID from Enduser.Card_Tbl
     * @return string
     */
    public function getAddressFromCardId($cardid)
    {
        $xml = '';
        $sql = "SELECT firstname,lastname,company,street,postalcode,city,state,countryid
				FROM EndUser".sSCHEMA_POSTFIX.".Address_tbl
				WHERE cardid = ". intval($cardid);

        //echo $sql ."\n";
        $RS = $this->getDBConn()->getName($sql);
        
        return $RS;
    }

    /**
     * Retrieves the card details and billing address of the card from EndUser.Card_tbl
     * and EndUser.Address_tbl
     *
     * @param integer 	$cardid 		ID from Enduser.Card_Tbl
     * @return string
     */
    public function getCardDetailsFromCardId($cardid)
    {
        $sql = "SELECT CARD.id, CARD.accountid, CARD.cardid, CARD.pspid, CARD.mask, CARD.expiry, 
                CARD.ticket, CARD.preferred, CARD.name, CARD.card_holder_name, CARD.chargetypeid,
				ADDR.countryid, ADDR.firstname, ADDR.lastname, ADDR.company, 
				ADDR.street, ADDR.postalcode, ADDR.city, ADDR.state
				FROM EndUser".sSCHEMA_POSTFIX.".Card_Tbl CARD
				LEFT OUTER JOIN EndUser".sSCHEMA_POSTFIX.".Address_Tbl ADDR ON CARD.id = ADDR.cardid
				WHERE CARD.id = ". intval($cardid) ." AND CARD.enabled = '1'";

        //echo $sql ."\n";
        $RS = $this->getDBConn()->getName($sql);

        $xml = '<card id="'. $RS["ID"] .'" eua-id="'. $RS["ACCOUNTID"] .'" type-id="'. $RS["CARDID"] .'" psp-id="'. $RS["PSPID"] .'" preferred="'. General::bool2xml($RS["PREFERRED"]) .'" charge-type-id="'. $RS['CHARGETYPEID'] .'">';
        $xml .= '<name>'. $RS["NAME"] .'</name>';
        $xml .= '<card-holder-name>'. $RS["CARD_HOLDER_NAME"] .'</card-holder-name>';
        $xml .= '<card-number-mask>'. $RS["MASK"] .'</card-number-mask>';
        $xml .= '<expiry>'. $RS["EXPIRY"] .'</expiry>';
        $xml .= '<token>'. $RS['TICKET'] .'</token>';
        if (intval($RS["COUNTRYID"]) > 0)
        {
            $xml .= '<address country-id="'. $RS["COUNTRYID"].'">';
            $xml .= '<first-name>'. $RS["FIRSTNAME"] .'</first-name>';
            $xml .= '<last-name>'. $RS["LASTNAME"] .'</last-name>';
            $xml .= '<street>'. $RS["STREET"] .'</street>';
            $xml .= '<postal-code>'. $RS["POSTALCODE"] .'</postal-code>';
            $xml .= '<city>'. $RS["CITY"] .'</city>';
            $xml .= '<state>'. $RS["STATE"] .'</state>';
            $xml .= '</address>';
        }
        $xml .= '</card>';
        return $xml;
    }

    /**
     * Retrieves the Ticket of the card from EndUser.Card_Tbl
     *
     * @param integer 	$profileid 		Profile ID of registered user
     * @return string	$id			EUA ID, mpoint enduser account id
     */
    private static function getAccountIdFromProfileId(RDB &$oDB, $profileid)
    {
        $sql = "SELECT id
				FROM EndUser".sSCHEMA_POSTFIX.".Account_Tbl
				WHERE profileid = '". $profileid ."'";

//		echo $sql ."\n";
        $RS = $oDB->getName($sql);

        return is_array($RS) === true ? $RS["ID"] : -1;
    }

    /**
     * Retrieves mask card for given end user accountid and card id
     * @param integer $accoutnid      End user account id
     * @param integer $cardid  Card number
     * @return string          Masked card number
     */
    public function getMaskCard($accoutnid, $cardid)
    {
        if($this->getDBConn() != NULL){
            $sql = "SELECT mask
				FROM EndUser".sSCHEMA_POSTFIX.".Card_Tbl
				WHERE accountid = ". (int)$accoutnid ." AND id = ". (int)$cardid;

            $RS = $this->getDBConn()->getName($sql);

            return is_array($RS) === true ? $RS["MASK"] : NULL;
        }
        return NULL;
    }

    protected function setClientConfig(ClientConfig $clientConfig): void
    {
        if($this->_obj_ClientConfig->getID() !== $clientConfig->getID())
        {
            $this->_obj_ClientConfig = $clientConfig;
            $this->_obj_CountryConfig = $clientConfig->getCountryConfig();
        }
    }
}
?>