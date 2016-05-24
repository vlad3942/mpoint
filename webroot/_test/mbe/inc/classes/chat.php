<?php

// Local mBE classes
require_once ("enduseraccount.php");
require_once ("transaction.php");

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
	$aCat['search'] = array('value' => '/\b(flight|flights|from|to)\b/i' , 'max' => 3);
	$aCat['number'] = array('value' => '/\b\d\b/i' , 'max' => 1);
	$aCat['option'] = array('value' => '/\b(option)\b/i' , 'max' => 1);
	$aCat['confirm'] = array('value' => '/\b(confirm)\b/i' , 'max' => 1);
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

function sendMessageForFlightQuery($obj_DB, $aGMConnInfo, $sText, $sChatName, $sSessionID, $obj_DB_MPOINT = NULL, $obj_ClientInfo = NULL)
{
	$aLookUp = array(
			'JED' => 'JEDDAH',
			'RUH' => 'RIYADH',
			'MED' => 'MEDINA',
			'DMM' => 'DAMMAM'
	);
	$aTXNStates = array (
			'SEARCH_QUERY_RECEIVED' => 500,
			'SEARCH_QUERY_RESPONDED' => 501,
			'PAX_DETAILS_RECEIVED' => 502,
			'PAX_DETAILS_RESPONDED' => 503,
			'FLIGHT_OPTION_RECEIVED' => 504,
			'FLIGHT_OPTION_RESPONDED' => 505,			
			'CONFIRM_RECEIVED' => 506			
	);
	$sString = strtolower($sText);
	$aWordsArray = explode(" ", $sString);	
	$iSourceKey = intval(array_search('from', $aWordsArray) ) + 1 ;
	$iDestinationKey = intval(array_search('to', $aWordsArray) ) + 1 ;	
	$sSource = array_search(strtoupper($aWordsArray[$iSourceKey]), $aLookUp);	
	$sDestination = array_search(strtoupper($aWordsArray[$iDestinationKey]), $aLookUp);
	$iDateKey = intval(array_search('on', $aWordsArray) ) + 1 ;	
	$sDate = date('d/m/Y', strtotime($aWordsArray[$iDateKey]." ".$aWordsArray[$iDateKey+1]) );
	$iTxnID = getTxnIDFromSessionID($obj_DB, $sSessionID);
	if($iTxnID <= 0 )
	{
		$iTxnID = addTransaction($obj_DB, $sSource, $sDestination, $sDate, $sDate, $sSessionID);
	}
	$iStateID = getCurrentStateFromTxnID($obj_DB, $iTxnID);
	$bReturnValue = false;
	
	switch($iStateID)
	{
		case 0 : //
			updateCurrentStateFromTxnID($obj_DB, $iTxnID, $sText, $aTXNStates['SEARCH_QUERY_RECEIVED']);
			$sMessageText = "Sure, How many passengers?";
			$bReturnValue = sendMessageFromSystem($obj_DB, $aGMConnInfo, $sChatName, $sMessageText, 2);
			updateCurrentStateFromTxnID($obj_DB, $iTxnID, $sMessageText, $aTXNStates['SEARCH_QUERY_RESPONDED']);
			break;
		case $aTXNStates['SEARCH_QUERY_RESPONDED'] : //
			updateCurrentStateFromTxnID($obj_DB, $iTxnID, $sText, $aTXNStates['PAX_DETAILS_RECEIVED']);
			updatePassengerCount($obj_DB, $iTxnID, intval($sText) );
			$sMessageText = getItineraryFromTxnID($obj_DB, $iTxnID);			
			$bReturnValue = sendFlightItinerary($obj_DB, $aGMConnInfo, $sMessageText, $sChatName, intval($sText));
			updateCurrentStateFromTxnID($obj_DB, $iTxnID, "Done", $aTXNStates['PAX_DETAILS_RESPONDED']);
			break;
		case $aTXNStates['PAX_DETAILS_RESPONDED'] : //
			$sCardMask = getMaskedCardNumber($obj_DB_MPOINT, $obj_ClientInfo->mobile, $obj_ClientInfo->email);
			updateCurrentStateFromTxnID($obj_DB, $iTxnID, $sText, $aTXNStates['FLIGHT_OPTION_RECEIVED']);			
			$sMessageText = "You will be charged 1200 on you stored card ".$sCardMask." ,Please reply with \"Confirm\" to continue booking.";
			$bReturnValue = sendMessageFromSystem($obj_DB, $aGMConnInfo, $sChatName, $sMessageText, 2);
			updateCurrentStateFromTxnID($obj_DB, $iTxnID, $sMessageText, $aTXNStates['FLIGHT_OPTION_RESPONDED']);
			break;
		case $aTXNStates['FLIGHT_OPTION_RESPONDED'] : //
			$sMessageText = "Thank You for confirming, your ticket would be sent to you shortly.";
			$bReturnValue = sendMessageFromSystem($obj_DB, $aGMConnInfo, $sChatName, $sMessageText, 2);
			updateCurrentStateFromTxnID($obj_DB, $iTxnID, $sText, $aTXNStates['CONFIRM_RECEIVED']);			
			break;
		case $aTXNStates['CONFIRM_RECEIVED'] :
		default:			
			break;
	}
}
function sendFlightItinerary($obj_DB, $aGMConnInfo, $sText, $sChatName, $iPaxCount = 1)
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
	$iDateKey = intval(array_search('on', $aWordsArray) ) + 1 ;
	$sDate = date('d/m/Y', strtotime($aWordsArray[$iDateKey]." ".$aWordsArray[$iDateKey+1]) );
	if($iSourceKey > 0 && $iDestinationKey > 0)
	{		
			$aReturnArray['JT'] = 0; //One Way Journey
			$aReturnArray['FR'] = array_search(strtoupper($aWordsArray[$iSourceKey]), $aLookUp);
			$aReturnArray['TO'] = array_search(strtoupper($aWordsArray[$iDestinationKey]), $aLookUp);
			$aReturnArray['DD'] = $sDate;
			$aReturnArray['AD'] = $sDate;
			$aReturnArray['A'] = $iPaxCount;
			$aReturnArray['C'] = 0;
			$aReturnArray['I'] = 0;
			$aReturnArray['CL'] = "Economy Class";
			$aReturnArray['TR'] = "02:00";
			$aReturnArray['ST'] = (string) (10+$i).":00";
			$aReturnArray['ET'] = (string) (12+$i).":00";		
			$aReturnArray['PR'] = (1200*intval($iPaxCount));
			$aReturnArray['FN'] = (string) "SGA-".(120 * ($i + 1) );
			$aReturnArray['SN'] = 0;			
				
	}
	$sMessageText = "There you go : Option 1";
	$bReturnValue = sendMessageFromSystem($obj_DB, $aGMConnInfo, $sChatName, $sMessageText, 3, $aReturnArray);

    $sMessageText = "Option 2";

	$aReturnArray['ST'] = (string) (16+$i).":00";
    $aReturnArray['ET'] = (string) (18+$i).":00";
         
    $bReturnValue = sendMessageFromSystem($obj_DB, $aGMConnInfo, $sChatName, $sMessageText, 3, $aReturnArray);
 
	
	return $bReturnValue;
}

function sendSalutationMessage($obj_DB, $aGMConnInfo, $sText, $sChatName, $sSessionID)
{	
	$bReturnValue = false;
	$sMessageText = "Hi,  Al Salam Alaikum";
	$bReturnValue = sendMessageFromSystem($obj_DB, $aGMConnInfo, $sChatName, $sMessageText);	
	deletePrevBookingRequest($obj_DB, $sSessionID);
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