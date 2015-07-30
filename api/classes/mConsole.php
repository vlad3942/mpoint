<?php
/**
 * The mConsole package provides the required business logic for administering mPoint.
 *
 * @author Rohit Malhotra
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package mConsole
 * @version 1.10
 */
class mConsole extends Admin
{
	// Constants for mConsole's Single Sign-On Service
	const iSERVICE_INTERNAL_ERROR = 1;
	const iSERVICE_CONNECTION_TIMEOUT_ERROR = 2;
	const iSERVICE_READ_TIMEOUT_ERROR = 3;
	const iUNAUTHORIZED_USER_ACCESS_ERROR = 4;
	const iINSUFFICIENT_USER_PERMISSIONS_ERROR = 5;
	const iINSUFFICIENT_CLIENT_LICENSE_ERROR = 6;
	const iAUTHORIZATION_SUCCESSFUL = 10;
	// mConsole permission codes
	const sPERMISSION_GET_PAYMENT_METHODS = "mpoint.payment-method-configuration.get.x";
	const sPERMISSION_GET_CLIENTS = "mpoint.client-configuration.get.x";
	const sPERMISSION_SAVE_CLIENT = "mpoint.client-configuration.save.x";
	const sPERMISSION_GET_PAYMENT_SERVICE_PROVIDERS = "mpoint.payment-service-provider-configuration.get.x";
	const sPERMISSION_SEARCH_TRANSACTION_LOGS = "mpoint.transaction-logs.search.x";	
	
