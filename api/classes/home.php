<?php
/**
 * The General package provides low level functionality that are shared accross several modules and/or pages
 * Obvious choices for functionality in this class are:
 * 	- Access Validation
 * 	- General validation methods: valEMail, valUsername, valPassword etc.
 * The Home subpackage provides general features accessible to a user that has successfully logged in as
 * well as basic navigation between the different modules in mPoint
 *
 * @author Jonatan Evald Buus
 * @package General
 * @subpackage Home
 * @license Cellpoint Mobile
 */

/**
 * The Home class provides general methods for basic navigation between the different modules in mPoint
 *
 */
class Home extends General
{
	/**
	 * Data object with the Country Configuration
	 *
	 * @var CountryConfig
	 */
	private $_obj_CountryConfig;

	/**
	 * Default Constructor.
	 *
	 * @param	RDB $oDB				Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param	TranslateText $oTxt 	Reference to the Text Translation Object for translating any text into a specific language
	 * @param 	CountryConfig $oCC 		Reference to the data object with the Country Configuration
	 */
	public function __construct(RDB &$oDB, TranslateText &$oTxt, CountryConfig &$oCC=null)
	{
		parent::__construct($oDB, $oTxt);

		$this->_obj_CountryConfig = $oCC;
	}

	/**
	 * Generates and sends a One Time Password to the End-User using the provided Mobile Number (MSISDN).
	 * 
	 * @see		GoMobileMessage::produceMessage()
	 * @see		General::getText()
	 * @see		Home::genActivationCode()
	 * @see		Home::sendMessage()
	 * @see		ClientConfig::produceConfig()
	 *
	 * @param 	GoMobileConnInfo $oCI 	Reference to the data object with the Connection Info required to communicate with GoMobile
	 * @param	integer $id 			Unqiue ID of the End-User's Account
	 * @param	CountryConfig $oCC		Configuration for the recipient's country
	 * @param	string $mob 			End-User's mobile number
	 * @return	integer
	 * @throws 	mPointException
	 */
	public function sendOneTimePassword(GoMobileConnInfo &$oCI, $id, CountryConfig &$oCC, $mob)
	{
		$sBody = $this->getText()->_("mPoint - Send One Time Password");
		$sBody = str_replace("{OTP}", $this->genActivationCode($id, $mob, date("Y-m-d H:i:s", time() + 60 * 60) ), $sBody);
		
		$obj_ClientConfig = ClientConfig::produceConfig($this->getDBConn(), $this->getCountryConfig()->getID(), -1);
		
		$obj_MsgInfo = GoMobileMessage::produceMessage(Constants::iMT_SMS_TYPE, $oCC->getID(), $oCC->getID()*100, $oCC->getChannel(), $obj_ClientConfig->getKeywordConfig()->getKeyword(), Constants::iMT_PRICE, $mob, utf8_decode($sBody) );
		$obj_MsgInfo->setDescription("mPoint - OTP");
		
		$iCode = $this->sendMessage($oCI, $obj_ClientConfig, $obj_MsgInfo);
		if ($iCode != 200) { $iCode = 91; }
		
		return $iCode;
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
		$obj_ClientConfig = ClientConfig::produceConfig($this->getDBConn(), $this->getCountryConfig()->getID(), -1);
		$sBody = $this->getText()->_("mPoint - Account Disabled");
		$sBody = str_replace("{CLIENT}", $obj_ClientConfig->getName(), $sBody);
		
		$obj_MsgInfo = GoMobileMessage::produceMessage(Constants::iMT_SMS_TYPE, $this->getCountryConfig()->getID(), $this->getCountryConfig()->getID()*100, $this->getCountryConfig()->getChannel(), $obj_ClientConfig->getKeywordConfig()->getKeyword(), Constants::iMT_PRICE, $mob, utf8_decode($sBody) );
		$obj_MsgInfo->setDescription("mPoint - Account Del");
		if ($this->getCountryConfig()->getID() != 200) { $obj_MsgInfo->setSender(substr($obj_ClientConfig->getName(), 0, 11) ); }
		
		$iCode = $this->sendMessage($oCI, $obj_ClientConfig, $obj_MsgInfo);
		if ($iCode != 200) { $iCode = 91; }
		
		return $iCode;
	}
	
	/**
	 * Returns a reference to the data object with the Country Configuration
	 * 
	 * @return CountryConfig
	 */
	public function &getCountryConfig() { return $this->_obj_CountryConfig; }
	
	/**
	 * Fetches the unique ID of the End-User's account from the database.
	 *
	 * @param	CountryConfig $oCC	Reference to the data object with the Configuration for the Country that the End-User Account must be active in
	 * @param	string $addr 		End-User's mobile number or E-Mail address
	 * @return	integer				Unqiue ID of the End-User's Account or -1 if no account was found
	 */
	public function getAccountID(CountryConfig &$oCC, $addr, $clid=-1)
	{
		if (floatval($addr) > $oCC->getMinMobile() ) { $sql = "A.mobile = '". floatval($addr) ."'"; }
		else { $sql = "Upper(A.email) = Upper('". $this->getDBConn()->escStr($addr) ."')"; }

		$sql = "SELECT DISTINCT A.id
				FROM EndUser.Account_Tbl A
				LEFT OUTER JOIN EndUser.CLAccess_Tbl Acc ON A.id = Acc.accountid
				WHERE A.countryid = ". $oCC->getID() ."
					AND ". $sql ." AND A.enabled = '1'";
		if ($clid > 0) { $sql ." AND Acc.clientid = ". intval($clid); }
//		echo $sql ."\n";
		$RS = $this->getDBConn()->getName($sql);

		return is_array($RS) === true ? $RS["ID"] : -1;
	}

