<?php
/**
 * The MConsole package provides the required business logic for administering mPoint.
 *
 * @author Rohit Malhotra
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package mConsole
 * @version 1.00
 */

class mConsole extends Admin
{
	const sPERMISSION_GET_PAYMENT_METHODS = "mPoint.GetPaymentMethods";
	const sPERMISSION_GET_CLIENT = "mPoint.GetClients";
	
	public function saveClient(&$clientid, $cc , $storecard, $autocapture, $name, $username, $password, 
									$lang, $smsrcpt, $emailrcpt, $mode, $method, $send_pspid, $identification, $transaction_ttl)
	{
        $newclient = false;
		if ($clientid > 0)
		{
			$sql = "UPDATE Client".sSCHEMA_POSTFIX.".Client_Tbl
					SET store_card = ".intval($storecard) .", auto_capture = '". intval($autocapture)."', name = '". $this->getDBConn()->escStr($name)."', username='". $this->getDBConn()->escStr($username)."', passwd='". $this->getDBConn()->escStr($password)."', countryid = ".$cc .",
					lang = '".$this->getDBConn()->escStr($lang)."', smsrcpt = '". intval($smsrcpt) ."', emailrcpt = '". intval($emailrcpt) ."' , mode = ". intval($mode) .", method = '".$this->getDBConn()->escStr($method)."', send_pspid = '".intval($send_pspid)."',
					identification = ".intval($identification).", transaction_ttl = ".intval($transaction_ttl)."
					WHERE id = ". intval($clientid)."";
			$res = $this->getDBConn()->query($sql);
		}
		else
		{
			$sql = "INSERT INTO Client".sSCHEMA_POSTFIX.".Client_Tbl
						(store_card, auto_capture, name, username, passwd, countryid, flowid, lang, smsrcpt, emailrcpt, mode, method, send_pspid, identification, transaction_ttl)
					VALUES
						(". intval($storecard).",'". intval($autocapture)."', '". $this->getDBConn()->escStr($name)."' , '". $this->getDBConn()->escStr($username)."', '". $this->getDBConn()->escStr($password)."',". intval($cc).", ".intval(1).",
						 '".$this->getDBConn()->escStr($lang)."', '". intval($smsrcpt) ."', '". intval($emailrcpt) ."' ,". intval($mode) .",'".$this->getDBConn()->escStr($method)."','".intval($send_pspid)."',".intval($identification).",".intval($transaction_ttl).")";
//			echo $sql ."\n";		
			$res = $this->getDBConn()->query($sql);
			if (is_resource($res))
			{
				$sql = "SELECT MAX(id) AS ID
						FROM Client".sSCHEMA_POSTFIX.".Client_Tbl";
//				echo $sql ."\n";
				$RS = $this->getDBConn()->getName($sql);
								
				if (is_array($RS) === true)
				{
					$clientid = $RS["ID"];						
					$newclient = true;				
				}
			}
		}
		return $newclient == true ? true : is_resource($res);
	}
	
	public function saveAccount(&$accountid, $clientid, $name, $markup)
	{	
        $newaccount = false;
		if ($accountid > 0)
		{
			$sql = "UPDATE Client".sSCHEMA_POSTFIX.".Account_Tbl
					SET name = '". $this->getDBConn()->escStr($name)."', markup='". $this->getDBConn()->escStr($markup)."'
					WHERE id = ". intval($accountid)." AND clientid = ".intval($clientid)."";
			$res = $this->getDBConn()->query($sql);
		}
		else
		{
			$sql =  "INSERT INTO Client".sSCHEMA_POSTFIX.".Account_Tbl 
						(clientid, name, markup)
					 VALUES
						(". intval($clientid).", '". $this->getDBConn()->escStr($name)."', '". $this->getDBConn()->escStr($markup) ."')";
//			echo $sql ."\n";	
			if (is_resource($this->getDBConn()->query($sql) ) === true)
			{
				$sql = "SELECT Max(id) AS ID
						FROM Client".sSCHEMA_POSTFIX.".Account_Tbl";
//				echo $sql ."\n";	
				$RS = $this->getDBConn()->getName($sql);
		
				if (is_array($RS) === true)
				{
					$accountid = $RS["ID"];
					$newaccount = true;
				}
			}
		}
		return $newaccount == true ? true : is_resource($res);
	}
	
