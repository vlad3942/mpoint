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
				FROM Admin.User_Tbl
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
				FROM Admin.Access_Tbl Acc
				INNER JOIN Log.Transaction_Tbl Txn ON Acc.clientid = Txn.clientid AND Txn.enabled = '1'
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
					FROM Admin.RoleAccess_Tbl Acc
					INNER JOIN Admin.Role_Tbl R ON Acc.roleid = R.id AND R.enabled = true
					WHERE Acc.userid = ". intval($id) ."
					ORDER BY R.name ASC";
	//			echo $sql ."\n";

		$res = $this->getDBConn()->query($sql);
		file_put_contents(sLOG_PATH ."/refundsend.log", var_export($sql, true) );
		
		$xml = '<status code="100">Roles fetched </status>';
		$xml .= '<roles>';
		while ($RS = $this->getDBConn()->fetchName($res) )
		{
			$xml .= "<role>". $RS["ID"] ."</role>";
		}
		$xml .= '</roles>';
	
		$sql = "SELECT clientid
					FROM Admin.Access_Tbl
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
	
}
?>