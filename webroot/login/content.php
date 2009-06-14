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
$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);

// Set Defaults
if (array_key_exists("temp", $_SESSION) === false) { $_SESSION['temp'] = array(); }
if (array_key_exists("countryid", $_SESSION['temp']) === false) { $_SESSION['temp']['countryid'] = $obj_mPoint->getCountryFromIP('213.173.252.91'); }

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/ajax/login/content.xsl"?>';
?>
<root type="page">
	<content>
		<headline><?= $_OBJ_TXT->_("Welcome to mPoint - User Admin. Please login?"); ?></headline>
		<country><?= $_OBJ_TXT->_("Country"); ?></country>
		<select><?= $_OBJ_TXT->_("( Select )"); ?></select>
		<username><?= $_OBJ_TXT->_("Username"); ?></username>
		<password><?= $_OBJ_TXT->_("Password"); ?></password>
		<submit><?= $_OBJ_TXT->_("Login"); ?></submit>

		<?= $obj_mPoint->getCountries(); ?>

		<?= $obj_mPoint->getSession(); ?>
	</content>
</root>