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
require_once("../inc/include.php");

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<root type="command">
	<?php
	if (General::val() == 1000)
	{
	?>
		<top-menu>
			<url>/home/topmenu.php</url>
		</top-menu>
		<content>
			<url>/home/content.php</url>
		</content>
	<?php
	}
	else
	{
	?>
		<top-menu>
			<url>/login/topmenu.php</url>
		</top-menu>
		<content>
			<url>/login/content.php</url>
		</content>
	<?php
	}
	?>
</root>