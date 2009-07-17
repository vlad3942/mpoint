<?php
/**
 * The Validate package provides methods for performing basic input validation.
 * The logic of classes containted in this package is utilised by all mPoint APIs to ensure that the data received from the client appears
 * to be useful.
 * The package contains a range of general validation methods, including:
 * 	- valEMail
 * 	- valUsername
 * 	- valPassword
 * 	- valName
 * 	- valURL
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Validate
 * @version 1.0
 */

/**
 * Validation Class containing the shared Business Logic for ensuring that the data received from a client appears to be useful.
 *
 */
class Validate
{
	/**
	 * Data object with the Country's default configuration
	 *
	 * @var CountryConfig
	 */
	private $_obj_CountryConfig;

	/**
	 * Default Constructor.
	 *
	 * @param 	CountryConfig $oCC 	Data object with the Country's default configuration
	 */
	public function __construct(CountryConfig &$oCC=null)
	{
		$this->_obj_CountryConfig = $oCC;
	}

	/**
	 * Performs basic validation ensuring that the client exists and the account is valid.
	 * The method will return the following status codes:
	 * 	 1. Undefined Client ID
	 * 	 2. Invalid Client ID
	 * 	 3. Unknown Client ID
	 * 	 4. Client Disabled
	 * 	11. Undefined Account
	 * 	12. Invalid Account
	 * 	13. Unknown Account
	 * 	14. Account Disabled
	 * 	100. Success
	 *
	 * @param 	RDB $oDB 		Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param 	integer $id 	Unique ID for the Client performing the request
	 * @param 	integer $acc 	Unique ID or Account Number that the transaction should be associated with, set to -1 to use the default account
	 * @return 	integer
	 */
	public static function valBasic(RDB &$oDB, $id, $acc)
	{
		if (empty($id) === true) { $code = 1; }			// Undefined Client ID
		elseif (intval($id) < 10000) { $code = 2; }		// Invalid Client ID
		elseif (empty($acc) === true) { $code = 11; }	// Undefined Account
		elseif (intval($acc) < -1 || (intval($acc) >= 1000 && intval($acc) < 100000) || intval($acc) == 0) { $code = 12; }	// Invalid Account
		else
		{
			$acc = (integer) $acc;
			$sql = "SELECT CL.id AS clientid, Cl.enabled AS clientactive,
						Acc.id AS accountid, Acc.enabled AS accountactive
					FROM Client.Client_Tbl Cl
					LEFT OUTER JOIN Client.Account_Tbl Acc ON Cl.id = Acc.clientid
					WHERE Cl.id = ". intval($id);
			// Use Default Account
			if ($acc == -1)
			{
				$sql .= "
						ORDER BY Acc.id ASC
						LIMIT 1";
			}
			// Use Account Number
			elseif ($acc < 1000)
			{
				$sql .= "
						ORDER BY Acc.id ASC
						LIMIT 1 OFFSET ". $acc;
			}
			// Use Account ID
			else
			{
				$sql .= " AND Acc.id = ". $acc;
			}
			$RS = $oDB->getName($sql);

			if (is_array($RS) === true)
			{
				if ($RS["CLIENTACTIVE"] === false) { $code = 4; }		// Client Disabled
				elseif (intval($RS["ACCOUNTID"]) == 0) { $code = 13; }	// Unkown Account
				elseif ($RS["ACCOUNTACTIVE"] == false) { $code = 14; }	// Account Disabled
				else { $code = 100; }
			}
			else { $code = 3; }	// Unknown Client ID
		}

		return $code;
	}

	/**
	 * Performs basic validation ensuring that the Country exists.
	 * The method will return the following status codes:
	 * 	 1. Undefined Country ID
	 * 	 2. Invalid Country ID
	 * 	 3. Unknown Country ID
	 * 	 4. Country Disabled
	 * 	10. Success
	 *
	 * @param 	RDB $oDB 		Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param 	integer $id 	Unique ID for the Country that should be validated
	 * @return 	integer
	 */
	public function valCountry(RDB &$oDB, $id)
	{
		if (empty($id) === true) { $code = 1; }				// Undefined Country ID
		elseif (intval($id) < 100) { $code = 2; }			// Invalid Country ID
		else
		{
			$sql = "SELECT enabled
					FROM System.Country_Tbl
					WHERE id = ". intval($id);
			$RS = $oDB->getName($sql);

			if (is_array($RS) === false) { $code = 3; }		// Unknown Country
			elseif ($RS["ENABLED"] === false) { $code = 4; }// Country Disabled
			else { $code = 10; }							// Success
		}

		return $code;
	}

