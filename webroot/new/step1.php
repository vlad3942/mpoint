<?php
/**
 *
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Admin
 * @subpackage CreateAccount
 */

// Require include file for including all Shared and General APIs
require_once("../inc/include.php");

// Initialize Standard content Object
$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);

// Set Defaults
if (array_key_exists("temp", $_SESSION) === false) { $_SESSION['temp'] = array(); }
if (array_key_exists("countryid", $_SESSION['temp']) === false) { $_SESSION['temp']['countryid'] = $obj_mPoint->getCountryFromIP($_SERVER['REMOTE_ADDR']); }
if (array_key_exists("checksum", $_GET) === true) { $_SESSION['temp']['checksum'] = strtoupper($_GET['checksum']); }
if (array_key_exists("email", $_GET) === true) { $_SESSION['temp']['email'] = strtolower($_GET['email']); }

// Account being created via a Mobile Device
if (array_key_exists("obj_UA", $_SESSION) === true)
{	
	$sMarkup = General::getMarkupLanguage($_SESSION['obj_UA']);
}
else { $sMarkup = "ajax"; }

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/'. $sMarkup .'/new/step1.xsl"?>';
?>
<root type="page">
<?php
// Account being created via a Mobile Device
if (array_key_exists("obj_UA", $_SESSION) === true)
{
?>
	<title><?= $_OBJ_TXT->_("Create Account"); ?></title>
	
	<?= $obj_mPoint->getSystemInfo(); ?>
	<user-info>
		<mobile countryid="<?= $_SESSION['obj_Info']->getInfo("countryid"); ?>"><?= $_SESSION['obj_Info']->getInfo("mobile"); ?></mobile>
	</user-info>
<?php
}
?>
	<content>		
		<headline><?= $_OBJ_TXT->_("Create Account"); ?></headline>

		<labels>
			<progress><?= $_OBJ_TXT->_("Step 1 of 2"); ?></progress>
			<country><?= $_OBJ_TXT->_("Country"); ?></country>
			<code><?= $_OBJ_TXT->_("Transfer Code"); ?></code>
			<firstname><?= $_OBJ_TXT->_("Firstname"); ?></firstname>
			<lastname><?= $_OBJ_TXT->_("Lastname"); ?></lastname>
			<mobile><?= $_OBJ_TXT->_("Mobile"); ?></mobile>
			<email><?= $_OBJ_TXT->_("E-Mail"); ?></email>
			<password><?= $_OBJ_TXT->_("Password"); ?></password>
			<repeat-password><?= $_OBJ_TXT->_("Repeat Password"); ?></repeat-password>
			<submit><?= $_OBJ_TXT->_("Create Account"); ?></submit>
			<select><?= $_OBJ_TXT->_("( Select )"); ?></select>
			<info><?= $_OBJ_TXT->_("Please complete the form below to set the password for your account"); ?></info>
		</labels>
		
		<guide><?= $_OBJ_TXT->_("Create Account Guide - Step 1"); ?></guide>
		
		<?= $obj_mPoint->getCountryConfigs(); ?>

		<?= $obj_mPoint->getSession(); ?>
		
		<?= $obj_mPoint->getMessages("Create Account"); ?>
	</content>
</root>