<?php
	$redirectURL = "";
	$error = "";
	if(empty($_POST['txnid']) === false)
	{
		require_once("../../inc/include.php");
		
		$mPointTxnid = intval($_POST['txnid']);
		
		$aRS = $_OBJ_DB->getName("SELECT extid AS id, clientid, accountid FROM log.Transaction_Tbl where id = ".$mPointTxnid);
		if(empty($aRS) === false)
		{
			$extId = $aRS['ID'];
			$clientId = intval($aRS['CLIENTID']);
			$accountId = intval($aRS['ACCOUNTID']);

			if(empty($extId) === false)
			{
				$aRSMA = $_OBJ_DB->getName("SELECT name, username, passwd FROM Client.MerchantAccount_Tbl where clientid = ".$clientId." and pspid=18");

				$redirectURL = "https://".$aRSMA['USERNAME'].":".$aRSMA['PASSWD']."@";
				$redirectURL .= "api-test.wirecard.com/engine/rest/merchants/".$aRSMA['NAME'];
				$redirectURL .= "/payments/".$extId;
				
			} else { $error="External id not present for given transaction id"; }
		} else { $error="Please enter correct mpoint transaction id as no data present for given transaction id"; }
	} else if(isset($_POST['txnid']) && empty($_POST['txnid']) === true){ $error="Please enter correct mpoint transaction id"; }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <title>mPoint Wire Card Transaction Details</title>
    <meta http-equiv="content-type" content="application/xhtml+xml; charset=ISO-8859-1"/>
</head>
<body>
    <p>
	<?php if(empty($error) === false){?>
	    <span style="color:red;"> <?php echo $error; ?></span>
	<?php } ?>
    </p>
    <form action="" method="POST">
	<p>
	<label> Enter mPoint transaction id for wirecard : </label>
	<input type="text" name="txnid" id="txnid" />
	</p>
	<p><input type="submit" name="getData" id="getData" /></p>
    </form>
    <?php
	if($redirectURL != "")
	{
	    ?>
    <script type='text/javascript'>window.open("<?php echo $redirectURL; ?>", '_blank');</script>
	    
    <?php
	}
    ?>
</body>
</html>

