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
	const sPERMISSION_GET_PAYMENT_METHODS = "mPoint.GetPaymentMethods";
	const sPERMISSION_GET_CLIENT = "mPoint.GetClients";
	const sPERMISSION_GET_PAYMENT_SERVICE_PROVIDERS = "mPoint.GetPaymentServiceProviders";
	const sPERMISSION_SEARCH_TRANSACTION_LOGS = "mPoint.SearchTransactionLogs";
	
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
	 * @param long $mob				The Customer's Mobile Number
	 * @param string $email			The Customer's E-Mail address
	 * @param string $cr			The Customer Reference for the End-User
	 * @param string $start			The start date / time for when transactions must have been created in order to be included in the search result
	 * @param string $end			The end date / time for when transactions must have been created in order to be included in the search result
	 * @param boolean $debug		Boolean flag indicating whether debug data shoud be included
	 * @return multitype:TransactionLogInfo
	 */
	public function searchTransactionLogs(array $aClientIDs, $id=-1, $ono="", $mob=-1, $email="", $cr="", $start="", $end="", $debug=false)
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
		if (floatval($mob) > 0) { $sql .= " AND EUA.mobile = '". floatval($mob) ."'"; }
		if (empty($email) === false) { $sql .= " AND EUA.email = '". $this->getDBConn()->escStr($email) ."'"; }
		if (empty($cr) === false) { $sql .= " AND EUA.externalid = '". $this->getDBConn()->escStr($cr) ."'"; }
		if (empty($start) === false && strlen($start) > 0) { $sql .= " AND '". $this->getDBConn()->escStr(date("Y-m-d H:i:s", strtotime($start) ) ) ."' <= EUT.created"; }
		if (empty($end) === false && strlen($end) > 0) { $sql .= " AND EUT.created <= '". $this->getDBConn()->escStr(date("Y-m-d H:i:s", strtotime($end) ) ) ."'"; }
		// Fetch all Purchases
		$sql .= "
				UNION
				SELECT Txn.id, Txn.orderid AS orderno, Txn.extid AS externalid, ". Constants::iCARD_PURCHASE_TYPE ." AS typeid, Txn.countryid, -1 AS toid, -1 AS fromid, Txn.created,
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
			
			if (array_key_exists($RS["COUNTRYID"], $aObj_CountryConfigurations) === false) { $aObj_CountryConfigurations[$RS["COUNTRYID"] ] = CountryConfig::produceConfig($this->getDBConn(), $RS["COUNTRYID"]); }
			$aObj_Messages = array();
			if ($debug === true && $RS["TYPEID"] == Constants::iCARD_PURCHASE_TYPE)
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
															 $RS["CAPTURE"],
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