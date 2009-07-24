<?php
/**
 *
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Admin
 * @subpackage Login
 */

// Require include file for including all Shared and General APIs
require_once("../../inc/include.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");

// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );

// Initialize Standard content Object
$obj_mPoint = new Home($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_CountryConfig']);
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
		case "otp":	// Validate One Time Password
			$aErrCd["otp"] = $obj_Validator->valCode( (integer) $input);
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
	$aErrCd["otp"] = $obj_Validator->valCode( (integer) $obj_XML->form->otp);
	if ($aErrCd["otp"] == 10) { $aErrCd["otp"] = $obj_mPoint->activateCode($_SESSION['temp']['accountid'], (integer) $obj_XML->form->otp) + 3; }
	
	// Check return codes for errors
	while (list($tag, $code) = each($aErrCd) )
	{
		// Error found in Input
		if ($code < 10)
		{
			$xml .= '<'. $tag .' id="'. $code .'">'. htmlspecialchars($_OBJ_TXT->_($tag ." - code: ". $code), ENT_NOQUOTES) .'</'. $tag .'>';
		}
	}

	// One Time Password validated
	if (empty($xml) === true)
	{
		$_SESSION['obj_Info']->setInfo("accountid", $_SESSION['temp']['accountid']);
		
		$sType = "multipart";
		$xml = '<document type="command">
					'. Home::getRecacheLogin() .'
				</document>
				<document type="status">
					<form id="100" name="'. (string) $obj_XML->form['name'] .'">'. htmlspecialchars($_OBJ_TXT->_("auth - code: 100"), ENT_NOQUOTES) .'</form>
				</document>
				<document type="command" msg="status">
					<redirect>
				 		<url>/home/default.php</url>
				 	</redirect>
				</document>';
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