	public function saveClient(&$clientid, $cc , $storecard, $autocapture, $name, $username, $password, 
									$lang, $smsrcpt, $emailrcpt, $mode, $method, $send_pspid, $identification, $transaction_ttl)
	{
        $newclient = false;
		if ($clientid > 0)
		{
			$sql = "UPDATE Client". sSCHEMA_POSTFIX .".Client_Tbl
					SET store_card = ". intval($storecard) .", auto_capture = '". intval($autocapture) ."', name = '". $this->getDBConn()->escStr($name) ."', username='". $this->getDBConn()->escStr($username) ."', passwd='". $this->getDBConn()->escStr($password) ."', countryid = ". $cc .",
					lang = '". $this->getDBConn()->escStr($lang) ."', smsrcpt = '". intval($smsrcpt) ."', emailrcpt = '". intval($emailrcpt) ."' , mode = ". intval($mode) .", method = '". $this->getDBConn()->escStr($method) ."', send_pspid = '". intval($send_pspid) ."',
					identification = ". intval($identification) .", transaction_ttl = ". intval($transaction_ttl) ."
					WHERE id = ". intval($clientid);
			$res = $this->getDBConn()->query($sql);
		}
		else
		{
			$sql = "INSERT INTO Client". sSCHEMA_POSTFIX .".Client_Tbl
						(store_card, auto_capture, name, username, passwd, countryid, flowid, lang, smsrcpt, emailrcpt, mode, method, send_pspid, identification, transaction_ttl)
					VALUES
						(". intval($storecard) .",'". intval($autocapture) ."', '". $this->getDBConn()->escStr($name) ."' , '". $this->getDBConn()->escStr($username) ."', '". $this->getDBConn()->escStr($password) ."',". intval($cc) .", ".intval(1) .",
						 '". $this->getDBConn()->escStr($lang) ."', '". intval($smsrcpt) ."', '". intval($emailrcpt) ."' ,". intval($mode) .",'". $this->getDBConn()->escStr($method) ."','". intval($send_pspid) ."',". intval($identification) .",". intval($transaction_ttl) .")";
//			echo $sql ."\n";		
			$res = $this->getDBConn()->query($sql);
			if (is_resource($res))
			{
				$sql = "SELECT MAX(id) AS ID
						FROM Client". sSCHEMA_POSTFIX .".Client_Tbl";
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
			$sql = "UPDATE Client". sSCHEMA_POSTFIX .".Account_Tbl
					SET name = '". $this->getDBConn()->escStr($name) ."', markup='". $this->getDBConn()->escStr($markup) ."',
					enabled = '". intval(true) ."'
					WHERE id = ". intval($accountid) ." AND clientid = ". intval($clientid);
			//echo $sql ."\n";
			$res = $this->getDBConn()->query($sql);	
				
		}
		else
		{
			$sql =  "INSERT INTO Client". sSCHEMA_POSTFIX .".Account_Tbl 
						(clientid, name, markup)
					 VALUES
						(". intval($clientid) .", '". $this->getDBConn()->escStr($name) ."', '". $this->getDBConn()->escStr($markup) ."')";
//			echo $sql ."\n";	
			if (is_resource($this->getDBConn()->query($sql) ) === true)
			{
				$sql = "SELECT Max(id) AS ID
						FROM Client". sSCHEMA_POSTFIX .".Account_Tbl";
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
	
	public function disableAccounts($clientid, $accountIDs = array())
	{
		$sql = "UPDATE Client". sSCHEMA_POSTFIX .".Account_Tbl
				SET enabled = '". intval(false)."'
				WHERE clientid = ". intval($clientid);
		//echo $sql ."\n";
				
		if(is_array($accountIDs) && count($accountIDs) > 0)
		{
			$accountIDlist = implode(",", $accountIDs);
			$sql .= " AND id NOT IN (". trim($accountIDlist) .")";
		}
		//echo $sql ."\n";	
		return is_resource($this->getDBConn()->query($sql) );
	}
	
	public function saveMerchantSubAccount($id, $accountid, $pspid, $name)
	{			
		$newsubaccount = false;
		if ($id > 0)
		{
			$sql = "UPDATE Client". sSCHEMA_POSTFIX .".MerchantSubAccount_Tbl
					SET name = '". $this->getDBConn()->escStr($name) ."', pspid = ". intval($pspid) ." , enabled = '". intval(true) ."'
					WHERE id = ". intval($id) ." AND accountid = ". intval($accountid);
			//echo $sql ."\n";
			$res = $this->getDBConn()->query($sql);		
		}
		else
		{
			$sql =  "INSERT INTO Client".sSCHEMA_POSTFIX.".MerchantSubAccount_Tbl 
					(accountid, pspid, name)
				 VALUES
					( ". intval($accountid) .", ". intval($pspid) .", '". $this->getDBConn()->escStr($name) ."')";
			//echo $sql ."\n";
			$res = $this->getDBConn()->query($sql);	
			$newsubaccount = true;
		}			
		return $newsubaccount == true ? true : is_resource($res);	
	}
	
	public function disableMerchantSubAccounts($accountid)
	{
		$sql = "UPDATE Client". sSCHEMA_POSTFIX .".MerchantSubAccount_Tbl
				SET enabled = '". intval(false)."'
				WHERE accountid = ". intval($accountid);
		//echo $sql ."\n";				
		return is_resource($this->getDBConn()->query($sql) );
	}
	
	public function saveMerchantAccount($id, $clientid, $pspid, $name, $username, $password, $storedcard)
	{	
		if ($id > 0)
		{
			$sql = "UPDATE Client". sSCHEMA_POSTFIX .".MerchantAccount_Tbl
					SET name = '". $this->getDBConn()->escStr($name) ."', username ='". $this->getDBConn()->escStr($username) ."', passwd ='". $this->getDBConn()->escStr($password) ."',
					pspid = ". intval($pspid) .", stored_card = '". intval($storedcard) ."', enabled = '". intval(true) ."'
					WHERE id = ". intval($id) ." AND clientid = ". intval($clientid);
			//echo $sql ."\n";
			$res = $this->getDBConn()->query($sql);		
		}
		if(is_resource($res) == false)
		{
			$sql = "INSERT INTO Client".sSCHEMA_POSTFIX.".MerchantAccount_Tbl 
						(clientid, pspid, name, username, passwd, stored_card )
					VALUES
						( ". intval($clientid) .", ". intval($pspid) .", '". $this->getDBConn()->escStr($name) ."', '". $this->getDBConn()->escStr($username) ."', '". $this->getDBConn()->escStr($password) ."', '". intval($storedcard) ."')";
			//echo $sql ."\n";
			$res = $this->getDBConn()->query($sql);	
				
		}		
		return is_resource($res);
	}
	
	public function disableMerchantAccounts($clientid)
	{
		$sql = "UPDATE Client". sSCHEMA_POSTFIX .".MerchantAccount_Tbl
				SET enabled = '". intval(false) ."'
				WHERE clientid = ". intval($clientid);		
		//echo $sql ."\n";		
		return is_resource($this->getDBConn()->query($sql) );
	}
	
	public function saveCardAccess($id, $clientid, $cardid, $pspid, $countryid, $stateid)
	{
		if ($id > 0)
		{
			$sql = "UPDATE Client". sSCHEMA_POSTFIX .".CardAccess_Tbl
					SET countryid = ". intval($countryid) .", pspid = ". intval($pspid) .", cardid = ". intval($cardid).", 
					stateid = ". intval($stateid) .", enabled = '".intval(true) ."'
					WHERE id = ". intval($id) ." AND clientid = ". intval($clientid);
			//echo $sql ."\n";
			$res = $this->getDBConn()->query($sql);			
		}
		if(is_resource($res) == false)
		{
			$sql = "INSERT INTO Client". sSCHEMA_POSTFIX .".CardAccess_Tbl 
						(clientid, cardid, pspid, countryid, stateid)
				    VALUES
						(". intval($clientid) .", ". intval($cardid) .", ". intval($pspid) .", ". intval($countryid) .", ". intval($stateid) .")";
			//echo $sql ."\n";	
			$res = $this->getDBConn()->query($sql);
		}
			
		return is_resource($res);	
	}
	
	public function disableAllCardAccess($clientid)
	{
		$sql = "UPDATE Client". sSCHEMA_POSTFIX .".CardAccess_Tbl
				SET enabled = '". intval(false) ."'
				WHERE clientid = ". intval($clientid);		
		//echo $sql ."\n";		
		return is_resource($this->getDBConn()->query($sql) );
	}
	
	public function saveKeyword ($id, $clientid, $name)
	{
		if ($id > 0)
		{
			$sql = "UPDATE Client". sSCHEMA_POSTFIX .".KeyWord_Tbl
					SET name = '". $this->getDBConn()->escStr($name) ."', enabled = '". intval(true) ."', standard = '".intval(true)."'
					WHERE id = ". intval($id) ." AND clientid = ". intval($clientid);
			//echo $sql ."\n";
			$res = $this->getDBConn()->query($sql);			
		}
		if(is_resource($res) == false)
		{
			$sql = "INSERT INTO Client". sSCHEMA_POSTFIX .".KeyWord_Tbl 
					(clientid , name, standard)
				VALUES
					( ". intval($clientid) .", '". $this->getDBConn()->escStr($name) ."', true)";
			//echo $sql ."\n";	
			$res = $this->getDBConn()->query($sql);
		}
			
		return is_resource($res);
	}
	
	public function disableKeyword($clientid)
	{
		$sql = "UPDATE Client". sSCHEMA_POSTFIX .".KeyWord_Tbl
				SET enabled = '". intval(false) ."'
				WHERE clientid = ". intval($clientid);		
		//echo $sql ."\n";		
		return is_resource($this->getDBConn()->query($sql) );
	}
	
	public function saveURL($id, $clientid, $typeid, $url)
	{			
		if ($id > 0 && intval($id) == intval($clientid) && intval($typeid) == ClientConfig::iCALLBACK_URL)
		{
			$sql = "UPDATE Client". sSCHEMA_POSTFIX .".Client_Tbl
					SET callbackurl = '". $this->getDBConn()->escStr($url) ."'
					WHERE id = ". intval($clientid);
			//echo $sql ."\n";
			$res = $this->getDBConn()->query($sql);
		}
		if ($id > 0 && intval($id) == intval($clientid) && intval($typeid) == ClientConfig::iLOGO_URL)
		{
			$sql = "UPDATE Client". sSCHEMA_POSTFIX .".Client_Tbl
					SET logourl = '". $this->getDBConn()->escStr($url) ."'
					WHERE id = ". intval($clientid);
			//echo $sql ."\n";
			$res = $this->getDBConn()->query($sql);
		}
		if ($id > 0 && intval($id) == intval($clientid) && intval($typeid) == ClientConfig::iCSS_URL)
		{
			$sql = "UPDATE Client". sSCHEMA_POSTFIX .".Client_Tbl
					SET cssurl = '". $this->getDBConn()->escStr($url) ."'
					WHERE id = ". intval($clientid);
			//echo $sql ."\n";
			$res = $this->getDBConn()->query($sql);
		}
		if ($id > 0 && intval($id) == intval($clientid) && intval($typeid) == ClientConfig::iCANCEL_URL)
		{
			$sql = "UPDATE Client". sSCHEMA_POSTFIX .".Client_Tbl
					SET cancelurl = '". $this->getDBConn()->escStr($url) ."'
					WHERE id = ". intval($clientid);
			//echo $sql ."\n";
			$res = $this->getDBConn()->query($sql);
		}
		if ($id > 0 && intval($id) == intval($clientid) && intval($typeid) == ClientConfig::iACCEPT_URL)
		{
			$sql = "UPDATE Client". sSCHEMA_POSTFIX .".Client_Tbl
					SET accepturl = '". $this->getDBConn()->escStr($url) ."'
					WHERE id = ". intval($clientid);
			//echo $sql ."\n";
			$res = $this->getDBConn()->query($sql);
		}
		if ($id > 0 && intval($id) == intval($clientid) && intval($typeid) == ClientConfig::iICON_URL)
		{
			$sql = "UPDATE Client". sSCHEMA_POSTFIX .".Client_Tbl
					SET iconurl = '". $this->getDBConn()->escStr($url) ."'
					WHERE id = ". intval($clientid);
			//echo $sql ."\n";
			$res = $this->getDBConn()->query($sql);
		}
		else
		{
			if ($id > 0)
			{
				$sql = "UPDATE Client". sSCHEMA_POSTFIX .".URL_Tbl
						SET url = '". $this->getDBConn()->escStr($url) ."', urltypeid = ". intval($typeid) .", enabled = '". intval(true) ."'
						WHERE id = ". intval($id) ." AND clientid = ". intval($clientid);
				//echo $sql ."\n";
				$res = $this->getDBConn()->query($sql);
			}
			if(is_resource($res) == false)
			{
				$sql = "INSERT INTO Client". sSCHEMA_POSTFIX .".URL_Tbl
							(clientid , urltypeid, url )
						VALUES
							(". intval($clientid) .", ". intval($typeid) .",'". $this->getDBConn()->escStr($url). "')";
				//echo $sql ."\n";
				$res = $this->getDBConn()->query($sql);
			}
		}		
		
		return is_resource($res);
	}
	
	public function disableURLs($clientid)
	{
		$sqlOne = "UPDATE Client". sSCHEMA_POSTFIX .".URL_Tbl
				SET enabled = '". intval(false) ."'
				WHERE clientid = ". intval($clientid);
		//echo $sqlOne ."\n";		
		$sqlTwo = "UPDATE Client". sSCHEMA_POSTFIX .".Client_Tbl
				SET logourl = NULL, cssurl = NULL , callbackurl = NULL, accepturl = NULL, cancelurl = NULL, iconurl = NULL
				WHERE id = ". intval($clientid);
		//echo $sqlTwo ."\n"; die;
		
		return (is_resource($this->getDBConn()->query($sqlOne)) && is_resource($this->getDBConn()->query($sqlTwo)));
	}

	public function saveIINRange($id, $clientid, $actionid, $min, $max)
	{
		if($id > 0)
		{
			$sql = "UPDATE Client". sSCHEMA_POSTFIX .".IINRange_Tbl
					SET actionid = ". intval($actionid) .", minrange = ". intval($min) .", maxrange = ". intval($max) .",
					enabled = '" . intval(true) . "'
					WHERE id = ". intval($id) ." AND clientid = ". intval($clientid);
			//echo $sql ."\n";
			$res = $this->getDBConn()->query($sql);
			
		}
		else
		{
			$sql = "INSERT INTO Client". sSCHEMA_POSTFIX .".IINRange_Tbl 
						(clientid , actionid, minrange, maxrange)
					VALUES
						( ". intval($clientid) .", ". intval($actionid) .", ". intval($min) .", ". intval($max) .")";
			//echo $sql ."\n";
		}
		return is_resource($this->getDBConn()->query($sql) );
	}
	
	public function disableIINRanges($clientid)
	{
		$sql = "UPDATE Client". sSCHEMA_POSTFIX .".IINRange_Tbl
				SET enabled = '". intval(false) ."'
				WHERE clientid = ". intval($clientid);		
		//echo $sql ."\n";		
		return is_resource($this->getDBConn()->query($sql) );
	}
	
	
	public function saveClientCardData($clientid, $storecard, $showallcards, $maxcards)
	{
		$sql = "UPDATE Client". sSCHEMA_POSTFIX .".Client_Tbl
				SET store_card = ". intval($storecard) .", show_all_cards = '". intval($showallcards) ."', max_cards = ". intval($maxcards) ."
				WHERE id = ". intval($clientid);
		//echo $sql ."\n";
		
		return is_resource($this->getDBConn()->query($sql) );	
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
	 * 	 1. Internal Error while Communicating with Single Sign-On Service
	 * 	 2. Single Sign-On Service unreachable
	 * 	 3. Single Sign-On Service unavailable
	 * 	 4. Unauthorized Access
	 * 	 5. Insufficient User Permissions
	 * 	 6. Insufficient Client License
	 * 	10. Success
	 * 
	 * @see		$aHTTP_CONN_INFO["mesb"]
	 * @see		iSERVICE_INTERNAL_ERROR
	 * @see		iSERVICE_CONNECTION_TIMEOUT_ERROR
	 * @see		iSERVICE_READ_TIMEOUT_ERROR
	 * @see		iUNAUTHORIZED_USER_ACCESS_ERROR
	 * @see		iINSUFFICIENT_USER_PERMISSIONS_ERROR
	 * @see		iINSUFFICIENT_CLIENT_LICENSE_ERROR
	 * @see		iAUTHORIZATION_SUCCESSFUL
	 * 
	 * @param	HTTPConnInfo $oCI		The connection information for the Mobile Enterprise Service Bus
	 * @param	string $authtoken		The user's authentication token which must be passed back to mConsole's Enterprise Security Manager
	 * @param	string $permissioncode	mConsole's Permission Code which should be used authorization as part of Single Sign-On
	 * @param	array $aClientIDs		A list of client IDs on which the operation is being executed
	 * @return	integer
	 */
	public function singleSignOn(HTTPConnInfo &$oCI, $authtoken, $permissioncode, array $aClientIDs=array() )
	{
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
			$h = trim($this->constHTTPHeaders() ) .HTTPClient::CRLF;
			$h .= "X-Auth-Token: ". $authtoken .HTTPClient::CRLF;
			$obj_HTTP = new HTTPClient(new Template(), $oCI);				
			$obj_HTTP->connect();
			$code = $obj_HTTP->send($h, $b);
			$obj_HTTP->disConnect();		
			
			switch ($code)
			{
			case (200):	// HTTP 200 OK
				trigger_error("Authorization accepted by Single-Sign On Service at: ". $oCI->toURL() ." with HTTP Code: ". $code, E_USER_NOTICE);
				return self::iAUTHORIZATION_SUCCESSFUL;
				break;
			case (401):	// HTTP 401 Unauthorized
				trigger_error("Single-Sign On Service at: ". $oCI->toURL() ." rejected authorization with HTTP Code: ". $code, E_USER_NOTICE);
				return self::iUNAUTHORIZED_USER_ACCESS_ERROR;
				break;
			case (402):	// HTTP 402 Payment Required
				trigger_error("Single-Sign On Service at: ". $oCI->toURL() ." rejected license with HTTP Code: ". $code, E_USER_NOTICE);
				return self::iINSUFFICIENT_CLIENT_LICENSE_ERROR;
				break;
			case (403):	// HTTP 403 Forbidden
				trigger_error("Single-Sign On Service at: ". $oCI->toURL() ." rejected permission with HTTP Code: ". $code, E_USER_NOTICE);
				return self::iINSUFFICIENT_USER_PERMISSIONS_ERROR;
				break;
			}
		}
		catch (HTTPConnectionException $e)
		{
			trigger_error("Single-Sign On Service at: ". $oCI->toURL() ." is unreachable due to ". get_class($e), E_USER_WARNING);
			return self::iSERVICE_CONNECTION_TIMEOUT_ERROR;
		}
		catch (HTTPSendException $e)
		{
			trigger_error("Single-Sign On Service at: ". $oCI->toURL() ." is unavailable due to ". get_class($e), E_USER_WARNING);
			return self::iSERVICE_READ_TIMEOUT_ERROR;
		}
		catch (HTTPException $e)
		{
			trigger_error("Internal error while communicating with Single-Sign On Service at: ". $oCI->toURL() ." due to ". get_class($e), E_USER_WARNING);
			return self::iSERVICE_INTERNAL_ERROR;
		}
	}

	/**
	 * Performs a search in mPoint's Transaction Logs based on the specified parameters
	 * 
	 * @param array $aClientIDs		A list of client IDs who must own the found transactions
	 * @param integer $id			mPoint's unique ID of the transaction
	 * @param string $ono			The client's order number for the transaction
	 * @param CustomerInfo $oCI		Information about the customer's profile
	 * @param string $start			The start date / time for when transactions must have been created in order to be included in the search result
	 * @param string $end			The end date / time for when transactions must have been created in order to be included in the search result
	 * @param boolean $verbose		Boolean flag indicating whether debug data shoud be included
	 * @param integer $limit		Number of results that are returned by the transaction search
	 * @param integer $offset		The offset from which the results returned by the search should start, any results before the offset are skipped by the search
	 * @return multitype:TransactionLogInfo
	 */
	public function searchTransactionLogs(array $aClientIDs, $id=-1, $ono="", CustomerInfo $oCI=null, $start="", $end="", $verbose=false, $limit=100, $offset=0)
	{
		$sql = "";
		// A search for an Order Number makes searching the end-user's Transaction table obsolete 
		if (empty($ono) === true)
		{
			// Fetch all Transfers
			$sql = "SELECT EUT.id, '' AS orderno, '' AS externalid, EUT.typeid, CL.countryid, EUT.toid, EUT.fromid, EUT.created, EUT.stateid AS stateid,
						EUA.id AS customerid, EUA.firstname, EUA.lastname, EUA.externalid AS customer_ref, EUA.countryid * 100 AS operatorid, EUA.mobile, EUA.email, '' AS language,
						CL.id AS clientid, CL.name AS client,
						-1 AS accountid, '' AS account,
						-1 AS pspid, '' AS psp,
						-1 AS paymentmethodid, '' AS paymentmethod,
						EUT.amount, -1 AS captured, -1 AS points, -1 AS reward, -1 AS refund, EUT.fee, 0 AS mode, EUT.ip, EUT.message AS description
					FROM EndUser".sSCHEMA_POSTFIX.".Transaction_Tbl EUT
	    			INNER JOIN EndUser".sSCHEMA_POSTFIX.".Account_Tbl EUA ON EUT.accountid = EUA.id
	    			INNER JOIN EndUser".sSCHEMA_POSTFIX.".CLAccess_Tbl CLA ON CLA.accountid = EUA.id
					INNER JOIN Client".sSCHEMA_POSTFIX.".Client_Tbl CL ON  CL.id = CLA.clientid
					WHERE EUT.txnid IS NULL AND CL.id IN (". implode(",", $aClientIDs) .")";
			if (intval($id) > 0) { $sql .= " AND EUA.id = '". floatval($id) ."'"; }
			if ( ($oCI instanceof CustomerInfo) === true)
			{
				if ($oCI->getMobile() > 0) { $sql .= " AND EUA.countryid = ". $oCI->getCountryID() ." AND EUA.mobile = '". $oCI->getMobile() ."'"; }
				if (strlen($oCI->getEMail() ) > 0) { $sql .= " AND EUA.email = '". $this->getDBConn()->escStr($oCI->getEMail() ) ."'"; }
				if (strlen($oCI->getCustomerRef() ) > 0) { $sql .= " AND EUA.externalid = '". $this->getDBConn()->escStr($oCI->getCustomerRef() ) ."'"; }
			}
			if (empty($start) === false && strlen($start) > 0) { $sql .= " AND '". $this->getDBConn()->escStr(date("Y-m-d H:i:s", strtotime($start) ) ) ."' <= EUT.created"; }
			if (empty($end) === false && strlen($end) > 0) { $sql .= " AND EUT.created <= '". $this->getDBConn()->escStr(date("Y-m-d H:i:s", strtotime($end) ) ) ."'"; }
			$sql .= "
					UNION";
		}
		// Fetch all Purchases
		$sql .= "
				SELECT Txn.id, Txn.orderid AS orderno, Txn.extid AS externalid, Txn.typeid, Txn.countryid, -1 AS toid, -1 AS fromid, Txn.created,
					(CASE
					 WHEN M4.stateid IS NOT NULL THEN M4.stateid
					 WHEN M3.stateid IS NOT NULL THEN M3.stateid
					 WHEN M2.stateid IS NOT NULL THEN M2.stateid
					 WHEN M1.stateid IS NOT NULL THEN M1.stateid
					 ELSE -1
					 END) AS stateid,
					EUA.id AS customerid, EUA.firstname, EUA.lastname, Coalesce(Txn.customer_ref, EUA.externalid) AS customer_ref, Txn.operatorid, Txn.mobile, Txn.email, Txn.lang AS language,
					CL.id AS clientid, CL.name AS client,
					Acc.id AS accountid, Acc.name AS account,
					PSP.id AS pspid, PSP.name AS psp,
					PM.id AS paymentmethodid, PM.name AS paymentmethod,
					Txn.amount, Txn.captured, Txn.points, Txn.reward, Txn.refund, Txn.fee, Txn.mode, Txn.ip, Txn.description
				FROM Log".sSCHEMA_POSTFIX.".Transaction_Tbl Txn
				INNER JOIN Client".sSCHEMA_POSTFIX.".Client_Tbl CL ON Txn.clientid = CL.id
				INNER JOIN Client".sSCHEMA_POSTFIX.".Account_Tbl Acc ON Txn.accountid = Acc.id
				INNER JOIN Log".sSCHEMA_POSTFIX.".Message_Tbl M ON Txn.id = M.txnid
				LEFT OUTER JOIN System".sSCHEMA_POSTFIX.".PSP_Tbl PSP ON Txn.pspid = PSP.id
				LEFT OUTER JOIN System".sSCHEMA_POSTFIX.".Card_Tbl PM ON Txn.cardid = PM.id
				LEFT OUTER JOIN Log".sSCHEMA_POSTFIX.".Message_Tbl M1 ON Txn.id = M1.txnid AND M1.stateid = ". Constants::iPAYMENT_ACCEPTED_STATE ."
				LEFT OUTER JOIN Log".sSCHEMA_POSTFIX.".Message_Tbl M2 ON Txn.id = M2.txnid AND M2.stateid = ". Constants::iPAYMENT_CAPTURED_STATE ."
				LEFT OUTER JOIN Log".sSCHEMA_POSTFIX.".Message_Tbl M3 ON Txn.id = M3.txnid AND M3.stateid = ". Constants::iPAYMENT_REFUNDED_STATE ."
				LEFT OUTER JOIN Log".sSCHEMA_POSTFIX.".Message_Tbl M4 ON Txn.id = M4.txnid AND M4.stateid = ". Constants::iPAYMENT_CANCELLED_STATE ."
				LEFT OUTER JOIN EndUser".sSCHEMA_POSTFIX.".Account_Tbl EUA ON Txn.euaid = EUA.id
				WHERE CL.id IN (". implode(",", $aClientIDs) .")";
		if (intval($id) > 0) { $sql .= " AND Txn.id = '". floatval($id) ."'"; }
		if (empty($ono) === false) { $sql .= " AND Txn.orderid = '". $this->getDBConn()->escStr($ono) ."'"; }
		if ( ($oCI instanceof CustomerInfo) === true)
		{
			if ($oCI->getMobile() > 0) { $sql .= " AND Txn.operatorid / 100 = ". $oCI->getCountryID() ." AND Txn.mobile = '". $oCI->getMobile() ."'"; }
			if (strlen($oCI->getEMail() ) > 0) { $sql .= " AND Txn.email = '". $this->getDBConn()->escStr($oCI->getEMail() ) ."'"; }
			if (strlen($oCI->getCustomerRef() ) > 0) { $sql .= " AND Txn.customer_ref = '". $this->getDBConn()->escStr($oCI->getCustomerRef() ) ."'"; }
		}
		if (empty($start) === false && strlen($start) > 0) { $sql .= " AND '". $this->getDBConn()->escStr(date("Y-m-d H:i:s", strtotime($start) ) ) ."' <= Txn.created"; }
		if (empty($end) === false && strlen($end) > 0) { $sql .= " AND Txn.created <= '". $this->getDBConn()->escStr(date("Y-m-d H:i:s", strtotime($end) ) ) ."'"; }
		$sql .= "
				ORDER BY created DESC";
		if (intval($limit) > 0 || intval($offset) > 0)
		{
			$sql .= "\n";
			if (intval($limit) > 0) { $sql .= "LIMIT ". intval($limit); }
			if (intval($offset) > 0) { $sql .= " OFFSET ". intval($offset); }
		}
//		echo $sql ."\n";
file_put_contents(sLOG_PATH ."/jona.log", $sql);
		$res = $this->getDBConn()->query($sql);
	
		$sql = "SELECT stateid
				FROM Log".sSCHEMA_POSTFIX.".Message_Tbl
				WHERE txnid = $1 AND stateid IN (". Constants::iINPUT_VALID_STATE .", ". Constants::iPAYMENT_INIT_WITH_PSP_STATE .", ". Constants::iPAYMENT_ACCEPTED_STATE .", ". Constants::iPAYMENT_CAPTURED_STATE .", ". Constants::iPAYMENT_DECLINED_STATE .")
				ORDER BY id DESC";
//		echo $sql ."\n";
		$stmt1 = $this->getDBConn()->prepare($sql);
		$sql = "SELECT id, stateid, data, created
				FROM Log".sSCHEMA_POSTFIX.".Message_Tbl
				WHERE txnid = $1
				ORDER BY id ASC";
//		echo $sql ."\n";
		$stmt2 = $this->getDBConn()->prepare($sql);
		
		$aObj_TransactionLogs = array();
		$aObj_CountryConfigurations = array();
		$aTypes = array(Constants::iCARD_PURCHASE_TYPE,
						Constants::iPURCHASE_VIA_WEB,
						Constants::iWEB_SUBSCR_TYPE,
						Constants::iPURCHASE_VIA_APP,
						Constants::iAPP_SUBSCR_TYPE,
						Constants::iPURCHASE_OF_EMONEY,
						Constants::iTOPUP_SUBSCR_TYPE,
						Constants::iPURCHASE_OF_POINTS);
	
		// Construct XML Document with data for Transaction
		while ($RS = $this->getDBConn()->fetchName($res) )
		{
			// Purchase
			if ($RS["STATEID"] < 0 && in_array($RS["TYPEID"], $aTypes) === true)
			{
				$aParams = array($RS["ID"]);
				$res1 = $this->getDBConn()->execute($stmt1, $aParams);
				if (is_resource($res1) === true)
				{
					$RS1 = $this->getDBConn()->fetchName($res1);
					if (is_array($RS1) === true) { $RS["STATEID"] = $RS1["STATEID"]; }
				}
			}
			
			if (array_key_exists($RS["COUNTRYID"], $aObj_CountryConfigurations) === false) { $aObj_CountryConfigurations[$RS["COUNTRYID"] ] = CountryConfig::produceConfig($this->getDBConn(), $RS["COUNTRYID"]); }
			$aObj_Messages = array();
			if ($verbose === true && in_array($RS["TYPEID"], $aTypes) === true)
			{
				$aParams = array($RS["ID"]);
				$res2 = $this->getDBConn()->execute($stmt2, $aParams);
			
				if (is_resource($res2) === true)
				{
					// Additional record sets
					while ($RS2 = $this->getDBConn()->fetchName($res2) )
					{
						$aObj_Messages[] = new MessageInfo($RS2["ID"],
														   $RS2["STATEID"],
														   gmdate("Y-m-d H:i:sP", strtotime(substr($RS2["CREATED"], 0, strpos($RS2["CREATED"], ".") ) ) ),
														   $RS2["DATA"]);
					}
				}
			}
			$aObj_TransactionLogs[] = new TransactionLogInfo($RS["ID"],
															 $RS["TYPEID"],
															 $RS["ORDERNO"],
															 $RS["EXTERNALID"],
															 new BasicConfig($RS["CLIENTID"], $RS["CLIENT"]),
															 new BasicConfig($RS["ACCOUNTID"], $RS["ACCOUNT"]),
															 $RS["PSPID"] > 0 ? new BasicConfig($RS["PSPID"], $RS["PSP"]) : null,
															 $RS["PAYMENTMETHODID"] > 0 ? new BasicConfig($RS["PAYMENTMETHODID"], $RS["PAYMENTMETHOD"]) : null,
															 $RS["STATEID"],
															 $aObj_CountryConfigurations[$RS["COUNTRYID"] ],
															 $RS["AMOUNT"],
															 $RS["CAPTURED"],
															 $RS["POINTS"],
															 $RS["REWARD"],
															 $RS["REFUND"],
															 $RS["FEE"],
															 $RS["MODE"],
															 new CustomerInfo($RS["CUSTOMERID"], $RS["OPERATORID"]/100, $RS["MOBILE"], $RS["EMAIL"], $RS["CUSTOMER_REF"], $RS["FIRSTNAME"] ." ". $RS["LASTNAME"], $RS["LANGUAGE"]),
															 $RS["IP"],
															 gmdate("Y-m-d H:i:sP", strtotime(substr($RS["CREATED"], 0, strpos($RS["CREATED"], ".") ) ) ),
															 $aObj_Messages);
		}
	
		return $aObj_TransactionLogs;
	}
}
?>