<?php
require_once ("inc/include.php");

//Define Constants for message types

$sTypes = array(
		'PAY_BY_LINK' => 1,
		'CHECKIN_BY_LINK' => 2,
		'MO_MESSAGE' => 3,
		'MT_MESSAGE' => 4
);

$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

$xml = '';

$iChannel = (integer) $obj_DOM->notify->{'CommunicationChannel'};

$sPushId = (string) $obj_DOM->notify->{'PushId'};

$iType = (integer) $obj_DOM->notify->type;


$bSendMessage = false;

if($iChannel > 0)
{
	switch($iChannel)
	{
		case 1: //Send only Push Notification.
			$aMsgTypes = array(11);
			break;
		case 2: //Send only MT-SMS.
			$aMsgTypes = array(2);
			break;
		case 3: //Send both Push Notification and MT SMS
			$aMsgTypes = array(2,11);
			break;
	}
}

if ( ($obj_DOM instanceof SimpleDOMElement) === true && count($obj_DOM->notify->{'Pay-by-link'}) > 0 && $iType == $sTypes['PAY_BY_LINK'])
{	
	// Instantiate object for holding the necessary information for connecting to GoMobile
	$obj_ConnInfo = GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO);
	// Instantiate client object for communicating with GoMobile
	$obj_GoMobile = new GoMobileClient($obj_ConnInfo);	

	foreach ($aMsgTypes as $iMsgType)
	{
		switch($iMsgType)
		{
			case(2):
				// Tell the client that it is OK to close the connection by now, callback is accepted, further processing will happen in the background
				header("HTTP/1.1 202 Accepted");
				header("Content-Length: 0");
				header("Connection: close");
				ignore_user_abort(true);
				flush();
				/* ========== Create MT-SMS Start ========== */
				$iType = 2;					
				$iCountry = (integer) $obj_DOM->{'client-info'}->mobile['country-id'];
				$iOperator = (integer) $obj_DOM->{'client-info'}->mobile['operator-id'];			
				$sChannel = 123;			
				$sKeyword = "CPM";
				$iPrice = 0;				
				$sRecipient = (string) $obj_DOM->{'client-info'}->mobile;	
				$bSendMessage = true;
				
				//Prepare query string for the URL.	
				$sQueryString = "FL=". (string) $obj_DOM->notify->{'Pay-by-link'}->{'FlightNumber'};
				$sQueryString .= "&OR=". (string) $obj_DOM->notify->{'Pay-by-link'}->{'OrderNumber'};
				$sQueryString .= "&BG=". (string) $obj_DOM->notify->{'Pay-by-link'}->{'Baggage'};
				$sQueryString .= "&AM=". (string) $obj_DOM->notify->{'Pay-by-link'}->{'Amount'};
				$sQueryString = base64_encode($sQueryString);
				
				$sBody = "Hello, To make payment for your excess baggage please click on the secure link below payByLink://?".$sQueryString;

				// Instantiate Message Object for holding the message data which will be sent to GoMobile
				$obj_MsgInfo = GoMobileMessage::produceMessage($iType, $iCountry, $iOperator, $sChannel, $sKeyword, $iPrice, $sRecipient, $sBody);
				$obj_MsgInfo->setDescription("Test MT-SMS 1");
				$obj_MsgInfo->setSender("CPM");
				/* ========== Create MT-SMS End ========== */
				break;
			
			case(11):
				/* ========== Create MT-PUSH NOTIFICATION Start ========== */				
				if(empty($sPushId) === false)
				{					
					$iType = 11;					
					$sChannel = 123;			
					$sKeyword = "CPM";						
					//print_r();die('here');
					$sBody = "Make the payment for your excess baggage securely through the application now.";
					if (empty($sPushId) === false)
					{
						$b = array();					
						$b["aps"] = array("alert" => array("body" => utf8_encode($sBody) ),
										  "sound" => "default",
										  "action" => "notify");
						$b['FL'] = (string) $obj_DOM->notify->{'Pay-by-link'}->{'FlightNumber'};
						$b['OR'] = (string) $obj_DOM->notify->{'Pay-by-link'}->{'OrderNumber'};
						$b['BG'] = (string) $obj_DOM->notify->{'Pay-by-link'}->{'Baggage'};
						$b['AM'] = (string) $obj_DOM->notify->{'Pay-by-link'}->{'Amount'};
						$b['CHAT'] = 0;						

						$obj_MsgInfo = GoMobileMessage::produceMessage($iType, $sChannel, $sKeyword, $sPushId, json_encode($b) );					
					}
					$bSendMessage = true;
				}
				else
				{
						$bSendMessage = false;
						$xml .= '<status code="11">Push ID not found in the given request.</status>';			
				}
				break;
		}	

		if($bSendMessage === true)
		{	
			// Tell the client that it is OK to close the connection by now, callback is accepted, further processing will happen in the background
			header("HTTP/1.1 202 Accepted");
			header("Content-Length: 0");
			header("Connection: close");
			ignore_user_abort(true);
			flush();
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
						$xml .= '<status code="'. $obj_MsgInfo->getReturnCodes() .'">Message successfully sent with ID: '. $obj_MsgInfo->getGoMobileID() .'</status>';			
					}
					// Error
					else
					{
						$xml .= '<status code="'. $obj_MsgInfo->getReturnCodes() .'">Message sending failed</status>';			
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
		else
		{
			header("HTTP/1.1 400 Bad Request");
			header("Content-Type: text/xml; charset=\"UTF-8\"");
			echo '<?xml version="1.0" encoding="UTF-8"?>';
			echo '<root>';
			echo preg_replace('~\s*(<([^>]*)>[^<]*</\2>|<[^>]*>)\s*~', '$1', $xml);
			echo '</root>';
		}
	}
}
else if ( ($obj_DOM instanceof SimpleDOMElement) === true && count($obj_DOM->notify->{'CheckIn-by-link'}) > 0 && $iType == $sTypes['CHECKIN_BY_LINK'])
{	
	// Instantiate object for holding the necessary information for connecting to GoMobile
	$obj_ConnInfo = GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO);
	// Instantiate client object for communicating with GoMobile
	$obj_GoMobile = new GoMobileClient($obj_ConnInfo);
	$sRecipientName = (string) $obj_DOM->notify->{'CheckIn-by-link'}->UserName;

	foreach ($aMsgTypes as $iMsgType)
	{
		switch($iMsgType)
		{
			case(2):
				
				// Tell the client that it is OK to close the connection by now, callback is accepted, further processing will happen in the background
				header("HTTP/1.1 202 Accepted");
				header("Content-Length: 0");
				header("Connection: close");
				ignore_user_abort(true);
				flush();
				
				/* ========== Create MT-SMS Start ========== */
				$iType = 2;
				$iCountry = (integer) $obj_DOM->{'client-info'}->mobile['country-id'];
				$iOperator = (integer) $obj_DOM->{'client-info'}->mobile['operator-id'];
				$sChannel = 123;
				$sKeyword = "CPM";
				$iPrice = 0;
				$sRecipient = (string) $obj_DOM->{'client-info'}->mobile;
				$bSendMessage = true;

				//Prepare query string for the URL.	
				$sQueryString = "FL=". (string) $obj_DOM->notify->{'CheckIn-by-link'}->{'FlightNumber'};
				$sQueryString .= "&TN=". (string) $obj_DOM->notify->{'CheckIn-by-link'}->{'TicketNumber'};				
				$sQueryString = base64_encode($sQueryString);
				
				$sBody = "Hello ".ucfirst(strtolower($sRecipientName)).", For online checkin click on the secure link below checkInByLink://?".$sQueryString;
				
				// Instantiate Message Object for holding the message data which will be sent to GoMobile
				$obj_MsgInfo = GoMobileMessage::produceMessage($iType, $iCountry, $iOperator, $sChannel, $sKeyword, $iPrice, $sRecipient, $sBody);
				$obj_MsgInfo->setDescription("Test MT-SMS 1");
				$obj_MsgInfo->setSender("CPM");
				/* ========== Create MT-SMS End ========== */
				break;
					
			case(11):
				/* ========== Create MT-PUSH NOTIFICATION Start ========== */
				if(empty($sPushId) === false)
				{
					$iType = 11;					
					$sChannel = 123;			
					$sKeyword = "CPM";						
					//print_r();die('here');
					$sBody = "Hello ".ucfirst(strtolower($sRecipientName)).", you can do your online checkin now.";
					if (empty($sPushId) === false)
					{
						$b = array();					
						$b["aps"] = array("alert" => array("body" => utf8_encode($sBody) ),
										  "sound" => "default",
										  "action" => "notify");
						$b['FL'] = (string) $obj_DOM->notify->{'CheckIn-by-link'}->{'FlightNumber'};
						$b['TN'] = (string) $obj_DOM->notify->{'CheckIn-by-link'}->{'TicketNumber'};
						$b['CHAT'] = 0;												

						$obj_MsgInfo = GoMobileMessage::produceMessage($iType, $sChannel, $sKeyword, $sPushId, json_encode($b) );					
					}
					$bSendMessage = true;
				}
				else
				{
					$bSendMessage = false;
					$xml .= '<status code="11">Push ID not found in the given request.</status>';
				}
				break;
		}

		if($bSendMessage === true)
		{
			// Tell the client that it is OK to close the connection by now, callback is accepted, further processing will happen in the background
			header("HTTP/1.1 202 Accepted");
			header("Content-Length: 0");
			header("Connection: close");
			ignore_user_abort(true);
			flush();
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
						$xml .= '<status code="'. $obj_MsgInfo->getReturnCodes() .'">Message successfully sent with ID: '. $obj_MsgInfo->getGoMobileID() .'</status>';
					}
					// Error
					else
					{
						$xml .= '<status code="'. $obj_MsgInfo->getReturnCodes() .'">Message sending failed</status>';
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
		else
		{
			header("HTTP/1.1 400 Bad Request");
			header("Content-Type: text/xml; charset=\"UTF-8\"");
			echo '<?xml version="1.0" encoding="UTF-8"?>';
			echo '<root>';
			echo preg_replace('~\s*(<([^>]*)>[^<]*</\2>|<[^>]*>)\s*~', '$1', $xml);
			echo '</root>';
		}
	}
}
else if ( ($obj_DOM instanceof SimpleDOMElement) === true && count($obj_DOM->notify->Messenger) > 0 && $iType == $sTypes['MO_MESSAGE'])
{
	// Tell the client that it is OK to close the connection by now, callback is accepted, further processing will happen in the background
	header("HTTP/1.1 202 Accepted");
	header("Content-Length: 0");
	header("Connection: close");
	ignore_user_abort(true);
	flush();
	
	$sChatName = (string) $obj_DOM->notify->ChatName;
	
	$iUserID = getIDFromChatName($_OBJ_DB_MBE, $sChatName);
	
	$iSystemUserID = getIDFromChatName($_OBJ_DB_MBE, 'System');
	
	if($iUserID == 0 )
	{
		$iUserID = register($_OBJ_DB_MBE, $sChatName, $sPushId);
	}
	
	$sText = (string) $obj_DOM->notify->Messenger->message;
	
	$code = saveMessage($_OBJ_DB_MBE, $iUserID, $iSystemUserID, $sText);
	
	if(checkIfFlightQuery($sText) === true)
	{
		sendFlightItinerary($_OBJ_DB_MBE, $aGM_CONN_INFO, $sText, $sChatName);
	}
	
	if ($code > 0)
	{
		$xml .= '<status code="20">Message successfully sent with ID: '. $code .'</status>';
	}
	// Error
	else
	{
		$xml .= '<status code="14">Message sending failed</status>';
	}	
	
}
else if ( ($obj_DOM instanceof SimpleDOMElement) === true && count($obj_DOM->notify->Messenger) > 0 && $iType == $sTypes['MT_MESSAGE'])
{
	$bSendMessage = false;
	
	// Instantiate object for holding the necessary information for connecting to GoMobile
	$obj_ConnInfo = GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO);
	// Instantiate client object for communicating with GoMobile
	$obj_GoMobile = new GoMobileClient($obj_ConnInfo);
	
	$sChatName = (string) $obj_DOM->notify->ChatName;
	
	$iUserID = getIDFromChatName($_OBJ_DB_MBE, $sChatName);
	
	$iPushIDForUser = getPushIDFromUserID($_OBJ_DB_MBE, $iUserID);
	
	$iSystemUserID = getIDFromChatName($_OBJ_DB_MBE, 'System');
	
	$sText = (string) $obj_DOM->notify->Messenger->message;
	
	$code = saveMessage($_OBJ_DB_MBE, $iSystemUserID, $iUserID, $sText);
	
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
			$obj_MsgInfo = GoMobileMessage::produceMessage($iType, $sChannel, $sKeyword, $iPushIDForUser, json_encode($b) );					
		}
		$bSendMessage = true;
	}
	else
	{
		$bSendMessage = false;
		$xml .= '<status code="11">Push ID not found in the given request.</status>';
	}
	
	if($bSendMessage === true)
	{
		// Tell the client that it is OK to close the connection by now, callback is accepted, further processing will happen in the background
		header("HTTP/1.1 202 Accepted");
		header("Content-Length: 0");
		header("Connection: close");
		ignore_user_abort(true);
		flush();
				
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
					$xml .= '<status code="'. $obj_MsgInfo->getReturnCodes() .'">Message successfully sent with ID: '. $obj_MsgInfo->getGoMobileID() .'</status>';
				}
				// Error
				else
				{
					$xml .= '<status code="'. $obj_MsgInfo->getReturnCodes() .'">Message sending failed</status>';
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
	else 
	{
		header("HTTP/1.1 400 Bad Request");
		header("Content-Type: text/xml; charset=\"UTF-8\"");
		echo '<?xml version="1.0" encoding="UTF-8"?>';
		echo '<root>';
		echo preg_replace('~\s*(<([^>]*)>[^<]*</\2>|<[^>]*>)\s*~', '$1', $xml);
		echo '</root>';
	}
	
}
// Error: Invalid XML Document
elseif ( ($obj_DOM instanceof SimpleDOMElement) === false)
{
	header("HTTP/1.1 415 Unsupported Media Type");

	$xml = '<status code="415">Invalid XML Document</status>';
	
	header("Content-Type: text/xml; charset=\"UTF-8\"");
	
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo '<root>';
	echo preg_replace('~\s*(<([^>]*)>[^<]*</\2>|<[^>]*>)\s*~', '$1', $xml);
	echo '</root>';
}
// Error: Wrong operation
elseif (count($obj_DOM->notify) == 0)
{
	header("HTTP/1.1 400 Bad Request");

	$xml = '';
	foreach ($obj_DOM->children() as $obj_Elem)
	{
		$xml .= '<status code="400">Wrong operation: '. $obj_Elem->getName() .'</status>';
	}
	header("Content-Type: text/xml; charset=\"UTF-8\"");
	
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo '<root>';
	echo preg_replace('~\s*(<([^>]*)>[^<]*</\2>|<[^>]*>)\s*~', '$1', $xml);
	echo '</root>';
}
elseif (count($obj_DOM->notify->type) == 0)
{
	header("HTTP/1.1 400 Bad Request");	
	
	$xml = '';
	
	$xml .= '<status code="400">Wrong operation: type missing in the given request</status>';
	header("Content-Type: text/xml; charset=\"UTF-8\"");
	
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo '<root>';
	echo preg_replace('~\s*(<([^>]*)>[^<]*</\2>|<[^>]*>)\s*~', '$1', $xml);
	echo '</root>';
	
}
/*
header("Content-Type: text/xml; charset=\"UTF-8\"");

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<root>';
echo preg_replace('~\s*(<([^>]*)>[^<]*</\2>|<[^>]*>)\s*~', '$1', $xml);
echo '</root>';
*/
?>