	public function saveMerchantSubAccount($accountid, $pspid, $name)
	{			
		if ($accountid > 0 && $pspid > 0)
		{
			$sql = "UPDATE Client".sSCHEMA_POSTFIX.".MerchantSubAccount_Tbl
					SET name = '". $this->getDBConn()->escStr($name)."'
					WHERE accountid = ". intval($accountid)." AND pspid = ".intval($pspid)."";
			
			$res = $this->getDBConn()->query($sql);		
		}
		if(is_resource($res) == false)
		{
			$sql =  "INSERT INTO Client".sSCHEMA_POSTFIX.".MerchantSubAccount_Tbl 
					(accountid, pspid, name)
				 VALUES
					( ". intval($accountid).", ". intval($pspid).", '". $this->getDBConn()->escStr($name)."')";
			$res = $this->getDBConn()->query($sql);	
				
		}
		return is_resource($res);		
	}
	
	public function saveMerchantAccount($clientid, $pspid, $name, $username, $password)
	{	
		if ($clientid > 0 && $pspid > 0)
		{
			$sql = "UPDATE Client".sSCHEMA_POSTFIX.".MerchantAccount_Tbl
					SET name = '". $this->getDBConn()->escStr($name)."', username ='". $this->getDBConn()->escStr($username)."', passwd ='". $this->getDBConn()->escStr($password)."'
					WHERE clientid = ". intval($clientid)." AND pspid = ".intval($pspid)."";
			
			$res = $this->getDBConn()->query($sql);		
		}
		if(is_resource($res) == false)
		{
			$sql = "INSERT INTO Client".sSCHEMA_POSTFIX.".MerchantAccount_Tbl 
						(clientid, pspid, name, username, passwd )
					VALUES
						( ". intval($clientid).", ". intval($pspid).", '". $this->getDBConn()->escStr($name)."', '". $this->getDBConn()->escStr($username)."', '". $this->getDBConn()->escStr($password)."')";
			$res = $this->getDBConn()->query($sql);	
				
		}
		return is_resource($res);
	}
	
	public function saveCardAccess($clientid, $cardid, $pspid, $countryid)
	{
		if ($clientid > 0 && $cardid > 0 && $pspid > 0)
		{
			$sql = "UPDATE Client".sSCHEMA_POSTFIX.".CardAccess_Tbl
					SET countryid =". intval($countryid)."
					WHERE clientid = ". intval($clientid)." AND pspid = ".intval($pspid)." AND cardid = ". intval($cardid)."";
			
			$res = $this->getDBConn()->query($sql);			
		}
		if(is_resource($res) == false)
		{
			$sql = "INSERT INTO Client".sSCHEMA_POSTFIX.".CardAccess_Tbl 
						(clientid, cardid, pspid, countryid)
				    VALUES
						(". intval($clientid).", ". intval($cardid).", ". intval($pspid).", ". intval($countryid).")";
				
			$res = $this->getDBConn()->query($sql);
		}
			
		return is_resource($res);	
	}
	
	public function saveKeyword ($clientid, $name)
	{
		$sql = "INSERT INTO Client".sSCHEMA_POSTFIX.".KeyWord_Tbl 
					(clientid , name, standard)
				VALUES
					( ". intval($clientid) .", '". $this->getDBConn()->escStr($name)."', true)";
//		echo $sql ."\n";
		return true;
	}
	
