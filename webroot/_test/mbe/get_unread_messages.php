<?php
require_once ("inc/include.php");

$iRecipientID = getIDFromChatName($_OBJ_DB_MBE, $_GET['to']);

$iSenderID = getIDFromChatName($_OBJ_DB_MBE, $_GET['from']);

$aReturn = getAllUnreadMessages($_OBJ_DB_MBE, $iRecipientID, $iSenderID);

$aReturnReverse = getAllUnreadMessages($_OBJ_DB_MBE, $iSenderID, $iRecipientID);

$arr = array_merge($aReturn, $aReturnReverse);
usort($arr, 'cmp');

echo "jsonpCallbackGetMessages(".json_encode($arr).")";

function cmp($a, $b){
	$ad = strtotime($a['TIME']);
	$bd = strtotime($b['TIME']);
	return ($ad-$bd);
}
