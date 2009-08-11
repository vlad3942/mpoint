<?php
/**
 *
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Admin
 * @subpackage General
 */

// Require include file for including all Shared and General APIs
require_once("../inc/include.php");

// Initialize Standard content Object
$obj_mPoint = new Home($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_CountryConfig']);

$obj_XML = simplexml_load_string($obj_mPoint->getAccountInfo($_SESSION['obj_Info']->getInfo("accountid") ) );

echo '<?xml version="1.0" encoding="UTF-8"?>';
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
?>
	<root type="multipart">
		<document type="command">
			<top-menu>
				<url>/home/topmenu.php</url>
			</top-menu>
			<content>
				<url>/home/content.php</url>
			</content>
		</document>
<?php
	if (strval($obj_XML->mobile) == "")
	{
		$popup = "mobile";
	}
	elseif (strval($obj_XML->email) == "")
	{
		$popup = "email";
	}
	elseif (strval($obj_XML->firstname) == "" || strval($obj_XML->lastname) == "")
	{
		$popup = "info";
	}
	
	if (isset($popup) === true)
	{
?>
		<document type="popup">
			<popup>
				<name>missing-data</name>
				<parent>left-menu</parent>
				<url>/home/missing_data.php?txt=<?= $popup; ?></url>
		 		<css>missing-data</css>
		 	</popup>
		 </document>
<?php
	}
?>
	</root>
<?php
}	// Access validation end
?>