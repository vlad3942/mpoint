<?php
/**
 *
 * @author Simon Boriis
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package mPoint
 */

// Require include file for including all Shared and General APIs
require_once("../inc/include.php");

header("Content-Type: text/plain");

if (iPURGED_DAYS > 0)
{
	set_time_limit(0);

	$start = time();
	// Initialize Standard content Object
	$obj_mPoint = new General($_OBJ_DB, $_OBJ_TXT);
	//For Log.Message_Tbl
	$iNumCards = $obj_mPoint->purgeMessageLogs(iPURGED_DAYS);
	echo date("Y-m-d H:i:s") ." - ". $iNumCards ." messages was purged in ". (time() - $start) ." seconds.\n";
	//For Log.AuditLog_Tbl
	$iNumCards = $obj_mPoint->purgeAuditLogs(iPURGED_DAYS);
	echo date("Y-m-d H:i:s") ." - ". $iNumCards ." Audit Logs was purged in ". (time() - $start) ." seconds.";
}
?>