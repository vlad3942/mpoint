<?php

function getAllUnreadMessages(RDB $obj_DB, $iRecipientID, $iSenderID)
{
	$aReturnArray = array();
	$sql = "SELECT A.chatname AS chatname, M.data AS text, M.created AS time
			FROM Log" . sSCHEMA_POSTFIX . ".Message_Tbl AS M
			INNER JOIN EndUser" . sSCHEMA_POSTFIX . ".Account_Tbl AS A ON M.fromid = A.id
			WHERE M.enabled = '1' AND A.enabled = '1' AND M.toid = ".intval($iRecipientID)." AND M.fromid = ".intval($iSenderID)."
			ORDER BY M.fromid, M.created ASC";	
	// echo $sql ."\n";
	
	$res = $obj_DB->query($sql);	
	
	while ($RS = $obj_DB->fetchName($res) )
	{
		array_push($aReturnArray, $RS);
	}	
	return $aReturnArray;	
}

function getAllActiveUsers(RDB $obj_DB)
{
	$aReturnArray = array();
	$sql = "SELECT s.count AS unread, a.id AS fromid, a.chatname AS name FROM
			(SELECT COUNT(id) AS count, fromid FROM Log". sSCHEMA_POSTFIX .".Message_Tbl 
			WHERE toid = 1 AND read = '0'
			GROUP BY fromid
			ORDER BY count DESC) AS s
			INNER JOIN 
			(SELECT MAX(created) AS maximum,fromid FROM Log". sSCHEMA_POSTFIX .".Message_Tbl
			WHERE toid = 1 AND read = '0'
			GROUP BY fromid
			ORDER BY maximum DESC) AS m ON s.fromid = m.fromid
			RIGHT OUTER JOIN 
			EndUser". sSCHEMA_POSTFIX .".Account_Tbl AS a ON m.fromid = a.id 
			ORDER BY m.maximum DESC";
	// echo $sql ."\n";

	$res = $obj_DB->query($sql);

	while ($RS = $obj_DB->fetchName($res) )
	{
		array_push($aReturnArray, $RS);
	}
	return $aReturnArray;
}


function getPreviousMessages(RDB $obj_DB, $iSenderID, $iRecipientID, $sBefore , $iLimit)
{
	$aReturnArray = array();
	$sql = "SELECT S.from, S.to, S.text, S.time
			FROM (
					SELECT A.chatname AS from, A2.chatname AS to, M.data AS text, M.created AS time
					FROM Log". sSCHEMA_POSTFIX .".Message_Tbl AS M
					INNER JOIN EndUser". sSCHEMA_POSTFIX .".Account_Tbl AS A ON M.fromid = A.id
					INNER JOIN EndUser". sSCHEMA_POSTFIX .".Account_Tbl AS A2 ON M.toid = A2.id
					WHERE M.enabled = '1' AND A.enabled = '1' AND M.read = '0' AND M.toid = ".intval($iRecipientID)." AND M.fromid = ".intval($iSenderID)." AND M.created < '".$sBefore."'
					ORDER BY M.created DESC 
					LIMIT ".intval($iLimit)."
				) AS S
				ORDER BY S.time ASC";
	// echo $sql ."\n";

	$res = $obj_DB->query($sql);

	while ($RS = $obj_DB->fetchName($res) )
	{
		array_push($aReturnArray, $RS);
	}
	return $aReturnArray;
}

function register(RDB $obj_DB, $sName, $sPushID)
{	
	$sql = "INSERT INTO Enduser". sSCHEMA_POSTFIX .".Account_Tbl
						(chatname, pushid)
					VALUES
						('". $sName ."', '". $sPushID ."')";
	
	//		echo $sql ."\n";
	$res = $obj_DB->query($sql);
	// Unable execute SQL query
	if (is_resource($res) === false) { $id = -1; }
	else{
		$sql2 = "SELECT MAX(id) AS id FROM Enduser". sSCHEMA_POSTFIX .".Account_Tbl";
		// echo $sql2 ."\n";
		
		$res2 = $obj_DB->getName($sql2);
		$id = $res2["ID"];
	}	
	return $id;	
	
}

function getIDFromChatName(RDB $obj_DB, $sName)
{
	$sql = "SELECT id FROM Enduser". sSCHEMA_POSTFIX .".Account_Tbl WHERE LOWER(chatname) = '".strtolower($sName)."'";
	// echo $sql ."\n";
	
	$res = $obj_DB->query($sql);	
	if (is_resource($res) === false) { $id = -1; }
	else {	
		$RS = $obj_DB->fetchName($res);
		$id = intval($RS["ID"]); 	
	}
	
	return $id;
}

function getPushIDFromUserID(RDB $obj_DB, $iUserID)
{
	$sql = "SELECT pushid FROM Enduser". sSCHEMA_POSTFIX .".Account_Tbl WHERE id = ".intval($iUserID);
	// echo $sql ."\n";
	
	$res = $obj_DB->query($sql);	
	if (is_resource($res) === false) { $id = -1; }
	else {	
		$RS = $obj_DB->fetchName($res);
		$id = $RS["PUSHID"]; 	
	}
	
	return $id;
}