	/**
	 * Validates that a Username has a valid format.
	 * The method will return the following status codes:
	 * 	 1. Undefined Username
	 * 	 2. Username is too short, as defined by iAUTH_MIN_LENGTH
	 * 	 3. Username is too long, as defined by iAUTH_MAX_LENGTH
	 *   4. Username contains invalid characters: [^a-z0-9 ����������.-]
	 * 	10. Success
	 *
	 * @see		Constants::iAUTH_MIN_LENGTH
	 * @see 	Constants::iAUTH_MAX_LENGTH
	 *
	 * @param	string $un 	Username to validate
	 * @return	integer
	 */
	public function valUsername($un)
	{
		$un = trim($un);
		// Validate Username
		if (empty($un) === true){ $code = 1; }											// Username is undefined
		elseif (strlen($un) < Constants::iAUTH_MIN_LENGTH) { $code = 2; }				// Username is too short
		elseif (strlen($un) > Constants::iAUTH_MAX_LENGTH) { $code = 3; }				// Username is too long
		elseif (eregi("[^a-z0-9 ����������.-]", utf8_encode($un) ) == true) { $code = 4; }	// Username contains Invalid Characters
		else { $code = 10; }															// Username is valid

		return $code;
	}

	/**
	 * Validates that a Password has a valid format.
	 * The method will return the following status codes:
	 * 	 1. Undefined Password
	 * 	 2. Password is too short, as defined by iAUTH_MIN_LENGTH
	 * 	 3. Password is too long, as defined by iAUTH_MAX_LENGTH
	 *   4. Password contains invalid characters: [\"']
	 *	10. Success
	 *
	 * @see		Constants::iAUTH_MIN_LENGTH
	 * @see		Constants::iAUTH_MAX_LENGTH
	 *
	 * @param	string $pwd 	Password to validate
	 * @return	integer
	 */
	public function valPassword($pwd)
	{
		$pwd = trim($pwd);
		// Validate Password
		if (empty($pwd) === true) { $code = 1; }							// Password is undefined
		elseif (strlen($pwd) < Constants::iAUTH_MIN_LENGTH) { $code = 2; }	// Password is too short
		elseif (strlen($pwd) > Constants::iAUTH_MAX_LENGTH) { $code = 3; }	// Password is too long
		elseif (ereg("[\"']", $pwd) == true) { $code = 4; }					// Password contains Invalid Characters
		else { $code = 10; }												// Password is valid

		return $code;
	}

	/**
	 * Validates that an E-Mail address has a valid format.
	 * The method will return the following status codes:
	 * 	 1. Undefined E-Mail address
	 * 	 2. E-Mail address is too short, as defined by iAUTH_MIN_LENGTH
	 * 	 3. E-Mail address is too long, as defined by iAUTH_MAX_LENGTH
	 *   4. E-Mail address contains invalid characters: [^0-9a-z����������_.@-]
	 *   5. E-Mail has an invalid form: ^[^@ ]+@[^@ ]+\.[^@ \.]+$
	 *	10. Success
	 *
	 * @see		Constants::iAUTH_MIN_LENGTH
	 * @see		Constants::iAUTH_MAX_LENGTH
	 *
	 * @param	string $email 	E-Mail address to validate
	 * @return	integer
	 */
	public function valEMail($email)
	{
		$email = trim($email);
		// Validate E-Mail
		if (empty($email) === true) { $code = 1; }								// E-Mail is undefined
		elseif (strlen($email) < Constants::iAUTH_MIN_LENGTH) { $code = 2; }	// E-Mail is too short
		elseif (strlen($email) > Constants::iAUTH_MAX_LENGTH) { $code = 3; }	// E-Mail is too long
		elseif (eregi("[^0-9a-z����������_.@-]", $email) == true) { $code = 4; }// E-Mail contains Invalid Characters
		elseif (ereg("^[^@]+@[^@]+\.[^@\.]+$", $email) == false) { $code = 5; }	// E-Mail has an invalid form
		else { $code = 10; }													// E-Mail is valid

		return $code;
	}

