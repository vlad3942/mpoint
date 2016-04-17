<?php

// Local mBE classes
require_once ("enduseraccount.php");

function saveMessage(RDB $obj_DB, $iFromID , $iToID, $sText)
{	
	$sql = "INSERT INTO Log". sSCHEMA_POSTFIX .".Message_Tbl
						(fromid, toid, data)
					VALUES
						(". intval($iFromID) .", '". intval($iToID) ."', '". $sText ."')";
	
	//		echo $sql ."\n";
	$res = $obj_DB->query($sql);
	// Unable execute SQL query
	if (is_resource($res) === false) { $id = -1; }
	else{
		$sql2 = "SELECT MAX(id) AS id FROM Log". sSCHEMA_POSTFIX .".Message_Tbl";
		// echo $sql2 ."\n";
	
		$res2 = $obj_DB->getName($sql2);
		$id = $res2["ID"];
		
		$sql3 = "UPDATE Log.Message_Tbl SET read = '1'
				WHERE fromid = ".$iToID." AND toid = ".$iFromID;
		$res3 = $obj_DB->query($sql3);		
	}
	return $id;
}

function checkIfFlightQuery($sText, $iMax = 3)
{	
	$aCat['search'] = array('flight','from','to');	
	$sString = strtolower($sText);
	
	$bMatched = false; 
	foreach($aCat as $k => $v) 
	{
	  $replaced = str_replace($v, '##########', $sString);
	  preg_match_all('/##########/i', $replaced, $matches);	 	  
	  if(count($matches[0]) >= $iMax)
	  {	    
	    $bMatched = true;
	  }
	}
	return $bMatched;
}

function sendFlightItinerary($obj_DB, $aGMConnInfo, $sText, $sChatName)
{	
	$aReturnArray = array();
	$bReturnValue = false;
	$sString = strtolower($sText);
	$aWordsArray = explode(" ", $sString);
	$iSourceKey = intval(array_search('from', $aWordsArray) ) + 1 ;
	$iDestinationKey = intval(array_search('to', $aWordsArray) ) + 1 ;
	if($iSourceKey > 0 && $iDestinationKey > 0){
		$aReturnArray['SRC'] = $aWordsArray[$iSourceKey];
		$aReturnArray['DEST'] = $aWordsArray[$iDestinationKey];
		
	}
	$sMessageText = "Please find the options below : ";
	$bReturnValue = sendMessageFromSystem($obj_DB, $aGMConnInfo, $sChatName, $sMessageText, $aReturnArray);
	return $bReturnValue;
}

function sendMessageFromSystem(RDB $obj_DB, $aGMConnInfo, $sChatName, $sText, $aParams = array() )
{
	$bReturn = true;
	
	$bSendMessage = false;
	
	// Instantiate object for holding the necessary information for connecting to GoMobile
	$obj_ConnInfo = GoMobileConnInfo::produceConnInfo($aGMConnInfo);
	// Instantiate client object for communicating with GoMobile
	$obj_GoMobile = new GoMobileClient($obj_ConnInfo);	
	
	$iUserID = getIDFromChatName($obj_DB, $sChatName);
	
	$iPushIDForUser = getPushIDFromUserID($obj_DB, $iUserID);
	
	$iSystemUserID = getIDFromChatName($obj_DB, 'System');	
	
	$code = saveMessage($obj_DB, $iSystemUserID, $iUserID, $sText);
	
	if(empty($iPushIDForUser) === false && $code > 0)
	{
		$iType = 11;
		$sChannel = 123;
		$sKeyword = "CPM";
		//print_r();die('here');
		$sBody = $sText;
		if (empty($iPushIDForUser) === false)
		{
			$b = array();
			$b["aps"] = array("alert" => array("body" => utf8_encode($sBody) ),
					"sound" => "default",
					"action" => "notify");
			$b['CHAT'] = 1;
			if(count($aParams) > 0)
			{
				foreach ($aParams as $key=>$value)
				{
					$b[$key] = $value;
				}
			}
			$obj_MsgInfo = GoMobileMessage::produceMessage($iType, $sChannel, $sKeyword, $iPushIDForUser, json_encode($b) );
		}
		$bSendMessage = true;
	}
	else
	{
		$bSendMessage = false;
		$bReturn  = false;
	}
	
	if($bSendMessage === true)
	{
		/* ========== Send MT-MESSAGE Start ========== */
		$bSend = true;		// Continue to send messages
		$iAttempts = 1;		// Number of Attempts
		// Send messages
		while ($bSend === true && $iAttempts <= 3)
		{
			$iAttempts++;
			try
			{
				// Send MT-MESSAGE to GoMobile
				if ($obj_GoMobile->send($obj_MsgInfo) == 200)
				{
					$bReturn = true;
				}
				// Error
				else
				{
					$bReturn = false;
				}
				$bSend = false;
			}
			// Communication error, retry message sending
			catch (HTTPException $e)
			{
				sleep(pow(10, $iAttempts) );
			}
		}
	}
	
	return $bReturn;
}