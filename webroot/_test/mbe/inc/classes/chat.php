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

function checkIfFlightQuery($sText)
{	
	$aCat['salut'] = array('value' => '/\b(hi|hello)\b/i' , 'max' => 1);
	$aCat['search'] = array('value' => '/\b(flight|from|to)\b/i' , 'max' => 3);
	$sString = strtolower($sText);
	
	$sMatched = ""; 
	foreach($aCat as $k => $v) 
	{
	  $replaced = preg_replace($v['value'], '##########', $sString);
	  preg_match_all('/##########/i', $replaced, $matches);	  
	  if(count($matches[0]) >= $v['max'])
	  {	    
	    $sMatched = $k;
	  }
	}
	return $sMatched;
}

function sendFlightItinerary($obj_DB, $aGMConnInfo, $sText, $sChatName)
{	
	$aReturnArray = array();
	$bReturnValue = false;
	$aLookUp = array(
		'JED' => 'JEDDAH',
		'RUH' => 'RIYADH',
		'MED' => 'MEDINA',
		'DMM' => 'DAMMAM'
	);
	$i = $iIterations = 1;
	$sString = strtolower($sText);
	$aWordsArray = explode(" ", $sString);
	$iSourceKey = intval(array_search('from', $aWordsArray) ) + 1 ;
	$iDestinationKey = intval(array_search('to', $aWordsArray) ) + 1 ;
	if($iSourceKey > 0 && $iDestinationKey > 0)
	{		
			$aReturnArray['JT'] = 0; //One Way Journey
			$aReturnArray['FR'] = array_search(strtoupper($aWordsArray[$iSourceKey]), $aLookUp);
			$aReturnArray['TO'] = array_search(strtoupper($aWordsArray[$iDestinationKey]), $aLookUp);
			$aReturnArray['DD'] = date("d/m/Y");
			$aReturnArray['AD'] = date("d/m/Y");
			$aReturnArray['A'] = 1;
			$aReturnArray['C'] = 0;
			$aReturnArray['I'] = 0;
			$aReturnArray['CL'] = "Economy Class";
			$aReturnArray['TR'] = "02:00";
			$aReturnArray['ST'] = (string) (10+$i).":00";
			$aReturnArray['ET'] = (string) (12+$i).":00";		
			$aReturnArray['PR'] = 1200;
			$aReturnArray['FN'] = (string) "SGA-".(120 * ($i + 1) );
			$aReturnArray['SN'] = 0;			
				
	}
	$sMessageText = "Please find the options below : ";
	$bReturnValue = sendMessageFromSystem($obj_DB, $aGMConnInfo, $sChatName, $sMessageText, 3, $aReturnArray);
	return $bReturnValue;
}

function sendSalutationMessage($obj_DB, $aGMConnInfo, $sText, $sChatName)
{	
	$bReturnValue = false;
	$sMessageText = "Hi,  Al Salam Alaikum";
	$bReturnValue = sendMessageFromSystem($obj_DB, $aGMConnInfo, $sChatName, $sMessageText);
	return $bReturnValue;
}

function sendMessageFromSystem(RDB $obj_DB, $aGMConnInfo, $sChatName, $sText, $iAction = 2, $aParams = array() )
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
			$b['ACTION'] = $iAction;
			if(count($aParams) > 0)
			{
				foreach ($aParams as $key=>$value)
				{
					$b["TD"][$key] = $value;
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