	/**
	 * Validates that a Name has a valid format.
	 * The method will return the following status codes:
	 * 	 1. Undefined Name
	 * 	 2. Name is too short, as defined by iAUTH_MIN_LENGTH
	 * 	 3. Name is too long, must be shorter than 100 characters
	 *   4. Name contains invalid characters: [^0-9a-z����������_.@-]
	 * 	10. Success
	 *
	 * @see		Constants::iAUTH_MIN_LENGTH
	 * @see		General::valUsername()
	 *
	 * @param	string $name 	Name to validate
	 * @return	integer
	 */
	public function valName($name)
	{
		$code = $this->valPassword($name);
		/**
		 * Name succesfully validated by valUsername or valUsername returned "username too long"
		 * but name is less than database retriction
		 */
		if ($code == 10 || ($code == 3 && strlen($name) < 100) ) { $code = 10; }

		return $code;
	}

	/**
	 * Validates that the Mobile Number is a valid MSISDN for the client's country.
	 * The method will return the following status codes:
	 * 	 1. Undefined Mobile Number
	 * 	 2. Mobile Number is too short, as defined by the database field: minmob for the Country
	 * 	 3. Mobile Number is too long, as defined by the database field: maxmob for the Country
	 * 	10. Success
	 *
	 * @see 	CountryInfo::getMinMobile()
	 * @see 	CountryInfo::getMaxMobile()
	 *
	 * @param 	string $mob 	The Mobile Number (MSISDN) which should be validated, please note this should be treated as a 64-bit Integer
	 * @return 	integer
	 */
	public function valMobile($mob)
	{
		$mob = trim($mob);
		// Validate Recipient's Mobile Number
		if (empty($mob) === true) { $code = 1; }											// Recipient is undefined
		elseif (floatval($mob) < $this->_obj_CountryConfig->getMinMobile() ) { $code = 2; }	// Recipient is too short
		elseif (floatval($mob) > $this->_obj_CountryConfig->getMaxMobile() ) { $code = 3; }	// Recipient is too long
		else { $code = 10; }

		return $code;
	}

	/**
	 * Performs basic validation of the recipient's Mobile Network Operator ensuring that there's a fair chance
	 * the Operator is available in the client's country.
	 * Please note that the validation if very rudimentary and does not ensure that the Opeator is available in GoMobile.
	 * The method will return the following status codes:
	 * 	 1. Undefined Operator ID
	 * 	 2. Operator ID is too short, the ID is lesser than Country ID * 1000
	 * 	 3. Operator ID is too big, the ID is greater than Country ID * 1000 + 99
	 * 	10. Success
	 * Please refer to: GoMobile - Overview for details on which Mobile Network Operators are available in each Country.
	 *
	 * @see 	CountryInfo::getID()
	 *
	 * @param 	integer $id 	GoMoble's Unique ID for the Recipient's Mobile Network Operator
	 * @return 	integer
	 */
	public function valOperator($id)
	{
		// Validate the Recipient's Mobile Network Operator
		if (empty($id) === true) { $code = 1; }																		// Operator ID is undefined
		elseif (floatval($id) < $this->_obj_CountryConfig->getID() * 100) { $code = 2; }		// Operator ID is too small
		elseif (floatval($id) > $this->_obj_CountryConfig->getID() * 100 + 99) { $code = 3; }	// Operator ID is too big
		else { $code = 10; }

		return $code;
	}

