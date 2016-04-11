<?php
require_once ("inc/include.php");

$bReturn = true;
 
$bSendMessage = false;

// Instantiate object for holding the necessary information for connecting to GoMobile
$obj_ConnInfo = GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO);
// Instantiate client object for communicating with GoMobile
$obj_GoMobile = new GoMobileClient($obj_ConnInfo);

$sChatName = (string) $_REQUEST['name'];

$iUserID = getIDFromChatName($_OBJ_DB_MBE, $sChatName);

$iPushIDForUser = getPushIDFromUserID($_OBJ_DB_MBE, $iUserID);

$iSystemUserID = getIDFromChatName($_OBJ_DB_MBE, 'System');

$sText = (string) $_REQUEST['message'];

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

echo "jsonpCallbackAddMessage(".json_encode(array('returnValue'=> (string)intval($bReturn))).")";