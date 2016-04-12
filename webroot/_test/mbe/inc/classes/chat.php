<?php

// Local mBE classes
require_once ("enduseraccount.php");

function saveMessage(RDB $obj_DB, $iFromID , $iToID, $sText)
{	
	$sql = "INSERT INTO Log". sSCHEMA_POSTFIX .".Message_Tbl
						(fromid, toid, data)
					VALUES
						(". intval($iFromID) .", '". intval($iToID) ."', '". $sText ."')";
	
	//		echo $sql ."\n";
	$res = $obj_DB->query($sql);
	// Unable execute SQL query
	if (is_resource($res) === false) { $id = -1; }
	else{
		$sql2 = "SELECT MAX(id) AS id FROM Log". sSCHEMA_POSTFIX .".Message_Tbl";
		// echo $sql2 ."\n";
	
		$res2 = $obj_DB->getName($sql2);
		$id = $res2["ID"];
		
		$sql3 = "UPDATE Log.Message_Tbl SET read = '1'
				WHERE fromid = ".$iToID." AND toid = ".$iFromID;
		$res3 = $obj_DB->query($sql3);		
	}
	return $id;
}