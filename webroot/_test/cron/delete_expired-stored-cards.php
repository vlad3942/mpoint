<?php 

// Require Global Include File
require_once("inc/include.php");

$sql = "SELECT id, expiry
		FROM EndUser".sSCHEMA_POSTFIX.".Card_Tbl
		WHERE enabled = '1'";
//echo $sql ."\n";
$res = $_OBJ_DB->query($sql);
$iCount = 0;
while ($RS = $_OBJ_DB->fetchName($res) )
{
	if(empty(trim($RS['EXPIRY'])) === false)
	{
		$aExpiry = explode('/', $RS['EXPIRY']);
		if(empty(trim($aExpiry[0])) === false && empty(trim($aExpiry[1])) === false && cardNotExpired(intval($aExpiry[0]), intval($aExpiry[1])) === false )
		{		
			$sql2 = "DELETE FROM EndUser".sSCHEMA_POSTFIX.".Card_Tbl						
						WHERE id = ". $RS['ID'];
			//echo $sql2 ."\n";
			$_OBJ_DB->query($sql2);		
			$iCount++;
		}
	}
}

echo "Deleted $iCount Expired Stored Cards.";


function cardNotExpired($month, $year) {
	/* Get timestamp of midnight on day after expiration month. */
	$exp_ts = mktime(0, 0, 0, $month + 1, 1, $year);

	$cur_ts = time();	

	if ($exp_ts > $cur_ts) {
		return true;
	} else {
		return false;
	}
}
?>