<?php
/**
 *
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Admin
 * @subpackage MyAccount
 */

// Require include file for including all Shared and General APIs
require_once("../../inc/include.php");

// Require Business logic for the My Account component
require_once(sCLASS_PATH ."/my_account.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );

// Initialize Standard content Object
$obj_mPoint = new MyAccount($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_CountryConfig']);
$obj_Validator = new Validate($_SESSION['obj_CountryConfig']);

$obj_XML = simplexml_load_string(trim($HTTP_RAW_POST_DATA) );

$xml = '';
$sType = "status";
// List of Error Codes
$aErrCd = array();

switch ($obj_XML["type"])
{
case "input":
	// Validate Input
	foreach ($obj_XML as $input)
	{
		switch ($input->getName() )
		{
		case "code":	// Validate Activation Code
			$aErrCd["code"] = $obj_Validator->valCode( (integer) $input);
			break;
		default:			// Error: Unknown tag
			$aErrCd["internal"] = 2;
			break;
		}
	}
	// Check return codes for errors
	while (list($tag, $code) = each($aErrCd) )
	{
		// Error found in Input
		if ($code < 10)
		{
			$xml .= '<'. $tag .' id="'. $code .'">'. htmlspecialchars($_OBJ_TXT->_($tag ." - code: ". $code), ENT_NOQUOTES) .'</'. $tag .'>';
		}
	}
	break;
case "form":
	// Validate Input
	$aErrCd["code"] = $obj_Validator->valCode( (integer) $obj_XML->form->code);
	if ($aErrCd["code"] == 10) { $aErrCd["code"] = $obj_mPoint->activateCode($_SESSION['obj_Info']->getInfo("accountid"), (integer) $obj_XML->form->code) + 3; }
	
	// Check return codes for errors
	while (list($tag, $code) = each($aErrCd) )
	{
		// Error found in Input
		if ($code < 10)
		{
			$xml .= '<'. $tag .' id="'. $code .'">'. htmlspecialchars($_OBJ_TXT->_($tag ." - code: ". $code), ENT_NOQUOTES) .'</'. $tag .'>';
		}
	}

	// Activation Code validated and consumed
	if (empty($xml) === true)
	{
		$code = $obj_mPoint->saveMobile($_SESSION['obj_Info']->getInfo("accountid"), $obj_mPoint->getActivationAddress($_SESSION['obj_Info']->getInfo("accountid"), (integer) $obj_XML->form->code) );

		// Activation code sent
		if ($code == 10)
		{
			$_SESSION['temp'] = array();
			$sType = "multipart";
			$xml = '<document type="command">
						<recache>
						 	<url>/home/my_account.php</url>
						</recache>
					</document>
					<document type="status">
						<form id="100" name="'. (string) $obj_XML->form['name'] .'">'. htmlspecialchars($_OBJ_TXT->_("save - code: 100"), ENT_NOQUOTES) .'</form>
					</document>
					<document type="command">
						<redirect>
					 		<url>/home/my_account.php</url>
					 	</redirect>
					</document>';
		}
		// Error in authentication, return status code and message
		else
		{
			$xml = '<form id="91" name="'. (string) $obj_XML->form['name'] .'">'. htmlspecialchars($_OBJ_TXT->_("save - code: 91"), ENT_NOQUOTES) .'</form>';
		}
	}
	break;
default:
	$xml = '<internal id="1">'. htmlspecialchars($_OBJ_TXT->_("internal - code: 2"), ENT_NOQUOTES) .'</internal>';
	break;
}

echo '<?xml version="1.0" encoding="UTF-8"?>'
?>
<root type="<?= $sType; ?>">
	<?= $xml; ?>
</root>