	/**
	 * Authenticates the End-User using the provided Account ID and Password.
	 * The method will return the following status codes:
	 * 	 1. Account ID / Password doesn't match
	 * 	 2. Account ID / Password doesn't match - Next invalid login will disable the account
	 * 	 3. Account ID / Password doesn't match - Account has been disabled
	 * 	 9. Account disabled
	 * 	10. Success
	 * 
	 * @see		Constants::iMAX_LOGIN_ATTEMPTS
	 *
	 * @param	integer $id 	Unqiue ID of the End-User's Account
	 * @param	string $pwd 	Password provided by the End-User
	 * @return	integer
	 */
	public function auth($id, $pwd, $disable=true)
	{
		$sql = "SELECT id, attempts, passwd AS password, mobile, enabled, mobile_verified
				FROM EndUser.Account_Tbl
				WHERE id = ". intval($id);
//		echo $sql ."\n";
		$RS = $this->getDBConn()->getName($sql);

		if (is_array($RS) === true)
		{
			// Invalid logins exceeded or Account disabled
 			if ($RS["ATTEMPTS"] + 1 > Constants::iMAX_LOGIN_ATTEMPTS || $RS["ENABLED"] === false)
			{
				$code = 9;
				$iAttempts = $RS["ATTEMPTS"] + 1;
				$bEnabled = false;
			}
			// Login successful
			elseif ($RS["PASSWORD"] == $pwd)
			{
				if ($RS["MOBILE_VERIFIED"] === true) { $code = 10; }
				else { $code = 11; }
				$iAttempts = 0;
				$bEnabled = true;
			}
			// Invalid login - Account has been disabled
			elseif ($RS["ATTEMPTS"] + 1 == Constants::iMAX_LOGIN_ATTEMPTS)
			{
				$code = 3;
				$iAttempts = $RS["ATTEMPTS"] + 1;
				$bEnabled = false;
			}
			// Invalid login - Next invalid login will disable the account
			elseif ($RS["ATTEMPTS"] + 2 == Constants::iMAX_LOGIN_ATTEMPTS)
			{
				$code = 2;
				$iAttempts = $RS["ATTEMPTS"] + 1;
				$bEnabled = true;
			}
			// Invalid login
			else
			{
				$code = 1;
				$iAttempts = $RS["ATTEMPTS"] + 1;
				$bEnabled = true;
			}
			if ($disable === true)
			{
				// Update number of login attempts for End-User
				$sql = "UPDATE EndUser.Account_Tbl
						SET attempts = ". $iAttempts .", enabled = '". ($bEnabled === true ? "1" : "0") ."'
						WHERE id = ". intval($id);
//				echo $sql ."\n";
				$this->getDBConn()->query($sql);
			}
		}
		// Account not found
		else { $code = 4; }

		return $code;
	}

	/**
	 * Returns all pages which should be re-chached after a user logs in
	 * The returned XML document has the following format:
	 * 	<recache>
	 * 		<url>{URL TO RECACHE}</url>
	 * 		<url>{URL TO RECACHE}</url>
	 * 		...
	 * 	</recache>
	 * Please refer to the GUI Protocol documentation for the Re-Cache protocol for
	 * further information.
	 *
	 * @link 	http://iemendo.cydev.biz/files/gui_protocols.pdf
	 *
	 * @return 	xml 	XML Document with URLs which should be re-cached
	 */
	public static function getRecacheLogin()
	{
		$xml = '<recache>
					<url>/home/default.php</url>
				 	<url>/home/content.php</url>
				 	<url>/home/topmenu.php</url>
				 	<url>/login/default.php</url>
				 	<url>/login/content.php</url>
		 		</recache>';

		return $xml;
	}

