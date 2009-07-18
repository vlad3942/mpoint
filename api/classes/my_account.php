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
	 * Generates a new activation code for the End-User's Account and inserts it into the database.
	 * The generated activation code is a number between 100000 and 999999
	 *
	 * @param	integer $id 	Unqiue ID of the End-User's Account
	 * @param	string $addr 	End-User's mobile number or E-Mail address
	 * @return 	integer
	 * 
	 * @throws	mPointException
	 */
	private function _genActivationCode($id, $addr)
	{
		$iCode = mt_rand(100000, 999999);
		// Insert generated activation code in the database
		$sql = "INSERT INTO EndUser.Activation_Tbl
					(accountid, address, code)
				VALUES
					(". intval($id) .", '". $this->getDBConn()->escStr($addr) ."', ". $iCode .")";
//		echo $sql ."\n";
		
		if (is_resource($this->getDBConn()->query($sql) ) === false)
		{
			throw new mPointException("Failed to Insert activation code: ". $iCode ." into Database", 1101);
		}
		
		return $iCode;
	}
	
	/**
	 * Generates and sends an Activation Code to the End-User using the provided MSISDN.
	 * 
	 * @see		GoMobileMessage::produceMessage()
	 * @see		General::getText()
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
		$sBody = str_replace("{CODE}", $this->_genActivationCode($id, $mob), $sBody);
		
		$obj_ClientConfig = ClientConfig::produceConfig($this->getDBConn(), $this->getCountryConfig()->getID(), -1);
		
		$obj_MsgInfo = GoMobileMessage::produceMessage(Constants::iMT_SMS_TYPE, $this->getCountryConfig()->getID(), $this->getCountryConfig()->getID()*100, $this->getCountryConfig()->getChannel(), $obj_ClientConfig->getKeywordConfig()->getKeyword(), Constants::iMT_PRICE, $mob, $sBody);
		
		$iCode = $this->sendMessage($oCI, $obj_ClientConfig, $obj_MsgInfo);
		if ($iCode != 200) { $iCode = 91; }
		
		return $iCode;
	}
	
	/**
	 * Activates the provided code.
	 * The method will return the following status codes:
	 * 	 1. Activation Code not found for Account
	 * 	 2. Activation Code disabled
	 * 	 3. Activation Code already consumed
	 * 	 4. Unable to consume Activation Code
	 * 	10. Success
	 *
	 * @param	integer $id 	Unqiue ID of the End-User's Account
	 * @param	integer $code 	Code which should be activated
	 * @return	integer
	 */
	public function activateCode($id, $code)
	{
		$sql = "SELECT id, enabled, active
				FROM EndUser.Activation_Tbl
				WHERE accountid = ". intval($id) ." AND code = ". intval($code);
//		echo $sql ."\n";
		$RS = $this->getDBConn()->getName($sql);

		if (is_array($RS) === false) { $iStatus = 1;}
		elseif ($RS["ENABLED"] === false) { $iStatus = 2;}
		elseif ($RS["ACTIVE"] === true) { $iStatus = 3;}
		else
		{
			$sql = "UPDATE EndUser.Activation_Tbl
					SET active = true
					WHERE id = ". $RS["ID"];
//			echo $sql ."\n";
			if (is_resource($this->getDBConn()->query($sql) ) === true)
			{
				$iStatus = 10; 
			}
			else { $iStatus = 4; }
		}
			
		return $iStatus;
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
				FROM EndUser.Activation_Tbl
				WHERE accountid = ". intval($id) ." AND code = ". intval($code) ."
				ORDER BY id DESC
				LIMIT 1";
//		echo $sql ."\n";
		$RS = $this->getDBConn()->getName($sql);
		
		return is_array($RS) === true ? $RS["ADDRESS"] : "";
	}
	
	/**
	 * Saves the specified Mobile Number for the End-User Account. 
	 *
	 * @param	integer $id 	Unqiue ID of the End-User's Account
	 * @param	string $mob 	The End-User's new Mobile Number (MSISDN) which should be saved to the account
	 * @return	boolean
	 */
	public function saveMobile($id, $mob)
	{
		$sql = "UPDATE EndUser.Account_Tbl
				SET mobile = '". floatval($mob) ."'  
				WHERE id = ". intval($id);
//		echo $sql ."\n";
		
		return is_resource($this->getDBConn()->query($sql) );
	}
	
	/**
	 * Generates and sends an Activation Code to the End-User using the provided MSISDN 
	 *
	 * @param	integer $id 	Unqiue ID of the End-User's Account
	 * @param	string $email 	End-User's new E-Mail address
	 * @return	boolean
	 */
	public function sendLink($id, $email)
	{
		$sSubject = $this->getText()->_("mPoint - Activation Link Subject");
		$sBody = $this->getText()->_("mPoint - Activation Link Body");
		
		$iCode = $this->_genActivationCode($id, $email);
		$sURL = "http://". sDEFAULT_MPOINT_DOMAIN ."/home/sys/save_email.php?id=". $id ."&c=". $iCode ."&chk=". md5($id . $iCode . $email);
		
		$sBody = str_replace("{URL}", $sURL, $sBody);
		
		return mail($email, $sSubject, $sBody, $this->constHeaders() );
	}
	
	/**
	 * Saves the specified E-Mail address for the End-User Account
	 *
	 * @param	integer $id 	Unqiue ID of the End-User's Account
	 * @param 	string $email	End-User's e-mail address
	 * @return	boolean
	 */
	public function saveEmail($id, $email)
	{
		$sql = "UPDATE EndUser.Account_Tbl
				SET email = '". $this->getDBConn()->escStr($email) ."'
				WHERE id = ". intval($id);
//		echo $sql ."\n";

		return is_resource($this->getDBConn()->query($sql) );
	}
}
?>