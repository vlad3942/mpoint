<?php

function addTransaction(RDB $obj_DB, $sSource, $sDestination, $sDepart, $sArrive, $sSessionID, $sJourneyType = 0, $sClass = "Economy", $iAdults = 0)
{	
	$sql = "INSERT INTO Log". sSCHEMA_POSTFIX .".Transaction_Tbl
						(source, destination, journeytype, departure, arrival, class, passengers, sessionid)
					VALUES
						('". $sSource ."', '". $sDestination ."', '". $sJourneyType ."', '". $sDepart ."', '". $sArrive ."', '". $sClass ."', ". $iAdults .", '". $sSessionID ."')";
	
	//		echo $sql ."\n";
	$res = $obj_DB->query($sql);
	// Unable execute SQL query
	if (is_resource($res) === false) { $id = -1; }
	else{
		$sql2 = "SELECT MAX(id) AS id FROM Log". sSCHEMA_POSTFIX .".Transaction_Tbl";
		// echo $sql2 ."\n";
		
		$res2 = $obj_DB->getName($sql2);
		$id = $res2["ID"];
	}	
	return $id;	
	
}

function getTxnIDFromSessionID(RDB $obj_DB, $sSessionID)
{
	$sql = "SELECT id FROM Log". sSCHEMA_POSTFIX .".Transaction_Tbl WHERE sessionid = '".$sSessionID."'";
	// echo $sql ."\n";
	
	$res = $obj_DB->query($sql);	
	if (is_resource($res) === false) { $id = -1; }
	else {
		$RS = $obj_DB->fetchName($res);
		$id = intval($RS["ID"]); 	
	}	
	return $id;
}

function getCurrentStateFromTxnID(RDB $obj_DB, $iTxnID)
{
	$sql = "SELECT stateid FROM Log". sSCHEMA_POSTFIX .".TxnState_Tbl WHERE txnid = '".$iTxnID."' ORDER BY created DESC LIMIT 1";
	// echo $sql ."\n";

	$res = $obj_DB->query($sql);
	if (is_resource($res) === false) { $id = -1; }
	else {
		$RS = $obj_DB->fetchName($res);
		$id = intval($RS["STATEID"]);
	}

	return $id;
}

function updateCurrentStateFromTxnID(RDB $obj_DB, $iTxnID, $sMessage, $iStateID)
{
	$sql = "INSERT INTO Log". sSCHEMA_POSTFIX .".TxnState_Tbl
						(txnid, stateid, data)
					VALUES
						(". $iTxnID .", ". $iStateID .", '". $sMessage ."')";
	
	//echo $sql ."\n";
	$res = $obj_DB->query($sql);
	// Unable execute SQL query
	if (is_resource($res) === false) { $id = -1; }
	else{
		$sql2 = "SELECT MAX(id) AS id FROM Log". sSCHEMA_POSTFIX .".TxnState_Tbl";
		// echo $sql2 ."\n";
		
		$res2 = $obj_DB->getName($sql2);
		$id = $res2["ID"];
	}	
	return $id;
}

function updatePassengerCount(RDB $obj_DB, $iTxnID, $iPax)
{
	$id = intval($iTxnID);
	$sql = "UPDATE Log". sSCHEMA_POSTFIX .".Transaction_Tbl
					SET passengers = ". intval($iPax) ."
					WHERE id = ". intval($iTxnID);

	//echo $sql ."\n";
	$res = $obj_DB->query($sql);
	// Unable execute SQL query
	if (is_resource($res) === false) { $id = -1; }	
	return $id;
}

function getItineraryFromTxnID(RDB $obj_DB, $iTxnID)
{
	$sql = "SELECT data FROM Log". sSCHEMA_POSTFIX .".TxnState_Tbl WHERE txnid = '".$iTxnID."' AND stateid = 500 ORDER BY created DESC LIMIT 1";
	// echo $sql ."\n";

	$res = $obj_DB->query($sql);
	if (is_resource($res) === false) { $id = -1; }
	else {
		$RS = $obj_DB->fetchName($res);
		$id = (string) $RS["DATA"];
	}
	return $id;
}

function getMaskedCardNumber($obj_DB_MPOINT, $sMobile, $sEmail)
{
	$sql = "SELECT mask FROM Enduser". sSCHEMA_POSTFIX .".Card_Tbl AS EUC
			INNER JOIN Enduser". sSCHEMA_POSTFIX .".Account_Tbl AS EUA ON EUA.id = EUC.accountid
			WHERE EUA.email = '".$sEmail."' AND EUA.mobile = '".$sMobile."' AND EUC.enabled = '1' 
			ORDER BY EUC.created DESC 
			LIMIT 1";
	// echo $sql ."\n";
	
	$res = $obj_DB_MPOINT->query($sql);
	if (is_resource($res) === false) { $id = -1; }
	else {
		$RS = $obj_DB_MPOINT->fetchName($res);
		$id = (string) $RS["MASK"];
	}
	return $id;
}

function deletePrevBookingRequest($obj_DB, $sSessionID)
{
	$id = $sSessionID;
	$sql = "DELETE FROM Log". sSCHEMA_POSTFIX .".Transaction_Tbl					
					WHERE sessionid = '". $sSessionID."'";
	//echo $sql ."\n";
	$res = $obj_DB->query($sql);
	// Unable execute SQL query
	if (is_resource($res) === false) { $id = -1; }	
	return $id;
}