	/**
	 * Fetches the Account Information for the End-User from the database.
	 * If a User Agent Profile is provided, the method will automatically calculate the width and height of the account logo
	 * after it has been resized to fit the screen resolution of the end-user's mobile device.
	 *
	 * The account information is returned as an XML Document in the following format:
	 * 	<account id="{UNIQUE ID FOR THE END-USER ACCOUNT}" countryid="{ID OF THE COUNTRY THE ACCOUNT IS VALID IN}">
	 *		<firstname>{END-USER'S FIRSTNAME}</firstname>
	 *		<lastname>{END-USER'S LASTNAME}</lastname>
	 *		<mobile>{END-USER'S MOBILE NUMBER (MSISDN) }</mobile>
	 *		<email>{END-USER'S E-MAIL ADDRESS}</email>
	 *		<password mask="{A STRING OF * WITH A LENGTH EQUIVALENT TO THE LENGTH OF THE PASSWORD}">{END-USER'S PASSWORD}</password>
	 *		<balance currency="{ISO-4217 CURRENCY CODE THAT THE BALANCE IS REPRESENTED IN}" symbol="{SYMBOL USED TO REPRESENT THE CURRENCY}">{PRE-PAID BALANCE AVAILABLE ON THE END-USER ACCOUNT IN COUNTRY'S SMALLEST CURRENCY}</balance>
	 *		<funds>{PRE-PAID BALANCE FORMATTED FOR BEING DISPLAYED IN THE GIVEN COUNTRY}</funds>
	 *		<created timestamp="{CREATION TIME IN SECONDS SINCE EPOCH}">{TIMESTAMP IDENTIFYING WHEN THE ACCOUNT WAS CREATED}</created>
	 *		<logo-width>{CALCULATED WIDTH OF THE ACCOUNT LOGO}</logo-width>
	 *		<logo-height>{CALCULATED HEIGHT OF THE ACCOUNT LOGO}</logo-height>
	 * 	</account>
	 *
	 * @param	integer $id 	Unqiue ID of the End-User's Account
	 * @param 	UAProfile $oUA 	Reference to the User Agent Profile for the Customer's Mobile Device (optional)
	 * @return 	string
	 */
	public function getAccountInfo($id, &$oUA=null)
	{
		/* ========== Calculate Logo Dimensions Start ========== */
		if (is_null($oUA) === false)
		{
			$iWidth = $oUA->getWidth() * iCARD_LOGO_SCALE / 100;
			$iHeight = $oUA->getHeight() * iCARD_LOGO_SCALE / 100;

			if ($iWidth / 180 > $iHeight / 115) { $fScale = $iHeight / 115; }
			else { $fScale = $iWidth / 180; }

			$iWidth = intval($fScale * 180);
			$iHeight = intval($fScale * 115);
		}
		else
		{
			$iWidth = iCARD_LOGO_SCALE ."%";
			$iHeight = iCARD_LOGO_SCALE ."%";
		}
		/* ========== Calculate Logo Dimensions End ========== */

		// Select information for the End-User's account
		$sql = "SELECT id, countryid, firstname, lastname, mobile, email, passwd AS password, balance, points, Extract('epoch' from created AT TIME ZONE 'Europe/Copenhagen') AS timestamp
				FROM EndUser.Account_Tbl
				WHERE id = ". intval($id) ." AND enabled = '1'";
//		echo $sql ."\n";
		$RS = $this->getDBConn()->getName($sql);
		
		$sql = "SELECT CL.id, CL.store_card, CL.name
				FROM EndUser.CLAccess_Tbl Acc
				INNER JOIN Client.Client_Tbl CL ON Acc.clientid = CL.id AND CL.enabled = '1'
				WHERE Acc.accountid = ". intval($id);
//		echo $sql ."\n";
		$aRS = $this->getDBConn()->getAllNames($sql);
		
		// Construct XML Document with account information
		$xml = '<account id="'. $RS["ID"] .'" country-id="'. $RS["COUNTRYID"] .'">';
		$xml .= '<first-name>'. htmlspecialchars($RS["FIRSTNAME"], ENT_NOQUOTES) .'</first-name>';
		$xml .= '<last-name>'. htmlspecialchars($RS["LASTNAME"], ENT_NOQUOTES) .'</last-name>';
		$xml .= '<mobile country-id="'. $RS["COUNTRYID"] .'">'. $RS["MOBILE"] .'</mobile>';
		$xml .= '<email>'. htmlspecialchars($RS["EMAIL"], ENT_NOQUOTES) .'</email>';
		$xml .= '<password mask="'. str_repeat("*", strlen($RS["PASSWORD"]) ) .'">'. htmlspecialchars($RS["PASSWORD"], ENT_NOQUOTES) .'</password>';
		$xml .= '<balance country-id="'. $this->_obj_CountryConfig->getID() .'" currency="'. $this->_obj_CountryConfig->getCurrency() .'" symbol="'. $this->_obj_CountryConfig->getSymbol() .'" format="'. $this->_obj_CountryConfig->getPriceFormat().'">'. intval($RS["BALANCE"]) .'</balance>';
		$xml .= '<funds>'. General::formatAmount($this->_obj_CountryConfig, $RS["BALANCE"]) .'</funds>';
		$xml .= '<points country-id="0" currency="points" symbol="points" format="{PRICE} {CURRENCY}">'. $RS["POINTS"] .'</points>';
		$xml .= '<clients>';
		for ($i=0; $i<count($aRS); $i++)
		{
			$xml .= '<client id="'. $aRS[$i]["ID"] .'" store-card="'. $aRS[$i]["STORE_CARD"] .'">'. htmlspecialchars($aRS[$i]["NAME"], ENT_NOQUOTES) .'</client>';
		}
		$xml .= '</clients>';
		$xml .= '<created timestamp="'. intval($RS["TIMESTAMP"]) .'">'. gmdate("Y-m-d H:i:sP", $RS["TIMESTAMP"]) .'</created>';
		$xml .= '<logo-width>'. $iWidth .'</logo-width>';
		$xml .= '<logo-height>'. $iHeight .'</logo-height>';
		$xml .= '</account>';
		
		return $xml;
	}

