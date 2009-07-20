<?php
/**
 *
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Admin
 * @subpackage TopUp
 */

// Require include file for including all Shared and General APIs
require_once("../inc/include.php");

/*
 * Use Output buffering to "magically" transform the XML via XSL behind the scene
 * This means that all PHP scripts must output a wellformed XML document.
 * The XML in turn must refer to an XSL Stylesheet by using the xml-stylesheet tag
 */
ob_start(array(new Output("all", false), "transform") );

// Initialize Standard content Object
$obj_mPoint = new Home($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_TxnInfo']->getClientConfig()->getCountryConfig() );

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/ajax/home/accept.xsl"?>';

?>
<root>
	<title><?= $_OBJ_TXT->_("Top-Up Account"); ?></title>
	
	<?= $obj_mPoint->getSystemInfo(); ?>

	<?= $_SESSION['obj_TxnInfo']->toXML($_SESSION['obj_UA']); ?>
	<?= $_SESSION['obj_TxnInfo']->getClientConfig()->toXML(); ?>

	<labels>
		<info><?= General::val() == 1000 ? $_OBJ_TXT->_("Account Top-Up - Completed") : $_OBJ_TXT->_("Account Top-Up - Authentication Error"); ?></info>
		<amount><?= $_OBJ_TXT->_("Amount"); ?></amount>
		<price><?= $_OBJ_TXT->_("Price"); ?></price>
		<close><?= $_OBJ_TXT->_("Close"); ?></close>
	</labels>
	
	<?php
		$obj_XML = simplexml_load_string($obj_mPoint->getAccountInfo($_SESSION['obj_TxnInfo']->getAccountID(), $_SESSION['obj_UA']) );
		echo str_replace('<?xml version="1.0"?>', '', $obj_XML->asXML() );
	?>
</root>