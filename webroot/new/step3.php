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

// Account being created via a Mobile Device
if (array_key_exists("obj_UA", $_SESSION) === true)
{
	$sMarkup = General::getMarkupLanguage($_SESSION['obj_UA']);
}
else { $sMarkup = "ajax"; }

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/'. $sMarkup .'/new/step3.xsl"?>';
?>
<root type="page">
<?php
// Account being created via a Mobile Device
if (array_key_exists("obj_UA", $_SESSION) === true)
{
?>
	<title><?= $_OBJ_TXT->_("Create Account"); ?></title>
	
	<?= $obj_mPoint->getSystemInfo(); ?>
<?php
}
?>
	
	<content>
		<headline><?= $_OBJ_TXT->_("Create Account"); ?></headline>
		<labels>
			<mpoint><?= $_OBJ_TXT->_("Thank you for signing up for"); ?></mpoint>
		</labels>
		<guide>
			<web><?= $_OBJ_TXT->_("Create Account Guide - Web Step 3"); ?></web>
			<mobile><?= $_OBJ_TXT->_("Create Account Guide - Mobile Step 3"); ?></mobile>
		</guide>
		
		<?= $obj_mPoint->getSession(); ?>
		
		<?= $obj_mPoint->getMessages("Create Account"); ?>
	</content>
</root>