	/**
	 * Fetches the Credit Cards that have been stored for the specific End-User Account.
	 * If a User Agent Profile is provided, the method will automatically calculate the width and height of the account logo
	 * after it has been resized to fit the screen resolution of the end-user's mobile device.
	 *
	 * The card data is returned as an XML Document in the following format:
	 * 	<stored-cards accountid="{UNIQUE ID FOR THE END-USER ACCOUNT}">
	 * 		<card id="{UNIQUE ID FOR THE SAVED CARD}" pspid="{UNIQUE ID FOR THE PSP THE CARD WAS SAVED THROUGH}" preferred="{BOOLEAN FLAG INDICATING WHETHER THE SAVED CARD IS THE END-USER'S PREFERRED}">
	 * 			<client id="{UNIQUE ID FOR THE CLIENT WITH WHOM THE CARD IS REGISTERED}">{NAME OF THE WITH WHOM THE CARD IS REGISTERED}</client>
	 * 			<type id="{UNIQUE ID FOR THE CARD TYPE}">{NAME OF THE CARD TYPE: DANKORT / VISA / MASTER CARD ETC.}</type>
	 *			<mask>{MASKED CARD NUMBER IN THE FORMAT: [CARD PREFIX]** **** [LAST 4 DIGITS]}</mask>
	 *			<expiry>{CARD EXPIRY DATE IN THE FORMAT: MM/YY}</expiry>
	 *			<ticket>{TICKET ID REPRESENTING THE STORED CARD WITH THE PSP}</ticket>
	 *			<logo-width>{CALCULATED WIDTH OF THE CARD LOGO}</logo-width>
	 *			<logo-height>{CALCULATED HEIGHT OF THE CARD LOGO}</logo-height>
	 *		</card>
	 *		<card id="{UNIQUE ID FOR THE SAVED CARD}" pspid="{UNIQUE ID FOR THE PSP THE CARD WAS SAVED THROUGH}" preferred="{BOOLEAN FLAG INDICATING WHETHER THE SAVED CARD IS THE END-USER'S PREFERRED}">
	 *			<client id="{UNIQUE ID FOR THE CLIENT WITH WHOM THE CARD IS REGISTERED}">{NAME OF THE WITH WHOM THE CARD IS REGISTERED}</client>
	 * 			<type id="{UNIQUE ID FOR THE CARD TYPE}">{NAME OF THE CARD TYPE: DANKORT / VISA / MASTER CARD ETC.}</type>
	 *			<mask>{MASKED CARD NUMBER IN THE FORMAT: [CARD PREFIX]** **** [LAST 4 DIGITS]}</mask>
	 *			<expiry>{CARD EXPIRY DATE IN THE FORMAT: MM/YY}</expiry>
	 *			<ticket>{TICKET ID REPRESENTING THE STORED CARD WITH THE PSP}</ticket>
	 *			<logo-width>{CALCULATED WIDTH OF THE CARD LOGO}</logo-width>
	 *			<logo-height>{CALCULATED HEIGHT OF THE CARD LOGO}</logo-height>
	 *		</card>
	 *		...
	 * 	</stored-cards>
	 *
	 * @param	integer $id 	Unqiue ID of the End-User's Account
	 * @param 	UAProfile $oUA 	Reference to the User Agent Profile for the Customer's Mobile Device (optional)
	 * @return 	string
	 */
	public function getStoredCards($id, &$oUA=null)
	{
		/* ========== Calculate Logo Dimensions Start ========== */
		if (is_null($oUA) === false)
		{
			$iWidth = $oUA->getWidth() * iCARD_LOGO_SCALE / 100;
			$iHeight = $oUA->getHeight() * iCARD_LOGO_SCALE / 100;

			if ($iWidth / 180 > $iHeight / 115) { $fScale = $iHeight / 115; }
			else { $fScale = $iWidth / 180; }

			$iWidth = intval($fScale * 180);
			$iHeight = intval($fScale * 115);
		}
		else
		{
			$iWidth = iCARD_LOGO_SCALE ."%";
			$iHeight = iCARD_LOGO_SCALE ."%";
		}
		/* ========== Calculate Logo Dimensions End ========== */

		// Select all active cards that are not yet expired
		$sql = "SELECT DISTINCT EUC.id, EUC.pspid, EUC.mask, EUC.expiry, EUC.ticket, EUC.preferred, EUC.name,
					SC.id AS typeid, SC.name AS type,
					CL.id AS clientid, CL.name AS client
				FROM EndUser.Card_Tbl EUC
				INNER JOIN System.PSP_Tbl PSP ON EUC.pspid = PSP.id AND PSP.enabled = '1'
				INNER JOIN System.Card_Tbl SC ON EUC.cardid = SC.id AND SC.enabled = '1'
				INNER JOIN Client.Client_Tbl CL ON EUC.clientid = CL.id AND CL.enabled = '1'
				INNER JOIN EndUser.Account_Tbl EUA ON EUC.accountid = EUA.id AND EUA.enabled = '1'
				LEFT OUTER JOIN EndUser.CLAccess_Tbl CLA ON EUA.id = CLA.accountid
				WHERE EUC.accountid = ". intval($id) ." AND EUC.enabled = '1'
					AND ( (substr(EUC.expiry, 4, 2) || substr(EUC.expiry, 1, 2) ) >= '". date("ym") ."' OR length(EUC.expiry) = 0 )
					AND (CLA.clientid = CL.id OR EUA.countryid = CLA.clientid 
						 OR NOT EXISTS (SELECT id
									    FROM EndUser.CLAccess_Tbl
										WHERE accountid = EUA.id) )
				ORDER BY CL.name ASC";
//		echo $sql ."\n";
		$res = $this->getDBConn()->query($sql);
		
		$xml = '<stored-cards accountid="'. $id .'">';
		while ($RS = $this->getDBConn()->fetchName($res) )
		{
			// Construct XML Document with data for saved cards
			$xml .= '<card id="'. $RS["ID"] .'" pspid="'. $RS["PSPID"] .'" preferred="'. General::bool2xml($RS["PREFERRED"]) .'">';
			$xml .= '<client id="'. $RS["CLIENTID"] .'">'. htmlspecialchars($RS["CLIENT"], ENT_NOQUOTES) .'</client>';
			$xml .= '<type id="'. $RS["TYPEID"] .'">'. $RS["TYPE"] .'</type>';
			$xml .= '<name>'. htmlspecialchars($RS["NAME"], ENT_NOQUOTES) .'</name>';
			$xml .= '<mask>'. chunk_split($RS["MASK"], 4, " ") .'</mask>';
			$xml .= '<expiry>'. $RS["EXPIRY"] .'</expiry>';
			$xml .= '<ticket>'. $RS["TICKET"] .'</ticket>';
			$xml .= '<logo-width>'. $iWidth .'</logo-width>';
			$xml .= '<logo-height>'. $iHeight .'</logo-height>';
			$xml .= '</card>';
		}
		$xml .= '</stored-cards>';

		return $xml;
	}

