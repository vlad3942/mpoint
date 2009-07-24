<?php
/**
 * This file contains the Controller for listing the available balanced which a prepaid account may be topped up with
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package TopUp
 * @subpackage Shop
 * @version 1.0
 */

// Require Global Include File
require_once("../inc/include.php");

// Require Business logic for the Top-Up Component
require_once(sCLASS_PATH ."/topup.php");

// Initialize Standard content Object
$obj_mPoint = new TopUp($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo']->getClientConfig()->getCountryConfig() );

// Store Original data object with Transaction Information so original payment flow may be resumed 
if (array_key_exists("obj_TxnInfo", $_SESSION) === true && array_key_exists("obj_OrgTxnInfo", $_SESSION) === false) { $_SESSION['obj_OrgTxnInfo'] = $_SESSION['obj_TxnInfo']; }

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/'. General::getMarkupLanguage($_SESSION['obj_UA']) .'/shop/topup.xsl"?>';
?>
<root>
	<title><?= $_OBJ_TXT->_("Top-Up Account"); ?></title>

	<?= $obj_mPoint->getSystemInfo(); ?>

	<?= $_SESSION['obj_TxnInfo']->toXML($_SESSION['obj_UA']); ?>
	<?= $_SESSION['obj_TxnInfo']->getClientConfig()->toXML(); ?>
	<?= $_SESSION['obj_TxnInfo']->getClientConfig()->getAccountConfig()->toXML(); ?>
	<?= $_SESSION['obj_TxnInfo']->getClientConfig()->getCountryConfig()->toXML(); ?>
	
	<labels>
		<info><?= $_OBJ_TXT->_("Account Top-Up - Info"); ?></info>
		<amount><?= $_OBJ_TXT->_("Amount"); ?></amount>
		<price><?= $_OBJ_TXT->_("Price"); ?></price>
	</labels>
	
	<?php
		$obj_XML = simplexml_load_string($obj_mPoint->getAccountInfo($_SESSION['obj_TxnInfo']->getAccountID(), $_SESSION['obj_UA']) );
		echo str_replace('<?xml version="1.0"?>', '', $obj_XML->asXML() );
	?>
	
	<?= $obj_mPoint->getDepositOptions( (integer) $obj_XML->balance); ?>
	
	<?= $obj_mPoint->getMessages("Shop Top-Up"); ?>
</root>