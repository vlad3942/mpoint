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
	protected $_obj_CountryConfig;

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

		$obj_ClientConfig = ClientConfig::produceConfig($this->getDBConn(), $this->getClientConfig()->getId(), -1);

		$obj_MsgInfo = GoMobileMessage::produceMessage(Constants::iMT_SMS_TYPE, $oCC->getID(), $oCC->getID()*100, $oCC->getChannel(), $obj_ClientConfig->getKeywordConfig()->getKeyword(), Constants::iMT_PRICE, $mob, utf8_decode($sBody) );
		$obj_MsgInfo->setDescription("mPoint - OTP");

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
	public function getAccountID()
	{
        $aArgs = func_get_args();
        $oCC = $aArgs[0];
        $addr = $aArgs[1];
        $clid =  $aArgs[2];
		if (floatval($addr) > $oCC->getMinMobile() ) { $sql = "A.mobile = '". floatval($addr) ."'"; }
		else { $sql = "Upper(A.email) = Upper('". $this->getDBConn()->escStr($addr) ."')"; }

		$sql = "SELECT DISTINCT A.id
				FROM EndUser".sSCHEMA_POSTFIX.".Account_Tbl A
				LEFT OUTER JOIN EndUser".sSCHEMA_POSTFIX.".CLAccess_Tbl Acc ON A.id = Acc.accountid
				WHERE A.countryid = ". $oCC->getID() ."
					AND ". $sql ." AND A.enabled = '1'";
		if ($clid > 0) { $sql ." AND Acc.clientid = ". intval($clid); }
		$sql .= "
				ORDER BY id DESC
				LIMIT 1";
//		echo $sql ."\n";
		$RS = $this->getDBConn()->getName($sql);

		return is_array($RS) === true ? $RS["ID"] : -1;
	}

	public function auth()
	{
		$aArgs = func_get_args();
		switch (count($aArgs) )
		{
		case (2):
			return $this->_authInternal($aArgs[0], $aArgs[1]);
			break;
		case (3):
			if ( ($aArgs[0] instanceof ClientConfig) === true)
			{
				return $this->_authExternal($aArgs[0], $aArgs[1], $aArgs[2]);
			}
			else { return $this->_authInternal($aArgs[0], $aArgs[1], $aArgs[2]); }
			break;
		case (4):
			if ( ($aArgs[0] instanceof ClientConfig) === true)
			{
				return $this->_authExternal($aArgs[0], $aArgs[1], $aArgs[2], $aArgs[3]);
			}
			else { return $this->_authInternal($aArgs[0], $aArgs[1], $aArgs[2]); }
			break;
		case (5):		

			if ( strlen($aArgs[1]->getCustomerRef()) > 0  ||  floatval($aArgs[1]->getMobile()) > 0 || strlen($aArgs[1]->getEMail()) > 0 || $aArgs[1]->getProfileID() !== '' )
	        {   
	        	if ( ($aArgs[0] instanceof ClientConfig) === true)
				{
					return $this->_authExternal($aArgs[0], $aArgs[1], $aArgs[2], $aArgs[3]);
				}
				else { return $this->_authInternal($aArgs[0], $aArgs[1], $aArgs[2]); }
	        } 
	        else {
	        	return 212 ; 
	        }
			
			break;
		default:
			break;
		}
	}
	/**
	 * Authenticates the End-User using the provided Account ID and Password.
	 * The method will return the following status codes:
	 * 	 1. Account ID / Password doesn't match
	 * 	 2. Account ID / Password doesn't match - Next invalid login will disable the account
	 * 	 3. Account ID / Password doesn't match - Account has been disabled
	 * 	 4. Account ID / Password doesn't match - Next invalid login will disable the account + And there is an active payment transaction
	 * 	 5. Account not found
	 * 	 9. Account disabled
	 * 	10. Login successful
	 * 	11. Login successful - Mobile Number not verified
	 *
	 * @see		Constants::iMAX_LOGIN_ATTEMPTS
	 *
	 * @param	integer $id 	Unqiue ID of the End-User's Account
	 * @param	string $pwd 	Password provided by the End-User
	 * @return	integer
	 */
	private function _authInternal($id, $pwd, $disable=true)
	{
		$sql = "SELECT id, attempts, passwd AS password, mobile, enabled, mobile_verified
				FROM EndUser".sSCHEMA_POSTFIX.".Account_Tbl
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
			elseif ($RS["PASSWORD"] == $pwd || $RS["PASSWORD"] == sha1($pwd))
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
				$sql1 = "SELECT EUA.id, MAX(Cli.transaction_ttl) ttl
				 		 FROM EndUser".sSCHEMA_POSTFIX.".Account_Tbl EUA
				  		 LEFT JOIN EndUser".sSCHEMA_POSTFIX.".CLAccess_Tbl Cla ON Cla.accountid = EUA.id AND Cla.enabled = '1'
				  		 LEFT JOIN Client".sSCHEMA_POSTFIX.".Client_Tbl Cli ON Cli.id = Cla.clientid AND Cli.enabled = '1'
						 WHERE EUA.id = ". intval($id) ."
						 GROUP BY EUA.id";
//		echo $sql1 ."\n";

				$res1 = $this->getDBConn()->query($sql1);

				if (is_resource($res1) === true )
				{
					$RS1 = $this->getDBConn()->fetchName($res1);
					if (is_array($RS) === true)
					{
						$iTTL = intval($RS1["TTL"]);
						if ($iTTL > 0)
						{
							$obj_Status = new Status($this->getDBConn(), $this->getText() );
							$iTo = time();
							$iFrom = $iTo-$iTTL;
							$aTxns = array();
							try
							{
								$aTxns = $obj_Status->getActiveTransactions($iFrom, $iTo, $id);
							} catch (mPointException $e) { trigger_error("An error occurred while trying to check the DB for active payment transactions", E_USER_WARNING); }

							// There is one or more active transactions
							if (count($aTxns) > 0) { $code = 4; }
						}
					}
				}

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
				$sql = "UPDATE EndUser".sSCHEMA_POSTFIX.".Account_Tbl
						SET attempts = ". $iAttempts .", enabled = '". ($bEnabled === true ? "1" : "0") ."'
						WHERE id = ". intval($id);
//				echo $sql ."\n";
				$this->getDBConn()->query($sql);
			}
		}
		// Account not found
		else { $code = 5; }

		return $code;
	}
	private function _authExternal(ClientConfig $obj_ClientConfig, CustomerInfo $obj_CustomerInfo, $pwd, $clientId=-1)
	{
        $oCI = HTTPConnInfo::produceConnInfo($obj_ClientConfig->getAuthenticationURL());
        $obj_ConnInfo = new HTTPConnInfo($oCI->getProtocol(), $oCI->getHost(), $oCI->getPort(), $oCI->getTimeout(), $oCI->getPath(), "POST", "text/xml", $obj_ClientConfig->getUsername(), $obj_ClientConfig->getPassword() );

		$b = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<login>';
		 if($clientId > 0)
		 	$b .= '<client-id>'.$clientId.'</client-id>' ;
		$b .= $obj_CustomerInfo->toXML();
		$b .= '<password>'. htmlspecialchars($pwd, ENT_NOQUOTES) .'</password>';
		$b .= '</login>';
		$b .= '</root>';

		try
		{
			$obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
			$obj_HTTP->connect();
			$code = $obj_HTTP->send($this->constHTTPHeaders(), $b);
			$obj_HTTP->disConnect();
			if ($code == 200)
			{
				trigger_error("Authorization accepted by Authentication Service at: ". $oCI->toURL() ." with HTTP Code: ". $code, E_USER_NOTICE);
                $obj_XML = simplexml_load_string($obj_HTTP->getReplyBody() );
                if(isset($obj_XML->profile_type)) {
                    $profile_type_id = (integer)$obj_XML->profile_type;
                    $obj_CustomerInfo->setProfileTypeID($profile_type_id);
                }
				return 10;
			}
			else
			{
				trigger_error("Authentication Service at: ". $oCI->toURL() ." rejected authorization with HTTP Code: ". $code, E_USER_NOTICE);
				return 1;
			}
		}
		catch (HTTPException $e)
		{
			trigger_error("Authentication Service at: ". $oCI->toURL() ." is unavailable due to ". get_class($e), E_USER_NOTICE);
			return 6;
		}
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
		$sql = "SELECT id, countryid, firstname, lastname, mobile, email, passwd AS password, balance, points, created,
					mobile_verified
				FROM EndUser".sSCHEMA_POSTFIX.".Account_Tbl
				WHERE id = ". intval($id) ." AND enabled = '1'";
//		echo $sql ."\n";
		$RS = $this->getDBConn()->getName($sql);

		$sql = "SELECT CL.id, CL.store_card, CL.name
				FROM EndUser".sSCHEMA_POSTFIX.".CLAccess_Tbl Acc
				INNER JOIN Client".sSCHEMA_POSTFIX.".Client_Tbl CL ON Acc.clientid = CL.id AND CL.enabled = '1'
				WHERE Acc.accountid = ". intval($id);
//		echo $sql ."\n";
		$aRS = $this->getDBConn()->getAllNames($sql);

		$ts = strtotime(substr($RS["CREATED"], 0, strpos($RS["CREATED"], ".") ) );

		// Construct XML Document with account information
		$xml = '<account id="'. $RS["ID"] .'" country-id="'. $RS["COUNTRYID"] .'">';
		$xml .= '<first-name>'. htmlspecialchars($RS["FIRSTNAME"], ENT_NOQUOTES) .'</first-name>';
		$xml .= '<last-name>'. htmlspecialchars($RS["LASTNAME"], ENT_NOQUOTES) .'</last-name>';
		$xml .= '<mobile country-id="'. $RS["COUNTRYID"] .'" verified="'. General::bool2xml($RS["MOBILE_VERIFIED"]) .'">'. $RS["MOBILE"] .'</mobile>';
		$xml .= '<email>'. htmlspecialchars($RS["EMAIL"], ENT_NOQUOTES) .'</email>';
		$xml .= '<password mask="'. str_repeat("*", strlen($RS["PASSWORD"]) ) .'">'. htmlspecialchars($RS["PASSWORD"], ENT_NOQUOTES) .'</password>';
		$xml .= '<balance country-id="'. $this->_obj_CountryConfig->getID() .'" currency="'. $this->_obj_CountryConfig->getCurrency() .'" symbol="'. utf8_encode($this->_obj_CountryConfig->getSymbol() ) .'" format="'. $this->_obj_CountryConfig->getPriceFormat().'">'. intval($RS["BALANCE"]) .'</balance>';
		$xml .= '<funds>'. General::formatAmount($this->_obj_CountryConfig, $RS["BALANCE"]) .'</funds>';
		$xml .= '<points country-id="0" currency="points" symbol="points" format="{PRICE} {CURRENCY}">'. $RS["POINTS"] .'</points>';
		$xml .= '<clients>';
		if(is_array($aRS) === TRUE) {
            for ($i = 0, $iMax = count($aRS); $i < $iMax; $i++) {
                $xml .= '<client id="' . $aRS[$i]["ID"] . '" store-card="' . $aRS[$i]["STORE_CARD"] . '">' . htmlspecialchars($aRS[$i]["NAME"], ENT_NOQUOTES) . '</client>';
            }
        }
		$xml .= '</clients>';
		$xml .= '<created timestamp="'. $ts .'">'. gmdate("Y-m-d H:i:sP", $ts) .'</created>';
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
	 * @param 	boolean $adc 	Include Stored Cards where the card type has been disabled, defaults to false
	 * @param 	UAProfile $oUA 	Reference to the User Agent Profile for the Customer's Mobile Device (optional)
     * @param   array   $aPaymentMethods
     * @param   integer $countryId
	 * @return 	string
	 */
	public function getStoredCards($id, ClientConfig &$oCC=null, $adc=false, &$oUA=null, $aPaymentMethods = array(), $countryId = null, $is_legacy='true')
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

        if(empty($aPaymentMethods) === false) {
            $aPaymentMethodsConfig = array();
            foreach ($aPaymentMethods as $paymentMethod) {
                $aPaymentMethodsConfig[$paymentMethod->id] = array(
                    'state_id' => $paymentMethod->state_id,
                    'preference' => $paymentMethod->preference);
            }
        }

        $sql = $this->getCardQuery($id, $oCC, $adc, $aPaymentMethods, $countryId, $is_legacy);

        $result = $this->getDBConn()->getAllNames($sql);

		$xml = '<stored-cards accountid="'. $id .'">';
        if (is_array($result) === true && count($result) > 0) {
            foreach ($result as $RS) {
                // Set stateid given by CRS into resultset
                if (empty($aPaymentMethodsConfig) === FALSE) {
                    $aCardConfig = isset($aPaymentMethodsConfig[$RS['CARDID']]) ? $aPaymentMethodsConfig[$RS['CARDID']] : NULL;
                    $RS['STATEID'] = isset($aCardConfig['state_id']) ? $aCardConfig['state_id'] : 1;
                }

                if (($oCC instanceof ClientConfig) === true) {
                    // Replace up 0-4 of the last 4-digits in the masked card number with *
                    $sMaskedCardNumber = substr_replace(trim($RS["MASK"]), str_repeat("*", 4 - $oCC->getNumberOfMaskedDigits()), -4, 4 - $oCC->getNumberOfMaskedDigits());
                } else {
                    $sMaskedCardNumber = trim($RS["MASK"]);
                }

                // set card expired status
                $aExpiry = explode('/', $RS['EXPIRY']);
                $bIsExpired = "false";
                $expiryMonth = trim($aExpiry[0]);
                $expiryYear = trim($aExpiry[1]);
                if (empty($expiryMonth) === false && empty($expiryYear) === false && $this->_cardNotExpired(intval($expiryMonth), intval($expiryYear)) === false) {
                    $bIsExpired = "true";
                }

                // Construct XML Document with data for saved cards
                $xml .= '<card id="' . $RS["ID"] . '" type-id="' . $RS["CARDID"] . '" pspid="' . $RS["PSPID"] . '" preferred="' . General::bool2xml($RS["PREFERRED"]) . '" state-id="' . $RS["STATEID"] . '" charge-type-id="' . $RS["CHARGETYPEID"] . '" cvc-length="' . $RS["CVCLENGTH"] . '" expired="' . $bIsExpired . '" cvcmandatory = "' . General::bool2xml($RS['CVCMANDATORY']) . '" dcc = "' . General::bool2xml($RS["DCCENABLED"]) . '">';
                $xml .= '<client id="' . $RS["CLIENTID"] . '">' . htmlspecialchars($RS["CLIENT"], ENT_NOQUOTES) . '</client>';
                $xml .= '<type id="' . $RS["TYPEID"] . '">' . $RS["TYPE"] . '</type>';
                $xml .= '<name>' . htmlspecialchars($RS["NAME"], ENT_NOQUOTES) . '</name>';
                $xml .= '<mask>' . chunk_split($sMaskedCardNumber, 4, " ") . '</mask>';
                $xml .= '<expiry>' . $RS["EXPIRY"] . '</expiry>';
                $xml .= '<enabled>' . General::bool2xml($RS["PREFERRED"]) . '</enabled>';
                $xml .= '<ticket>' . $RS["TICKET"] . '</ticket>';
                $xml .= '<card-holder-name>' . htmlspecialchars($RS["CARD_HOLDER_NAME"], ENT_NOQUOTES) . '</card-holder-name>';
                $xml .= '<logo-width>' . $iWidth . '</logo-width>';
                $xml .= '<logo-height>' . $iHeight . '</logo-height>';

                if (intval($RS["COUNTRYID"]) > 0) {
                    $xml .= '<address country-id="' . $RS["COUNTRYID"] . '">';
                    $xml .= '<first-name>' . htmlspecialchars($RS["FIRSTNAME"], ENT_NOQUOTES) . '</first-name>';
                    $xml .= '<last-name>' . htmlspecialchars($RS["LASTNAME"], ENT_NOQUOTES) . '</last-name>';
                    $xml .= '<street>' . htmlspecialchars($RS["STREET"], ENT_NOQUOTES) . '</street>';
                    $xml .= '<postal-code>' . $RS["POSTALCODE"] . '</postal-code>';
                    $xml .= '<city>' . htmlspecialchars($RS["CITY"], ENT_NOQUOTES) . '</city>';
                    if ((empty($RS["CODE"]) === false && $RS["CODE"] != "N/A") || empty($RS["STATE"]) === false) {
                        $xml .= '<state code="' . htmlspecialchars($RS["CODE"], ENT_NOQUOTES) . '">' . htmlspecialchars($RS["STATE"], ENT_NOQUOTES) . '</state>';
                    }
                    $xml .= '</address>';
                }
                $xml .= '</card>';
            }
        }
		$xml .= '</stored-cards>';
		return $xml;
	}

    /**
     * Used to get list of all stored card for the particular cleint
     *
     * @param integer $id             Unqiue ID of the End-User's Account
     * @param $ClientConfig oCC       Hold object of ClientConfig class
     * @param boolean $adc            Include Stored Cards where the card type has been disabled, defaults to false
     * @param array $aPaymentMethods  Holds list of payment methods given by CRS
     * @param integer $countryId      Hold unqiue ID cof the country
     * @param string $is_legacy       Hold a flag which will deside whether to use legacy flow or not
     * @return string
     */
	private function getCardQuery($id, $oCC, $adc, $aPaymentMethods, $countryId, $is_legacy)
    {
        $sql = '';
        $aCardId = array_map(function ($paymentMethod) { return $paymentMethod->id; }, $aPaymentMethods);
        if(strtolower($is_legacy) == 'false')
        {
            $sql = "SELECT DISTINCT ON (EUC.id, EUC.cardid, EUC.pspid, EUC.mask, EUC.expiry, EUC.ticket) EUC.id, EUC.cardid, EUC.pspid, EUC.mask, EUC.expiry, EUC.ticket, EUC.preferred, EUC.name, EUC.enabled, EUC.card_holder_name, EUC.chargetypeid,
					SC.id AS typeid, SC.name AS type, SC.cvclength AS cvclength,
					CL.id AS clientid, CL.name AS client,
					EUAD.countryid, EUAD.firstname, EUAD.lastname,
					EUAD.company, EUAD.street,
					EUAD.postalcode, EUAD.city,
                    true  AS cvcmandatory,
					true As dccenabled
				FROM EndUser".sSCHEMA_POSTFIX.".Card_Tbl EUC
				INNER JOIN System".sSCHEMA_POSTFIX.".PSP_Tbl PSP ON EUC.pspid = PSP.id AND PSP.enabled = '1'
				INNER JOIN System".sSCHEMA_POSTFIX.".Card_Tbl SC ON EUC.cardid = SC.id AND SC.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".Client_Tbl CL ON EUC.clientid = CL.id AND CL.enabled = '1'
				INNER JOIN EndUser".sSCHEMA_POSTFIX.".Account_Tbl EUA ON EUC.accountid = EUA.id AND EUA.enabled = '1'
				LEFT OUTER JOIN EndUser".sSCHEMA_POSTFIX.".Address_Tbl EUAD ON EUC.id = EUAD.cardid and EUA.enabled ='1'
				LEFT OUTER JOIN EndUser".sSCHEMA_POSTFIX.".CLAccess_Tbl CLA ON EUA.id = CLA.accountid
				WHERE EUC.accountid = ". (int)$id;

            if(empty($aPaymentMethods) === false){ $sql .= " AND SC.id IN (". implode(',', $aCardId).")"; }
            if ($oCC->showAllCards() === false) { $sql .= " AND EUC.enabled = '1' AND ( (substr(EUC.expiry, 4, 2) || substr(EUC.expiry, 1, 2) ) >= '". date("ym") ."' OR length(EUC.expiry) = 0)"; }
            if (is_null($oCC) === true || $oCC->getStoreCard() <= 3)
            {
                $sql .= " AND (CLA.clientid = CL.id OR NOT EXISTS (SELECT id FROM EndUser".sSCHEMA_POSTFIX.".CLAccess_Tbl WHERE accountid = EUA.id) )";
            }
            $sql .= " ORDER BY EUC.id, EUC.cardid, EUC.pspid, EUC.mask, EUC.expiry, EUC.ticket, SC.name ASC";
            $resultset = $this->getDBConn()->getAllNames($sql);
        }
        else
        {
            // Select all active cards that are not yet expired
            $sql = "SELECT DISTINCT ON (EUC.id, EUC.cardid, EUC.pspid, EUC.mask, EUC.expiry, EUC.ticket) EUC.id, EUC.cardid, EUC.pspid, EUC.mask, EUC.expiry, EUC.ticket, EUC.preferred, EUC.name, EUC.enabled, EUC.card_holder_name, EUC.chargetypeid,
					SC.id AS typeid, SC.name AS type, CA.stateid, SC.cvclength AS cvclength,
					CL.id AS clientid, CL.name AS client,
					EUAD.countryid, EUAD.firstname, EUAD.lastname,
					EUAD.company, EUAD.street,
					EUAD.postalcode, EUAD.city,
					CA.position AS client_position,
                    SRLC.cvcmandatory,
					CA.dccenabled
				FROM EndUser".sSCHEMA_POSTFIX.".Card_Tbl EUC
				INNER JOIN System".sSCHEMA_POSTFIX.".PSP_Tbl PSP ON EUC.pspid = PSP.id AND PSP.enabled = '1'
				INNER JOIN System".sSCHEMA_POSTFIX.".Card_Tbl SC ON EUC.cardid = SC.id AND SC.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".Client_Tbl CL ON EUC.clientid = CL.id AND CL.enabled = '1'
				INNER JOIN Client".sSCHEMA_POSTFIX.".CardAccess_Tbl CA ON CL.id = CA.clientid AND SC.id = CA.cardid
				INNER JOIN EndUser".sSCHEMA_POSTFIX.".Account_Tbl EUA ON EUC.accountid = EUA.id AND EUA.enabled = '1'
				LEFT OUTER JOIN EndUser".sSCHEMA_POSTFIX.".Address_Tbl EUAD ON EUC.id = EUAD.cardid and EUA.enabled ='1'
				LEFT OUTER JOIN EndUser".sSCHEMA_POSTFIX.".CLAccess_Tbl CLA ON EUA.id = CLA.accountid
				LEFT OUTER JOIN Client".sSCHEMA_POSTFIX.".StaticRouteLevelConfiguration SRLC ON SRLC.cardaccessid = CA.id AND SRLC.enabled = '1'
				WHERE EUC.accountid = ". (int)$id;
            if($countryId !== NULL)
            {
                $sql .= " AND (CA.countryid = ". $countryId ." OR CA.countryid IS NULL)";
            }
            if(empty($aPaymentMethods) === false){ $sql .= " AND SC.id IN (". implode(',', $aCardId).")"; }
            if ($oCC->showAllCards() === false) { $sql .= " AND EUC.enabled = '1' AND ( (substr(EUC.expiry, 4, 2) || substr(EUC.expiry, 1, 2) ) >= '". date("ym") ."' OR length(EUC.expiry) = 0)"; }
            if ($adc === false) { $sql .= "  AND CA.enabled = '1'"; }
            if (is_null($oCC) === true || $oCC->getStoreCard() <= 3)
            {
                $sql .= " AND (CLA.clientid = CL.id OR NOT EXISTS (SELECT id
														       FROM EndUser".sSCHEMA_POSTFIX.".CLAccess_Tbl
														       WHERE accountid = EUA.id) )";
            }
            $sql .= " ORDER BY EUC.id, EUC.cardid, EUC.pspid, EUC.mask, EUC.expiry, EUC.ticket, CA.position ASC NULLS LAST, SC.name ASC";
        }
        return $sql;
    }


    private function _cardNotExpired($month, $year) {
        /* Get timestamp of midnight on day after expiration month. */
        $exp_ts = mktime(0, 0, 0, $month + 1, 1, $year);

        $cur_ts = time();

        if ($exp_ts > $cur_ts) {
            return true;
        } else {
            return false;
        }
    }
	/**
	 * Searches for transaction history given a client ID plus any of the other parameters
	 *
	 * @param	integer $uid 	Unqiue Client ID
	 * @param	integer $clid 	Unqiue Client ID
	 * @param	string $txnno	Unqiue ID of a Transaction
	 * @param	string $ono		Unqiue Order Number for a Transaction
	 * @param	long $mob		The End-User's Mobile Number
	 * @param	string $email	The End-User's E-Mail
	 * @param	string $cr		The Customer Reference for the End-User
	 * @param	string $start	The start date / time for when transactions must have been created in order to be included in the search result
	 * @param	string $end		The end date / time for when transactions must have been created in order to be included in the search result
	 * @param	boolean $debug	The Customer Reference for the End-User
	 * @return 	string
	 */
	public function searchTxnHistory($uid, $clid=-1, $txnno="", $ono="", $mob=-1, $email="", $cr="", $start="", $end="", $debug=false)
	{
		// Fetch all Transfers
		$sql = "SELECT EUT.id, EUT.typeid, EUT.toid, EUT.fromid, EUT.created, EUT.stateid AS stateid,
					EUA.id AS customerid, EUA.firstname, EUA.lastname, EUA.externalid AS customer_ref, EUA.mobile, EUA.email,
					CL.id AS clientid, CL.name AS client,
					'' AS orderno
				FROM EndUser".sSCHEMA_POSTFIX.".Transaction_Tbl EUT
    			INNER JOIN EndUser".sSCHEMA_POSTFIX.".Account_Tbl EUA ON EUT.accountid = EUA.id
    			INNER JOIN EndUser".sSCHEMA_POSTFIX.".CLAccess_Tbl CLA ON CLA.accountid = EUA.id
				INNER JOIN Admin".sSCHEMA_POSTFIX.".Access_Tbl Acc ON CLA.clientid = Acc.clientid
				INNER JOIN Client".sSCHEMA_POSTFIX.".Client_Tbl CL ON  CL.id = Acc.clientid
				WHERE EUT.txnid IS NULL AND Acc.userid = ". intval($uid);
		if (intval($clid) > 0) { $sql .= " AND CL.id = ". intval($clid); }
		if (floatval($mob) > 0) { $sql .= " AND EUA.mobile = '". floatval($mob) ."'"; }
		if (empty($email) === false) { $sql .= " AND EUA.email = '". $this->getDBConn()->escStr($email) ."'"; }
		if (empty($cr) === false) { $sql .= " AND EUA.externalid = '". $this->getDBConn()->escStr($cr) ."'"; }
		if (empty($start) === false && strlen($start) > 0) { $sql .= " AND '". $this->getDBConn()->escStr(date("Y-m-d H:i:s", strtotime($start) ) ) ."' <= EUT.created"; }
		if (empty($end) === false && strlen($end) > 0) { $sql .= " AND EUT.created <= '". $this->getDBConn()->escStr(date("Y-m-d H:i:s", strtotime($end) ) ) ."'"; }
		// Fetch all Purchases
		$sql .= "
				UNION
				SELECT Txn.id, ". Constants::iCARD_PURCHASE_TYPE ." AS typeid, -1 AS toid, -1 AS fromid, Txn.created,
					(CASE
					 WHEN M4.stateid IS NOT NULL THEN M4.stateid
					 WHEN M3.stateid IS NOT NULL THEN M3.stateid
					 WHEN M2.stateid IS NOT NULL THEN M2.stateid
					 WHEN M1.stateid IS NOT NULL THEN M1.stateid
					 ELSE -1
					 END) AS stateid,
					EUA.id AS customerid, EUA.firstname, EUA.lastname, Coalesce(Txn.customer_ref, EUA.externalid) AS customer_ref, Txn.mobile, Txn.email,
					CL.id AS clientid, CL.name AS client,
					Txn.orderid AS orderno
				FROM Log".sSCHEMA_POSTFIX.".Transaction_Tbl Txn
				LEFT OUTER JOIN Log".sSCHEMA_POSTFIX.".message_tbl M1 ON Txn.id = M1.txnid AND M1.stateid = ". Constants::iPAYMENT_ACCEPTED_STATE ."
				LEFT OUTER JOIN Log".sSCHEMA_POSTFIX.".message_tbl M2 ON Txn.id = M2.txnid AND M2.stateid = ". Constants::iPAYMENT_CAPTURED_STATE ."
				LEFT OUTER JOIN Log".sSCHEMA_POSTFIX.".message_tbl M3 ON Txn.id = M3.txnid AND M3.stateid = ". Constants::iPAYMENT_REFUNDED_STATE ."
				LEFT OUTER JOIN Log".sSCHEMA_POSTFIX.".message_tbl M4 ON Txn.id = M4.txnid AND M4.stateid = ". Constants::iPAYMENT_CANCELLED_STATE ."
				INNER JOIN Client".sSCHEMA_POSTFIX.".Client_Tbl CL ON Txn.clientid = CL.id
				INNER JOIN Admin".sSCHEMA_POSTFIX.".Access_Tbl Acc ON Txn.clientid = Acc.clientid
				INNER JOIN Log".sSCHEMA_POSTFIX.".Message_Tbl M ON Txn.id = M.txnid
				LEFT OUTER JOIN EndUser".sSCHEMA_POSTFIX.".Account_Tbl EUA ON Txn.euaid = EUA.id
				WHERE Acc.userid = ". intval($uid);
		if (intval($clid) > 0) { $sql .= " AND CL.id = ". intval($clid); }
		if (empty($ono) === false) { $sql .= " AND Txn.orderid = '". $this->getDBConn()->escStr($ono) ."'"; }
		if (floatval($mob) > 0) { $sql .= " AND Txn.mobile = '". floatval($mob) ."'"; }
		if (empty($email) === false) { $sql .= " AND Txn.email = '". $this->getDBConn()->escStr($email) ."'"; }
		if (empty($cr) === false) { $sql .= " AND Txn.customer_ref = '". $this->getDBConn()->escStr($cr) ."'"; }
		if (empty($start) === false && strlen($start) > 0) { $sql .= " AND '". $this->getDBConn()->escStr(date("Y-m-d H:i:s", strtotime($start) ) ) ."' <= Txn.created"; }
		if (empty($end) === false && strlen($end) > 0) { $sql .= " AND Txn.created <= '". $this->getDBConn()->escStr(date("Y-m-d H:i:s", strtotime($end) ) ) ."'"; }
		$sql .= "
				ORDER BY created DESC";
//		echo $sql ."\n";
		$res = $this->getDBConn()->query($sql);

		$sql = "SELECT stateid
				FROM Log".sSCHEMA_POSTFIX.".Message_Tbl
				WHERE txnid = $1 AND stateid IN (". Constants::iINPUT_VALID_STATE .", ". Constants::iPAYMENT_INIT_WITH_PSP_STATE .", ". Constants::iPAYMENT_ACCEPTED_STATE .", ". Constants::iPAYMENT_CAPTURED_STATE .", ". Constants::iPAYMENT_CAPTURE_FAILED_STATE .", ". Constants::iPAYMENT_CANCEL_FAILED_STATE .", ". Constants::iPAYMENT_REFUND_FAILED_STATE .", ". Constants::iPAYMENT_REQUEST_CANCELLED_STATE .", ". Constants::iPAYMENT_REQUEST_EXPIRED_STATE.")
				ORDER BY id DESC";
//		echo $sql ."\n";
		$stmt1 = $this->getDBConn()->prepare($sql);
		$sql = "SELECT id, stateid, data, created
				FROM Log".sSCHEMA_POSTFIX.".Message_Tbl
				WHERE txnid = $1 AND stateid IN (". Constants::iINPUT_VALID_STATE .", ". Constants::iPSP_PAYMENT_REQUEST_STATE .", ". Constants::iPSP_PAYMENT_RESPONSE_STATE .", ". Constants::iPAYMENT_INIT_WITH_PSP_STATE .", ". Constants::iPAYMENT_ACCEPTED_STATE .", ". Constants::iPAYMENT_CAPTURED_STATE .", ". Constants::iPAYMENT_CAPTURE_FAILED_STATE ." ". Constants::iPAYMENT_CANCEL_FAILED_STATE .", ". Constants::iPAYMENT_REFUND_FAILED_STATE .", ". Constants::iPAYMENT_REQUEST_CANCELLED_STATE .", ". Constants::iPAYMENT_REQUEST_EXPIRED_STATE.")
				ORDER BY id ASC";
//		echo $sql ."\n";
		$stmt2 = $this->getDBConn()->prepare($sql);

		$xml = '<transactions sorted-by="timestamp" sort-order="descending">';
		// Construct XML Document with data for Transaction
		while ($RS = $this->getDBConn()->fetchName($res) )
		{
			// Purchase
			if ($RS["STATEID"] < 0 && $RS["TYPEID"] == Constants::iCARD_PURCHASE_TYPE)
			{
				$aParams = array($RS["ID"]);
				$res1 = $this->getDBConn()->execute($stmt1, $aParams);
				if (is_resource($res1) === true)
				{
					$RS1 = $this->getDBConn()->fetchName($res1);
					if (is_array($RS1) === true) { $RS["STATEID"] = $RS1["STATEID"]; }
				}
			}
			
			$xml .= '<transaction id="'. $RS["ID"] .'" type-id="'. $RS["TYPEID"] .'" state-id="'. intval($RS["STATEID"]) .'" order-no="'. htmlspecialchars($RS["ORDERNO"], ENT_NOQUOTES) .'">';
			$xml .= '<client id="'. $RS["CLIENTID"] .'">'. htmlspecialchars($RS["CLIENT"], ENT_NOQUOTES) .'</client>';
			$xml .= '<customer id="'. $RS["CUSTOMERID"] .'" customer-ref="'. $RS["CUSTOMER_REF"] .'">';
			$xml .= '<full-name>'. htmlspecialchars($RS["FIRSTNAME"] ." ". $RS["LASTNAME"], ENT_NOQUOTES) .'</full-name>';
			$xml .= '<mobile>'. floatval($RS["MOBILE"]) .'</mobile>';
			$xml .= '<email>'. htmlspecialchars($RS["EMAIL"], ENT_NOQUOTES) .'</email>';
			$xml .= '</customer>';
			$xml .= '<timestamp>'. gmdate("Y-m-d H:i:sP", strtotime(substr($RS["CREATED"], 0, strpos($RS["CREATED"], ".") ) ) ) .'</timestamp>';
			if ($debug === true && $RS["TYPEID"] == Constants::iCARD_PURCHASE_TYPE)
			{
				$aParams = array($RS["ID"]);
				$res2 = $this->getDBConn()->execute($stmt2, $aParams);
				
				if (is_resource($res2) === true)
				{
					$xml .= '<messages>';
					// Additional record sets
					while ($RS2 = $this->getDBConn()->fetchName($res2) )
					{
						$xml .= '<message id="'. $RS2["ID"] .'" state-id="'. $RS2["STATEID"] .'">';
						$xml .= '<data>'. htmlspecialchars(trim($RS2["DATA"]), ENT_NOQUOTES) .'</data>';
						$xml .= '<timestamp>'. gmdate("Y-m-d H:i:sP", strtotime(substr($RS2["CREATED"], 0, strpos($RS2["CREATED"], ".") ) ) ) .'</timestamp>';
						$xml .= '</message>';
					}
					$xml .= '</messages>';
				}
			}
			$xml .= '</transaction>';
		}

		$xml .= '</transactions>';

		return $xml;
	}
	public function getTxn($txnid)
	{
/*
		$sql = "SELECT EUT.id, EUT.typeid, EUT.toid, EUT.fromid,EUT.message, Extract('epoch' from EUT.created AT TIME ZONE 'Europe/Copenhagen') AS timestamp,
					(CASE
						WHEN EUT.amount = 0 THEN Txn.amount
					 	WHEN EUT.amount IS NULL THEN Txn.amount
					 	ELSE abs(EUT.amount)
					 END) AS amount,
					C.id AS countryid, C.currency, C.symbol, C.priceformat,
					CL.id AS clientid, CL.name AS client,
					(EUAT.firstname || ' ' || EUAT.lastname) AS to_name, EUAT.id AS to_id, EUAT.countryid AS to_countryid,
					EUAT.mobile AS to_mobile, EUAT.countryid AS to_m, EUAT.email AS to_email,
					(EUAF.firstname || ' ' || EUAF.lastname) AS from_name,EUAF.id AS from_id, EUAF.countryid AS from_countryid,
					EUAF.mobile AS from_mobile, EUAF.email AS from_email,
					Txn.id AS mpointid, Txn.orderid, Txn.cardid, Card.name AS card, Txn.refund AS refund_amount, Txn.pspid, Txn.orderid AS orderno,
					Extract('epoch' from M1.created) AS authorized, Extract('epoch' from M2.created) AS captured, Extract('epoch' from M3.created) AS refunded,
					EUT.accountid AS end_user_id
				FROM EndUser".sSCHEMA_POSTFIX.".Transaction_Tbl EUT
				LEFT OUTER JOIN EndUser".sSCHEMA_POSTFIX.".Account_Tbl EUAT ON EUT.toid = EUAT.id
				LEFT OUTER JOIN EndUser".sSCHEMA_POSTFIX.".Account_Tbl EUAF ON EUT.fromid = EUAF.id
				LEFT OUTER JOIN Log".sSCHEMA_POSTFIX.".Transaction_Tbl Txn ON EUT.txnid = Txn.id
				LEFT OUTER JOIN Client".sSCHEMA_POSTFIX.".Client_Tbl CL ON Txn.clientid = CL.id
				LEFT OUTER JOIN System".sSCHEMA_POSTFIX.".Country_Tbl C ON Txn.countryid = C.id
				LEFT OUTER JOIN System".sSCHEMA_POSTFIX.".Card_Tbl Card ON Txn.cardid = Card.id
				LEFT OUTER JOIN Log".sSCHEMA_POSTFIX.".message_tbl M1 ON Txn.id = M1.txnid AND M1.stateid = ". Constants::iPAYMENT_ACCEPTED_STATE ."
				LEFT OUTER JOIN Log".sSCHEMA_POSTFIX.".message_tbl M2 ON Txn.id = M2.txnid AND M2.stateid = ". Constants::iPAYMENT_CAPTURED_STATE ."
				LEFT OUTER JOIN Log".sSCHEMA_POSTFIX.".message_tbl M3 ON Txn.id = M3.txnid AND M3.stateid = ". Constants::iPAYMENT_REFUNDED_STATE ."
				WHERE EUT.id = '". $this->getDBConn()->escStr( (string) $txnid) ."'";
*/
		$sql = "SELECT Txn.id, Txn.typeid, Extract('epoch' from Txn.created AT TIME ZONE 'Europe/Copenhagen') AS timestamp,
					   Txn.amount AS amount, Txn.cardid,
					   C.id AS countryid, C.currency, C.symbol, C.priceformat, PSP.name AS pspname,
					   CL.id AS clientid, CL.name AS client,
					   Txn.id AS mpointid, Txn.orderid, Txn.cardid, Card.name AS card, Txn.refund AS refund_amount, Txn.pspid, Txn.orderid AS orderno,
					   Extract('epoch' from M1.created) AS authorized, Extract('epoch' from M2.created) AS captured, Extract('epoch' from M3.created) AS refunded,
					   Txn.accountid AS end_user_id, Txn.email , Txn.mobile
				FROM Log.Transaction_Tbl Txn
				LEFT OUTER JOIN System".sSCHEMA_POSTFIX.".PSP_Tbl PSP ON Txn.pspid = PSP.id
				LEFT OUTER JOIN Client".sSCHEMA_POSTFIX.".Client_Tbl CL ON Txn.clientid = CL.id
				LEFT OUTER JOIN System".sSCHEMA_POSTFIX.".Country_Tbl C ON Txn.countryid = C.id
				LEFT OUTER JOIN System".sSCHEMA_POSTFIX.".Card_Tbl Card ON Txn.cardid = Card.id
				LEFT OUTER JOIN Log".sSCHEMA_POSTFIX.".message_tbl M1 ON Txn.id = M1.txnid AND M1.stateid = ". Constants::iPAYMENT_ACCEPTED_STATE ."
				LEFT OUTER JOIN Log".sSCHEMA_POSTFIX.".message_tbl M2 ON Txn.id = M2.txnid AND M2.stateid = ". Constants::iPAYMENT_CAPTURED_STATE ."
				LEFT OUTER JOIN Log".sSCHEMA_POSTFIX.".message_tbl M3 ON Txn.id = M3.txnid AND M3.stateid = ". Constants::iPAYMENT_REFUNDED_STATE ."
				WHERE Txn.id = '". $this->getDBConn()->escStr( (string) $txnid) ."'
				ORDER BY Txn.created DESC";
//		echo $sql ."\n";
		$RS = $this->getDBConn()->getName($sql);

		$sql = "SELECT id, countryid,(firstname || ' ' || lastname) AS name,
				mobile, email
				FROM EndUser".sSCHEMA_POSTFIX.".Account_Tbl
				WHERE id = ". $RS["END_USER_ID"];
//		echo $sql ."\n";
		$RSs = $this->getDBConn()->getName($sql);

		$obj_ClientConfig = ClientConfig::produceConfig($this->getDBConn(), $RS["CLIENTID"]);

		$xml .= '<transaction id="'. $RS["ID"] .'" mpoint-id="'. $RS["MPOINTID"] .'" psp-id="'. $RS["PSPID"] .'" psp-name="'. $RS["PSPNAME"] .'" order-no="'. $RS["ORDERNO"] .'" type-id="'. $RS["TYPEID"] .'" card-id="'. $RS["CARDID"] .'">';
		$xml .= '<amount country-id="'. $RS["COUNTRYID"] .'" currency="'. $this->_obj_CountryConfig->getCurrency()  .'" symbol="'. utf8_encode($this->_obj_CountryConfig->getSymbol() ) .'" format="'. $this->_obj_CountryConfig->getPriceFormat() .'">'. htmlspecialchars($RS["AMOUNT"], ENT_NOQUOTES) .'</amount>';
		$xml .= '<refund country-id="'. $RS["COUNTRYID"] .'" currency="'. $this->_obj_CountryConfig->getCurrency() .'" symbol="'. utf8_encode($this->_obj_CountryConfig->getSymbol() ) .'" format="'. $this->_obj_CountryConfig->getPriceFormat() .'">'. htmlspecialchars($RS["REFUND_AMOUNT"], ENT_NOQUOTES) .'</refund>';
		$xml .= '<client id="'. $obj_ClientConfig->getID() .'">';
		$xml .= '<name>'. htmlspecialchars($obj_ClientConfig->getName(), ENT_NOQUOTES) .'</name>';
		$xml .= '</client>';
		$xml .= '<customer id="'. $RSs["ID"] .'">';
		$xml .= '<name>'. htmlspecialchars($RSs["NAME"], ENT_NOQUOTES) .'</name>';
		$xml .= '<mobile country-id="'. $RS["COUNTRYID"] .'">'. floatval($RS["MOBILE"]) .'</mobile>';
		$xml .= '<email>'. htmlspecialchars($RS["EMAIL"], ENT_NOQUOTES) .'</email>';
		$xml .= '</customer>';
		$xml .= '<timestamp>'. date("d/m-y H:i:s", $RS["TIMESTAMP"]) .'</timestamp>';
		if ($RS["AUTHORIZED"] > 0) { $xml .= '<authorized epoch="'. $RS["AUTHORIZED"] .'">'. date("d/m-y H:i:s", $RS["AUTHORIZED"]) .'</authorized>'; }
		else { $xml .= '<authorized />'; }
		if ($RS["CAPTURED"] > 0) { $xml .= '<captured epoch="'. $RS["CAPTURED"] .'">'. date("d/m-y H:i:s", $RS["CAPTURED"]) .'</captured>'; }
		else { $xml .= '<captured />'; }
		if ($RS["REFUNDED"] > 0) { $xml .= '<refunded epoch="'. $RS["REFUNDED"] .'">'. date("d/m-y H:i:s", $RS["REFUNDED"]) .'</refunded>'; }
		else { $xml .= '<refunded />'; }
		if (empty($RS["TO_NAME"]) === false)
		{
			$xml .= '<transfer>';
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
			$xml .= '<message>'. htmlspecialchars($RS["MESSAGE"], ENT_NOQUOTES) .'</message>';
			$xml .= '</transfer>';
		}
		$sql = "SELECT N.id, N.message, Extract('epoch' from N.created) AS created, U.id AS userid, U.email
				FROM enduser".sSCHEMA_POSTFIX.".Transaction_Tbl Txn
				INNER JOIN Log".sSCHEMA_POSTFIX.".Note_Tbl N ON Txn.id = N.txnid AND N.enabled = true
				INNER JOIN Admin".sSCHEMA_POSTFIX.".user_tbl U ON N.userid = U.id
				WHERE Txn.id = ". intval($txnid);
//		echo $sql ."\n";
		$aRS = $this->getDBConn()->getAllNames($sql);
		$xml .= '<notes>';
		if (is_array($aRS) === true && count($aRS) > 0)
		{
			for ($i=0; $i<count($aRS); $i++)
			{
				$xml .= '<note id="'. $aRS[$i]["ID"] .'">';
				$xml .= '<user id="'. $aRS[$i]["USERID"] .'">'. htmlspecialchars($aRS[$i]["EMAIL"], ENT_NOQUOTES) .'</user>';
				$xml .= '<message>'. htmlspecialchars($aRS[$i]["MESSAGE"], ENT_NOQUOTES) .'</message>';
				$xml .= '<created>'. date("d/m-y H:i", $aRS[$i]["CREATED"]) .'</created>';
				$xml .= '</note>';
			}
		}
		$xml .= '</notes>';

		$xml .= '</transaction>';

		return $xml;
	}


    public function getTxnStatus($txnId,$clientid,$mode,$sessionId=0)
    {
		try
        {
		    $xml = '';

		    $aTxnId = array();
		    if($sessionId !== 0)
            {
                $sql = "SELECT id  FROM Log".sSCHEMA_POSTFIX.".Transaction_Tbl Where sessionid = ".$sessionId;
                $RSTxnId = $this->getDBConn()->query($sql);
                while ($RS = $this->getDBConn()->fetchName($RSTxnId) ) { $aTxnId[] = (int)$RS["ID"]; }
            }
		    else { $aTxnId[0] = $txnId; }

            foreach ($aTxnId as $index => $txnid)
            {


                $obj_TxnInfo = TxnInfo::produceInfo($txnid,  $this->getDBConn());

                if( $obj_TxnInfo->hasEitherState($this->getDBConn(),array(Constants::iTRANSACTION_CREATED))=== true && $obj_TxnInfo->getPSPID()=== 0) continue;
                $objPaymentMethod = $obj_TxnInfo->getPaymentMethod($this->getDBConn());

              //  mode param is optional when populated with value 1 then status code will return only after session is closed and
             // only final payment status code will be returned to avoid extra checks at API consumer side
                if($mode === 1)
                {
                    $state = -1;
                    if($objPaymentMethod->PaymentType == Constants::iPAYMENT_TYPE_OFFLINE)
                    {
                        $state = Constants::iPAYMENT_PENDING_STATE;
                    }
                    $RSMsg = false;

                    if(($obj_TxnInfo->getPaymentSession()->getStateId() == Constants::iSESSION_COMPLETED ||
                        $obj_TxnInfo->getPaymentSession()->getStateId() == Constants::iSESSION_PARTIALLY_COMPLETED ||
                        $obj_TxnInfo->getPaymentSession()->getStateId() == Constants::iSESSION_FAILED_MAXIMUM_ATTEMPTS ||
                        $obj_TxnInfo->getPaymentSession()->getStateId() == Constants::iPAYMENT_3DS_FAILURE_STATE) ||
                        $obj_TxnInfo->hasEitherState($this->getDBConn(),array(Constants::iPAYMENT_REJECTED_STATE ,Constants::iPRE_FRAUD_CHECK_REJECTED_STATE,Constants::iPOST_FRAUD_CHECK_REJECTED_STATE,$state)))
                    {
                        $sql = "WITH WT1 as
                           (SELECT DISTINCT stateid, txnid, S.name,m.id  FROM Log".sSCHEMA_POSTFIX.".Message_Tbl m INNER JOIN Log".sSCHEMA_POSTFIX.".State_Tbl S on M.stateid = S.id WHERE txnid = ".$txnid." and M.enabled = true),
                            WT2 as (SELECT stateid,txnid,name,id FROM (SELECT *,rank() over(partition by txnid order by id desc) FROM WT1 WHERE stateid in (".Constants::iPAYMENT_ACCEPTED_STATE.",".Constants::iPAYMENT_CAPTURED_STATE.",
                            ".Constants::iPAYMENT_CANCELLED_STATE.",".Constants::iPAYMENT_REFUNDED_STATE.",".Constants::iPAYMENT_3DS_VERIFICATION_STATE.",".Constants::iPAYMENT_3DS_SUCCESS_STATE.",
                            ".Constants::iPAYMENT_REJECTED_STATE.",".Constants::iPAYMENT_REJECTED_INCORRECT_INFO_STATE.",".Constants::iPAYMENT_REJECTED_PSP_UNAVAILABLE_STATE.",
                            ".Constants::iPAYMENT_REJECTED_3D_SECURE_FAILURE_STATE.",".Constants::iPAYMENT_TIME_OUT_STATE.",".Constants::iPSP_TIME_OUT_STATE.",".Constants::iPAYMENT_CAPTURE_FAILED_STATE.",".Constants::iPAYMENT_3DS_FAILURE_STATE.",
                            ".Constants::iPAYMENT_CANCEL_FAILED_STATE.",".Constants::iPAYMENT_REFUND_FAILED_STATE.",".Constants::iPAYMENT_REQUEST_CANCELLED_STATE.",".Constants::iPAYMENT_REQUEST_EXPIRED_STATE.",
                            ".Constants::iPAYMENT_DUPLICATED_STATE.",".Constants::iPAYMENT_3DS_SUCCESS_AUTH_NOT_ATTEMPTED_STATE.",".$state.")) s where s.rank=1
                            UNION
                            SELECT * FROM WT1 WHERE stateid in (".Constants::iPRE_FRAUD_CHECK_ACCEPTED_STATE.",".Constants::iPRE_FRAUD_CHECK_UNAVAILABLE_STATE.",".Constants::iPRE_FRAUD_CHECK_UNKNOWN_STATE.",".Constants::iPRE_FRAUD_CHECK_REVIEW_STATE.",".Constants::iPRE_FRAUD_CHECK_REJECTED_STATE.",
                            ".Constants::iPRE_FRAUD_CHECK_CONNECTION_FAILED_STATE.",".Constants::iPOST_FRAUD_CHECK_ACCEPTED_STATE.",".Constants::iPOST_FRAUD_CHECK_UNAVAILABLE_STATE.",".Constants::iPOST_FRAUD_CHECK_UNKNOWN_STATE.",".Constants::iPOST_FRAUD_CHECK_REVIEW_STATE.",
                            ".Constants::iPOST_FRAUD_CHECK_REJECTED_STATE.",".Constants::iPOST_FRAUD_CHECK_CONNECTION_FAILED_STATE.",".Constants::iPOST_FRAUD_CHECK_SKIP_RULE_MATCHED_STATE.") 
                            )
                           SELECT  *,row_number() OVER(ORDER BY id ASC) AS rownum FROM WT2";

                            $RSMsg = $this->getDBConn()->query($sql);
                    }
                }
                else
                {
                    $sql = "SELECT DISTINCT stateid, txnid, row_number() OVER(ORDER BY m.id ASC) AS rownum, S.name 
                                  FROM Log".sSCHEMA_POSTFIX.".Message_Tbl m INNER JOIN Log".sSCHEMA_POSTFIX.".State_Tbl S on M.stateid = S.id
                                  WHERE txnid = ".$txnid." and M.enabled = true";
                    $RSMsg = $this->getDBConn()->query($sql);

                }

                    $objCurrConf = $obj_TxnInfo->getCurrencyConfig();
                    $objCountryConf = $obj_TxnInfo->getCountryConfig();
            		$objClientConf = $obj_TxnInfo->getClientConfig();
            		if($objClientConf->getID() === $clientid)
            		{
                        $sTxnAdditionalDataXml = "";
                        $aTxnAdditionalData = $obj_TxnInfo->getAdditionalData();
                        if($aTxnAdditionalData !== null)
                        {
                            $sTxnAdditionalDataXml ="<additional-data>";
                            foreach ($aTxnAdditionalData as $key => $value)
                            {
                                $sTxnAdditionalDataXml .= "<param name='".$key."'>". $value ."</param>";
                            }
                            $sTxnAdditionalDataXml .="</additional-data>";
                        }

                         $obj_paymentSession = $obj_TxnInfo->getPaymentSession();
                         $pendingAmount = intval($obj_paymentSession->getPendingAmount());
                         $objPSPType = $obj_TxnInfo->getPSPType($this->getDBConn());

                         $amount = $obj_TxnInfo->getAmount();

                         $sStatusMessagesXML = '';
                         while ($RS = $this->getDBConn()->fetchName($RSMsg) )
                         {
                             $sStatusMessagesXML .= '<status-message id = "'.$RS['STATEID'].'" position = "'.$RS['ROWNUM'] .'">' . $RS['NAME'] . '</status-message>';
                         }

                         $sessionType = $objClientConf->getAdditionalProperties(Constants::iInternalProperty, "sessiontype");
                         $googleAnalyticsId = $objClientConf->getAdditionalProperties(Constants::iInternalProperty,"googleAnalyticsId");
                         $paymentCompleteMethod = $objClientConf->getAdditionalProperties(Constants::iInternalProperty,"hppFormRedirectMethod");
                         $isEmbeddedHpp = $objClientConf->getAdditionalProperties(Constants::iInternalProperty,"isEmbeddedHpp");
                         $isAutoRedirect = $objClientConf->getAdditionalProperties(Constants::iInternalProperty,"isAutoRedirect");
                         $cardMask = $obj_TxnInfo->getCardMask();
                         $cardExpiry = $obj_TxnInfo->getCardExpiry();
                         $acceptUrl = $obj_TxnInfo->getAcceptURL();
                         $cancelUrl = $obj_TxnInfo->getCancelURL();
                         $cssUrl = $obj_TxnInfo->getCSSURL();
                         $logoUrl = $obj_TxnInfo->getLogoURL();
                         if($sessionId > 0 && $index === 0)
                         {
                             $xml .= $obj_TxnInfo->getPaymentSessionXML();
                         }
                         $xml .= '<transaction id="' . $txnid . '" mpoint-id="' . $txnid . '" order-no="' . $obj_TxnInfo->getOrderID() . '" accoutid="' . $objClientConf->getAccountConfig()->getID() . '" clientid="' . $objClientConf->getID(). '" language="' . $obj_TxnInfo->getLanguage(). '"  card-id="' . $obj_TxnInfo->getCardID() . '" psp-id="' . $obj_TxnInfo->getPSPID() . '" payment-method-id="' . $objPaymentMethod->PaymentType . '"   session-id="' . $obj_TxnInfo->getSessionId(). '" session-type="' . $sessionType . '" extid="' . $obj_TxnInfo->getExternalID() . '" approval-code="' . $obj_TxnInfo->getApprovalCode() . '" walletid="' . $obj_TxnInfo->getWalletID(). '">';
                         $xml .= '<amount country-id="' . $objCountryConf->getID() . '" currency="' . $objCurrConf->getID() . '" symbol="' . utf8_encode($objCurrConf->getSymbol()) . '" format="' . $objCountryConf->getPriceFormat() . '" pending = "' . $pendingAmount . '"  currency-code = "' . $objCurrConf->getCode() . '" decimals = "' . $objCurrConf->getDecimals() . '" conversationRate = "' . $obj_TxnInfo->getConversationRate() . '">' . htmlspecialchars($amount, ENT_NOQUOTES) . '</amount>';
                         if($obj_TxnInfo->getConversationRate() !=1 )
                         {
                             $xml .= '<initialize_amount country-id="' . $obj_TxnInfo->getID() . '" currency="' . $obj_TxnInfo->getInitializedCurrencyConfig()->getID() . '" symbol="' . utf8_encode($obj_TxnInfo->getInitializedCurrencyConfig()->getSymbol()) . '" format="' . $objCountryConf->getPriceFormat() . '" pending = "' . $pendingAmount . '"  currency-code = "' . $obj_TxnInfo->getInitializedCurrencyConfig()->getCode() . '" decimals = "' . $obj_TxnInfo->getInitializedCurrencyConfig()->getDecimals() . '">' . htmlspecialchars($obj_TxnInfo->getInitializedAmount(), ENT_NOQUOTES) . '</initialize_amount>';
                         }
                         if(empty($cardMask) === false){ $xml .= '<card-mask>'.htmlspecialchars($cardMask, ENT_NOQUOTES).'</card-mask>'; }
                         if(empty($cardExpiry) === false){ $xml .= '<card-expiry>'.htmlspecialchars($cardExpiry, ENT_NOQUOTES).'</card-expiry>'; }
                         $xml .= '<card-name>'.$objPaymentMethod->CardName.'</card-name>';
                         $xml .= '<psp-name>'.$objPSPType->PSPName.'</psp-name>';
                         $xml .= '<accept-url>' . htmlspecialchars($acceptUrl, ENT_NOQUOTES) . '</accept-url>';
                         $xml .= '<cancel-url>' . htmlspecialchars($cancelUrl, ENT_NOQUOTES) . '</cancel-url>';
                         $xml .= '<css-url>' . htmlspecialchars($cssUrl, ENT_NOQUOTES) . '</css-url>';
                         $xml .= '<logo-url>' . htmlspecialchars($logoUrl, ENT_NOQUOTES) . '</logo-url>';
                         $xml .= '<google-analytics-id>' . $googleAnalyticsId . '</google-analytics-id>';
                         $xml .= '<form-method>' . $paymentCompleteMethod . '</form-method>';
                         if (empty($isEmbeddedHpp) === false) { $xml .= '<embedded-hpp>' . $isEmbeddedHpp . '</embedded-hpp>'; }
                         if (empty($isAutoRedirect) === false) { $xml .= '<auto-redirect>' . $isAutoRedirect . '</auto-redirect>'; }
            		     $xml .= '<createdDate>'. htmlspecialchars(date("Y-m-d", strtotime($obj_TxnInfo->getCreatedTimestamp())), ENT_NOQUOTES) .'</createdDate>'; //YYMMDD
            		     $xml .= '<createdTime>'. htmlspecialchars(date("H:i:s", strtotime($obj_TxnInfo->getCreatedTimestamp())), ENT_NOQUOTES) .'</createdTime>'; //hhmmss

                         $xml .= '<status>' . $sStatusMessagesXML . '</status>';
                         $xml .= '<sign>' . md5($objClientConf->getID() . '&' . $obj_TxnInfo->getID() . '&' . $obj_TxnInfo->getOrderID() . '&' . $objCurrConf->getID() . '&' . htmlspecialchars($amount, ENT_NOQUOTES) . '&' . $RS["STATEID"] . '.' . $objClientConf->getSalt()) . '</sign>';
                     //  $xml .= '<pre-sign>'.  $RS["CLIENTID"] .','. $RS["MPOINTID"] .','. $RS["ORDERID"] .','. $RS["CURRENCY"] .','.  htmlspecialchars($amount, ENT_NOQUOTES) .','. $RS["STATEID"] .','. $RS["SALT"] .'</pre-sign>';
            		     $xml .= '<client-info language="' . $obj_TxnInfo->getLanguage() . '" platform="' . $obj_TxnInfo->getMarkupLanguage() . '"';
            		     if ($obj_TxnInfo->getProfileID() !== '') { $xml .= ' profileid="'.$obj_TxnInfo->getProfileID().'"'; }
            		     $xml .= '>';
                         $xml .= '<mobile operator-id="' . (int)$obj_TxnInfo->getOperator() . '" country-id="' . (int)$obj_TxnInfo->getOperator()/100 . '">' . $obj_TxnInfo->getMobile() . '</mobile>';
                         $xml .= '<email>' . $obj_TxnInfo->getEMail() . '</email>';
                         $xml .= '<customer-ref>' . $obj_TxnInfo->getCustomerRef() . '</customer-ref>';
                         $xml .= '<device-id>' . $obj_TxnInfo->getDeviceID() . '</device-id>';
                         $xml .= '</client-info>';
                         $xml .= $sTxnAdditionalDataXml;
                         $aShippingAddress = $obj_TxnInfo->getBillingAddr();
                         if (empty($aShippingAddress) === false)
                         {
                             $obj_CountryConfig = CountryConfig::produceConfig($this->getDBConn(), (integer)$aShippingAddress['country']);
                             $xml .= '<address>';
                             $xml .= '<first-name>' . $aShippingAddress['first_name'] . '</first-name>';
                             $xml .= '<last-name>' . $aShippingAddress['last_name'] . '</last-name>';
                             $xml .= '<street>' . $aShippingAddress['street'] . '</street>';
                             $xml .= '<street2>' . $aShippingAddress['street2'] . '</street2>';
                             $xml .= '<postal-code>' . $aShippingAddress['zip'] . '</postal-code>';
                             $xml .= '<city>' . $aShippingAddress['city'] . '</city>';
                             $xml .= '<state>' . $aShippingAddress['state'] . '</state>';
                             if (($obj_CountryConfig instanceof CountryConfig) === true)
                             {
                                 $xml .= '<country>';
                                 $xml .= '<name>' . $obj_CountryConfig->getName() . '</name>';
                                 $xml .= '<code>' . $obj_CountryConfig->getNumericCode() . '</code>';
                                 $xml .= '<alpha2code>' . $obj_CountryConfig->getAlpha2code() . '</alpha2code>';
            		     		 $xml .= '<alpha3code>' . $obj_CountryConfig->getAlpha3code() . '</alpha3code>';
            		     		 $xml .= '</country>';
                             }
                             if (empty($aShippingAddress['mobile']) === false)
                             {
                                 $obj_MobileCountryConfig = CountryConfig::produceConfig($this->getDBConn(), (integer)$aShippingAddress['mobile_country_id']);
                                 $xml .= '<mobile idc="' . $obj_MobileCountryConfig->getCountryCode() .'">' . $aShippingAddress['mobile'] . '</mobile>';
                             }
                             if (empty($aShippingAddress['email']) === false){ $xml .= '<email>' . $aShippingAddress['email'] . '</email>'; }
                             $xml .= '</address>';
                         }
                         $xml .= '</transaction>';

                         if ( ($objCountryConf instanceof CountryConfig) === true)
                         {
                             $iAccountID = $obj_TxnInfo->getAccountID();

                             $cardsSql = "SELECT EC.id, EC.cardid, EC.mask, EC.expiry FROM EndUser".sSCHEMA_POSTFIX.".Card_Tbl EC
                                          WHERE EC.accountid = $iAccountID AND EC.enabled = '1'
                                          ORDER BY EC.created DESC LIMIT 1";
                             $resultSet = $this->getDBConn()->getName($cardsSql);
                             if (empty($resultSet) === false)
                             {
                                 $xml .= '<stored-card>';
                                 $xml .= '<card-id>' . $resultSet['ID'] . '</card-id>';
                                 $xml .= '<card-mask>' . $resultSet['MASK'] . '</card-mask>';
                                 $xml .= '<card-expiry>' . $resultSet['EXPIRY'] . '</card-expiry>';
                                 $xml .= '<card-type>' . $resultSet['CARDID'] . '</card-type>';
                                 $xml .= '</stored-card>';
                             }
            		     }
            		}
            		else { trigger_error("Txn Id : ". $txnid. " doesn't belongs to the client: ". $clientid, E_USER_NOTICE); }
            }
        }
        catch (mPointException $e) { return $xml; }
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
		$sql = "SELECT EUT.id, EUT.typeid, EUT.toid, EUT.fromid, Extract('epoch' from EUT.created AT TIME ZONE 'Europe/Copenhagen') AS timestamp, Txn.captured AS capturedamount,
					(CASE
					 WHEN EUT.amount = 0 THEN Txn.amount
					 WHEN EUT.amount IS NULL THEN Txn.amount
					 ELSE abs(EUT.amount)
					 END) AS amount,
					(CASE
					WHEN Txn.fee > 0 THEN Txn.fee
					ELSE Abs(EUT.fee)
					END) AS fee,
					 EUT.address, EUT.message, EUT.stateid, 
					(CASE
					 WHEN M4.stateid IS NOT NULL THEN M4.stateid
					 WHEN M3.stateid IS NOT NULL THEN M3.stateid
					 WHEN M2.stateid IS NOT NULL THEN M2.stateid
					 WHEN M1.stateid IS NOT NULL THEN M1.stateid
					 END) AS messagestateid,
					(CASE
					 WHEN EUT.typeid = ". Constants::iPURCHASE_USING_EMONEY ." THEN Txn.ip
					 WHEN EUT.typeid = ". Constants::iPURCHASE_USING_POINTS ." THEN Txn.ip
					 WHEN EUT.typeid = ". Constants::iCARD_PURCHASE_TYPE ." THEN Txn.ip
					 ELSE EUT.ip
					 END) AS ip,
					C.id AS countryid, CT.id as currency, CT.symbol, C.priceformat,
					CL.id AS clientid, CL.name AS client,
					(EUAT.firstname || ' ' || EUAT.lastname) AS to_name, EUAT.countryid AS to_countryid, EUAT.mobile AS to_mobile, EUAT.countryid AS to_m, EUAT.email AS to_email,
					(EUAF.firstname || ' ' || EUAF.lastname) AS from_name, EUAF.countryid AS from_countryid, EUAF.mobile AS from_mobile, EUAF.email AS from_email,
					Txn.id AS mpointid, Txn.orderid, Txn.cardid, Card.name AS card
				FROM EndUser".sSCHEMA_POSTFIX.".Transaction_Tbl EUT
				LEFT OUTER JOIN EndUser".sSCHEMA_POSTFIX.".Account_Tbl EUAT ON EUT.toid = EUAT.id
				LEFT OUTER JOIN EndUser".sSCHEMA_POSTFIX.".Account_Tbl EUAF ON EUT.fromid = EUAF.id
				LEFT OUTER JOIN Log".sSCHEMA_POSTFIX.".Transaction_Tbl Txn ON EUT.txnid = Txn.id
				LEFT OUTER JOIN Log".sSCHEMA_POSTFIX.".message_tbl M1 ON Txn.id = M1.txnid AND M1.stateid = ". Constants::iPAYMENT_ACCEPTED_STATE ."
				LEFT OUTER JOIN Log".sSCHEMA_POSTFIX.".message_tbl M2 ON Txn.id = M2.txnid AND M2.stateid = ". Constants::iPAYMENT_CAPTURED_STATE ."
				LEFT OUTER JOIN Log".sSCHEMA_POSTFIX.".message_tbl M3 ON Txn.id = M3.txnid AND M3.stateid = ". Constants::iPAYMENT_REFUNDED_STATE ."
				LEFT OUTER JOIN Log".sSCHEMA_POSTFIX.".message_tbl M4 ON Txn.id = M4.txnid AND M4.stateid = ". Constants::iPAYMENT_CANCELLED_STATE ."
				LEFT OUTER JOIN Client".sSCHEMA_POSTFIX.".Client_Tbl CL ON Txn.clientid = CL.id
				LEFT OUTER JOIN System".sSCHEMA_POSTFIX.".Country_Tbl C ON Txn.countryid = C.id
				LEFT OUTER JOIN System".sSCHEMA_POSTFIX.".Currency_tbl CT ON CT.id = C.currencyid
				LEFT OUTER JOIN System".sSCHEMA_POSTFIX.".Card_Tbl Card ON Txn.cardid = Card.id
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
			if ($RS["CAPTUREDAMOUNT"] > 0) { $RS["AMOUNT"] = $RS["CAPTUREDAMOUNT"]; }
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
					$xml .= '<amount country-id="'. $RS["COUNTRYID"] .'" currency="'. $RS["CURRENCY"] .'" symbol="'. utf8_encode($RS["SYMBOL"]) .'" format="'. $RS["PRICEFORMAT"] .'">'. $RS["AMOUNT"] .'</amount>';
					$xml .= '<price>'. General::formatAmount($this->_obj_CountryConfig, abs($RS["AMOUNT"]) ) .'</price>';
					$xml .= '<fee country-id="'. $RS["COUNTRYID"] .'" currency="'. $RS["CURRENCY"] .'" symbol="'. utf8_encode($RS["SYMBOL"]) .'" format="'. $RS["PRICEFORMAT"] .'">'. $RS["FEE"] .'</fee>';
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
				$xml .= '<amount country-id="'. $this->_obj_CountryConfig->getID() .'" currency="'. $this->_obj_CountryConfig->getCurrency() .'" symbol="'. utf8_encode($this->_obj_CountryConfig->getSymbol() ) .'" format="'. $this->_obj_CountryConfig->getPriceFormat() .'">'. $RS["AMOUNT"] .'</amount>';
				$xml .= '<price>'. General::formatAmount($this->_obj_CountryConfig, $RS["AMOUNT"]) .'</price>';
				$xml .= '<fee country-id="'. $this->_obj_CountryConfig->getID() .'" currency="'. $this->_obj_CountryConfig->getCurrency() .'" symbol="'. utf8_encode($this->_obj_CountryConfig->getSymbol() ) .'" format="'. $this->_obj_CountryConfig->getPriceFormat() .'">'. $RS["FEE"] .'</fee>';
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
				$xml .= '<transaction id="'. $RS["ID"] .'" type-id="'. $RS["TYPEID"] .'" mpoint-id="'. $RS["MPOINTID"] .'" order-no="'. htmlspecialchars($RS["ORDERID"], ENT_NOQUOTES) .'" state-id="'. $RS["MESSAGESTATEID"] .'">';
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
					$xml .= '<amount country-id="'. $RS["COUNTRYID"] .'" currency="'. $RS["CURRENCY"] .'" symbol="'. utf8_encode($RS["SYMBOL"]) .'" format="'. $RS["PRICEFORMAT"] .'">'. $RS["AMOUNT"] .'</amount>';
					$xml .= '<price>'. General::formatAmount($this->_obj_CountryConfig, abs($RS["AMOUNT"]) ) .'</price>';
					$xml .= '<fee country-id="'. $RS["COUNTRYID"] .'" currency="'. $RS["CURRENCY"] .'" symbol="'. utf8_encode($RS["SYMBOL"]) .'" format="'. $RS["PRICEFORMAT"] .'">'. $RS["FEE"] .'</fee>';
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
		$sql = "UPDATE EndUser".sSCHEMA_POSTFIX.".Account_Tbl
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
		$sql = "UPDATE EndUser".sSCHEMA_POSTFIX.".Account_Tbl
				SET firstname = '". $this->getDBConn()->escStr($fn) ."', lastname = '". $this->getDBConn()->escStr($ln) ."'
				WHERE id = ". intval($id);
//		echo $sql ."\n";

		return is_resource($this->getDBConn()->query($sql) );
	}

   	/**
	 * Saves the specified Mobile Number for the End-User Account.
	 *
	 * @param	integer $id 	Unqiue ID of the End-User's Account
	 * @param	string $mob 	The End-User's new Mobile Number (MSISDN) which should be saved to the account. Set to NULL to clear.
	 * @return	boolean
	 */
	public function saveMobile($id, $mob, $miv=true)
	{
		$sql = "UPDATE EndUser".sSCHEMA_POSTFIX.".Account_Tbl
				SET mobile = ". (is_null($mob) === true ? "NULL" : "'". floatval($mob) ."'") .",
					mobile_verified = '". intval($miv) ."'
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
     * @param integer $cid ID of the country the End-User Account should be created in
     * @param string $mob End-User's mobile number (optional)
     * @param string $pwd Password for the created End-User Account (optional)
     * @param string $email End-User's e-mail address (optional)
     * @param string $cr the Client's Reference for the Customer (optional)
     * @param string $pid
     * @param bool $enable
     * @param string $profileid
     * @return    integer        The unique ID of the created End-User Account
     * @throws SQLQueryException
     */
	public function newAccount($cid, $mob = '', $pwd = '', $email = '', $cr = '', $pid = '', $enable = true, $profileid = '')
	{
		$sql = "SELECT Nextvalue('EndUser".sSCHEMA_POSTFIX.".Account_Tbl_id_seq') AS id FROM DUAL";
		$RS = $this->getDBConn()->getName($sql);
		$sql = "INSERT INTO EndUser".sSCHEMA_POSTFIX.".Account_Tbl
					(id, countryid, mobile, passwd, email, externalid, pushid, enabled, profileid)
				VALUES
					(". $RS["ID"] .", ". (int)$cid .", ". ((float)$mob > 0 ? "'". (float)$mob ."'" : "NULL") .", '". $this->getDBConn()->escStr($pwd) ."', ". ($email !== '' ? "'". $this->getDBConn()->escStr($email) ."'" : "NULL") .", '". $this->getDBConn()->escStr($cr) ."', ". ($pid != '' ? "'". $this->getDBConn()->escStr($pid) ."'" : "NULL") . ($enable == false ? ", false" : ", true") . ($profileid !== '' ? ", '". $profileid . "'" : ", NULL").")";
		//echo $sql ."\n";
		$res = $this->getDBConn()->query($sql);

		return $RS["ID"];
	}


    public function saveProfile(ClientConfig $obj_ClientConfig, $cid, $mob, $email="", $cr="", $guestUser="true", $validated="false")
    {
        $aURLInfo = parse_url($obj_ClientConfig->getMESBURL());

        $obj_ConnInfo = new HTTPConnInfo($aURLInfo["scheme"], $aURLInfo["host"], $aURLInfo["port"], 120, Constants::sSaveProfileEndPoint, "POST", "text/xml", $obj_ClientConfig->getUsername(), $obj_ClientConfig->getPassword() );


        $b = '<?xml version="1.0" encoding="UTF-8"?>';
        $b .= '<root>';
        $b .= '<save-customer-profile>';
        $countryID = $cid > 0 ? $cid : $obj_ClientConfig->getCountryConfig()->getID();
        if (strlen($cr) > 0) {
            $b .= '<profile guest="' . $guestUser . '" country-id="' . $countryID . '" external-id="'.$cr.'">';
        } else {
            $b .= '<profile guest="' . $guestUser . '" country-id="' . $cid . '">';
        }
        if($guestUser=="true") {
            if($obj_ClientConfig->getAdditionalProperties(Constants::iInternalProperty,"PROFILE_EXPIRY") > 0) {
                $profileExpiryDays = (integer) $obj_ClientConfig->getAdditionalProperties(Constants::iInternalProperty,"PROFILE_EXPIRY");
            } else {
                $profileExpiryDays = Constants::iProfileExpiry;
            }
            $b .= '<expiry-date>'.date('Y-m-d', strtotime("+$profileExpiryDays day")).'</expiry-date>';
        }
        if(floatval($mob) > 0) {
            $b .= '<mobile country-id="' . $cid . '" validated="'.$validated.'">' . floatval($mob) . '</mobile>';
        }
        if(strlen($email) > 0) {
            $b .= '<email validated="'.$validated.'">' . $email . '</email>';
        }
        $b .= '</profile>';
        $b .= '</save-customer-profile>';
        $b .= '</root>';

        try
        {
            $obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
            $obj_HTTP->connect();

            $h = trim($this->constHTTPHeaders()) .HTTPClient::CRLF;
            $h .= "X-CPM-client-id: ". $obj_ClientConfig->getID(). HTTPClient::CRLF;
            $obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
            $obj_HTTP->connect();
            $HTTPResponseCode = $obj_HTTP->send($h, $b);
            $response = simplexml_load_string($obj_HTTP->getReplyBody());

            if(intval($HTTPResponseCode) == 200 && count($response->{'save-customer-profile'}->{'profile'}) > 0)
            {
                $profileid=(string)$response->{'save-customer-profile'}->{'profile'}["id"];
                return trim($profileid);
            }
            else
            {
                trigger_error("mProfile save profile response HTTP Code: ". $HTTPResponseCode. " and body: ". $obj_HTTP->getReplyBody(), E_USER_NOTICE);
            }
        }
        catch (HTTPException $e)
        {
            trigger_error("mProfile Save Profile Service at: ". $obj_ConnInfo->toURL() ." is unavailable due to ". get_class($e), E_USER_NOTICE);
        }
        return '';
    }


    public function getProfile(ClientConfig $obj_ClientConfig, $cid, $mob, $email="", $cr="")
    {
        $aURLInfo = parse_url($obj_ClientConfig->getMESBURL());

        $obj_ConnInfo = new HTTPConnInfo($aURLInfo["scheme"], $aURLInfo["host"], $aURLInfo["port"], 120, Constants::sGetProfileEndPoint, "POST", "text/xml", $obj_ClientConfig->getUsername(), $obj_ClientConfig->getPassword() );


        $b = '<?xml version="1.0" encoding="UTF-8"?>';
        $b .= '<root>';
        $b .= '<get-customer-profile client-id="'. $obj_ClientConfig->getID() .'"/>';
        $b .= '</root>';

        try
        {
            $obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
            $obj_HTTP->connect();

            $h = trim($this->constHTTPHeaders()) .HTTPClient::CRLF;
            $h .= "X-CPM-client-id: ". $obj_ClientConfig->getID(). HTTPClient::CRLF;
            $h .= "X-CPM-token:". $obj_ClientConfig->getAdditionalProperties(Constants::iInternalProperty,"PROFILE_TOKEN"). HTTPClient::CRLF;
            if(floatval($mob) > 0) {
                $h .= "X-CPM-mobile: ". $mob. HTTPClient::CRLF;
                $countryID = $cid > 0 ? $cid : $obj_ClientConfig->getCountryConfig()->getID();
                $h .= "X-CPM-country-id: ". $countryID. HTTPClient::CRLF;
            }
            if(strlen($email) > 0) {
                $h .= "X-CPM-email: ". $email. HTTPClient::CRLF;
            }
            if (strlen($cr) > 0) {
                $h .= "X-CPM-external-id: ". $cr. HTTPClient::CRLF;
            }

            $obj_HTTP = new HTTPClient(new Template(), $obj_ConnInfo);
            $obj_HTTP->connect();
            $HTTPResponseCode = $obj_HTTP->send($h, $b);
            $response = simplexml_load_string($obj_HTTP->getReplyBody());

            if(intval($HTTPResponseCode) == 200 && count($response->{'get-profile'}->{'profile'}) > 0)
            {
                $profileid= $response->{'get-profile'}->{'profile'}["id"];
                return trim($profileid);
            }
            else
            {
                trigger_error("mProfile get profile response HTTP Code: ". $HTTPResponseCode. " and body: ". $obj_HTTP->getReplyBody(), E_USER_NOTICE);
            }
        }
        catch (HTTPException $e)
        {
            trigger_error("mProfile get Profile Service at: ". $obj_ConnInfo->toURL() ." is unavailable due to ". get_class($e), E_USER_NOTICE);
        }
        return '';
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
		$sql = "INSERT INTO EndUser".sSCHEMA_POSTFIX.".Activation_Tbl
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
				FROM EndUser".sSCHEMA_POSTFIX.".Activation_Tbl
				WHERE accountid = ". intval($id) ." AND code = ". intval($code);
//		echo $sql ."\n";
		$RS = $this->getDBConn()->getName($sql);

		if (is_array($RS) === false) { $iStatus = 1; }		// Activation Code not found for Account
		elseif ($RS["EXPIRY"] < time() ) { $iStatus = 2; }	// Activation Code expired
		elseif ($RS["ACTIVE"] === true) { $iStatus = 3; }	// Activation Code already consumed
		elseif ($RS["ENABLED"] === false) { $iStatus = 4; }	// Activation Code disabled
		else
		{
			$sql = "UPDATE EndUser".sSCHEMA_POSTFIX.".Activation_Tbl
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
		$sql = "UPDATE EndUser".sSCHEMA_POSTFIX.".Account_Tbl
				SET mobile_verified = true
				WHERE id = ". intval($id);
//		echo $sql ."\n";

		if (is_resource($this->getDBConn()->query($sql) ) === true)
		{
			$sql = "UPDATE EndUser".sSCHEMA_POSTFIX.".Transaction_Tbl
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

	public function newNote($uid, $oid, $msg)
	{
		$sql = "INSERT INTO Log".sSCHEMA_POSTFIX.".Note_Tbl
					(userid, txnid, message)
				VALUES
					(". intval($uid) .", ". intval($oid)  .", '". $this->getDBConn()->escStr($msg) ."')";
		//		echo $sql ."\n";

		return is_resource($this->getDBConn()->query($sql) );
	}
	
	/**
	 * Saves the customer ref number for the End-User Account.
	 *
	 * @param 	string $cr		the Client's Reference for the Customer (optional)
	 * @return	integer 		The unique ID of the created End-User Account
	 * @return	boolean
	 */
	public function saveCustomerReference($id, $cr = '')
	{
		$sql = "UPDATE EndUser".sSCHEMA_POSTFIX.".Account_Tbl
				SET externalid = '".$cr."'
				WHERE id = ". intval($id);
		//		echo $sql ."\n";
	
		return is_resource($this->getDBConn()->query($sql) );
	}

    public function getOrphanAuthorizedTransactionList(int $clientid, string $interval, ?int $pspid = NULL): array
    {
        $sql = "SELECT transactionid as id
                    FROM (
                             SELECT DISTINCT ON (transactionid) transactionid,
                                                                performedopt, 
                                                                passbook.modified
                             FROM log.txnpassbook_tbl passbook
                             {INNER_JOIN}
                             WHERE passbook.clientid = $clientid
                               AND passbook.enabled = true
                               AND passbook.status = '". Constants::sPassbookStatusDone ."'
                               AND performedopt is NOT null
                               AND performedopt <> ". Constants::iINPUT_VALID_STATE ."
                               AND passbook.created > now() - INTERVAL '1 DAY' - INTERVAL '$interval'                                
                               {INNER_JOIN_CONDITION}
                             ORDER BY transactionid, passbook.created DESC                             
                         ) sub
                    WHERE performedopt = ". Constants::iPAYMENT_ACCEPTED_STATE ." and modified < now() - INTERVAL '$interval' ";

        if (isset($pspid) === FALSE) {
            $sql = str_replace(['{INNER_JOIN}','{INNER_JOIN_CONDITION}'],'', $sql);
        } else {
            $sql = str_replace('{INNER_JOIN}'," INNER JOIN log.transaction_tbl txn on passbook.transactionid = txn.id and pspid= $pspid ", $sql);
            $sql = str_replace(['{INNER_JOIN}','{INNER_JOIN_CONDITION}']," AND txn.clientid = $clientid AND txn.enabled = true AND txn.created > now() - INTERVAL '$interval' - INTERVAL '1 DAY' ", $sql);
        }
        $aTransactionIds = $this->getDBConn()->getAllNames($sql);
        if (is_array($aTransactionIds)) {
            return $aTransactionIds;
        }

        return [];
    }

    public function getAutoVoidConfig(?int $clientid = NULL, ?int $pspid = NULL): array
    {
        $sql = 'SELECT clientid, pspid, expiry FROM CLIENT' . sSCHEMA_POSTFIX . '.AUTOVOIDCONFIG_TBL WHERE ENABLED=TRUE';
        if ($clientid !== NULL) {
            $sql .= " AND clientid = $clientid";
        }
        if ($pspid !== NULL) {
            $sql .= " AND pspid = $pspid";
        }
        $sql .= ' ORDER BY clientid ASC';

        $aAutoVoidConfig = $this->getDBConn()->getAllNames($sql);
        if (is_array($aAutoVoidConfig)) {
            return $aAutoVoidConfig;
        }
        return [];
    }


}

?>