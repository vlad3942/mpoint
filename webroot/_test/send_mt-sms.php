<?php
/**
 * This file serves to illustrate how an MT-SMS message can be sent to a mobile device through GoMobile.
 * GoMobile's Client API uses the underlying HTTP Client API for handling all the HTTP Communication to
 * the GoMobile server.
 * The HTTP Client API in return uses the Template class for parsing its header template and perform
 * replacement of the {TEXT_TAGS} found.
 *
 * Please refer to GoMobile Overview.pdf for details about the values the following variables can take:
 * 	- $iType
 * 	- $iCountry
 * 	- $iOperator
 * 	- $sChannel
 * 	- $sKeyword
 * 	- $iPrice
 * 	- $sRecipient
 * 	- $sBody
 * Any status codes returned by GoMobile if the message sending fails can also be found in this document.
 *
 * Please refer to GoMobile Client API for PHP for details on how to use the GoMobile client classes:
 * 	- GoMobileMessage
 * 	- GoMobileConnInfo
 * 	- GoMobileClient
 *
 * @author Jonatan Evald Buus
 * @package GoMobile
 * @subpackage Examples
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com Cellpoint Mobile
 *
 */

/**
 * Define path to the directory which holds the configuration file
 *
 */
define("sCONF_PATH", "conf");
define("sGOMOBILE_API_PATH", "conf/lib/gomobile");
/* ========== Define System path Start ========== */
// HTTP Request
if(isset($_SERVER['DOCUMENT_ROOT']) === true && empty($_SERVER['DOCUMENT_ROOT']) === false)
{
	$_SERVER['DOCUMENT_ROOT'] = str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT']);
	// Define system path constant
	define("sSYSTEM_PATH", substr($_SERVER['DOCUMENT_ROOT'], 0, strrpos($_SERVER['DOCUMENT_ROOT'], "/") ) );
}
// Command line
else
{
	$aTemp = explode("/", str_replace("\\", "/", __FILE__) );
	$sPath = "";
	for($i=0; $i<count($aTemp)-3; $i++)
	{
		$sPath .= $aTemp[$i] ."/";
	}
	// Define system path constant
	define("sSYSTEM_PATH", substr($sPath, 0, strlen($sPath)-1) );
}
/* ========== Define System path End ========== */

// Define path to the General API classes
define("sAPI_CLASS_PATH", substr(sSYSTEM_PATH, 0, strrpos(sSYSTEM_PATH, "/") ) ."/../php5api/classes/");

// Require API for parsing HTTP Header Template with text tags: {TEXT_TAG}
require_once(sAPI_CLASS_PATH ."/template.php");
// Require API for handling the connection to a remote webserver using HTTP
require_once(sAPI_CLASS_PATH ."/http_client.php");
// Require the PHP API for handling the connection to GoMobile
require_once(sGOMOBILE_API_PATH ."/gomobile.php");
// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."/simpledom.php");

// Require global configuration file
require_once(sCONF_PATH ."/gomobile.php");

$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

$xml = '';

$iChannel = (integer) $obj_DOM->{'Pay-by-link'}->{'CommunicationChannel'};

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

