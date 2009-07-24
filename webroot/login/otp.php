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

// Initialize Standard content Object
$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/ajax/login/otp.xsl"?>';
?>
<root type="page">
	<one-time-password>
		<headline><?= $_OBJ_TXT->_("One Time Password"); ?></headline>

		<?= $_SESSION['obj_CountryConfig']->toXML(); ?>

		<labels>
			<password><?= $_OBJ_TXT->_("Password"); ?></password>
			<submit><?= $_OBJ_TXT->_("Login"); ?></submit>
		</labels>
		
		<guide><?= $_OBJ_TXT->_("One Time Password Guide"); ?></guide>
	</one-time-password>
</root>