	public function saveURL($clientid, $typeid, $url)
	{	
		if (intval($typeid) > 0 && $clientid > 0)
		{
			$sql = "UPDATE Client".sSCHEMA_POSTFIX.".URL_Tbl
					SET url = '". $this->getDBConn()->escStr($url) ."'
					WHERE clientid = ".intval($clientid)." AND urltypeid = ".intval($typeid)."" ;
			$res = $this->getDBConn()->query($sql);
		}
		if(is_resource($res) == false)
		{
			$sql = "INSERT INTO Client".sSCHEMA_POSTFIX.".URL_Tbl
						(clientid , urltypeid, url )
					VALUES
						(". intval($clientid).", ". intval($typeid).",'". $this->getDBConn()->escStr($url)."')";
			$res = $this->getDBConn()->query($sql);
		}
		if (intval($typeid) == 4)
		{
			$sql = "UPDATE Client".sSCHEMA_POSTFIX.".Client_Tbl
					SET callbackurl = ". $this->getDBConn()->escStr($url) ."
					WHERE id = ".intval($clientid);
			$res = $this->getDBConn()->query($sql);
		}		
		
		return is_resource($res);
	}
	
	public function saveIINRange($clientid, $actionid, $min, $max)
	{
		$sql = "INSERT INTO Client".sSCHEMA_POSTFIX.".IINRange_Tbl 
					(clientid , actionid, minrange, maxrange)
				VALUES
					( ". intval($clientid) .", ". intval($actionid) .", ". intval($min) .", ". intval($max) .")";
//		echo $sql ."\n";
		return is_resource($this->getDBConn()->query($sql) );
	}
	
	
	public function saveClientCardData($clientid, $storecard, $showallcards, $maxcards)
	{
		$sql = "UPDATE Client".sSCHEMA_POSTFIX.".Client_Tbl
				SET store_card = ".intval($storecard) .", show_all_cards = '". intval($showallcards)."', max_cards = ". intval($maxcards)."
				WHERE id = ". intval($clientid)."";
		
		return is_resource($this->getDBConn()->query($sql));	
	}	
	
	public function auth()
	{
		$aArgs = func_get_args();
		if (count($aArgs) == 5)
		{		
			if (($aArgs[0] instanceof HTTPConnInfo) === true)
			{
				return $this->_singleSignOn($aArgs[0], $aArgs[1], $aArgs[2], $aArgs[3], $aArgs[4]);
			}		
		}
	}
	
	/**
	 * Performs Single Sign-On for the user by invoking mConsole's Enterprise Security Manager through the Mobile Enterprise Service Bus.
	 * The method will return the following status codes:
	 * 	 1. Single Sign-On Service unavailable
	 * 	 2. Unauthorized Access 
	 * 	 3. Insufficient Permissions
	 * 	10. Success
	 * 
	 * @see		$aHTTP_CONN_INFO["mesb"]
	 * 
	 * @param	HTTPConnInfo $oCI		The connection information for the Mobile Enterprise Service Bus
	 * @param	string $authtoken		The user's authentication token which must be passed back to mConsole's Enterprise Security Manager
	 * @param	string $permissioncode	mConsole's Permission Code which should be used authorization as part of Single Sign-On
	 * @param	array $aClientIDs		A list of client IDs on which the operation is being executed
	 * @return	integer
	 */
	public function singleSignOn(HTTPConnInfo &$oCI, $authtoken, $permissioncode, array $aClientIDs=array() )
	{
		$obj_ConnInfo = new HTTPConnInfo($oCI->getProtocol(), $oCI->getHost(), $oCI->getPort(), $oCI->getTimeout(), $oCI->getPath(), "POST", "text/xml", $oCI->getUsername(), $oCI->getPassword() );		
		$b = '<?xml version="1.0" encoding="UTF-8"?>';
		$b .= '<root>';
		$b .= '<single-sign-on permission-code="'.htmlspecialchars($permissioncode, ENT_NOQUOTES) .'">';
		if (is_null($aClientIDs) == false && count($aClientIDs) > 0)
		{
			$b .= '<clients>';
			foreach($aClientIDs as $clid)
			{
				$b .= '<client-id>'. intval($clid) .'</client-id>';
			}
			$b .= '</clients>';
		}
		$b .= '</single-sign-on>';
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
				return 10;
			}
			else
			{
				trigger_error("Authentication Service at: ". $oCI->toURL() ." rejected authorization with HTTP Code: ". $code, E_USER_WARNING);
				return 2;
			}
		}
		catch (HTTPException $e)
		{
			trigger_error("Authentication Service at: ". $oCI->toURL() ." is unavailable due to ". get_class($e), E_USER_WARNING);
			return 1;
		}
	}
}
?>