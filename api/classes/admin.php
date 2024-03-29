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
	
	public function getUserRolesAndAccess($id)
	{
		$sql = "SELECT R.id
				FROM Admin".sSCHEMA_POSTFIX.".RoleAccess_Tbl Acc
				INNER JOIN Admin".sSCHEMA_POSTFIX.".Role_Tbl R ON Acc.roleid = R.id AND R.enabled = true
				WHERE Acc.userid = ". intval($id) ."
				ORDER BY R.name ASC";
//		echo $sql ."\n";
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
//		echo $sql ."\n";
		$res = $this->getDBConn()->query($sql);
		$xml .= "<clients>";
		while ($RS = $this->getDBConn()->fetchName($res) )
		{
			$xml .= "<client>". $RS["CLIENTID"] ."</client>";
		}
		$xml .= "</clients>";
		
		return  $xml;
	}	
	
	public function saveMerchantSubAccount($accountid, $pspid, $name)
	{			
		$sql =  "INSERT INTO Client".sSCHEMA_POSTFIX.".MerchantSubAccount_Tbl 
					(accountid, pspid, name)
				 VALUES
					( ". intval($accountid).", ". intval($pspid).", '". $this->getDBConn()->escStr($name)."')";
//		echo $sql ."\n";	
		return is_resource($this->getDBConn()->query($sql) );
	}
	
	public function deleteMerchantSubAccount($accountid)
	{
		$sql = "DELETE FROM Client".sSCHEMA_POSTFIX.".MerchantSubAccount_Tbl
				WHERE accountid = ". intval($accountid);
		return is_resource($this->getDBConn()->query($sql) );
	}

    public function saveAccount($clientid, $name, $markup, $id = -1)
    {

        if(empty($id) === true )
        {
            //Entry exists but is disabled.
            $sqlSelect = "SELECT id FROM Client". sSCHEMA_POSTFIX .".Account_Tbl
						WHERE name = '". $this->getDBConn()->escStr($name) ."'
						AND markup = '". $this->getDBConn()->escStr($markup) ."'
						AND clientid = ". intval($clientid);

            $RSONE = $this->getDBConn()->getName($sqlSelect);

            $id = $RSONE["ID"];
        }

        if ($id > 0)
        {
            $sql = "UPDATE Client". sSCHEMA_POSTFIX .".Account_Tbl
					SET name = '". $this->getDBConn()->escStr($name) ."', markup='". $this->getDBConn()->escStr($markup) ."', enabled = '1'
					WHERE id = ". intval($id) ." AND (name != '". $this->getDBConn()->escStr($name) ."' OR markup !='". $this->getDBConn()->escStr($markup) ."')";
            $res = $this->getDBConn()->query($sql);

            if($this->getDBConn()->countAffectedRows($res) > 0)
            {
                //This query will force trigger to update modified date to same date where account_tbl is updated.
                $sql = "UPDATE Client". sSCHEMA_POSTFIX .".MerchantSubAccount_Tbl
						SET accountid = '". intval($id)."'
						WHERE accountid = ". intval($id);

                $res = $this->getDBConn()->query($sql);
            }
        }
        else
        {
            $sql = "SELECT Nextval('Client". sSCHEMA_POSTFIX .".Account_Tbl_id_seq') AS id";
            $RS = $this->getDBConn()->getName($sql);
            $id = $RS["ID"];

            $sql =  "INSERT INTO Client". sSCHEMA_POSTFIX .".Account_Tbl 
						(id, clientid, name, markup)
					 VALUES
						(". $id .", ". intval($clientid) .", '". $this->getDBConn()->escStr($name) ."', '". $this->getDBConn()->escStr($markup) ."')";
            $res = $this->getDBConn()->query($sql);
        }
        //echo $sql ."\n";

        // Unable execute SQL query
        if (is_resource($res) === false) { $id = -1; }

        return $id;
    }
	
	public function deleteAccount($clientid)
	{
		$sql = "DELETE FROM Client".sSCHEMA_POSTFIX.".Account_Tbl
				WHERE clientid = ". intval($clientid)."";
		return is_resource($this->getDBConn()->query($sql) );
	}
	
	public function saveURL($clientid, $typeid, $url)
	{	
		if (intval($typeid) == 4)
		{
			$sql = "UPDATE Client".sSCHEMA_POSTFIX.".Client_Tbl
					SET callbackurl = ". $this->getDBConn()->escStr($url) ."
					WHERE clientid = ".intval($clientid);
		}
		else
		{
			$sql = "INSERT INTO Client".sSCHEMA_POSTFIX.".URL_Tbl
						(clientid , urltypeid, url )
					VALUES
						(". intval($clientid).", ". intval($typeid).",'". $this->getDBConn()->escStr($url)."')";
		}
//		echo $sql ."\n";
		return $this->getDBConn()->query($sql);
	}
	
	public function deleteURL($clientid)
	{
		$sql = "DELETE FROM Client".sSCHEMA_POSTFIX.".URL_Tbl
				WHERE clientid = ". intval($clientid)."";
		return is_resource($this->getDBConn()->query($sql) );
	}

    public function saveMerchantAccount($clientid, $pspid, $name, $username, $password, $storedcard, $id = -1)
    {

        if (empty($storedcard) === true )
        {
            $storedcard = "NULL" ;
            $storedcardClause = "stored_card IS NULL";

        }
        else
        {
            $storedcard = "'". intval($storedcard) ."'";
            $storedcardClause = "stored_card = ". $storedcard ;
        }

        if(empty($id) === true )
        {
            //Entry exists but is disabled.
            $sqlSelect = "SELECT id FROM Client". sSCHEMA_POSTFIX .".MerchantAccount_Tbl
						WHERE clientid = ". intval($clientid) ." AND pspid = ". intval($pspid) ." AND ". $storedcardClause;
            $RSONE = $this->getDBConn()->getName($sqlSelect);
            $id = $RSONE["ID"];
        }

        if (intval($id) > 0)
        {
            $sql = "UPDATE Client". sSCHEMA_POSTFIX .".MerchantAccount_Tbl
					SET name = '". $this->getDBConn()->escStr($name) ."', username ='". $this->getDBConn()->escStr($username) ."', passwd ='". $this->getDBConn()->escStr($password) ."',
						pspid = ". intval($pspid) .", stored_card = ". $storedcard .", enabled = '". intval(true) ."'
					WHERE id = ". intval($id) ." AND clientid = ". intval($clientid);
        }
        else
        {
            $sql = "SELECT Nextval('Client". sSCHEMA_POSTFIX .".MerchantAccount_Tbl_id_seq') AS id";
            $RS = $this->getDBConn()->getName($sql);
            $id = $RS["ID"];

            $sql = "INSERT INTO Client".sSCHEMA_POSTFIX.".MerchantAccount_Tbl 
						(id, clientid, pspid, name, username, passwd, stored_card )
					VALUES
						(". $id .", ". intval($clientid) .", ". intval($pspid) .", '". $this->getDBConn()->escStr($name) ."', '". $this->getDBConn()->escStr($username) ."', '". $this->getDBConn()->escStr($password) ."', ". $storedcard .")";
        }
        //echo $sql ."\n";
        $res = $this->getDBConn()->query($sql);
        //Unable execute SQL query
        if (is_resource($res) === false) { $id = -1; }

        return $id;
    }


    public function deleteMerchantAccount($clientid)
	{
		$sql = "DELETE FROM Client".sSCHEMA_POSTFIX.".MerchantAccount_Tbl
				WHERE clientid = ". intval($clientid)."";
		return is_resource($this->getDBConn()->query($sql) );
	}
	
	public function saveKeyword ($clientid, $name)
	{
		$sql = "INSERT INTO Client".sSCHEMA_POSTFIX.".KeyWord_Tbl 
					(clientid , name, standard)
				VALUES
					( ". intval($clientid) .", '". $this->getDBConn()->escStr($name)."', true)";
//		echo $sql ."\n";
		return is_resource($this->getDBConn()->query($sql) );
	}
	
	public function deleteKeyword($clientid)
	{
		$sql = "DELETE FROM Client".sSCHEMA_POSTFIX.".KeyWord_Tbl
				WHERE clientid = ". intval($clientid)."";
		return is_resource($this->getDBConn()->query($sql) );
	}

    public function saveClient($cc, $storecard, $name, $username, $password, $maxamt, $lang, $smsrcpt, $emailrcpt, $mode, $method, $send_pspid, $identification, $transaction_ttl, $salt, $channels, $id = -1)
    {
        if ($id > 0)
        {
            $sql = "UPDATE Client". sSCHEMA_POSTFIX .".Client_Tbl
					SET store_card = ". intval($storecard) .", name = '". $this->getDBConn()->escStr($name) ."', username='". $this->getDBConn()->escStr($username) ."', passwd='". $this->getDBConn()->escStr($password) ."', countryid = ". $cc .",
						maxamount = ". intval($maxamt) .", lang = '". $this->getDBConn()->escStr($lang) ."', smsrcpt = '". intval($smsrcpt) ."', emailrcpt = '". intval($emailrcpt) ."' , mode = ". intval($mode) .", method = '". $this->getDBConn()->escStr($method) ."', send_pspid = '". intval($send_pspid) ."',
						identification = ". intval($identification) .", transaction_ttl = ". intval($transaction_ttl) .", salt = '". $this->getDBConn()->escStr($salt) ."', communicationchannels = ". intval($channels) ."
					WHERE id = ". intval($id);
        }
        else
        {
            $sql = "SELECT Nextval('Client". sSCHEMA_POSTFIX .".Client_Tbl_id_seq') AS id";
            $RS = $this->getDBConn()->getName($sql);
            $id = $RS["ID"];

            $sql = "INSERT INTO Client". sSCHEMA_POSTFIX .".Client_Tbl
						(id, store_card, auto_capture, name, username, passwd, countryid, flowid, maxamount, lang, smsrcpt, emailrcpt, mode, method, send_pspid, identification, transaction_ttl, salt)
					VALUES
						(". $id .", ". intval($storecard) .",'". $this->getDBConn()->escStr($name) ."' , '". $this->getDBConn()->escStr($username) ."', '". $this->getDBConn()->escStr($password) ."',". intval($cc) .", ".intval(1) .",
						 ". intval($maxamt) .", '". $this->getDBConn()->escStr($lang) ."', '". intval($smsrcpt) ."', '". intval($emailrcpt) ."' ,". intval($mode) .",'". $this->getDBConn()->escStr($method) ."','". intval($send_pspid) ."',". intval($identification) .",". intval($transaction_ttl) .", ". $this->getDBConn()->escStr($salt) .")";
        }
//		echo $sql ."\n";
        $res = $this->getDBConn()->query($sql);
        // Unable execute SQL query
        if (is_resource($res) === false) { $id = -1; }

        return $id;
    }
		
	public function deleteCardAccess($clientid)
	{
		$sql = "DELETE FROM Client".sSCHEMA_POSTFIX.".CardAccess_Tbl 
					WHERE clientid = ". intval($clientid)."";
		return is_resource($this->getDBConn()->query($sql) );
	}
	/**
	 * Gets a list of all payment options that are available for the giving list of clients
	 *
	 * @param	array   $aClientids 	Array of all clients that should be looked up
	 * @param	integer $uid 			Used to return the unique ID of the mPoint Administrator
	 *
	 *  @return	XML
	 */
	public function getCards(array $aClientids, $uid)
	{
		$sql = "SELECT CA.id, CA.cardid, C.name AS cardname , CA.stateid, CA.enabled, CA.pspid, PSP.name AS pspname, CA.countryid , CA.clientid, CL.name AS clientname
				FROM Client".sSCHEMA_POSTFIX.".CardAccess_Tbl CA
				INNER JOIN System".sSCHEMA_POSTFIX.".Card_Tbl C ON CA.cardid = C.id
				INNER JOIN Client".sSCHEMA_POSTFIX.".Client_Tbl CL ON CA.clientid = CL.id
				INNER JOIN System".sSCHEMA_POSTFIX.".PSP_Tbl PSP ON CA.pspid = PSP.id
				INNER JOIN Admin".sSCHEMA_POSTFIX.".Access_Tbl Acc ON CA.clientid = Acc.clientid
				WHERE CA.clientid in(". $this->getDBConn()->escStr(implode(',', $aClientids) ) .") AND Acc.userid = ". intval($uid) ."
				ORDER BY CA.clientid DESC";

//		echo $sql;
		
		$aRS = $this->getDBConn()->getAllNames($sql);
		$xml = "<clients>";
		if (is_array($aRS) === true && count($aRS) > 0)
		{
			$iCurrentClientID = 0;
			
			for ($i=0; $i<count($aRS); $i++)
			{
				
				if ($iCurrentClientID === 0 || $iCurrentClientID !== $aRS[$i]["CLIENTID"])
				{
					if($iCurrentClientID != 0)
					{
						$xml .= '</cards>';
						$xml .='</client>';
					}
					
					$xml .= '<client id="'.  $aRS[$i]["CLIENTID"] .'">';
					$xml .= '<name>'. $aRS[$i]["CLIENTNAME"] .'</name>';	
					$xml .= '<cards>';
				}
				
				$xml .= '<card id="'. $aRS[$i]["ID"] .'" type="'. $aRS[$i]["CARDID"] .'" enabled="'. General::bool2xml($aRS[$i]["ENABLED"]) .'" state-id="'. $aRS[$i]["STATEID"] .'" country-id="'. $aRS[$i]["COUNTRYID"] .'">';
				$xml .= '<name>'. $aRS[$i]["CARDNAME"] .'</name>';
				$xml .= '<psp id="'. $aRS[$i]["PSPID"] .'">'. $aRS[$i]["PSPNAME"] .'</psp>';
				$xml .= '</card>';
				if($iCurrentClientID != $aRS[$i]["CLIENTID"] ) { $iCurrentClientID = $aRS[$i]["CLIENTID"]; }
			}
			$xml .= '</cards>';
			$xml .='</client>';
			$xml .= "</clients>";
		}

		return  $xml;
	}
	/*	Used for updationg the enabled state of a card
	 * 	
	 * 	 1. Error in updating the Card
	 * 	10. Card update successfully 
	 * 
	 */
	public function updateCardAccess($id, $state, $uid)
	{
		$sql = "UPDATE Client".sSCHEMA_POSTFIX.".CardAccess_Tbl ";
		if (is_numeric($state) === true)	{ $sql .= "SET stateid = ". intval($state) ." "; }
		else { $sql .= "SET enabled = ". $this->getDBConn()->escStr($state) ." "; }
				
		$sql.=	"WHERE id = ". intval($id) ." AND clientid IN(SELECT clientid
															 FROM Admin.Access_Tbl 
															 WHERE userid = ". intval($uid) .")" ;
	//echo $sql ."\n";	
		
		$res = $this->getDBConn()->query($sql);
		$code = 1;
		if ($this->getDBConn()->countAffectedRows($res) > 0)  { $code = 10; }
		else  {	$code .= 1; }
		$xml .= '</card>';
		
		return $xml;
	}
}
?>