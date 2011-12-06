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
	case "form":
		// Validate Input
		$aErrCd["cardid"] = $obj_Validator->valStoredCard($_OBJ_DB, $_SESSION['obj_Info']->getInfo("accountid"), (integer) $obj_XML->form->cardid);
		
		// Check return codes for errors
		foreach ($aErrCd as $tag => $code)
		{
			// Error found in Input
			if ($code < 10)
			{
				$xml .= '<'. $tag .' id="'. $code .'">'. htmlspecialchars($_OBJ_TXT->_($tag ." - code: ". $code), ENT_NOQUOTES) .'</'. $tag .'>';
			}
		}
	
		// Card ID Validated
		if (empty($xml) === true)
		{
			switch (strtolower($obj_XML->form->command) )
			{
			case "delete":		// Delete the Stored Card
				if ($obj_mPoint->delStoredCard($_SESSION['obj_Info']->getInfo("accountid"), (integer) $obj_XML->form->cardid) === true)
				{
					$code = 101;
				}
				else { $code = 92; } 
				break;
			case "preferred":	// Change the Preferred Card for the Client (Merchant)
				$code = $obj_mPoint->setPreferredCard($_SESSION['obj_Info']->getInfo("accountid"), (integer) $obj_XML->form->cardid) + 92;
				break;
			default:			// Error: Unknown Command
				$code = 91;
				break;
			}
	
			// Success: Command Executed
			if ($code >= 100)
			{
				$_SESSION['temp'] = array();
				$sType = "multipart";
				$xml = '<document type="status">
							<form id="'. $code .'" name="'. (string) $obj_XML->form['name'] .'">'. htmlspecialchars($_OBJ_TXT->_("manage card - code: ". $code), ENT_NOQUOTES) .'</form>
						</document>
						<document type="command">
							<recache>
							 	<url>/home/my_account.php</url>
							</recache>
						</document>
						<document type="command" msg="status">
							<redirect>
						 		<url>/home/my_account.php</url>
						 	</redirect>
						</document>';
			}
			// Error while executing command
			else
			{
				$xml = '<form id="'. $code .'" name="'. (string) $obj_XML->form['name'] .'">'. htmlspecialchars($_OBJ_TXT->_("manage card - code: ". $code), ENT_NOQUOTES) .'</form>';
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