	/**
	 *
	 *
	 * @param	integer $id 	Unqiue ID of the End-User's Account
	 * @return 	string
	 */
	public function getTxnHistory($id, $num=0, $offset=-1)
	{
		// Fetch Transaction history for End-User
		$sql = "SELECT EUT.id, EUT.typeid, EUT.toid, EUT.fromid, Extract('epoch' from EUT.created AT TIME ZONE 'Europe/Copenhagen') AS timestamp,
					(CASE
					 WHEN EUT.amount = 0 THEN Txn.amount
					 WHEN EUT.amount IS NULL THEN Txn.amount
					 ELSE abs(EUT.amount)
					 END) AS amount,
					Abs(EUT.fee) AS fee, EUT.address, EUT.message, EUT.stateid,
					(CASE
					 WHEN EUT.typeid = ". Constants::iPURCHASE_USING_EMONEY ." THEN Txn.ip
					 WHEN EUT.typeid = ". Constants::iPURCHASE_USING_POINTS ." THEN Txn.ip
					 WHEN EUT.typeid = ". Constants::iCARD_PURCHASE_TYPE ." THEN Txn.ip
					 ELSE EUT.ip
					 END) AS ip,
					C.id AS countryid, C.currency, C.symbol, C.priceformat,
					CL.id AS clientid, CL.name AS client,
					(EUAT.firstname || ' ' || EUAT.lastname) AS to_name, EUAT.countryid AS to_countryid, EUAT.mobile AS to_mobile, EUAT.countryid AS to_m, EUAT.email AS to_email,
					(EUAF.firstname || ' ' || EUAF.lastname) AS from_name, EUAF.countryid AS from_countryid, EUAF.mobile AS from_mobile, EUAF.email AS from_email,
					Txn.id AS mpointid, Txn.orderid, Txn.cardid, Card.name AS card
				FROM EndUser.Transaction_Tbl EUT
				LEFT OUTER JOIN EndUser.Account_Tbl EUAT ON EUT.toid = EUAT.id
				LEFT OUTER JOIN EndUser.Account_Tbl EUAF ON EUT.fromid = EUAF.id
				LEFT OUTER JOIN Log.Transaction_Tbl Txn ON EUT.txnid = Txn.id
				LEFT OUTER JOIN Client.Client_Tbl CL ON Txn.clientid = CL.id
				LEFT OUTER JOIN System.Country_Tbl C ON Txn.countryid = C.id
				LEFT OUTER JOIN System.Card_Tbl Card ON Txn.cardid = Card.id
				WHERE EUT.accountid = ". intval($id);
		if ( ($num > 0 && $offset <= 0) || $num < 0)
		{
			$sql .= "
					ORDER BY EUT.id DESC";
		}
		else
		{
			$sql .= "
					ORDER BY EUT.id ASC";
		}
		if ($num > 0)
		{
			$sql .= "
					LIMIT ". intval($num);
		}
		if ($offset > 0) { $sql .= " OFFSET ". intval($offset); }
//		echo $sql ."\n";
		$res = $this->getDBConn()->query($sql);

		$xml = '<history account-id="'. $id .'">';
		// Construct XML Document with data for Transaction
		while ($RS = $this->getDBConn()->fetchName($res) )
		{
			// E-Money / Points Top-Up or Points Reward
			if ($RS["TYPEID"] == Constants::iTOPUP_OF_EMONEY || $RS["TYPEID"] == Constants::iTOPUP_OF_POINTS || $RS["TYPEID"] == Constants::iREWARD_OF_POINTS)
			{
				$xml .= '<transaction id="'. $RS["ID"] .'" type-id="'. $RS["TYPEID"] .'" mpoint-id="'. $RS["MPOINTID"] .'">';
				if ($RS["TYPEID"] == Constants::iTOPUP_OF_POINTS || $RS["TYPEID"] == Constants::iREWARD_OF_POINTS)
				{
					if ($RS["COUNTRYID"] == 103 || $RS["COUNTRYID"] == 200) { $seperator = ","; }
					else { $seperator = "."; }
					$xml .= '<amount country-id="0" currency="points" symbol="points" format="{PRICE} {CURRENCY}">'. $RS["AMOUNT"] .'</amount>';
					$xml .= '<price>'. number_format($RS["AMOUNT"], 0, "", $seperator) .' points</price>';
					$xml .= '<fee country-id="0" currency="points" symbol="points" format="{PRICE} {CURRENCY}">'. $RS["FEE"] .'</fee>';
				}
				else
				{
					$xml .= '<amount country-id="'. $RS["COUNTRYID"] .'" currency="'. $RS["CURRENCY"] .'" symbol="'. $RS["SYMBOL"] .'" format="'. $RS["PRICEFORMAT"] .'">'. $RS["AMOUNT"] .'</amount>';
					$xml .= '<price>'. General::formatAmount($this->_obj_CountryConfig, abs($RS["AMOUNT"]) ) .'</price>';
					$xml .= '<fee country-id="'. $RS["COUNTRYID"] .'" currency="'. $RS["CURRENCY"] .'" symbol="'. $RS["SYMBOL"] .'" format="'. $RS["PRICEFORMAT"] .'">'. $RS["FEE"] .'</fee>';
				}
				$xml .= '<ip>'. $RS["IP"] .'</ip>';
				$xml .= '<address>'. htmlspecialchars($RS["ADDRESS"], ENT_NOQUOTES) .'</address>';
				$xml .= '<timestamp>'. gmdate("Y-m-d H:i:sP", $RS["TIMESTAMP"]) .'</timestamp>';
				$xml .= '</transaction>';
			}
			// E-Money Transfer
			elseif ($RS["TYPEID"] == Constants::iTRANSFER_OF_EMONEY)
			{
				$xml .= '<transaction id="'. $RS["ID"] .'" type-id="'. $RS["TYPEID"] .'" state-id="'. $RS["STATEID"] .'">';
				$xml .= '<amount country-id="'. $this->_obj_CountryConfig->getID() .'" currency="'. $this->_obj_CountryConfig->getCurrency() .'" symbol="'. $this->_obj_CountryConfig->getSymbol() .'" format="'. $this->_obj_CountryConfig->getPriceFormat() .'">'. $RS["AMOUNT"] .'</amount>';
				$xml .= '<price>'. General::formatAmount($this->_obj_CountryConfig, $RS["AMOUNT"]) .'</price>';
				$xml .= '<fee country-id="'. $this->_obj_CountryConfig->getID() .'" currency="'. $this->_obj_CountryConfig->getCurrency() .'" symbol="'. $this->_obj_CountryConfig->getSymbol() .'" format="'. $this->_obj_CountryConfig->getPriceFormat() .'">'. $RS["FEE"] .'</fee>';
				$xml .= '<ip>'. $RS["IP"] .'</ip>';
				$xml .= '<address>'. htmlspecialchars($RS["ADDRESS"], ENT_NOQUOTES) .'</address>';
				$xml .= '<from account-id="'. $RS["FROMID"] .'">';
				$xml .= '<name>'. htmlspecialchars($RS["FROM_NAME"], ENT_NOQUOTES) .'</name>';
				$xml .= '<mobile country-id="'. $RS["FROM_COUNTRYID"] .'">'. $RS["FROM_MOBILE"] .'</mobile>';
				$xml .= '<email>'. htmlspecialchars($RS["FROM_EMAIL"], ENT_NOQUOTES) .'</email>';
				$xml .= '</from>';
				$xml .= '<to account-id="'. $RS["TOID"] .'">';
				$xml .= '<name>'. htmlspecialchars($RS["TO_NAME"], ENT_NOQUOTES) .'</name>';
				$xml .= '<mobile country-id="'. $RS["TO_COUNTRYID"] .'">'. $RS["TO_MOBILE"] .'</mobile>';
				$xml .= '<email>'. htmlspecialchars($RS["TO_EMAIL"], ENT_NOQUOTES) .'</email>';
				$xml .= '</to>';
				$xml .= '<timestamp>'. gmdate("Y-m-d H:i:sP", $RS["TIMESTAMP"]) .'</timestamp>';
				$xml .= '<message>'. htmlspecialchars($RS["MESSAGE"], ENT_NOQUOTES) .'</message>';
				$xml .= '</transaction>';
			}
			// E-Money Purchase or Card / Premium SMS based purchase associated with the End-User account
			else
			{
				$xml .= '<transaction id="'. $RS["ID"] .'" type-id="'. $RS["TYPEID"] .'" mpoint-id="'. $RS["MPOINTID"] .'" order-no="'. htmlspecialchars($RS["ORDERID"], ENT_NOQUOTES) .'">';
				$xml .= '<client id="'. $RS["CLIENTID"] .'">'. htmlspecialchars($RS["CLIENT"], ENT_NOQUOTES) .'</client>';
				if ($RS["TYPEID"] == Constants::iPURCHASE_USING_POINTS)
				{
					if ($RS["COUNTRYID"] == 103 || $RS["COUNTRYID"] == 200) { $seperator = ","; }
					else { $seperator = "."; }
					$xml .= '<amount country-id="0" currency="points" symbol="points" format="{PRICE} {CURRENCY}">'. $RS["AMOUNT"] .'</amount>';
					$xml .= '<price>'. number_format($RS["AMOUNT"], 0, "", $seperator) .' points</price>';
					$xml .= '<fee country-id="0" currency="points" symbol="points" format="{PRICE} {CURRENCY}">'. $RS["FEE"] .'</fee>';
				}
				else
				{
					$xml .= '<amount country-id="'. $RS["COUNTRYID"] .'" currency="'. $RS["CURRENCY"] .'" symbol="'. $RS["SYMBOL"] .'" format="'. $RS["PRICEFORMAT"] .'">'. $RS["AMOUNT"] .'</amount>';
					$xml .= '<price>'. General::formatAmount($this->_obj_CountryConfig, abs($RS["AMOUNT"]) ) .'</price>';
					$xml .= '<fee country-id="'. $RS["COUNTRYID"] .'" currency="'. $RS["CURRENCY"] .'" symbol="'. $RS["SYMBOL"] .'" format="'. $RS["PRICEFORMAT"] .'">'. $RS["FEE"] .'</fee>';
				}
				$xml .= '<ip>'. $RS["IP"] .'</ip>';
				$xml .= '<address>'. htmlspecialchars($RS["ADDRESS"], ENT_NOQUOTES) .'</address>';
				$xml .= '<card id="'. $RS["CARDID"] .'">'. htmlspecialchars($RS["CARD"], ENT_NOQUOTES) .'</card>';
				$xml .= '<timestamp>'. gmdate("Y-m-d H:i:sP", $RS["TIMESTAMP"]) .'</timestamp>';
				$xml .= '</transaction>';
			}
		}
		$xml .= '</history>';

		return $xml;
	}
	
