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
 * @subpackage MyAccount
 * @license Cellpoint Mobile
 */

/**
 *
 *
 */
class MyAccount extends Home
{
	/**
	 * Generates and sends an Activation Code to the End-User using the provided Mobile Number (MSISDN).
	 *
	 * @see		GoMobileMessage::produceMessage()
	 * @see		General::getText()
	 * @see		Home::genActivationCode()
	 * @see		Home::sendMessage()
	 * @see		ClientConfig::produceConfig()
	 *
	 * @param 	GoMobileConnInfo $oCI 	Reference to the data object with the Connection Info required to communicate with GoMobile
	 * @param	integer $id 			Unqiue ID of the End-User's Account
	 * @param	string $mob 			End-User's mobile number
	 * @return	integer
	 * @throws 	mPointException
	 */
	public function sendCode(GoMobileConnInfo &$oCI, $id, $mob)
	{
		$sBody = $this->getText()->_("mPoint - Send Activation Code");
		$sBody = str_replace("{CODE}", $this->genActivationCode($id, $mob), $sBody);

		$obj_ClientConfig = ClientConfig::produceConfig($this->getDBConn(), $this->getCountryConfig()->getID(), -1);

		$obj_MsgInfo = GoMobileMessage::produceMessage(Constants::iMT_SMS_TYPE, $this->getCountryConfig()->getID(), $this->getCountryConfig()->getID()*100, $this->getCountryConfig()->getChannel(), $obj_ClientConfig->getKeywordConfig()->getKeyword(), Constants::iMT_PRICE, $mob, utf8_decode($sBody) );

		$iCode = $this->sendMessage($oCI, $obj_ClientConfig, $obj_MsgInfo);
		if ($iCode != 200) { $iCode = 91; }

		return $iCode;
	}

	/**
	 * Fetches the newest activated address for the specified Account ID and Activation Code.
	 * The method will return an empty string if the address couldn't be found
	 *
	 * @param	integer $id 	Unqiue ID of the End-User's Account
	 * @param	integer $code 	Activated code for which the address should be found
	 * @return	boolean
	 */
	public function getActivationAddress($id, $code)
	{
		$sql = "SELECT address
				FROM EndUser".sSCHEMA_POSTFIX.".Activation_Tbl
				WHERE accountid = ". intval($id) ." AND code = ". intval($code) ."
				ORDER BY id DESC
				LIMIT 1";
//		echo $sql ."\n";
		$RS = $this->getDBConn()->getName($sql);

		return is_array($RS) === true ? $RS["ADDRESS"] : "";
	}

	/**
	 * Generates and sends an Activation Code to the End-User using the provided E-Mail Address
	 *
	 * @param	integer $id 	Unqiue ID of the End-User's Account
	 * @param	string $email 	End-User's new E-Mail address
	 * @return	boolean
	 */
	public function sendLink($id, $email)
	{
		$sSubject = $this->getText()->_("mPoint - Activation Link Subject");
		$sBody = $this->getText()->_("mPoint - Activation Link Body");

		$iCode = $this->genActivationCode($id, $email);
		$sURL = "http://". sDEFAULT_MPOINT_DOMAIN ."/home/sys/save_email.php?id=". $id ."&c=". $iCode ."&chk=". md5($id . $iCode . $email);

		$sBody = str_replace("{URL}", $sURL, $sBody);

		return mail($email, $sSubject, $sBody, $this->constSMTPHeaders() );
	}

	/**
	 * Saves the specified E-Mail address for the End-User Account
	 *
	 * @param	integer $id 	Unqiue ID of the End-User's Account
	 * @param 	string $email	End-User's e-mail address, set to NULL to clear
	 * @return	boolean
	 */
	public function saveEMail($id, $email)
	{
		$sql = "UPDATE EndUser".sSCHEMA_POSTFIX.".Account_Tbl
				SET email = ". (is_null($email) === true ? "NULL" : "'". $this->getDBConn()->escStr($email) ."'") ."
				WHERE id = ". intval($id);
//		echo $sql ."\n";

		return is_resource($this->getDBConn()->query($sql) );
	}

