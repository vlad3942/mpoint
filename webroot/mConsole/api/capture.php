<?php
/** 
 *
 * @author Rohit Malhotra
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package mConsole
 * @version 1.10
 */

// Require Global Include File
require_once("../../inc/include.php");

// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");

// Require Business logic for General Administration of mPoint
require_once(sCLASS_PATH ."admin.php");

// Require Business logic for the mConsole Module
require_once(sCLASS_PATH ."/mConsole.php");

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );
 

/*
$_SERVER['PHP_AUTH_USER'] = "CPMDemo";
$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<capture>';
$HTTP_RAW_POST_DATA .= '<transactions client-id="10007" account = "100006">';
$HTTP_RAW_POST_DATA .= '<transaction id="1798769" order-no="1412177706">';
$HTTP_RAW_POST_DATA .= '<amount country-id="100">20</amount>';
$HTTP_RAW_POST_DATA .= '</transaction>';
$HTTP_RAW_POST_DATA .= '</transactions>';
$HTTP_RAW_POST_DATA .= '</capture>';
$HTTP_RAW_POST_DATA .= '</root>';
*/

$xml = '';

$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{	
	if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mconsole.xsd") === true && count($obj_DOM->{'capture'}) > 0)
	{
		/*==================================Start Single Sign-On=================================================================*/
		
		$obj_val = new Validate();	
		$obj_mPoint = new mConsole($_OBJ_DB, $_OBJ_TXT);
		$aClientIDs = array();
		
		for ($i=0; $i<count($obj_DOM->{'capture'}->transactions); $i++)
		{
			$aClientIDs[] = (integer) $obj_DOM->{'capture'}->transactions[$i]["client-id"];
		}
				
		$aHTTP_CONN_INFO["mesb"]["path"] = Constants::sMCONSOLE_SINGLE_SIGN_ON_PATH;
		$aHTTP_CONN_INFO["mesb"]["username"] = trim($_SERVER['PHP_AUTH_USER']);
		$aHTTP_CONN_INFO["mesb"]["password"] = trim($_SERVER['PHP_AUTH_PW']);
		
		$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["mesb"]);
				
		$code = $obj_mPoint->singleSignOn($obj_ConnInfo, $_SERVER['HTTP_X_AUTH_TOKEN'], mConsole::sPERMISSION_CAPTURE_PAYMENTS, $aClientIDs);
		
		switch ($code)
		{
		case (mConsole::iSERVICE_CONNECTION_TIMEOUT_ERROR):
			header("HTTP/1.1 504 Gateway Timeout");
		
			$xml = '<status code="'. $code .'">Single Sign-On Service is unreachable</status>';
			break;
		case (mConsole::iSERVICE_READ_TIMEOUT_ERROR):
			header("HTTP/1.1 502 Bad Gateway");
			
			$xml = '<status code="'. $code .'">Single Sign-On Service is unavailable</status>';
			break;
		case (mConsole::iUNAUTHORIZED_USER_ACCESS_ERROR):
			header("HTTP/1.1 401 Unauthorized");
		
			$xml = '<status code="'. $code .'">Unauthorized User Access</status>';
			break;
		case (mConsole::iINSUFFICIENT_USER_PERMISSIONS_ERROR):
			header("HTTP/1.1 403 Forbidden");
			
			$xml = '<status code="'. $code .'">Insufficient User Permissions</status>';
			break;
		case (mConsole::iINSUFFICIENT_CLIENT_LICENSE_ERROR):
			header("HTTP/1.1 402 Payment Required");
		
			$xml = '<status code="'. $code .'">Insufficient Client License</status>';
			break;
		case (mConsole::iAUTHORIZATION_SUCCESSFUL):
			header("HTTP/1.1 200 OK"); 			
			break;
		default:
			header("HTTP/1.1 500 Internal Server Error");
	
			$xml = '<status code="'. $code .'">Internal Error</status>';
			break;
		}
		
		/*====================================End Single Sign-On=================================================================*/
		
		if ($code == mConsole::iAUTHORIZATION_SUCCESSFUL)
		{				
			/* ========== INPUT VALIDATION START ========== */
			$obj_Validate = new Validate();
			$aMsgCodes = array();		
			for ($i=0; $i<count($obj_DOM->{'capture'}->transactions); $i++)
			{										
				$iClientID = intval($obj_DOM->{'capture'}->transactions[$i]["client-id"]);		
				$iAccountID = (intval($obj_DOM->{'capture'}->transactions[$i]["account"]) == 0 ) ? -1 : intval($obj_DOM->{'capture'}->transactions[$i]["account"]);	
				$code = $obj_Validate->valBasic($_OBJ_DB, $iClientID, $iAccountID);				
				if ($iAccountID < 0 && in_array($code, array(14, 100) ) === false) 
				{ 
					$aMsgCodes[$iClientID][] = new BasicConfig($code + 10, "Validation of Client : ". $iClientID ." failed"); 
				}
				elseif ($iAccountID > 0 && $code < 100 )
				{
					$aMsgCodes[$iClientID][] = new BasicConfig($code + 20, "Validation of Account : ". $iAccountID ." failed"); 	
				}						
				
			}		
			/* ========== INPUT VALIDATION END ========== */
			
			if(count($aMsgCodes) == 0 )
			{		 
				for ($i=0; $i<count($obj_DOM->{'capture'}->transactions); $i++)
				{
					$xml .= '<transactions client-id = "'. intval($obj_DOM->{'capture'}->transactions[$i]["client-id"] ) .'" >';
					
					for ($j=0; $j<count($obj_DOM->{'capture'}->transactions[$i]->transaction); $j++)
					{
						$xml .= '<transaction 
									id = "'. intval($obj_DOM->{'capture'}->transactions[$i]->transaction[$j]["id"] ) .'"
									order-no = "'. urlencode($obj_DOM->{'capture'}->transactions[$i]->transaction[$j]["order-no"] ) .'" >';
						
						$obj_Client = new HTTPClient(new Template, HTTPConnInfo::produceConnInfo("http://". $_SERVER["HTTP_HOST"] ."/buy/capture.php") );
						
						$obj_Client->connect();
						
						$h = $obj_mPoint->constHTTPHeaders();
						
						$b = "clientid=". intval($obj_DOM->{'capture'}->transactions[$i]["client-id"] ) .							
							"&mpointid=". intval($obj_DOM->{'capture'}->transactions[$i]->transaction[$j]["id"] ) .
							"&orderid=". urlencode($obj_DOM->{'capture'}->transactions[$i]->transaction[$j]["order-no"] ) .
							"&amount=". intval($obj_DOM->{'capture'}->transactions[$i]->transaction[$j]->amount) ;						
						
						$code = $obj_Client->send($h, $b);
							
						$aResponse = array();
						$aMessages = array();
						if(is_int(strpos($obj_Client->getReplyBody(), "&") ) === true && (substr_count($obj_Client->getReplyBody(), "msg=") > 1 ))
						{
							$aMessages = explode("&", $obj_Client->getReplyBody() );
							
							if(is_array($aMessages) === true && count($aMessages) > 0 )
							{
								foreach ($aMessages as $sMsg)
								{
									$parts = explode("=", $sMsg );
									if(trim($parts[0]) == 'msg')
									{
										$aResponse[] = $parts[1];
									}
								}
							}
						}
						else if(is_int(strpos($obj_Client->getReplyBody(), "=") ) === true && (substr_count($obj_Client->getReplyBody(), "msg=") == 1 ))						
						{
							$aResponse = explode("=", $obj_Client->getReplyBody() );
						}
									
						if(is_array($aResponse) === true && count($aResponse) > 0 )
						{
							switch ($code)
							{
								case 200 : 
									header("HTTP/1.1 200 OK");
									
									$xml .= '<status code="'. $aResponse[1] .'">Capture Successful</status>';
									break;
								case 502 : 
									header("HTTP/1.1 502 Bad Gateway");								
									
									if( is_int($aResponse[1]) && $aResponse[1] == 999 )
										$xml .= '<status code="'. $aResponse[1] .'">Capture Declined by PSP</status>';
									else if( is_int($aResponse[1]) && $aResponse[1] == 998 )
										$xml .= '<status code="'. $aResponse[1] .'">Error while communicating with PSP</status>';
									else
										$xml .= '<status code="502">Unknown Error</status>';
									break;
								case 405 : 
									header("HTTP/1.0 405 Method Not Allowed");								
									
									$xml .= '<status code="'. $aResponse[1] .'">Capture not supported by PSP</status>';
									break;		
								case 400 : 									
									header("HTTP/1.0 400 Bad Request");
									foreach ($aResponse as $sResponseCode)
									{										
										switch ($sResponseCode)
										{
											case 171:
												$xml .= '<status code="'. $sResponseCode .'">Undefined mPoint Transaction ID</status>';
												break;
											case 172:
												$xml .= '<status code="'. $sResponseCode .'">Invalid mPoint Transaction ID</status>';
												break;
											case 173:
												$xml .= '<status code="'. $sResponseCode .'">Transaction Not Found</status>';
												break;
											case 174:
												$xml .= '<status code="'. $sResponseCode .'">Transaction Disabled</status>';
												break;
											case 175:
												$xml .= '<status code="'. $sResponseCode .'">Payment Rejected for Transaction</status>';
												break;
											case 176:
												$xml .= '<status code="'. $sResponseCode .'">Payment already Captured for Transaction</status>';
												break;
											case 177:
												$xml .= '<status code="'. $sResponseCode .'">Payment already Refunded for Transaction</status>';
												break;
											case 181:
												$xml .= '<status code="'. $sResponseCode .'">Undefined Order ID</status>';
												break;											
											case 183:
												$xml .= '<status code="'. $sResponseCode .'">Order ID doesn\'t match Transaction</status>';
												break;											
										}
									}
									break;
								default:
									header("HTTP/1.0 500 Internal Error");								
									
									$xml .= '<status code="500">Internal Server Error</status>';
									break;		
							}
						}
						else 
						{
							header("HTTP/1.0 500 Internal Error");						
									
							$xml .= '<status code="500">Internal Server Error</status>';
						}
						
						$obj_Client->disconnect();
					}
					$xml .= '</transaction>';					
				}
				$xml .= '</transactions>';
			}
			else 
			{
				header("HTTP/1.1 400 Bad Request");
		
				foreach ($aMsgCodes as $iClientID => $obj_MessageContainer)
				{					
					foreach($obj_MessageContainer as $obj_Message)
					{
						$xml .= '<status code="'. $obj_Message->getID() .'">'. $obj_Message->getName() .' for Client '. $iClientID .'</status>' ;
					}				
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
	elseif (count($obj_DOM->{'capture'} ) == 0)
	{
		header("HTTP/1.1 400 Bad Request");
	
		$xml = '';
		foreach ($obj_DOM->children() as $obj_Elem)
		{
			$xml = '<status code="400">Wrong operation: '. $obj_Elem->getName() .'</status>'; 
		}
	}
	// Error: Invalid Input
	else
	{
		header("HTTP/1.1 400 Bad Request");
		$aObj_Errs = libxml_get_errors();
		
		$xml = '';
		for ($i=0; $i<count($aObj_Errs); $i++)
		{
			$xml = '<status code="400">'. htmlspecialchars($aObj_Errs[$i]->message, ENT_NOQUOTES) .'</status>';
		}
	}
}
else
{
	header("HTTP/1.1 401 Unauthorized");
	
	$xml = '<status code="401">Authorization required</status>';
}
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<root>';
echo $xml;
echo '</root>';
?>