	/**
	 * Saves the specified Password for the End-User Account.
	 *
	 * @param	integer $id 	Unqiue ID of the End-User's Account
	 * @param 	string $pwd 	Password for the created End-User Account
	 * @return	boolean
	 */
	public function savePassword($id, $pwd)
	{
		$sql = "UPDATE EndUser.Account_Tbl
				SET passwd = '". $this->getDBConn()->escStr($pwd) ."'
				WHERE id = ". intval($id);
//		echo $sql ."\n";
		
		return is_resource($this->getDBConn()->query($sql) );
	}
	/**
	 * Saves the specified Information for the End-User Account. 
	 *
	 * @param	integer $id 	Unqiue ID of the End-User's Account
	 * @param	string $fn 		End-User's first name
	 * @param	string $ln 		End-User's last name
	 * @return	boolean
	 */
	public function saveInfo($id, $fn, $ln)
	{
		$sql = "UPDATE EndUser.Account_Tbl
				SET firstname = '". $this->getDBConn()->escStr($fn) ."', lastname = '". $this->getDBConn()->escStr($ln) ."'  
				WHERE id = ". intval($id);
//		echo $sql ."\n";
		
		return is_resource($this->getDBConn()->query($sql) );
	}
	
	/**
	 * Constructs the SMTP Headers for the E-Mail Receipt.
	 * The method will return a string in the following format:
	 * 	To: {CUSTOMER'S EMAIL ADDRESS}
	 *	From: mPoint <no-reply@cellpointmobile.com>
	 *	Reply-To: no-reply@cellpointmobile.com
	 *	Content-Type: text/plain
	 *
	 * @return 	string
	 */
	public function constSMTPHeaders()
	{
		// Construct Mail headers
//		$sHeaders = 'To: '. $this->_obj_TxnInfo->getEMail() ."\n";
		$sHeaders = 'From: "mPoint" <no-reply@cellpointmobile.com>' ."\n";
		$sHeaders .= 'Reply-To: no-reply@cellpointmobile.com' ."\n";
		$sHeaders .= 'Content-Type: text/plain' ."\n";

		return $sHeaders;
	}
	