	/**
	 * Validates the total Amount the customer is paying.
	 * The method will return the following status codes:
	 * 	 1. Undefined Amount
	 * 	 2. Amount is too small, amount must be greater than 1 (0,01 of the country's currency)
	 * 	 3. Amount is too great, amount must be smaller than the max amount specified by the client
	 * 	10. Success
	 *
	 * @param 	integer $max 	Maximum amount allowed for the Client
	 * @param 	integer $prc 	The price of the merchandise the customer is buying in the country's smallest currency (cents for USA, �re for Denmark etc.)
	 * @return 	integer
	 */
	public function valAmount($max, $prc)
	{
		// Validate the total Amount the customer will be paying
		if (empty($prc) === true) { $code = 1; }	// Amount is undefined
		elseif (intval($prc) < 1) { $code = 2; }	// Amount is too small
		elseif (intval($prc) > intval($max) ) { $code = 3; }	// Amount is too great
		else { $code = 10; }

		return $code;
	}

	/**
	 * Validates the URL appears to be useful.
	 * Please note that the method will only validate the format of the URL, it will not actually make lookups
	 * to ensure that the domain can be found and the file referenced by the URL exists on the remote server.
	 * The method will return the following status codes:
	 * 	 1. Undefined URL
	 * 	 2. URL is too short, min length is the length of ftp://d.dk
	 * 	 3. URL is too long, max length is 255 characters
	 * 	 4. URL is malformed
	 * 	 5. URL is Invalid, no Protocol specified
	 * 	 6. URL is Invalid, no Host specified
	 * 	 7. URL is Invalid, no Path specified
	 * 	10. Success
	 *
	 * @param 	integer $url 	The URL that should be validated
	 * @return 	integer
	 */
	public function valURL($url)
	{
		// Validate the total Amount the customer will be paying
		if (empty($url) === true) { $code = 1; }					// URL is undefined
		elseif (strlen($url) < strlen("ftp://d.dk") ) { $code = 2; }// URL is too short
		elseif (strlen($url) > 255) { $code = 3; }					// URL is too long
		else
		{
			$aURLInfo = parse_url($url);
			// URL has a valid format
			if (is_array($aURLInfo) === true)
			{
				if (array_key_exists("scheme", $aURLInfo) === false) { $code = 5; }		// Invalid URL, no Protocol specified
				elseif (array_key_exists("host", $aURLInfo) === false) { $code = 6; }	// Invalid URL, no Host specified
				if (array_key_exists("path", $aURLInfo) === false) { $code = 7; }		// Invalid URL, no Path specified
				else { $code = 10; }
			}
			else { $code = 4; } 									// URL is malformed
		}

		return $code;
	}

	/**
	 * Validates the Product Information used to generate the Order Overview page.
	 * The method will return the following status codes:
	 * 	 1. Undefined Product Names (array size = 0)
	 * 	 2. Undefined Product Quantities (array size = 0)
	 * 	 3. Undefined Product Prices (array size = 0)
	 * 	 4. Invalid Arrays sizes (the 3 arrays differs in size)
	 * 	 5. Array key not found in Product Quantities
	 * 	 6. Array key not found in Product Prices
	 * 	 7. Invalid URL found in array of Logo URLs
	 * 	10. Success
	 *
	 * @param 	array $aNames 		Reference to the list of Product Names
	 * @param 	array $aQuantities 	Reference to the list of Product Qantities
	 * @param 	array $aPrices 		Reference to the list of Product Prices
	 * @param 	array $aLogos 		Reference to the list of URLs to the Logo for each Product
	 * @return 	integer
	 */
	public function valProducts(array &$aNames, array &$aQuantities, array &$aPrices, array &$aLogos)
	{
		if (count($aNames) == 0) { $code = 1; }				// Undefined Product Names
		elseif (count($aQuantities) == 0) { $code = 2; }	// Undefined Product Quantities
		elseif (count($aPrices) == 0) { $code = 3; }		// Undefined Product Prices
		elseif (count($aNames) != count($aQuantities) || count($aNames) != count($aPrices) ) { $code = 4; }	// Invalid Arrays sizes
		// Basic Product data appears to be correct
		else
		{
			// Assume that array keys will match
			$code = 10;
			reset($aNames);
			reset($aQuantities);
			reset($aPrices);

			while ( (list($key) = each($aNames) ) && $code == 10)
			{
				if (array_key_exists($key, $aQuantities) === false) { $code = 5; }	// Array key not found in Product Quantities
				elseif (array_key_exists($key, $aPrices) === false) { $code = 6; }	// Array key not found in Product Prices
			}
			// Mandatory Product data appears to be valid
			if ($code == 10)
			{
				reset($aLogos);
				while ( (list($key, $url) = each($aLogos) ) && $code == 10)
				{
					if ($this->valURL($url) != 10) { $code = 7; }					// Invalid Logo URL
				}
			}
		}

		return $code;
	}

