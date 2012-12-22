<?php
/**
 *
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Admin
 * @subpackage ViewTransactions
 */

// Require include file for including all Shared and General APIs
require_once("../inc/include.php");

// Initialize Standard content Object
$obj_mPoint = new Home($_OBJ_DB, $_OBJ_TXT, $_SESSION['obj_CountryConfig']);

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
	echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/ajax/home/history.xsl"?>';
?>
	<root type="page">
		<content>
			<headline><?= $_OBJ_TXT->_("Transaction History"); ?></headline>

			<labels>
				<purchase-history><?= $_OBJ_TXT->_("Purchase History"); ?></purchase-history>
				<transfer-history><?= $_OBJ_TXT->_("Transfer History"); ?></transfer-history>
				<topup-history><?= $_OBJ_TXT->_("Top-Up History"); ?></topup-history>
				<reward-history><?= $_OBJ_TXT->_("Reward History"); ?></reward-history>
				<id><?= $_OBJ_TXT->_("ID"); ?></id>
				<mpointid><?= $_OBJ_TXT->_("mPoint ID"); ?></mpointid>
				<amount><?= $_OBJ_TXT->_("Amount"); ?></amount>
				<card><?= $_OBJ_TXT->_("Method"); ?></card>
				<price><?= $_OBJ_TXT->_("Price"); ?></price>
				<fee><?= $_OBJ_TXT->_("Fee"); ?></fee>
				<client><?= $_OBJ_TXT->_("Merchant"); ?></client>
				<orderid><?= $_OBJ_TXT->_("Order No."); ?></orderid>
				<sender><?= $_OBJ_TXT->_("Sender"); ?></sender>
				<recipient><?= $_OBJ_TXT->_("Recipient"); ?></recipient>
				<ip><?= $_OBJ_TXT->_("IP"); ?></ip>
				<timestamp><?= $_OBJ_TXT->_("Timestamp"); ?></timestamp>
			</labels>

			<?= $obj_mPoint->getTxnHistory($_SESSION['obj_Info']->getInfo("accountid") ); ?>
		</content>
	</root>
<?php
}	// Access validation end
?>