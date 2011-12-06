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
	echo '<?xml-stylesheet type="text/xsl" href="/templates/'. sTEMPLATE .'/ajax/home/missing_data.xsl"?>';
?>
	<root type="page">
		<missing-data>
			<headline><?= $_OBJ_TXT->_("Missing Data"); ?></headline>

			<labels>
				<close><?= $_OBJ_TXT->_("OK"); ?></close>
				<my-account><?= $_OBJ_TXT->_("My Account"); ?></my-account>
			</labels>
			
			<guide text="<?= $_GET['txt']; ?>">
				<missing-mobile><?= $_OBJ_TXT->_("Missing Mobile Number Guide"); ?></missing-mobile>
				<missing-email><?= $_OBJ_TXT->_("Missing E-Mail Address Guide"); ?></missing-email>
				<missing-info><?= $_OBJ_TXT->_("Missing Profile Info Guide"); ?></missing-info>
			</guide>
		</missing-data>
	</root>
<?php
}	// Access validation end
?>