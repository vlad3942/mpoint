<?php
/**
 * The Administration package provides the required business logic for administering mPoint.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Admin
 * @version 1.00
 */

/**
 * 
 *
 */
class Admin extends General
{	
	/**
	 * Authenticates the mPoint Administrator using the provided Username and Password.
	 * The method will return the following status codes:
	 * 	 1. Username / Password doesn't match
	 * 	 2. Administration account disabled
	 * 	10. Success
	 *
	 * @param	string $un 		Username provided by the mPoint Administrator
	 * @param	string $pwd 	Password provided by the mPoint Administrator
	 * @param	integer $uid 	Used to return the unique ID of the mPoint Administrator
	 * @return	integer
	 */
	public function auth($un, $pwd, &$uid=-1)
	{
		$sql = "SELECT id, enabled
				FROM Admin".sSCHEMA_POSTFIX.".User_Tbl
				WHERE Upper(username) = Upper('". $this->getDBConn()->escStr($un) ."') AND passwd = '". $this->getDBConn()->escStr($pwd) ."'";
//		echo $sql ."\n";
		$RS = $this->getDBConn()->getName($sql);
		
		if (is_array($RS) === true)
		{
			if ($RS["ENABLED"] === true)
			{
				$uid = $RS["ID"];
				$code = 10;
			}
			else { $code = 2; }
		}
		else { $code = 1; }

		return $code;
	}
	
	/**
	 * Returns information about the last transaction across clients that the administrative user has access to.
	 * The method optionally takes a 2nd parameter: $clid which specifies which mPoint client the information
	 * should be returned for.
	 * The method will return an array with the following format:
	 * 	id => mPoint's unique ID for the transaction
	 * 	timestamp => Database timestamp for when the transaction occurred  
	 *
	 * @param	integer $uid 	Unique ID for the mPoint Administrator
	 * @param	integer $clid 	Unique ID of the mPoint Client for which the last transaction should be found
	 * @return	array
	 */
	public function getLastTransaction($uid, $clid=-1)
	{
		$sql = "SELECT Txn.id, Txn.created AS timestamp
				FROM Admin".sSCHEMA_POSTFIX.".Access_Tbl Acc
				INNER JOIN Log".sSCHEMA_POSTFIX.".Transaction_Tbl Txn ON Acc.clientid = Txn.clientid AND Txn.enabled = '1'
				WHERE Acc.userid = ". intval($uid);
		if ($clid > 0) { $sql .= " AND Txn.clientid = ". intval($clid); }
		$sql .= "
				ORDER BY Txn.id DESC
				LIMIT 1";
//		echo $sql ."\n";
		$RS = $this->getDBConn()->getName($sql);

		return array_change_key_case($RS, CASE_LOWER);
	}
	
	public function GetUserRolesAndAccess($id)
	{
		$sql = "SELECT R.id
					FROM Admin".sSCHEMA_POSTFIX.".RoleAccess_Tbl Acc
					INNER JOIN Admin".sSCHEMA_POSTFIX.".Role_Tbl R ON Acc.roleid = R.id AND R.enabled = true
					WHERE Acc.userid = ". intval($id) ."
					 ORDER BY R.name ASC";
	//			echo $sql ."\n";

		$res = $this->getDBConn()->query($sql);
		
		$xml = '<status code="100">Roles fetched </status>';
		$xml .= '<roles>';
		while ($RS = $this->getDBConn()->fetchName($res) )
		{
			$xml .= "<role>". $RS["ID"] ."</role>";
		}
		$xml .= '</roles>';
	
		$sql = "SELECT clientid
					FROM Admin".sSCHEMA_POSTFIX.".Access_Tbl
					WHERE userid = ". intval($id);
						//			echo $sql ."\n";
		$res = $this->getDBConn()->query($sql);
		$xml .= "<clients>";
		while ($RS = $this->getDBConn()->fetchName($res) )
		{
			$xml .= "<client>". $RS["CLIENTID"] ."</client>";
		}
		$xml .= "</clients>";
		
		return  $xml;
	}	
	