	/**
	 * Creates a new End-User Account.
	 *
	 * @param	integer $cid 	ID of the country the End-User Account should be created in
	 * @param	string $mob 	End-User's mobile number (optional)
	 * @param 	string $pwd 	Password for the created End-User Account (optional)
	 * @param 	string $email	End-User's e-mail address (optional)
	 * @return	integer 		The unique ID of the created End-User Account
	 */
	public function newAccount($cid, $mob="", $pwd="", $email="")
	{
		$sql = "SELECT Nextvalue('EndUser.Account_Tbl_id_seq') AS id FROM DUAL";
		$RS = $this->getDBConn()->getName($sql);
		$sql = "INSERT INTO EndUser.Account_Tbl
					(id, countryid, mobile, passwd, email)
				VALUES
					(". $RS["ID"] .", ". intval($cid) .", ". (floatval($mob) > 0 ? "'". floatval($mob) ."'" : "NULL") .", '". $this->getDBConn()->escStr($pwd) ."', ". (strlen($email) > 0 ? "'". $this->getDBConn()->escStr($email) ."'" : "NULL") .")";
//		echo $sql ."\n";
		$res = $this->getDBConn()->query($sql);

		return $RS["ID"];
	}
	
	/**
	 * Sends the provided SMS Message to GoMobile on behalf of the provided Client.
	 * 
	 * @param 	GoMobileConnInfo $oCI	Reference to the data object with the Connection Info required to communicate with GoMobile
	 * @param 	ClientConfig $oCC		Reference to the data object with the Client Configuration
	 * @param 	SMS $oMI				Reference to the Message Object for holding the message data which will be sent to GoMobile
	 * @return 	integer
	 */
	public function sendMessage(GoMobileConnInfo &$oCI, ClientConfig &$oCC, SMS &$oMI)
	{
		$iCode = -1;
		// Re-Instantiate Connection Information for GoMobile using the Client's username / password
		$oCI = new GoMobileConnInfo($oCI->getProtocol(), $oCI->getHost(), $oCI->getPort(), $oCI->getTimeout(), $oCI->getPath(), $oCI->getMethod(), $oCI->getContentType(), $oCC->getUsername(), $oCC->getPassword(), $oCI->getLogPath(), $oCI->getMode() );

		// Instantiate client object for communicating with GoMobile
		$obj_GoMobile = new GoMobileClient($oCI);

		/* ========== Send MT Start ========== */
		$bSend = true;		// Continue to send messages
		$iAttempts = 0;		// Number of Attempts
		// Send messages
		while ($bSend === true && $iAttempts < 3)
		{
			$iAttempts++;
			try
			{
				$iCode = $obj_GoMobile->communicate($oMI);
				// Error: Message rejected by GoMobile
				if ($iCode == 200){ $bSend = false; }
			}
			// Communication error, retry message sending
			catch (HTTPException $e)
			{
				// Error: Unable to connect to GoMobile
				if ($iAttempts == 3)
				{
					throw new mPointException("Unable to connect to GoMobile", 1013);
				}
				else { sleep(pow(5, $iAttempts) ); }
			}
		}
		/* ========== Send MT End ========== */
		
		return $iCode;
	}
	
