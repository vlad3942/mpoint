<?php
/**
 * Created by IntelliJ IDEA.
 * User: Urmila Sridharan
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: server
 * Package:
 * File Name:get_stored_card.php
 */

// Require Global Include File
require_once("../../inc/include.php");

// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
// Require Business logic for the End-User Account Component
require_once(sCLASS_PATH ."/enduser_account.php");
// Require Data Class for Client Information
require_once(sCLASS_PATH . "/clientinfo.php");


// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );
/*
$_SERVER['PHP_AUTH_USER'] = "CPMDemo";
$_SERVER['PHP_AUTH_PW'] = "DEMOisNO_2";

$HTTP_RAW_POST_DATA = '<?xml version="1.0" encoding="UTF-8"?>';
$HTTP_RAW_POST_DATA .= '<root>';
$HTTP_RAW_POST_DATA .= '<get-stored-card client-id="10007" account="100007">';
$HTTP_RAW_POST_DATA .= '<cards>';
$HTTP_RAW_POST_DATA .= '<card>65332</card>';
$HTTP_RAW_POST_DATA .= '<card>54321</card>';
$HTTP_RAW_POST_DATA .= '</cards>';
$HTTP_RAW_POST_DATA .= '</get-stored-card>';
$HTTP_RAW_POST_DATA .= '</root>';
*/
$obj_DOM = simpledom_load_string($HTTP_RAW_POST_DATA);

if (array_key_exists("PHP_AUTH_USER", $_SERVER) === true && array_key_exists("PHP_AUTH_PW", $_SERVER) === true)
{
	if ( ($obj_DOM instanceof SimpleDOMElement) === true && $obj_DOM->validate(sPROTOCOL_XSD_PATH ."mpoint.xsd") === true && count($obj_DOM->{'get-stored-card'}) > 0)
	{
        $obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);
        for ($i=0; $i<count($obj_DOM->{'get-stored-card'}); $i++)
        {
            // Set Global Defaults
            if (empty($obj_DOM->{'get-stored-card'}[$i]["account"]) === true || intval($obj_DOM->{'get-stored-card'}[$i]["account"]) < 1)
            {
                $obj_DOM->{'get-stored-card'}[$i]["account"] = -1;
            }

            // Validate basic information
            $code = Validate::valBasic($_OBJ_DB, (integer)$obj_DOM->{'get-stored-card'}[$i]["client-id"], (integer)$obj_DOM->{'get-stored-card'}[$i]["account"]);
            if ($code == 100)
            {
                $obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, (integer)$obj_DOM->{'get-stored-card'}[$i]["client-id"], (integer)$obj_DOM->{'get-stored-card'}[$i]["account"]);

                // Client successfully authenticated
                if ($obj_ClientConfig->getUsername() == trim($_SERVER['PHP_AUTH_USER']) && $obj_ClientConfig->getPassword() == trim($_SERVER['PHP_AUTH_PW'])) {
                    $obj_mPoint = new EndUserAccount($_OBJ_DB, $_OBJ_TXT, $obj_ClientConfig);

                    $aCardIDs = $obj_DOM->{'get-stored-card'}->cards->{'card'};

                    for ($i = 0; $i < count($aCardIDs); $i++) {
                        $xml .= $obj_mPoint->getCardDetailsFromCardId($aCardIDs[$i]);
                    }
                } else {
                    header("HTTP/1.1 401 Unauthorized");

                    $xml = '<status code="401">Username / Password doesn\'t match</status>';
                }
            } else {
                header("HTTP/1.1 400 Bad Request");

                $xml = '<status code="' . $code . '">Client ID / Account doesn\'t match </status>';
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
	elseif (count($obj_DOM->{'get-stored-card'}) == 0)
	{
		header("HTTP/1.1 400 Bad Request");
	
		$xml = '';
		foreach ($obj_DOM->children() as $obj_Elem)
		{
			$xml .= '<status code="400">Wrong operation: '. $obj_Elem->getName() .'</status>'; 
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
			$xml .= '<status code="400">'. htmlspecialchars($aObj_Errs[$i]->message, ENT_NOQUOTES) .'</status>';
		}
	}
}
else
{
	header("HTTP/1.1 401 Unauthorized");
	
	$xml = '<status code="401">Authorization required</status>';
}
header("Content-Type: text/xml; charset=\"UTF-8\"");

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<root>';
echo $xml;
echo '</root>';
?>