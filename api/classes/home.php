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
	public function getAccountID(CountryConfig &$oCC, $addr)
	{
		if (floatval($addr) > $oCC->getMinMobile() ) { $sql = "mobile = '". floatval($addr) ."'"; }
		else { $sql = "Upper(email) = Upper('". $this->getDBConn()->escStr($addr) ."')"; }

		$sql = "SELECT DISTINCT EUA.id
				FROM EndUser.Account_Tbl EUA
				WHERE EUA.countryid = ". $oCC->getID() ."
					AND ". $sql ." AND EUA.enabled = true";
//		echo $sql ."\n";
		$RS = $this->getDBConn()->getName($sql);

		return is_array($RS)===true?$RS["ID"]:-1;
	}

	/**
	 * Authenticates the End-User using the provided address (MSISDN or E-Mail) and Password.
	 * The method will return the following status codes:
	 * 	 1. Account / Password doesn't match
	 * 	10. Success
	 *
	 * @param	integer $id 	Unqiue ID of the End-User's Account
	 * @param	string $pwd 	Password provided by the End-User
	 * @return	integer
	 */
	public function auth($id, $pwd)
	{
		$sql = "SELECT id
				FROM EndUser.Account_Tbl
				WHERE id = ". intval($id) ." AND countryid = ". $this->_obj_CountryConfig->getID() ."
					AND passwd = '". $this->getDBConn()->escStr($pwd) ."' AND enabled = true";
//		echo $sql ."\n";
		$RS = $this->getDBConn()->getName($sql);

		if (is_array($RS) === true)
		{
			$code = 10;
		}
		else { $code = 1; }

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
	 *		<balance currency="{CURRENCY BALANCE IS REPRESENTED IN}" symbol="{SYMBOL USED TO REPRESENT THE CURRENCY}">{PRE-PAID BALANCE AVAILABLE ON THE END-USER ACCOUNT IN COUNTRY'S SMALLEST CURRENCY}</balance>
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
		$sql = "SELECT id, countryid, firstname, lastname, mobile, email, passwd AS password, balance, date_trunc('second', created) AS created, Extract('epoch' from created) AS timestamp
				FROM EndUser.Account_Tbl
				WHERE id = ". intval($id) ." AND enabled = true";
//		echo $sql ."\n";
		$RS = $this->getDBConn()->getName($sql);

		// Construct XML Document with account information
		$xml = '<account id="'. $RS["ID"] .'" countryid="'. $RS["COUNTRYID"] .'">';
		$xml .= '<firstname>'. htmlspecialchars($RS["FIRSTNAME"], ENT_NOQUOTES) .'</firstname>';
		$xml .= '<lastname>'. htmlspecialchars($RS["LASTNAME"], ENT_NOQUOTES) .'</lastname>';
		$xml .= '<mobile>'. $RS["MOBILE"] .'</mobile>';
		$xml .= '<email>'. htmlspecialchars($RS["EMAIL"], ENT_NOQUOTES) .'</email>';
		$xml .= '<password mask="'. str_repeat("*", strlen($RS["PASSWORD"]) ) .'">'. htmlspecialchars($RS["PASSWORD"], ENT_NOQUOTES) .'</password>';
		$xml .= '<balance currency="'. $this->_obj_CountryConfig->getCurrency() .'" symbol="'. $this->_obj_CountryConfig->getSymbol() .'">'. $RS["BALANCE"] .'</balance>';
		$xml .= '<funds>'. General::formatAmount($this->_obj_CountryConfig, $RS["BALANCE"]) .'</funds>';
		$xml .= '<created timestamp="'. $RS["TIMESTAMP"] .'">'. $RS["CREATED"] .'</created>';
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
	 * 		<card id="{UNIQUE ID FOR THE SAVED CARD}" type="{UNIQUE ID FOR THE CARD TYPE}" pspid="{UNIQUE ID FOR THE PSP THE CARD WAS SAVED THROUGH}" preferred="{BOOLEAN FLAG INDICATING WHETHER THE SAVED CARD IS THE END-USER'S PREFERRED}">
	 *			<mask>{MASKED CARD NUMBER IN THE FORMAT: [CARD PREFIX]** **** [LAST 4 DIGITS]}</mask>
	 *			<expiry>{CARD EXPIRY DATE IN THE FORMAT: MM/YY}</expiry>
	 *			<ticket>{TICKET ID REPRESENTING THE STORED CARD WITH THE PSP}</ticket>
	 *			<logo-width>{CALCULATED WIDTH OF THE CARD LOGO}</logo-width>
	 *			<logo-height>{CALCULATED HEIGHT OF THE CARD LOGO}</logo-height>
	 *		</card>
	 *		<card id="{UNIQUE ID FOR THE SAVED CARD}" type="{UNIQUE ID FOR THE CARD TYPE}" pspid="{UNIQUE ID FOR THE PSP THE CARD WAS SAVED THROUGH}" preferred="{BOOLEAN FLAG INDICATING WHETHER THE SAVED CARD IS THE END-USER'S PREFERRED}">
	 *			<mask>{MASKED CARD NUMBER IN THE FORMAT: [CARD PREFIX]** **** [LAST 4 DIGITS]}</mask>
	 *			<expiry>{CARD EXPIRY DATE IN THE FORMAT: MM/YY}</expiry>
	 *			<ticket>{TICKET ID REPRESENTING THE STORED CARD WITH THE PSP}</ticket>
	 *			<logo-width>{CALCULATED WIDTH OF THE CARD LOGO}</logo-width>
	 *			<logo-height>{CALCULATED HEIGHT OF THE CARD LOGO}</logo-height>
	 *		</card>
	 *		...
	 * 	</account>
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
		$sql = "SELECT EUC.id, EUC.pspid, EUC.mask, EUC.expiry, EUC.ticket, EUC.preferred, SC.id AS typeid, SC.name AS type
				FROM EndUser.Card_Tbl EUC
				INNER JOIN System.PSP_Tbl PSP ON EUC.pspid = PSP.id AND PSP.enabled = true
				INNER JOIN System.Card_Tbl SC ON EUC.cardid = SC.id AND SC.enabled = true
				WHERE EUC.accountid = ". intval($id) ." AND EUC.enabled = true
					AND (substr(EUC.expiry, 4, 2) || substr(EUC.expiry, 1, 2) ) >= '". date("ym") ."'";
//		echo $sql ."\n";
		$res = $this->getDBConn()->query($sql);

		$xml = '<stored-cards accountid="'. $id .'">';
		while ($RS = $this->getDBConn()->fetchName($res) )
		{
			// Construct XML Document with data for saved cards
			$xml .= '<card id="'. $RS["ID"] .'" pspid="'. $RS["PSPID"] .'" preferred="'. General::bool2xml($RS["PREFERRED"]) .'">';
			$xml .= '<type id="'. $RS["TYPEID"] .'">'. $RS["TYPE"] .'</type>';
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
	 * Fetches the Credit Cards that have been stored for the specific End-User Account.
	 * If a User Agent Profile is provided, the method will automatically calculate the width and height of the account logo
	 * after it has been resized to fit the screen resolution of the end-user's mobile device.
	 *
	 * The card data is returned as an XML Document in the following format:
	 * 	<stored-cards accountid="{UNIQUE ID FOR THE END-USER ACCOUNT}">
	 * 		<card id="{UNIQUE ID FOR THE SAVED CARD}" type="{UNIQUE ID FOR THE CARD TYPE}" pspid="{UNIQUE ID FOR THE PSP THE CARD WAS SAVED THROUGH}" preferred="{BOOLEAN FLAG INDICATING WHETHER THE SAVED CARD IS THE END-USER'S PREFERRED}">
	 *			<mask>{MASKED CARD NUMBER IN THE FORMAT: [CARD PREFIX]** **** [LAST 4 DIGITS]}</mask>
	 *			<expiry>{CARD EXPIRY DATE IN THE FORMAT: MM/YY}</expiry>
	 *			<ticket>{TICKET ID REPRESENTING THE STORED CARD WITH THE PSP}</ticket>
	 *			<logo-width>{CALCULATED WIDTH OF THE CARD LOGO}</logo-width>
	 *			<logo-height>{CALCULATED HEIGHT OF THE CARD LOGO}</logo-height>
	 *		</card>
	 *		<card id="{UNIQUE ID FOR THE SAVED CARD}" type="{UNIQUE ID FOR THE CARD TYPE}" pspid="{UNIQUE ID FOR THE PSP THE CARD WAS SAVED THROUGH}" preferred="{BOOLEAN FLAG INDICATING WHETHER THE SAVED CARD IS THE END-USER'S PREFERRED}">
	 *			<mask>{MASKED CARD NUMBER IN THE FORMAT: [CARD PREFIX]** **** [LAST 4 DIGITS]}</mask>
	 *			<expiry>{CARD EXPIRY DATE IN THE FORMAT: MM/YY}</expiry>
	 *			<ticket>{TICKET ID REPRESENTING THE STORED CARD WITH THE PSP}</ticket>
	 *			<logo-width>{CALCULATED WIDTH OF THE CARD LOGO}</logo-width>
	 *			<logo-height>{CALCULATED HEIGHT OF THE CARD LOGO}</logo-height>
	 *		</card>
	 *		...
	 * 	</account>
	 *
	 * @param	integer $id 	Unqiue ID of the End-User's Account
	 * @return 	string
	 */
	public function getTxnHistory($id)
	{
		// Fetch Transaction history for End-User
		$sql = "SELECT EUT.id, EUT.typeid, EUT.toid, EUT.fromid, Extract('epoch' from EUT.created) AS timestamp,
					(CASE
					 WHEN EUT.amount = 0 THEN Txn.amount
					 WHEN EUT.amount IS NULL THEN Txn.amount
					 ELSE abs(EUT.amount)
					 END) AS amount,
					C.currency,
					CL.id AS clientid, CL.name AS client,
					(EUAT.firstname || ' ' || EUAT.lastname) AS to_name, EUAT.mobile AS to_mobile, EUAT.email AS to_email,
					(EUAF.firstname || ' ' || EUAF.lastname) AS from_name, EUAF.mobile AS from_mobile, EUAF.email AS from_email,
					Txn.id AS mpointid, Txn.orderid, Txn.cardid, Card.name AS card
				FROM EndUser.Transaction_Tbl EUT
				LEFT OUTER JOIN EndUser.Account_Tbl EUAT ON EUT.toid = EUAT.id
				LEFT OUTER JOIN EndUser.Account_Tbl EUAF ON EUT.fromid = EUAF.id
				LEFT OUTER JOIN Log.Transaction_Tbl Txn ON EUT.txnid = Txn.id
				LEFT OUTER JOIN Client.Client_Tbl CL ON Txn.clientid = CL.id
				LEFT OUTER JOIN System.Country_Tbl C ON Txn.countryid = C.id
				LEFT OUTER JOIN System.Card_Tbl Card ON Txn.cardid = Card.id
				WHERE EUT.accountid = ". intval($id) ."
				ORDER BY EUT.id ASC";
//		echo $sql ."\n";
		$res = $this->getDBConn()->query($sql);

		$xml = '<history accountid="'. $id .'">';
		// Construct XML Document with data for Transaction
		while ($RS = $this->getDBConn()->fetchName($res) )
		{
			// E-Money Top-Up
			if ($RS["TYPEID"] == Constants::iEMONEY_TOPUP_TYPE)
			{
				$xml .= '<transaction id="'. $RS["ID"] .'"  type="'. $RS["TYPEID"] .'" mpointid="'. $RS["MPOINTID"] .'">';
				$xml .= '<amount currency="'. trim($RS["CURRENCY"]) .'">'. $RS["AMOUNT"] .'</amount>';
				$xml .= '<price>'. General::formatAmount($this->_obj_CountryConfig, $RS["AMOUNT"]) .'</price>';
				$xml .= '<timestamp>'. date("Y-m-d H:i:s", $RS["TIMESTAMP"]) .'</timestamp>';
				$xml .= '</transaction>';
			}
			// E-Money Transfer
			elseif ($RS["TYPEID"] == Constants::iEMONEY_TRANSFER_TYPE)
			{
				$xml .= '<transaction id="'. $RS["ID"] .'"  type="'. $RS["TYPEID"] .'">';
				$xml .= '<amount currency="'. trim($RS["CURRENCY"]) .'">'. $RS["AMOUNT"] .'</amount>';
				$xml .= '<price>'. General::formatAmount($this->_obj_CountryConfig, $RS["AMOUNT"]) .'</price>';
				$xml .= '<from accountid="'. $RS["FROMID"] .'">';
				$xml .= '<name>'. htmlspecialchars($RS["FROM_NAME"], ENT_NOQUOTES) .'</name>';
				$xml .= '<mobile>'. $RS["FROM_MOBILE"] .'</mobile>';
				$xml .= '<email>'. htmlspecialchars($RS["FROM_EMAIL"], ENT_NOQUOTES) .'</email>';
				$xml .= '</from>';
				$xml .= '<to accountid="'. $RS["TOID"] .'">';
				$xml .= '<name>'. htmlspecialchars($RS["TO_NAME"], ENT_NOQUOTES) .'</name>';
				$xml .= '<mobile>'. $RS["TO_MOBILE"] .'</mobile>';
				$xml .= '<email>'. htmlspecialchars($RS["TO_EMAIL"], ENT_NOQUOTES) .'</email>';
				$xml .= '</to>';
				$xml .= '<timestamp>'. date("Y-m-d H:i:s", $RS["TIMESTAMP"]) .'</timestamp>';
				$xml .= '</transaction>';
			}
			// E-Money Purchase or Card / Premium SMS based purchase associated with the End-User account
			else
			{
				$xml .= '<transaction id="'. $RS["ID"] .'" mpointid="'. $RS["MPOINTID"] .'" type="'. $RS["TYPEID"] .'">';
				$xml .= '<client id="'. $RS["CLIENTID"] .'">'. htmlspecialchars($RS["CLIENT"], ENT_NOQUOTES) .'</client>';
				$xml .= '<orderid>'. $RS["ORDERID"] .'</orderid>';
				$xml .= '<amount currency="'. trim($RS["CURRENCY"]) .'">'. $RS["AMOUNT"] .'</amount>';
				$xml .= '<price>'. General::formatAmount($this->_obj_CountryConfig, abs($RS["AMOUNT"]) ) .'</price>';
				$xml .= '<card id="'. $RS["CARDID"] .'">'. htmlspecialchars($RS["CARD"], ENT_NOQUOTES) .'</card>';
				$xml .= '<timestamp>'. date("Y-m-d H:i:s", $RS["TIMESTAMP"]) .'</timestamp>';
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
	 * Constructs the SMTP Headers for the E-Mail Receipt.
	 * The method will return a string in the following format:
	 * 	To: {CUSTOMER'S EMAIL ADDRESS}
	 *	From: mPoint <no-reply@cellpointmobile.com>
	 *	Reply-To: no-reply@cellpointmobile.com
	 *	Content-Type: text/plain
	 *
	 * @return 	string
	 */
	public function constHeaders()
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
	 * @param	string $mob 	End-User's mobile number
	 * @param 	string $pwd 	Password for the created End-User Account (optional)
	 * @param 	string $email	End-User's e-mail address (optional)
	 * @return	integer 		The unique ID of the created End-User Account
	 */
	public function newAccount($cid, $mob, $pwd="", $email="")
	{
		$sql = "SELECT Nextval('EndUser.Account_Tbl_id_seq') AS id";
		$RS = $this->getDBConn()->getName($sql);
		$sql = "INSERT INTO EndUser.Account_Tbl
					(id, countryid, mobile, passwd, email)
				VALUES
					(". $RS["ID"] .", ". intval($cid) .", '". floatval($mob) ."', '". $this->getDBConn()->escStr($pwd) ."', '". $this->getDBConn()->escStr($email) ."')";
//		echo $sql ."\n";
		$res = $this->getDBConn()->query($sql);

		return $RS["ID"];
	}
	
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
}
?>