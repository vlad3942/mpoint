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

/* ==================== mConsole Exception Classes Start ==================== */
/**
 * Super class for all exceptions thrown by mConsole operations
 */
class mConsoleException extends Exception { }


//For the save client related exceptions.
class mConsoleSaveFailedException extends mConsoleException { }
class mConsoleSaveClientFailedException extends mConsoleSaveFailedException { }
class mConsoleSaveAccountFailedException extends mConsoleSaveClientFailedException { }
class mConsoleSaveMerchantSubAccountFailedException extends mConsoleSaveAccountFailedException { }
class mConsoleSaveMerchantAccountFailedException extends mConsoleSaveClientFailedException { }
class mConsoleSaveCardAccessFailedException extends mConsoleSaveClientFailedException { }
class mConsoleSaveKeywordFailedException extends mConsoleSaveClientFailedException { }
class mConsoleSaveURLFailedException extends mConsoleSaveClientFailedException { }
class mConsoleSaveIINRangeFailedException extends mConsoleSaveClientFailedException { }


//For the disable client related exceptions.
class mConsoleDisableFailedException extends mConsoleException { }
class mConsoleDisableClientFailedException extends mConsoleDisableFailedException { }
class mConsoleDisableAccountFailedException extends mConsoleDisableClientFailedException { }
class mConsoleDisableMerchantSubAccountFailedException extends mConsoleDisableAccountFailedException { }
class mConsoleDisableMerchantAccountFailedException extends mConsoleDisableClientFailedException { }
class mConsoleDisableCardAccessFailedException extends mConsoleDisableClientFailedException { }
class mConsoleDisableKeywordFailedException extends mConsoleDisableClientFailedException { }
class mConsoleDisableURLFailedException extends mConsoleDisableClientFailedException { }
class mConsoleDisableIINRangeFailedException extends mConsoleDisableClientFailedException { }

