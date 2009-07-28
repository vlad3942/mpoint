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

// Error: Unauthorized access
if (General::val() != 1000)
{
?>
	<root type="command">
		<redirect>
			<url>/internal/unauthorized.php?code=<?= General::val(); ?></url>
		</redirect>
	</root>
<?php
}
// Success: Access granted
else
{
	// Add allowed min and max length for the password to the list of constants used for Text Tag Replacement
	$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );
	
	// Initialize Standard content Object
	$obj_mPoint = new MyAccount($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_CountryConfig']);
	$obj_Validator = new Validate();
	
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
			case "firstname":	// Validate First name
				$aErrCd["firstname"] = $obj_Validator->valName( (string) $input);
				break;
			case "lastname":	// Validate Lastname
				$aErrCd["lastname"] = $obj_Validator->valName( (string) $input);
				break;
			default:			// Error: Unknown tag
				$aErrCd["internal"] = 2;
				break;
			}
		}
		// Check return codes for errors
		foreach ($aErrCd as $tag => $code)
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
		$aErrCd["firstname"] = $obj_Validator->valName( (string) $obj_XML->form->firstname);
		$aErrCd["lastname"] = $obj_Validator->valName( (string) $obj_XML->form->lastname);
	
		// Check return codes for errors
		foreach ($aErrCd as $tag => $code)
		{
			// Error found in Input
			if ($code != 10)
			{
				$xml .= '<'. $tag .' id="'. $code .'">'. htmlspecialchars($_OBJ_TXT->_($tag ." - code: ". $code), ENT_NOQUOTES) .'</'. $tag .'>';
			}
		}
	
		// Username / Password validated
		if (empty($xml) === true)
		{
			$code = $obj_mPoint->saveInfo($_SESSION['obj_Info']->getInfo("accountid"), (string) $obj_XML->form->firstname, (string) $obj_XML->form->lastname);
	
			// Account Info saved
			if ($code === true)
			{
				$sType = "multipart";
				$xml = '<document type="status">
							<form id="100" name="'. (string) $obj_XML->form['name'] .'">'. htmlspecialchars($_OBJ_TXT->_("save - code: 100"), ENT_NOQUOTES) .'</form>
						</document>
						<document type="command">
							<recache>
							 	<url>/home/my_account.php</url>
							 	<url>/home/history.php</url>
							</recache>
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
<?php
}	// Access validation end
?>