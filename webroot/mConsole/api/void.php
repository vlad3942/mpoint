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
$HTTP_RAW_POST_DATA .= '<void>';
$HTTP_RAW_POST_DATA .= '<transactions client-id="10007" account = "100006">';
$HTTP_RAW_POST_DATA .= '<transaction id="1798769" order-no="1412177706" order-ref="1412177706">';
$HTTP_RAW_POST_DATA .= '<amount country-id="100">20</amount>';
$HTTP_RAW_POST_DATA .= '</transaction>';
$HTTP_RAW_POST_DATA .= '</transactions>';
$HTTP_RAW_POST_DATA .= '</void>';
$HTTP_RAW_POST_DATA .= '</root>';
*/

$xml = '';

$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{	
	if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mconsole.xsd") === true && count($obj_DOM->{'void'}) > 0)
	{
		/* ========== SINGLE SIGN-ON START ========== */
		$obj_val = new Validate();	
		$obj_mPoint = new mConsole($_OBJ_DB, $_OBJ_TXT);
		$aClientIDs = array();
		
		for ($i=0; $i<count($obj_DOM->{'void'}->transactions); $i++)
		{
			$aClientIDs[] = (integer) $obj_DOM->{'void'}->transactions[$i]["client-id"];
		}
				
		$aHTTP_CONN_INFO["mesb"]["path"] = Constants::sMCONSOLE_SINGLE_SIGN_ON_PATH;
		$aHTTP_CONN_INFO["mesb"]["username"] = trim($_SERVER['PHP_AUTH_USER']);
		$aHTTP_CONN_INFO["mesb"]["password"] = trim($_SERVER['PHP_AUTH_PW']);
		
		$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["mesb"]);
				
		$code = $obj_mPoint->singleSignOn($obj_ConnInfo, $_SERVER['HTTP_X_AUTH_TOKEN'], mConsole::sPERMISSION_VOID_PAYMENTS, $aClientIDs, $_SERVER['HTTP_VERSION']);
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
		/* ========== SINGLE SIGN-ON END ========== */
		
		if ($code == mConsole::iAUTHORIZATION_SUCCESSFUL)
		{				
			/* ========== INPUT VALIDATION START ========== */
			$obj_Validate = new Validate();
			$aMsgCodes = array();		
			for ($i=0; $i<count($obj_DOM->{'void'}->transactions); $i++)
			{										
				$iClientID = intval($obj_DOM->{'void'}->transactions[$i]["client-id"]);		
				$iAccountID = (integer) $obj_DOM->{'void'}->transactions[$i]["account"];
				if ($iAccountID <= 0) { $iAccountID = -1; }		
				$code = $obj_Validate->valBasic($_OBJ_DB, $iClientID, $iAccountID);							
				if ($code < 10) { $aMsgCodes[$iClientID][] = new BasicConfig($code + 10, "Validation of Client : ". $iClientID ." failed"); }
				elseif ($code < 20) { $aMsgCodes[$iClientID][] = new BasicConfig($code + 10, "Validation of Account : ". $iAccountID ." failed"); }						
				
			}		
			/* ========== INPUT VALIDATION END ========== */
			
			if (count($aMsgCodes) == 0 )
			{		 
				for ($i=0; $i<count($obj_DOM->{'void'}->transactions); $i++)
				{
					$xml .= '<transactions client-id="'. intval($obj_DOM->{'void'}->transactions[$i]["client-id"]) .'">';
					$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, (integer) $obj_DOM->{'void'}->transactions[$i]["client-id"]);
					
					for ($j=0; $j<count($obj_DOM->{'void'}->transactions[$i]->transaction); $j++)
					{
						$xml .= '<transaction id="'. intval($obj_DOM->{'void'}->transactions[$i]->transaction[$j]["id"]) .'" order-no="'. htmlspecialchars($obj_DOM->{'void'}->transactions[$i]->transaction[$j]["order-no"], ENT_NOQUOTES) .'">';
						
						$aMsgCodes = $obj_mPoint->void(HTTPConnInfo::produceConnInfo("http://". $_SERVER["HTTP_HOST"] ."/buy/refund.php"),
													  (integer) $obj_DOM->{'void'}->transactions[$i]["client-id"],
													  $obj_ClientConfig->getUsername(),
													  $obj_ClientConfig->getPassword(),
													  (integer) $obj_DOM->{'void'}->transactions[$i]->transaction[$j]["id"],
													  trim($obj_DOM->{'void'}->transactions[$i]->transaction[$j]["order-no"]),
													  (integer) $obj_DOM->{'void'}->transactions[$i]->transaction[$j]->amount,
                                                        $iAccountID,
                                                        urlencode($obj_DOM->void->transactions[$i]->transaction[$j]["order-ref"])
													  );
						foreach ($aMsgCodes as $code)
						{
							switch ($code)
							{
							case (mConsole::iSERVICE_INTERNAL_ERROR):
							case (500):
								header("HTTP/1.0 500 Internal Server Error");
								break;
							case (403):
								header("HTTP/1.0 403 Forbidden");
								break;
							case (mConsole::iSERVICE_CONNECTION_TIMEOUT_ERROR):
								header("HTTP/1.1 504 Gateway Timeout");
								break;
							case (mConsole::iSERVICE_READ_TIMEOUT_ERROR):
							case (998):
							case (999):
								header("HTTP/1.1 502 Bad Gateway");
								break;
							case (997):
								header("HTTP/1.0 405 Method Not Allowed");
								break;
							case (1000):
							case (1100):
							case (1001):
								header("HTTP/1.1 200 OK");
								break;
							default:
								header("HTTP/1.0 400 Bad Request");
								break;
							}
							if ($code < 10) { $code += 980; }	// Ensure codes for Service Errors are unique for the API
							$xml .= '<status code="'. $code .'" />';
						}
					}
					$xml .= '</transaction>';					
				}
				$xml .= '</transactions>';
			}
			else 
			{
				header("HTTP/1.1 400 Bad Request");
		
				foreach ($aMsgCodes as $iClientID => $aObj_Messages)
				{					
					foreach($aObj_Messages as $obj_Message)
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
	elseif (count($obj_DOM->{'void'} ) == 0)
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