	/**
	 * Validates the specified mobile number (MSISDN) against the content of database table: EndUser.Account_Tbl.
	 * The method will return the following status codes:
	 * 	 1. Mobile Number already belongs to the end-user's account
	 * 	 2. Mobile Number already belongs to another end-user's account
	 * 	 3. Mobile Number already belongs to an end-user account which has not yet been activated
	 * 	10. Success
	 *
	 * @param	integer $id		Unqiue ID of the End-User's Account
	 * @param 	string $mob		The End-User's new Mobile Number (MSISDN) which should be validated
	 * @return 	integer
	 */
	public function valMobile($id, $mob)
	{
		$sql = "SELECT id, passwd AS password
				FROM EndUser".sSCHEMA_POSTFIX.".Account_Tbl
				WHERE countryid = ". $this->getCountryConfig()->getID() ." AND mobile = '". floatval($mob) ."'";
//		echo $sql ."\n";
		$RS = $this->getDBConn()->getName($sql);

		if (is_array($RS) === true)
		{
			if ($RS["ID"] == $id) { $code = 1; }
			elseif (empty($RS["PASSWORD"]) === false) { $code = 2; }
			else { $code = 3; }
		}
		else { $code = 10; }

		return $code;
	}

	/**
	 * Validates the specified e-mail address against the content of database table: EndUser.Account_Tbl.
	 * The method will return the following status codes:
	 * 	 1. E-Mail Address already belongs to the end-user's account
	 * 	 2. E-Mail Address already belongs to another end-user's account
	 * 	 3. E-Mail Address already belongs to an end-user account which has not yet been activated
	 * 	10. Success
	 *
	 * @param	integer $id		Unqiue ID of the End-User's Account
	 * @param 	string $email	The End-User's new e-mail address which should be validated
	 * @return 	integer
	 */
	public function valEMail($id, $email)
	{
		$sql = "SELECT id, passwd AS password
				FROM EndUser".sSCHEMA_POSTFIX.".Account_Tbl
				WHERE countryid = ". $this->getCountryConfig()->getID() ." AND Upper(email) = Upper('". $this->getDBConn()->escStr($email) ."')";
//		echo $sql ."\n";
		$RS = $this->getDBConn()->getName($sql);

		if (is_array($RS) === true)
		{
			if ($RS["ID"] == $id) { $code = 1; }
			elseif (empty($RS["PASSWORD"]) === false) { $code = 2; }
			else { $code = 3; }
		}
		else { $code = 10; }

		return $code;
	}

	/**
	 * Validates the Account Information for the specified Transfer Checksum against the provided address .
	 * The method will return the following status codes:
	 * 	 0. Specified Address not registered for Account
	 * 	 1. Account Information doesn't match provided Mobile Number (MSISDN)
	 * 	 2. Account Information doesn't match provided E-Mail Address
	 * 	 9. Invalid Address provided
	 * 	10. Success
	 *
	 * @param 	string $chk 	Transfer Checksum that should be validated
	 * @param 	string $addr	End-User's mobile number or E-Mail address
	 * @return 	integer
	 */
	public function valChecksum($chk, $addr)
	{
		list(, $id) = spliti("Z", $chk);
		$id = base_convert($id, 32, 10);
		$obj_XML = simplexml_load_string($this->getAccountInfo($id) );

		// Mobile Number (MSISDN) provided for validation
		if (floatval($addr) > $this->getCountryConfig()->getMinMobile() )
		{
			// Mobile Number (MSISDN) registered for Account
			if (floatval($obj_XML->mobile) > $this->getCountryConfig()->getMinMobile() )
			{
				if (strval($obj_XML->mobile) != $addr) { $code = 1; }
				else { $code = 10; }
			}
			else { $code = 0; }
		}
		// E-Mail Address provided for validation
		elseif (strstr($addr, "@") == true)
		{
			// E-Mail Address registered for Account
			if (strval($obj_XML->email) != "")
			{
				if (strval($obj_XML->email) != $addr) { $code = 2; }
				else { $code = 10; }
			}
			else { $code = 0; }
		}
		else { $code = 9; }

		return $code;
	}

