<?php 

// Require Global Include File
require_once("inc/include.php");

$obj_CountryConfig = CountryConfig::produceConfig($_OBJ_DB, 100);
$obj_mPoint = new MyAccount($_OBJ_DB, $_OBJ_TXT, $obj_CountryConfig);

$sql = "SELECT id, accountid, expiry
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
		$iAccountID = intval($RS['ACCOUNTID']);
		$iCardID = intval($RS['ID']);
		if(empty(trim($aExpiry[0])) === false && empty(trim($aExpiry[1])) === false && cardNotExpired(intval($aExpiry[0]), intval($aExpiry[1])) === false )
		{		
			$iDelStatus = $obj_mPoint->delStoredCard($iAccountID, $iCardID);					
			if(intval($iDelStatus) == 10)
			{
				$iCount++;
			}			
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