/* ==================== mConsole Exception Classes End ==================== */

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
	const sPERMISSION_VOID_PAYMENTS = "mpoint.void-payments.get.x";
	const sPERMISSION_CAPTURE_PAYMENTS = "mpoint.capture-payments.get.x";	
	
	public function saveClient($cc, $storecard, $autocapture, $name, $username, $password, $maxamt, $lang, $smsrcpt, $emailrcpt, $mode, $method, $send_pspid, $identification, $transaction_ttl, $id = -1)
	{
		if ($id > 0)
		{
			$sql = "UPDATE Client". sSCHEMA_POSTFIX .".Client_Tbl
					SET store_card = ". intval($storecard) .", auto_capture = '". intval($autocapture) ."', name = '". $this->getDBConn()->escStr($name) ."', username='". $this->getDBConn()->escStr($username) ."', passwd='". $this->getDBConn()->escStr($password) ."', countryid = ". $cc .",
						maxamount = ". intval($maxamt) .", lang = '". $this->getDBConn()->escStr($lang) ."', smsrcpt = '". intval($smsrcpt) ."', emailrcpt = '". intval($emailrcpt) ."' , mode = ". intval($mode) .", method = '". $this->getDBConn()->escStr($method) ."', send_pspid = '". intval($send_pspid) ."',
						identification = ". intval($identification) .", transaction_ttl = ". intval($transaction_ttl) ."
					WHERE id = ". intval($id);
		}
		else
		{
			$sql = "SELECT Nextval('Client". sSCHEMA_POSTFIX .".Client_Tbl_id_seq') AS id";
			$RS = $this->getDBConn()->getName($sql);
			$id = $RS["ID"];
			
			$sql = "INSERT INTO Client". sSCHEMA_POSTFIX .".Client_Tbl
						(id, store_card, auto_capture, name, username, passwd, countryid, flowid, maxamount, lang, smsrcpt, emailrcpt, mode, method, send_pspid, identification, transaction_ttl)
					VALUES
						(". $id .", ". intval($storecard) .",'". intval($autocapture) ."', '". $this->getDBConn()->escStr($name) ."' , '". $this->getDBConn()->escStr($username) ."', '". $this->getDBConn()->escStr($password) ."',". intval($cc) .", ".intval(1) .",
						 ". intval($maxamt) .", '". $this->getDBConn()->escStr($lang) ."', '". intval($smsrcpt) ."', '". intval($emailrcpt) ."' ,". intval($mode) .",'". $this->getDBConn()->escStr($method) ."','". intval($send_pspid) ."',". intval($identification) .",". intval($transaction_ttl) .")";
		}
//		echo $sql ."\n";
		$res = $this->getDBConn()->query($sql);
		// Unable execute SQL query
		if (is_resource($res) === false) { $id = -1; }
		
		return $id;
	}
	
	public function saveAccount($clientid, $name, $markup, $id = -1)
	{	
		if ($id > 0)
		{
			$sql = "UPDATE Client". sSCHEMA_POSTFIX .".Account_Tbl
					SET name = '". $this->getDBConn()->escStr($name) ."', markup='". $this->getDBConn()->escStr($markup) ."', enabled = '1'
					WHERE id = ". intval($id);
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
		}
		//echo $sql ."\n";
		$res = $this->getDBConn()->query($sql);
		// Unable execute SQL query
		if (is_resource($res) === false) { $id = -1; }
		
		return $id;
	}
	
	public function disableAccounts($clientid)
	{
		$sql = "UPDATE Client". sSCHEMA_POSTFIX .".Account_Tbl
				SET enabled = '0'
				WHERE clientid = ". intval($clientid);
//		echo $sql ."\n";
				
		return is_resource($this->getDBConn()->query($sql) );
	}
	
	public function saveMerchantSubAccount($accountid, $pspid, $name, $id = -1)
	{			
		if ($id > 0)
		{
			$sql = "UPDATE Client". sSCHEMA_POSTFIX .".MerchantSubAccount_Tbl
					SET name = '". $this->getDBConn()->escStr($name) ."', pspid = ". intval($pspid) ." , enabled = '1'
					WHERE id = ". intval($id);
		}
		else
		{
			$sql = "SELECT Nextval('Client". sSCHEMA_POSTFIX .".MerchantSubAccount_Tbl_id_seq') AS id";
			$RS = $this->getDBConn()->getName($sql);
			$id = $RS["ID"];
			
			$sql =  "INSERT INTO Client".sSCHEMA_POSTFIX.".MerchantSubAccount_Tbl 
						(id, accountid, pspid, name)
				 	VALUES
						(". $id .", ". intval($accountid) .", ". intval($pspid) .", '". $this->getDBConn()->escStr($name) ."')";
		}			
		//echo $sql ."\n";
		$res = $this->getDBConn()->query($sql);
		// Unable execute SQL query
		if (is_resource($res) === false) { $id = -1; }
		
		return $id;
	}
	
	public function disableMerchantSubAccounts($accountid)
	{
		$sql = "UPDATE Client". sSCHEMA_POSTFIX .".MerchantSubAccount_Tbl
				SET enabled = '". intval(false)."'
				WHERE accountid = ". intval($accountid);
		//echo $sql ."\n";				
		return is_resource($this->getDBConn()->query($sql) );
	}
	
	public function saveMerchantAccount($clientid, $pspid, $name, $username, $password, $storedcard, $id = -1)
	{	
		
		if(empty($id) === true )
		{
			//Entry exists but is disabled.
			$sqlSelect = "Select id from Client". sSCHEMA_POSTFIX .".MerchantAccount_Tbl
						WHERE clientid = ". intval($clientid) ." AND pspid = ". intval($pspid);
			$RSONE = $this->getDBConn()->getName($sqlSelect);
			$id = $RSONE["ID"];			
		}
		
		if (intval($id) > 0)
		{
			$sql = "UPDATE Client". sSCHEMA_POSTFIX .".MerchantAccount_Tbl
					SET name = '". $this->getDBConn()->escStr($name) ."', username ='". $this->getDBConn()->escStr($username) ."', passwd ='". $this->getDBConn()->escStr($password) ."',
						pspid = ". intval($pspid) .", stored_card = '". intval($storedcard) ."', enabled = '". intval(true) ."'
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
						(". $id .", ". intval($clientid) .", ". intval($pspid) .", '". $this->getDBConn()->escStr($name) ."', '". $this->getDBConn()->escStr($username) ."', '". $this->getDBConn()->escStr($password) ."', '". intval($storedcard) ."')";
		}		
		//echo $sql ."\n";
		$res = $this->getDBConn()->query($sql);
		//Unable execute SQL query
		if (is_resource($res) === false) { $id = -1; }
		
		return $id;
	}
	
	public function disableMerchantAccounts($clientid)
	{
		$sql = "UPDATE Client". sSCHEMA_POSTFIX .".MerchantAccount_Tbl
				SET enabled = '". intval(false) ."'
				WHERE clientid = ". intval($clientid);		
		//echo $sql ."\n";		
		return is_resource($this->getDBConn()->query($sql) );
	}
	
	/**
	 * Saves the configuration for a static route.
	 * 
	 * @param integer $clientid		The unique ID of the Client whose routing configuration should be saved
	 * @param integer $pmid			The unique ID of the Payment Method (Card) that the routing configuration is applicable for
	 * @param integer $pspid		The unique ID of the Payment Service Provider (PSP) that payments made with the specified payment method should be routed to
	 * @param integer $stateid		The unique ID of the route's current state:
	 * 									1. Enabled
	 * 									2. Disabled By Merchant
	 * 									3. Disabled By PSP
	 * 									4. Pre-Requisite not Met
	 * 									5. Temporarily Unavailable
	 * @param integer $countryid	The unique ID for which the route this applicable or -1 for "All" countries for which a routing rule hasn't been specifically configured
	 * @param integer $id			The unique ID of the existing routing configuration that should be changed, pass -1 to create a new routing configuration
	 * @return integer
	 */
	public function saveStaticRoute($clientid, $pmid, $pspid, $stateid, $countryid=-1, $id=-1)
	{
		$clientid = (integer) $clientid;
		$pmid = (integer) $pmid;
		$pspid = (integer) $pspid;
		$countryid = (integer) $countryid;
		if ($countryid <= 0) { $countryid = "NULL"; }
		$id = (integer) $id;
		
		if ($id <= 0)
		{
			// Static Route exists but is disabled.
			$sql = "SELECT id
					FROM Client". sSCHEMA_POSTFIX .".CardAccess_Tbl
					WHERE clientid = ". $clientid ." AND cardid = ". $pmid ." AND pspid = ". $pspid ." AND countryid = ". $countryid;
//			echo $sql ."\n";
			$RS = $this->getDBConn()->getName($sql);
			$id = $RS["ID"];			
		}
		
		if ($id > 0)
		{
			$sql = "UPDATE Client". sSCHEMA_POSTFIX .".CardAccess_Tbl
					SET countryid = ". $countryid .", cardid = ". $pmid .", pspid = ". $pspid .", 
						stateid = ". intval($stateid) .", enabled = '1'
					WHERE id = ". $id;				
		}
		else
		{
			$sql = "SELECT Nextval('Client". sSCHEMA_POSTFIX .".CardAccess_Tbl_id_seq') AS id";
			$RS = $this->getDBConn()->getName($sql);
			$id = $RS["ID"];
			
			$sql = "INSERT INTO Client". sSCHEMA_POSTFIX .".CardAccess_Tbl 
						(id, clientid, cardid, pspid, countryid, stateid)
				    VALUES
						(". $id .", ". $clientid .", ". $pmid .", ". $pspid .", ". $countryid .", ". intval($stateid) .")";
		}		
//		echo $sql ."\n";
		$res = $this->getDBConn()->query($sql);
		//Unable execute SQL query
		if (is_resource($res) === false) { $id = -1; }
		
		return $id;		
	}
	
	public function disableAllCardAccess($clientid)
	{
		$sql = "UPDATE Client". sSCHEMA_POSTFIX .".CardAccess_Tbl
				SET enabled = '". intval(false) ."'
				WHERE clientid = ". intval($clientid);		
		//echo $sql ."\n";		
		return is_resource($this->getDBConn()->query($sql) );
	}
	
	public function saveKeyword ($clientid, $name, $id = -1)
	{
		if ($id > 0)
		{
			$sql = "UPDATE Client". sSCHEMA_POSTFIX .".KeyWord_Tbl
					SET name = '". $this->getDBConn()->escStr($name) ."', enabled = '". intval(true) ."', standard = '".intval(true)."'
					WHERE id = ". intval($id) ." AND clientid = ". intval($clientid);						
		}
		else
		{
			$sql = "SELECT Nextval('Client". sSCHEMA_POSTFIX .".KeyWord_Tbl_id_seq') AS id";
			$RS = $this->getDBConn()->getName($sql);
			$id = $RS["ID"];
			
			$sql = "INSERT INTO Client". sSCHEMA_POSTFIX .".KeyWord_Tbl 
					(id, clientid , name, standard)
				VALUES
					(". $id .", ". intval($clientid) .", '". $this->getDBConn()->escStr($name) ."', true)";
		}		
		//echo $sql ."\n";
		$res = $this->getDBConn()->query($sql);
		//Unable execute SQL query
		if (is_resource($res) === false) { $id = -1; }
		
		return $id;		
	}
	
	public function disableKeyword($clientid)
	{
		$sql = "UPDATE Client". sSCHEMA_POSTFIX .".KeyWord_Tbl
				SET enabled = '". intval(false) ."'
				WHERE clientid = ". intval($clientid);		
		//echo $sql ."\n";		
		return is_resource($this->getDBConn()->query($sql) );
	}
	
	public function saveURL($clientid, $typeid, $url, $id = -1)
	{			
		if ($id > 0 && intval($id) == intval($clientid) && intval($typeid) == ClientConfig::iCALLBACK_URL)
		{
			$sql = "UPDATE Client". sSCHEMA_POSTFIX .".Client_Tbl
					SET callbackurl = '". $this->getDBConn()->escStr($url) ."'
					WHERE id = ". intval($clientid);
		}
		else if ($id > 0 && intval($id) == intval($clientid) && intval($typeid) == ClientConfig::iLOGO_URL)
		{
			$sql = "UPDATE Client". sSCHEMA_POSTFIX .".Client_Tbl
					SET logourl = '". $this->getDBConn()->escStr($url) ."'
					WHERE id = ". intval($clientid);
		}
		else if ($id > 0 && intval($id) == intval($clientid) && intval($typeid) == ClientConfig::iCSS_URL)
		{
			$sql = "UPDATE Client". sSCHEMA_POSTFIX .".Client_Tbl
					SET cssurl = '". $this->getDBConn()->escStr($url) ."'
					WHERE id = ". intval($clientid);
		}
		else if ($id > 0 && intval($id) == intval($clientid) && intval($typeid) == ClientConfig::iCANCEL_URL)
		{
			$sql = "UPDATE Client". sSCHEMA_POSTFIX .".Client_Tbl
					SET cancelurl = '". $this->getDBConn()->escStr($url) ."'
					WHERE id = ". intval($clientid);
		}
		else if ($id > 0 && intval($id) == intval($clientid) && intval($typeid) == ClientConfig::iACCEPT_URL)
		{
			$sql = "UPDATE Client". sSCHEMA_POSTFIX .".Client_Tbl
					SET accepturl = '". $this->getDBConn()->escStr($url) ."'
					WHERE id = ". intval($clientid);
		}
		else if ($id > 0 && intval($id) == intval($clientid) && intval($typeid) == ClientConfig::iICON_URL)
		{
			$sql = "UPDATE Client". sSCHEMA_POSTFIX .".Client_Tbl
					SET iconurl = '". $this->getDBConn()->escStr($url) ."'
					WHERE id = ". intval($clientid);			
		}
		else
		{
			if ($id > 0)
			{
				$sql = "UPDATE Client". sSCHEMA_POSTFIX .".URL_Tbl
						SET url = '". $this->getDBConn()->escStr($url) ."', urltypeid = ". intval($typeid) .", enabled = '". intval(true) ."'
						WHERE id = ". intval($id) ." AND clientid = ". intval($clientid);				
			}
			else
			{
				$sql = "SELECT Nextval('Client". sSCHEMA_POSTFIX .".URL_Tbl_id_seq') AS id";
				$RS = $this->getDBConn()->getName($sql);
				$id = $RS["ID"];
				
				$sql = "INSERT INTO Client". sSCHEMA_POSTFIX .".URL_Tbl
							(id, clientid , urltypeid, url )
						VALUES
							(". $id .", ". intval($clientid) .", ". intval($typeid) .", '". $this->getDBConn()->escStr($url). "')";
			}			
		}	
		//echo $sql ."\n";
		$res = $this->getDBConn()->query($sql);
		//Unable execute SQL query
		if (is_resource($res) === false) { $id = -1; }
				
		return $id;
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
		
		return ( is_resource($this->getDBConn()->query($sqlOne) ) && is_resource($this->getDBConn()->query($sqlTwo) ) );
	}

	public function saveIINRange($clientid, $actionid, $min, $max, $id = -1)
	{
		if($id > 0)
		{
			$sql = "UPDATE Client". sSCHEMA_POSTFIX .".IINList_Tbl
					SET iinactionid = ". intval($actionid) .", min = ". intval($min) .", max = ". intval($max) .",
					enabled = '" . intval(true) . "'
					WHERE id = ". intval($id) ." AND clientid = ". intval($clientid);			
		}
		else
		{
			$sql = "SELECT Nextval('Client". sSCHEMA_POSTFIX .".IINList_Tbl_id_seq') AS id";
			$RS = $this->getDBConn()->getName($sql);
			$id = $RS["ID"];
				
			$sql = "INSERT INTO Client". sSCHEMA_POSTFIX .".IINList_Tbl 
						(id, clientid , iinactionid, min, max)
					VALUES
						(". $id .", ". intval($clientid) .", ". intval($actionid) .", ". intval($min) .", ". intval($max) .")";			
		}
		//echo $sql ."\n";
		$res = $this->getDBConn()->query($sql);
		//Unable execute SQL query
		if (is_resource($res) === false) { $id = -1; }
				
		return $id;
	}
	
	public function disableIINRanges($clientid)
	{
		$sql = "UPDATE Client". sSCHEMA_POSTFIX .".IINList_Tbl
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
				return self::iAUTHORIZATION_SUCCESSFUL;
				break;
			case (401):	// HTTP 401 Unauthorized
				trigger_error("Single-Sign On Service at: ". $oCI->toURL() ." rejected authorization with HTTP Code: ". $code, E_USER_WARNING);
				return self::iUNAUTHORIZED_USER_ACCESS_ERROR;
				break;
			case (402):	// HTTP 402 Payment Required
				trigger_error("Single-Sign On Service at: ". $oCI->toURL() ." rejected license with HTTP Code: ". $code, E_USER_WARNING);
				return self::iINSUFFICIENT_CLIENT_LICENSE_ERROR;
				break;
			case (403):	// HTTP 403 Forbidden
				trigger_error("Single-Sign On Service at: ". $oCI->toURL() ." rejected permission with HTTP Code: ". $code, E_USER_WARNING);
				return self::iINSUFFICIENT_USER_PERMISSIONS_ERROR;
				break;
			default:
				trigger_error("Single-Sign On Service at: ". $oCI->toURL() ." returned unexpected HTTP Code: ". $code, E_USER_WARNING);
				return self::iSERVICE_INTERNAL_ERROR;
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
	
	/**
	 * Performs a capture operation on the specified transaction by invoking mPoint's "Capture" API in the "Buy" API suite.
	 * The method may return an array containing the following status codes:
	 * 	 1. Internal Error while Communicating with Capture Service
	 *	 2. Capture Service unreachable
	 * 	 3. Capture Service unavailable
	 * 	 51. Amount is undefined
	 * 	 52. Amount is too small
	 * 	 53. Amount is too great
	 * 	171. Undefined mPoint Transaction ID
	 * 	172. Invalid mPoint Transaction ID
	 * 	173. Transaction Not Found
	 * 	174. Transaction Disabled
	 * 	175. Payment Rejected for Transaction
	 * 	176. Payment already Captured for Transaction
	 * 	177. Payment already Refunded for Transaction
	 * 	181. Undefined Order ID
	 * 	183. Order ID doesn't match Transaction
	 * 	500. Unknown Error
	 * 	997. Capture not supported by PSP
	 * 	998. Error while communicating with PSP
	 * 	999. Capture Declined
	 * 1000. Success
	 * 
	 * @param HTTPConnInfo $oCI		The connection information for the mPoint's "Capture" API in the "Buy" API suite
	 * @param integer $clientid		The unique ID of the Client on whose behalf the Capture operation is being performed
	 * @param integer $txnid		The unique ID of the transaction that should be captured
	 * @param string $ono			The order number for the transaction that should be captured
	 * @param integer $amt			The amount that should be captured for the transaction
	 * @return array
	 */
	public function capture(HTTPConnInfo $oCI, $clientid, $txnid, $ono, $amt)
	{
		try
		{
			$h = str_replace("{METHOD}", "POST", $this->constHTTPHeaders() );
			$b = "clientid=". intval($clientid) ."&mpointid=". intval($txnid) ."&orderid=". urlencode($ono) ."&amount=". intval($amt);
			
			$obj_Client = new HTTPClient(new Template, $oCI);
			$obj_Client->connect();
			$code = $obj_Client->send($h, $b);
			$obj_Client->disconnect();
			
			$aStatusCodes = array();
			$aMessages = explode("&", $obj_Client->getReplyBody() );		
			foreach ($aMessages as $msg)
			{
				$aStatusCodes[] = (integer) substr($msg, strpos($msg, "=") + 1); 
			}
			if (count($aStatusCodes) == 0) { $aStatusCodes[] = 500; }
		}
		catch (HTTPConnectionException $e)
		{
			trigger_error("Capture Service at: ". $oCI->toURL() ." is unreachable due to ". get_class($e), E_USER_WARNING);
			$aStatusCodes[] = self::iSERVICE_CONNECTION_TIMEOUT_ERROR;
		}
		catch (HTTPSendException $e)
		{
			trigger_error("Capture Service at: ". $oCI->toURL() ." is unavailable due to ". get_class($e), E_USER_WARNING);
			$aStatusCodes[] = self::iSERVICE_READ_TIMEOUT_ERROR;
		}
		catch (HTTPException $e)
		{
			trigger_error("Internal error while communicating with Capture Service at: ". $oCI->toURL() ." due to ". get_class($e), E_USER_WARNING);
			$aStatusCodes[] = self::iSERVICE_INTERNAL_ERROR;
		}
		
		return $aStatusCodes;
	}
	
	/**
	 * Performs a refund operation on the specified transaction by invoking mPoint's "Refund" API in the "Buy" API suite.
	 * The method may return an array containing the following status codes:
	 * 	 1. Internal Error while Communicating with Capture Service
	 *	 2. Capture Service unreachable
	 * 	 3. Capture Service unavailable
	 * 	 21. Username is undefined
	 * 	 22. Username is too short
	 * 	 23. Username is too long
	 * 	 24. Username contains Invalid Characters
	 * 	 31. Password is undefined
	 * 	 32. Password is too short
	 * 	 33. Password is too long
	 * 	 34. Password contains Invalid Characters 
	 * 	 51. Amount is undefined
	 * 	 52. Amount is too small
	 * 	 53. Amount is too great
	 * 	171. Undefined mPoint Transaction ID
	 * 	172. Invalid mPoint Transaction ID
	 * 	173. Transaction Not Found
	 * 	174. Transaction Disabled
	 * 	175. Payment Rejected for Transaction
	 * 	176. Payment already Captured for Transaction
	 * 	177. Payment already Refunded for Transaction
	 * 	181. Undefined Order ID
	 * 	182. Transaction not found
	 * 	183. Order ID doesn't match Transaction
	 * 	403. Username / Password doesn't match
	 * 	500. Unknown Error
	 * 	997. Capture not supported by PSP
	 * 	998. Error while communicating with PSP
	 * 	999. Refund Declined
	 * 1000. Success
	 * 
	 * @param HTTPConnInfo $oCI		The connection information for the mPoint's "Capture" API in the "Buy" API suite
	 * @param integer $clientid		The unique ID of the Client on whose behalf the Capture operation is being performed
	 * @param string $username		The mPoint admin username to access the transactions.
	 * @param string $password		The mPoint admin password to access the transactions.
	 * @param integer $txnid		The unique ID of the transaction that should be captured
	 * @param string $ono			The order number for the transaction that should be captured
	 * @param integer $amt			The amount that should be captured for the transaction
	 * @return array
	 */
	public function void(HTTPConnInfo $oCI, $clientid, $username, $password, $txnid, $ono, $amt)
	{
		try
		{
			$h = str_replace("{METHOD}", "POST", $this->constHTTPHeaders() );
			$b = "clientid=". intval($clientid) ."&username=". urlencode($username) ."&password=". urlencode($password) ."&mpointid=". intval($txnid) ."&orderid=". urlencode($ono) ."&amount=". intval($amt);			
			
			$obj_Client = new HTTPClient(new Template, $oCI);
			$obj_Client->connect();
			$code = $obj_Client->send($h, $b);
			$obj_Client->disconnect();
			
			$aStatusCodes = array();
			switch ($code)
			{
			case (403):	// Username / Password doesn't match
				trigger_error("Refund Service at: ". $oCI->toURL() ." did not accept credentials: ". urlencode($username) .":".  urlencode($password) ." for client: ". intval($clientid), E_USER_WARNING);
				$aStatusCodes[] = self::iSERVICE_INTERNAL_ERROR;
				break;
			default:
				$aMessages = explode("&", $obj_Client->getReplyBody() );
				foreach ($aMessages as $msg)
				{
					if(!empty($msg) === true )
						$aStatusCodes[] = (integer) substr($msg, strpos($msg, "=") + 1);
				}
				if (count($aStatusCodes) == 0) { $aStatusCodes[] = 500; }
				break;
			}
		}
		catch (HTTPConnectionException $e)
		{
			trigger_error("Refund Service at: ". $oCI->toURL() ." is unreachable due to ". get_class($e), E_USER_WARNING);
			$aStatusCodes[] = self::iSERVICE_CONNECTION_TIMEOUT_ERROR;
		}
		catch (HTTPSendException $e)
		{
			trigger_error("Refund Service at: ". $oCI->toURL() ." is unavailable due to ". get_class($e), E_USER_WARNING);
			$aStatusCodes[] = self::iSERVICE_READ_TIMEOUT_ERROR;
		}
		catch (HTTPException $e)
		{
			trigger_error("Internal error while communicating with Refund Service at: ". $oCI->toURL() ." due to ". get_class($e), E_USER_WARNING);
			$aStatusCodes[] = self::iSERVICE_INTERNAL_ERROR;
		}
		
		return $aStatusCodes;
	}
	
	/**
	 * Performs a search in mPoint's Transaction Logsand Message tables based on the specified parameters
	 * 
	 * @param array $aClientIDs		A list of client IDs who must own the found transactions
	 * @param string $start			The start date / time for when transactions must have been created in order to be included in the search result
	 * @param string $end			The end date / time for when transactions must have been created in order to be included in the search result
	 * @param array $aAccountIDs		A list of acount IDs related with client ids who must own the found transactions 
	 * @return multitype:TransactionStatisticsInfo
	 */
	public function getTransactionStats(array $aClientIDs, $start, $end, array $aAccountIDs = array() )
	{
		$aStateIDS = array(Constants::iINPUT_VALID_STATE, Constants::iPAYMENT_INIT_WITH_PSP_STATE, Constants::iPAYMENT_ACCEPTED_STATE, Constants::iPAYMENT_CANCELLED_STATE, Constants::iPAYMENT_CAPTURED_STATE, Constants::iPAYMENT_REFUNDED_STATE, Constants::iPAYMENT_REJECTED_STATE, Constants::iPAYMENT_DECLINED_STATE);
		
		$sql = "SELECT date(Msg.created) as createddate, Msg.stateid as stateid, COUNT(Msg.stateid) as stateidcount 
			FROM Log".sSCHEMA_POSTFIX.".Transaction_Tbl Txn, 
			Log".sSCHEMA_POSTFIX.".Message_Tbl Msg 
			WHERE Msg.stateid IN (". implode(",", $aStateIDS) .") AND 
			(Msg.created BETWEEN '". $this->getDBConn()->escStr(date("Y-m-d H:i:s", strtotime($start) ) ) ."' AND 
			'". $this->getDBConn()->escStr(date("Y-m-d H:i:s", strtotime($end) ) ) ."') AND
			Msg.created = (SELECT max(created) FROM Log".sSCHEMA_POSTFIX.".Message_Tbl WHERE txnid = Txn.id) AND 
			Txn.clientid IN (". implode(",", $aClientIDs) .") " ;
		
		if(empty($aAccountIDs) === false)
		{
			$sql .= " AND Txn.accountid IN (". implode(",", $aAccountIDs) .")";
		}
		
		$sql .= " GROUP BY createddate, Msg.stateid ";
		$sql .= " ORDER BY createddate ";
		
		//echo $sql ."\n";exit;
		
		$aRS = array();
		
		$RS = array();
		
		$res = $this->getDBConn()->query($sql);

		if (is_resource($res) === true)
		{

			while($RS = $this->getDBConn()->fetchName($res))
			{
				if (is_array($RS) === true) 
				{
					$aRS[$RS['CREATEDDATE']][$RS['STATEID']] = $RS['STATEIDCOUNT']; 
				}
			}
			
			if(empty($aRS) === false)
			{
				$aTransactionStats = array();

				foreach($aRS as $createddate => $transactioncountdata)
				{
					$missingstateids = array_diff($aStateIDS, array_flip($transactioncountdata));
					foreach($missingstateids as $stateid)
					{
						$aTransactionStats[$createddate][$stateid] = 0;
					}

					$aTransactionStats[$createddate] += $transactioncountdata;
				}

				return new TransactionStatisticsInfo($aTransactionStats);
			} else { return false; }
		} else { return false; }

		
	}
    }
?>