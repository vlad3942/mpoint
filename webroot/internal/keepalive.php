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

// Session object not initialized
if (isset($_SESSION['obj_Info']) === false)
{
	$_SESSION['obj_Info'] = new WebSession();
}

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<root type="status"><keepalive id="100"><?= session_id(); ?></keepalive></root>