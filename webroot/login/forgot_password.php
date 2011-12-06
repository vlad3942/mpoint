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

// Set Defaults
if (array_key_exists("temp", $_SESSION) === false) { $_SESSION['temp'] = array(); }
if (array_key_exists("countryid", $_SESSION['temp']) === false) { $_SESSION['temp']['countryid'] = $obj_mPoint->getCountryFromIP($_SERVER['REMOTE_ADDR']); }

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/ajax/login/forgot_password.xsl"?>';
?>
<root type="page">
	<content>
		<headline><?= $_OBJ_TXT->_("Forgot Password"); ?></headline>
		
		<labels>
			<country><?= $_OBJ_TXT->_("Country"); ?></country>
			<username><?= $_OBJ_TXT->_("Mobile / E-Mail"); ?></username>
			<submit><?= $_OBJ_TXT->_("Send Password"); ?></submit>
		</labels>
		
		<?= $obj_mPoint->getCountryConfigs(); ?>

		<?= $obj_mPoint->getSession(); ?>
	</content>
</root>