	/**
	 * Deletes a stored card from an End-User Account.
	 *
	 * @param 	integer $enduserid	Unqiue ID of the End-User's Account
	 * @param 	integer $cardid		Unique ID of the Stored Card that should be deleted
	 * @return 	1 Active transactions
	 * 			2 No matching cards
	 * 			3 Internal Error
	 * 		   10 Success
	 */
	public function delStoredCard($enduserid, $cardid)
	{
		$sql1 = "SELECT Card.id cardid, Cli.transaction_ttl ttl
				 FROM EndUser".sSCHEMA_POSTFIX.".Card_Tbl Card
				 LEFT JOIN Client".sSCHEMA_POSTFIX.".Client_Tbl Cli ON Cli.id = Card.clientid AND Cli.enabled = '1'
				 WHERE Card.id = ". intval($cardid);
//		echo $sql1 ."\n";

		$res1 = $this->getDBConn()->query($sql1);

		if (is_resource($res1) === true )
		{
			$RS = $this->getDBConn()->fetchName($res1);
			if (is_array($RS) === true)
			{
				$iTTL = intval($RS["TTL"]);
				if ($iTTL > 0)
				{
					$obj_Status = new Status($this->getDBConn(), $this->getText() );
					$iTo = time();
					$iFrom = $iTo-$iTTL;
					$aTxns = array();
					try
					{
						$aTxns = $obj_Status->getActiveTransactions($iFrom, $iTo, $enduserid);
					} catch (mPointException $e) { trigger_error("An error occurred while trying to check the DB for active payment transactions", E_USER_WARNING); }

					// There is one or more active transactions
					if (count($aTxns) > 0) { return 1; }
				}
			}
			else { return 2; }

			$sql2 = "DELETE FROM EndUser".sSCHEMA_POSTFIX.".Card_Tbl
					 WHERE id = ". intval($RS["CARDID"]);
//			echo $sql ."\n";

			$res2 = $this->getDBConn()->query($sql2);
			if (is_resource($res2) === true && $this->getDBConn()->countAffectedRows($res2) > 0) { return 10; }
		}

		return 3;
	}
	
	/**
	 * Sets a new preferred card for a specific client.
	 * The method will reset the "preferred" flag for all other cards the End-User has stored for the Client
	 * to ensure that there's only one preferred card pr Client.
	 * The method will return the following status codes:
	 * 	 1. Unable to reset "preferred" flags for all the cards the End-User has stored for the Client
	 * 	 2. Unable to set specified card as preferred
	 * 	10. Success, preferred card has been changed
	 *
	 * @param 	integer $id			Unqiue ID of the End-User's Account
	 * @param 	integer $cardid		Unique ID of the Stored Card that should be set as preferred
	 * @return 	integer
	 */
	public function setPreferredCard($id, $cardid)
	{
		// Start database transaction
		$this->getDBConn()->query("START TRANSACTION");  // START TRANSACTION does not work with Oracle db

		$sql = "UPDATE EndUser".sSCHEMA_POSTFIX.".Card_Tbl
				SET preferred = '0'
				WHERE clientid = (SELECT clientid
								  FROM EndUser".sSCHEMA_POSTFIX.".Card_Tbl
								  WHERE id = ". intval($cardid) .")
					AND accountid = ". intval($id);
//		echo $sql ."\n";

		// Reset "preferred" flag for all the cards the End-User has stored for the Client
		if (is_resource($this->getDBConn()->query($sql) ) === true)
		{
			$sql = "UPDATE EndUser".sSCHEMA_POSTFIX.".Card_Tbl
					SET preferred = '1'
					WHERE accountid = ". intval($id) ." AND id = ". intval($cardid);
//			echo $sql ."\n";
			// Set specified as preferred
			if (is_resource($this->getDBConn()->query($sql) ) === true)
			{
				// Commit database transaction
				$this->getDBConn()->query("COMMIT");

				$code = 10;
			}
			else
			{
				// Abort database transaction and rollback to previous state
				$this->getDBConn()->query("ROLLBACK");

				$code = 2;
			}
		}
		else
		{
			// Abort database transaction and rollback to previous state
			$this->getDBConn()->query("ROLLBACK");

			$code = 1;
		}

		return $code;
	}
}
?>