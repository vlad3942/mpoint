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

// Require the PHP API for handling the connection to GoMobile
require_once(sAPI_CLASS_PATH ."/gomobile.php");

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
			case "mobile":	// Validate Mobile
				$aErrCd["mobile"] = $obj_Validator->valMobile( (string) $input);
				if ($aErrCd["mobile"] == 10) { $aErrCd["mobile"] = $obj_mPoint->valMobile($_SESSION['obj_Info']->getInfo("accountid"), (string) $input) + 3; }
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
		$aErrCd["mobile"] = $obj_Validator->valMobile( (string) $obj_XML->form->mobile);
		if ($aErrCd["mobile"] == 10) { $aErrCd["mobile"] = $obj_mPoint->valMobile($_SESSION['obj_Info']->getInfo("accountid"), (string) $obj_XML->form->mobile) + 3; }
		
		// Check return codes for errors
		while (list($tag, $code) = each($aErrCd) )
		{
			// Error found in Input
			if ($code < 10)
			{
				$xml .= '<'. $tag .' id="'. $code .'">'. htmlspecialchars($_OBJ_TXT->_($tag ." - code: ". $code), ENT_NOQUOTES) .'</'. $tag .'>';
			}
		}
	
		// New Mobile Number validated
		if (empty($xml) === true)
		{
			$code = $obj_mPoint->sendCode(GoMobileConnInfo::produceConnInfo($aGM_CONN_INFO), $_SESSION['obj_Info']->getInfo("accountid"), (string) $obj_XML->form->mobile);
			
			// Activation code sent
			if ($code == 200)
			{
				$sType = "multipart";
				$xml = '<document type="command" msg="status">
							<redirect>
						 		<url>/home/code.php</url>
						 	</redirect>
						</document>
						<document type="status">
							<form id="100" name="'. (string) $obj_XML->form['name'] .'">'. htmlspecialchars($_OBJ_TXT->_("send - code: 100"), ENT_NOQUOTES) .'</form>
						</document>';
			}
			// Error in authentication, return status code and message
			else
			{
				$xml = '<form id="91" name="'. (string) $obj_XML->form['name'] .'">'. htmlspecialchars($_OBJ_TXT->_("send - code: 91"), ENT_NOQUOTES) .'</form>';
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