	public function saveMerchantSubAccount($accountid, $pspid, $name, $found)
	{		
		$sql = "SELECT id
				FROM Client".sSCHEMA_POSTFIX.".MerchantSubAccount_Tbl 
				WHERE accountid = ". intval($accountid)."";
		$res = $this->getDBConn()->query($sql);
		
		if (is_resource($res) === true)
		{
			$del_sql = "DELETE FROM Client".sSCHEMA_POSTFIX.".MerchantSubAccount_Tbl 
					WHERE accountid = ". intval($accountid)."";
			$del_res = $this->getDBConn()->query($del_sql);
		}
		
		if (is_resource($res) === false || is_resource($del_res) === true)
		{
			$in_sql =  "INSERT INTO Client".sSCHEMA_POSTFIX.".MerchantSubAccount_Tbl (Accountid, pspid, name)
							VALUES( $accountid, $pspid, $name)";
			$in_res = $this->getDBConn()->query($in_sql);
		}
		return is_resource($in_res);
	}
	
	public function saveAccount($accountid, $clientid, $name, $markup, $found)
	{
		$sql = "SELECT id
				FROM Client".sSCHEMA_POSTFIX.".Account_Tbl
				WHERE id = ". intval($accountid)."";
		$res = $this->getDBConn()->query($sql);
	
		if (is_resource($res) === true)
		{
			$del_sql = "DELETE FROM Client".sSCHEMA_POSTFIX.".Account_Tbl
						WHERE id = ". intval($accountid)."";
			$del_res = $this->getDBConn()->query($del_sql);
		}
	
		if (is_resource($res) === false || is_resource($del_res) === true)
		{
			$in_sql =  "INSERT INTO Client".sSCHEMA_POSTFIX.".Account_Tbl (id,clientid, name, markup)
			VALUES( $accountid, $clientid, $name, $markup)";
			$in_res = $this->getDBConn()->query($in_sql);
		}
		return is_resource($in_res);
	}
	
	public function saveURL($clientid, $typeid, $url, $found)
	{
		//TODO check if client id is not null
		$sql = "SELECT id
				FROM Client".sSCHEMA_POSTFIX.".URL_Tbl
				WHERE clientid = ". intval($clientid)."";
		$res = $this->getDBConn()->query($sql);
		
		if (is_resource($res) === true)
		{
			$del_sql = "DELETE FROM Client".sSCHEMA_POSTFIX.".URL_Tbl
						WHERE clientid = ". intval($clientid)."";
			$del_res = $this->getDBConn()->query($del_sql);
		}
		if (is_resource($res) === false || is_resource($del_res) === true)
		{
			if (intval($typeid) == 4)
			{
				$up_sql = "UPDATE Client".sSCHEMA_POSTFIX.".Client_Tbl
						   SET callback = ".$url."
							WHERE clientid = ".intval($clientid)."";
				$up_res = $this->getDBConn()->query($in_sql);
			}
			if (intval($typeid) != 4 || (intval($typeid) == 4 && is_resource($up_res)))
			{
				$in_sql = "INSERT INTO Client".sSCHEMA_POSTFIX.".URL_Tbl (clientid , urltypeid, url )
							VALUES( $clientid, $typeid,$url)";
				$in_res = $this->getDBConn()->query($in_sql);
			}
		}
		return is_resource($in_res);
	}
	