	/**
	 * Generates a new activation code for the End-User's Account and inserts it into the database.
	 * The generated activation code is a number between 100000 and 999999
	 *
	 * @param	integer $id 	Unqiue ID of the End-User's Account
	 * @param	string $addr 	End-User's mobile number or E-Mail address
	 * @param	timestamp $exp 	Timestamp indicating when the generated activation code should expire in the format: YYYY-MM-DD hh:mm:ss, set to null for default (24 hours from "now")
	 * @return 	integer
	 * 
	 * @throws	mPointException
	 */
	protected function genActivationCode($id, $addr, $exp=null)
	{
		$iCode = mt_rand(100000, 999999);
		// Insert generated activation code in the database
		$sql = "INSERT INTO EndUser.Activation_Tbl
					(accountid, address, code". (is_null($exp) == false ? ", expiry" : "") .")
				VALUES
					(". intval($id) .", '". $this->getDBConn()->escStr($addr) ."', ". $iCode . (is_null($exp) == false ? ", '". $this->getDBConn()->escStr($exp) ."'" : "") .")";
//		echo $sql ."\n";
		
		if (is_resource($this->getDBConn()->query($sql) ) === false)
		{
			throw new mPointException("Failed to Insert activation code: ". $iCode ." into Database", 1101);
		}
		
		return $iCode;
	}
	
	/**
	 * Activates the provided code.
	 * The method will return the following status codes:
	 * 	 1. Activation Code not found for Account
	 * 	 2. Activation Code expired
	 * 	 3. Activation Code already consumed
	 * 	 4. Activation Code disabled
	 * 	 5. Unable to consume Activation Code
	 * 	10. Success
	 *
	 * @param	integer $id 	Unqiue ID of the End-User's Account
	 * @param	integer $code 	Code which should be activated
	 * @return	integer
	 */
	public function activateCode($id, $code)
	{
		$sql = "SELECT id, enabled, active, extract('epoch' from expiry) AS expiry
				FROM EndUser.Activation_Tbl
				WHERE accountid = ". intval($id) ." AND code = ". intval($code);
//		echo $sql ."\n";
		$RS = $this->getDBConn()->getName($sql);

		if (is_array($RS) === false) { $iStatus = 1; }		// Activation Code not found for Account
		elseif ($RS["EXPIRY"] < time() ) { $iStatus = 2; }	// Activation Code expired
		elseif ($RS["ACTIVE"] === true) { $iStatus = 3; }	// Activation Code already consumed
		elseif ($RS["ENABLED"] === false) { $iStatus = 4; }	// Activation Code disabled
		else
		{
			$sql = "UPDATE EndUser.Activation_Tbl
					SET active = '1'
					WHERE id = ". $RS["ID"];
//			echo $sql ."\n";
			if (is_resource($this->getDBConn()->query($sql) ) === true)
			{
				$iStatus = 10; 
			}
			else { $iStatus = 5; }
		}
			
		return $iStatus;
	}
	public function verifyMobile($id)
	{
		$sql = "UPDATE EndUser.Account_Tbl
				SET mobile_verified = true
				WHERE id = ". intval($id);
//		echo $sql ."\n";

		if (is_resource($this->getDBConn()->query($sql) ) === true)
		{
			$sql = "UPDATE EndUser.Transaction_Tbl
					SET stateid = ". Constants::iTRANSACTION_COMPLETED_STATE ."
					WHERE toid = ". intval($id) ." AND stateid = ". Constants::iTRANSFER_PENDING_STATE;
//			echo $sql ."\n";
			if (is_resource($this->getDBConn()->query($sql) ) === true)
			{
				$code = 10;
			}
			else { $code = 2; }
		}
		else { $code = 1; }
		
		return $code;
	}
}
?>