	/**
	 * Validates that the language exists.
	 * The method will return the following status codes:
	 * 	 1. Undefined Language
	 * 	 2. Invalid Language, language contains invalid characters which are NOT a-z or _
	 * 	 3. Language not supported (language folder not found)
	 * 	10. Success
	 *
	 * @see 	sLANGUAGE_PATH
	 *
	 * @param 	string $lang 	Language that should be used as the default for the customer's purchase experience
	 * @return 	integer
	 */
	public function valLanguage($lang)
	{
		if (empty($lang) === true) { $code = 1;}							// Undefined Language
		elseif (eregi("[^a-z_]", $lang) == true) { $code = 2; }				// Invalid Language
		elseif (is_dir(sLANGUAGE_PATH . $lang) === false) { $code = 3; }	// Language not supported
		else { $code = 10; }

		return $code;
	}

	/**
	 * Validates the entered postal code for country.
	 * The method will return the following status code:
	 * 	 1. Zip code is Undefined
	 * 	 2. Zip code is too small
	 * 	 3. Zip code is too great
	 * 	 4. Zip code has an invalid length
	 * 	10. Success
	 * The method currently supports the following countries:
	 * 	- Denmark
	 * 	- Sweden
	 * 	- Norway
	 * 	- UK
	 * 	- Finland
	 * 	- USA
	 *
	 * @param 	string $zip 	Entered zip code to validate
	 * @return 	integer
	 *
	 * @throws 	mPointException
	 */
	public function valZipCode($zip)
	{
		if (empty($zip) === true) { $code = 1; }
		else
		{
			switch ($this->_obj_CountryConfig->getID() )
			{
			case (100):	// Denmark
				if (intval($zip) < 800) { $code = 2; }
				elseif (intval($zip) > 9999) { $code = 3; }
				else { $code = 10; }
				break;
			case (101):	// Sweden
				if (intval($zip) < 10000) { $code = 2; }
				elseif (intval($zip) > 99999) { $code = 3; }
				else { $code = 10; }
				break;
			case (102):	// Norway
				if (intval($zip) < 100) { $code = 2; }
				elseif (intval($zip) > 9999) { $code = 3; }
				else { $code = 10; }
				break;
			case (103):	// UK
				if (preg_match('/(GIR 0AA|[A-PR-UWYZ]([0-9]{1,2}|([A-HK-Y][0-9]|[A-HK-Y][0-9]([0-9]|[ABEHMNPRV-Y]))|[0-9][A-HJKS-UW]) [0-9][ABD-HJLNP-UW-Z]{2})/i') == false) { $code = 5; }
				else { $code = 10; }
				break;
			case (104):	// Finland
				if (intval($zip) < 0) { $code = 2; }
				elseif (intval($zip) > 99999) { $code = 3; }
				elseif (strlen($zip) != 5) { $code = 4; }
				else { $code = 10; }
				break;
			case (200):	// USA
				if (intval($zip) < 10000) { $code = 2; }
				elseif (intval($zip) > 99999) { $code = 3; }
				else { $code = 10; }
				break;
			default:	// Error: Invalid Country
				$code = -1;
				throw new mPointException("Invalid Country: ". $this->_obj_CountryConfig->getID(), 1101);
				break;
			}
		}

		return $code;
	}

