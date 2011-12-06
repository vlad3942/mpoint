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
require_once("../inc/include.php");

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<root type="multipart">
	<document type="command">
		<recache>
		 	<url>/home/topmenu.php</url>
		 	<url>/home/topup.php</url>
		 	<url>/home/transfer.php</url>
		 	<url>/home/history.php</url>
		 	<url>/home/my_account.php</url>
		</recache>
	</document>
	<document type="command">
		<redirect>
	 		<url>/home/topmenu.php</url>
	 		<url>/home/topup.php</url>
	 	</redirect>
	</document>
</root>