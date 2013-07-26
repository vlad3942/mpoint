<?php
/**
 * This file contains the Controller for mPoint's Payment Completed compont.
 * The component will generate a page using the Client Configuration providing Post Payment options:
 * 	- Send E-Mail Receipt
 * 	- Go to Client's Accept URL
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package NetAxept
 * @subpackage Accept
 * @version 1.00
 */

// Require Global Include File
//require_once("../inc/include.php");
define("sLOG_PATH", "/var/log/cpm/mRetail");

// Query String: mpoint-id=1529673&transactionId=1529673-1356466550&responseCode=01
file_put_contents(sLOG_PATH ."/jona.log", var_export($_POST, true), FILE_APPEND);
file_put_contents(sLOG_PATH ."/jona.log", var_export($_GET, true), FILE_APPEND);
file_put_contents(sLOG_PATH ."/jona.log", var_export($_SERVER, true), FILE_APPEND);
?>