	public function saveMerchantAccount($clientid, $pspid, $name, $username, $password ,$storecard, $found)
	{
		//TODO check if client id is not null
		$sql = "SELECT id
				FROM Client".sSCHEMA_POSTFIX.".MerchantAccount_Tbl
				WHERE clientid = ". intval($clientid)."";
		$res = $this->getDBConn()->query($sql);
		
		if (is_resource($res) === true)
		{
			$del_sql = "DELETE FROM Client".sSCHEMA_POSTFIX.".MerchantAccount_Tbl
						WHERE clientid = ". intval($clientid)."";
			$del_res = $this->getDBConn()->query($del_sql);
		}
		if (is_resource($res) === false || is_resource($del_res) === true)
		{
			$in_sql = "INSERT INTO Client".sSCHEMA_POSTFIX.".MerchantAccount_Tbl (clientid, pspid, name, username, passwd ,stored_card)
						VALUES( $clientid, $pspid, $name, $username, $password, $storecard)";
			$in_res = $this->getDBConn()->query($in_sql);
		}
		return is_resource($in_res);
	}
	
	public function saveKeyWord ($clientid, $name)
	{
		//TODO check if client id is not null
		$sql = "SELECT id
				FROM Client".sSCHEMA_POSTFIX.".KeyWord_Tbl
				WHERE clientid = ". intval($clientid)."";
		$res = $this->getDBConn()->query($sql);
		
		if (is_resource($res) === true)
		{
			$del_sql = "DELETE FROM Client".sSCHEMA_POSTFIX.".KeyWord_Tbl
						WHERE clientid = ". intval($clientid)."";
			$del_res = $this->getDBConn()->query($del_sql);
		}
		if (is_resource($res) === false || is_resource($del_res) === true)
		{
			$in_sql = "INSERT INTO Client".sSCHEMA_POSTFIX.".KeyWord_Tbl (clientid , name)
						VALUES( $clientid, $name)";
			$in_res = $this->getDBConn()->query($in_sql);
		}
		return is_resource($in_res);
	}
	
	
	public function saveClient ($clientid, $cc , $storecard, $autocapture, $name, $username, $password, $found)
	{
		//TODO check if client id is not null
		if ($found === true)
		{
			$up_sql = "UPDATE Client".sSCHEMA_POSTFIX.".Client_Tbl
						SET storecard = ".$storecard .", autocapture = '". $this->getDBConn()->escStr($autocapture)."', name = '". $this->getDBConn()->escStr($name)."', username='". $this->getDBConn()->escStr($username)."', password='". $this->getDBConn()->escStr($password)."', countryid = ".$cc ."
						WHERE clientid = ". intval($clientid)."";
			$up_res = $this->getDBConn()->query($del_sql);
		}
		if ($found === false)
		{
			$in_sql = "INSERT INTO Client".sSCHEMA_POSTFIX.".Client_Tbl (clientid, storecard, autocapture, name, username, password,countryid )
					   VALUES(". $clientid .", ".$storecard.",'". $this->getDBConn()->escStr($autocapture)."', '". $this->getDBConn()->escStr($name)."' , '". $this->getDBConn()->escStr($username)."', '". $this->getDBConn()->escStr($password)."',". $cc.")";
			$in_res = $this->getDBConn()->query($in_sql);
		}
		return ($found===true) ? is_resource($up_res) : is_resource($in_res);
	}
	
	
	public function saveCardAccess ($clientid, $cardid, $pspid, $countyid)
	{
			//TODO check if client id is not null
			$sql = "SELECT id
					FROM Client".sSCHEMA_POSTFIX.".CardAccess_Tbl
					WHERE clientid = ". intval($clientid)." , cardid = ". intval($cardid)." , pspid = ".intval($pspid) ." ";
			$res = $this->getDBConn()->query($sql);
					
			if (is_resource($res) === false)
			{
				$in_sql = "INSERT INTO Client".sSCHEMA_POSTFIX.".CardAccess_Tbl (clientid, cardid, pspid, countryid)
				  			VALUES( $clientid, $cardid, $pspid, $countryid)";
				$in_res = $this->getDBConn()->query($in_sql);
			}
			
			return (is_resource($res)===true) ? true : is_resource($in_res);	
	}
	
	
}
?>