	/**
	 * Validates that a Delivery date appears sensible.
	 * The method will return the following status code:
	 * 	 1. Year is Undefined
	 * 	 2. Year is in the Past
	 * 	 3. Month is undefined
	 * 	 4. Month is in the Past
	 * 	 5. Day is undefined
	 * 	 6. Day is in the Past
	 * 	 7. Delivery Date is in the Past
	 * 	10. Success
	 *
	 * @param 	integer $year 	Year part of the Delivery Data which should be validated
	 * @param 	integer $month 	Momth part of the Delivery Data which should be validated
	 * @param 	integer $day 	Day part of the Delivery Data which should be validated
	 * @return 	integer
	 */
	public function valDeliveryDate($year, $month, $day)
	{
		if (empty($year) === true) { $code = 1; }
		elseif (date("Y") > intval($year) ) { $code = 2; }
		elseif (empty($month) === true) { $code = 3; }
		elseif (date("m") > intval($month) ) { $code = 4; }
		elseif (empty($day) === true) { $code = 5; }
		elseif (date("d") > intval($day) ) { $code = 6; }
		elseif (strtotime(date("Y-m-d") ) > strtotime($year ."-". $month ."-". $day) ) { $code = 7; }
		else { $code = 10; }

		return $code;
	}

	/**
	 * Validates the ID of the Saved Card
	 * The method will return the following status codes:
	 * 	 1. Undefined Card ID
	 * 	 2. Card ID is too small, amount must be greater than 0
	 * 	 3. Card not found
	 * 	 4. Card disabled
	 * 	10. Success
	 *
	 * @param 	RDB $oDB 		Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param 	integer $id 	Unique ID of the End-User's account
	 * @param 	integer $cid 	Unique ID of the Saved Card which should be validated
	 * @return 	integer
	 */
	public function valStoredCard(RDB &$oDB, $id, $cid)
	{
		if (empty($cid) === true) { $code = 1; }		// Card ID is undefined
		elseif (intval($cid) < 1) { $code = 2; }		// Card ID is too small
		else
		{
			$sql = "SELECT enabled
					FROM EndUser.Card_Tbl
					WHERE accountid = ". intval($id) ." AND id = ". intval($cid);
//			echo $sql ."\n";
			$RS = $oDB->getName($sql);

			if (is_array($RS) === false) { $code = 3;}
			elseif ($RS["ENABLED"] === false) { $code = 4;}
			else { $code = 10; }
		}

		return $code;
	}

	/**
	 * Validates the End-User's prepaid Account
	 * The method will return the following status codes:
	 * 	 1. Undefined Account ID
	 * 	 2. Invalid Account ID
	 * 	 3. Account not found
	 * 	 4. Account disabled
	 * 	 5. Account balance too low
	 * 	10. Success
	 *
	 * @param 	RDB $oDB 		Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param 	integer $id 	Unique ID of the End-User's account
	 * @param 	integer $amount	Total amount for the transaction that the End-User will be charged upon completion
	 * @return 	integer
	 */
	public function valAccount(RDB &$oDB, $id, $amount)
	{
		if (empty($id) === true) { $code = 1; }	// Account ID is undefined
		elseif (intval($id) < 0) { $code = 2; }	// Account ID is invalid
		else
		{
			$sql = "SELECT enabled, balance
					FROM EndUser.Account_Tbl
					WHERE id = ". intval($id);
//			echo $sql ."\n";
			$RS = $oDB->getName($sql);

			if (is_array($RS) === false) { $code = 3;}
			elseif ($RS["ENABLED"] === false) { $code = 4;}
			elseif ($RS["BALANCE"] < $amount) { $code = 5;}
			else { $code = 10; }
		}

		return $code;
	}
	
	/**
	 * Validates the Activation Code.
	 * The method will return the following status codes:
	 * 	 1. Undefined Activation Code
	 * 	 2. Activation Code is too small, min value is 100.000
	 * 	 3. Activation Code is too great, max value is 999.999
	 * 	10. Success
	 *
	 * @param 	integer $ac		Activation code which should be validated
	 * @return 	integer
	 */
	public function valCode($ac)
	{
		if (empty($ac) === true) { $code = 1; }			// Activation Code is undefined
		elseif (intval($ac) < 100000) { $code = 2; }	// Activation Code is too small
		elseif (intval($ac) > 999999) { $code = 3; }	// Activation Code is too great
		else { $code = 10; }

		return $code;
	}
}
?>
