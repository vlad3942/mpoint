<?php
/**
 *
 * @author Simon Boriis
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package mPoint
 * @version 1.0
 */
// Require Global Include File
require_once("../../inc/include.php");

// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");

// Require Business logic for the validating client Input
require_once(sCLASS_PATH ."/validate.php");
$_OBJ_TXT->loadConstants(array("AUTH MIN LENGTH" => Constants::iAUTH_MIN_LENGTH, "AUTH MAX LENGTH" => Constants::iAUTH_MAX_LENGTH) );

$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);


$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["mesb"]);

echo $obj_mPoint->getTransactionStatus($obj_ConnInfo, 3600);

?>