if ( ($obj_DOM instanceof SimpleDOMElement) === true && count($obj_DOM->{'Pay-by-link'}) > 0 )
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
				/* ========== Create MT-SMS Start ========== */
				$iType = 2;					
				$iCountry = (integer) $obj_DOM->{'Pay-by-link'}->{'Country'};
				$iOperator = (integer) $obj_DOM->{'Pay-by-link'}->{'OperatorId'};;			
				$sChannel = 123;			
				$sKeyword = "CPM";
				$iPrice = 0;				
				$sRecipient = (string) $obj_DOM->{'Pay-by-link'}->Mobile;	
				
				//Prepare query string for the URL.	
				$sQueryString = "FL=". (string) $obj_DOM->{'Pay-by-link'}->{'FlightNumber'};
				$sQueryString .= "&OR=". (string) $obj_DOM->{'Pay-by-link'}->{'OrderNumber'};
				$sQueryString .= "&BG=". (string) $obj_DOM->{'Pay-by-link'}->{'Baggage'};
				$sQueryString .= "&AM=". (string) $obj_DOM->{'Pay-by-link'}->{'Amount'};
				
				$sBody = "Hello, To make payment for your excess baggage please click on the secure link below payByLink://?".$sQueryString;

				// Instantiate Message Object for holding the message data which will be sent to GoMobile
				$obj_MsgInfo = GoMobileMessage::produceMessage($iType, $iCountry, $iOperator, $sChannel, $sKeyword, $iPrice, $sRecipient, $sBody);
				$obj_MsgInfo->setDescription("Test MT-SMS 1");
				$obj_MsgInfo->setSender("CPM");
				/* ========== Create MT-SMS End ========== */
				break;
			
			case(11):
				/* ========== Create MT-SMS Start ========== */
				$iType = 11;					
				$sChannel = 123;			
				$sKeyword = "CPM";				
				$sPushId = '0957ce678dd8707e007e4966ad8ad01e7eeb654fcf67baaaf452f0024f68c260';				
				$sBody = "Make the payment for your excess baggage securely through the application now.";
				if (empty($sPushId) === false)
				{
					$b = array();					
					$b["aps"] = array("alert" => array("body" => utf8_encode($sBody) ),
									  "sound" => "default",
									  "action" => "notify");
					$b['FL'] = (string) $obj_DOM->{'Pay-by-link'}->{'FlightNumber'};
					$b['OR'] = (string) $obj_DOM->{'Pay-by-link'}->{'OrderNumber'};
					$b['BG'] = (string) $obj_DOM->{'Pay-by-link'}->{'Baggage'};
					$b['AM'] = (string) $obj_DOM->{'Pay-by-link'}->{'Amount'};

					$obj_MsgInfo = GoMobileMessage::produceMessage($iType, $sChannel, $sKeyword, $sPushId, json_encode($b) );					
				}
				break;
		}	

		/* ========== Send MT-MESSAGE Start ========== */
		$bSend = true;		// Continue to send messages
		$iAttempts = 0;		// Number of Attempts
		// Send messages
		while ($bSend === true && $iAttempts < 3)
		{
			$iAttempts++;
			try
			{
				// Send MT-MESSAGE to GoMobile
				if ($obj_GoMobile->send($obj_MsgInfo) == 200)
				{
					$xml = '<status code="'. $obj_MsgInfo->getReturnCodes() .'">Message successfully sent with ID: '. $obj_MsgInfo->getGoMobileID() .'</status>';			
				}
				// Error
				else
				{
					$xml = '<status code="'. $obj_MsgInfo->getReturnCodes() .'">Message sending failed</status>';			
				}
				$bSend = false;
			}
			// Communication error, retry message sending
			catch (HTTPException $e)
			{
				sleep(pow(5, $iAttempts) );
			}
		}
	}
}
// Error: Invalid XML Document
elseif ( ($obj_DOM instanceof SimpleDOMElement) === false)
{
	header("HTTP/1.1 415 Unsupported Media Type");

	$xml = '<status code="415">Invalid XML Document</status>';
}
// Error: Wrong operation
elseif (count($obj_DOM->{'Pay-by-link'}) == 0)
{
	header("HTTP/1.1 400 Bad Request");

	$xml = '';
	foreach ($obj_DOM->children() as $obj_Elem)
	{
		$xml .= '<status code="400">Wrong operation: '. $obj_Elem->getName() .'</status>';
	}
}

header("Content-Type: text/xml; charset=\"UTF-8\"");

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<root>';
echo preg_replace('~\s*(<([^>]*)>[^<]*</\2>|<[^>]*>)\s*~', '$1', $xml);
echo '</root>';
?>