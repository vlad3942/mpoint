<?php
    require_once("../../inc/include.php");
    $accounts = array();
    $urls = array();
    $id = "";
    $RSCountry = array();
    if(isset($_REQUEST['id']) && $_REQUEST['id'] > 0)
    {
		$id = $_REQUEST['id'];
		
		$obj_ClientConfig = ClientConfig::produceConfig($_OBJ_DB, $_REQUEST['id'], -1);
		
		$sql = "SELECT id, name, currency, symbol, maxbalance, mintransfer, minmob, maxmob, channel, priceformat, decimals,
					addr_lookup, doi, add_card_amount, max_psms_amount, min_pwd_amount, min_2fa_amount
				FROM System".sSCHEMA_POSTFIX.".Country_Tbl
				WHERE enabled = '1' ORDER BY name";
		//		echo $sql ."\n";
		$RSCountry = $_OBJ_DB->getAllNames($sql);
		
		if(is_object( $obj_ClientConfig )) 
		{
			$accounts = $obj_ClientConfig->getAccountsConfigurations();
			
			/* if($obj_ClientConfig->getCallbackURL() !== "")
				$urls["Callback"] = $obj_ClientConfig->getCallbackURL()."?".htmlentities("from=webpage&url=callback");
			else 
				$urls["Callback"] = "";
			
			if($obj_ClientConfig->getNotificationURL() !== "")
				$urls["Notification"] = $obj_ClientConfig->getNotificationURL()."?".htmlentities("from=webpage&url=notification");
			else
				$urls["Notification"] = ""; */
			
			if($obj_ClientConfig->getAcceptURL() !== "")
				$urls["Accept"] = htmlentities($obj_ClientConfig->getAcceptURL());
			else 
				$urls["Accept"] = "";
			
			if($obj_ClientConfig->getCancelURL() !== "")
			$urls['Cancel'] = htmlentities($obj_ClientConfig->getCancelURL());
			else
				$urls["Cancel"] = "";
			
			/* if($obj_ClientConfig->getAuthenticationURL() !== "")
			$urls['SSO'] = $obj_ClientConfig->getAuthenticationURL()."?".htmlentities("from=webpage&url=SSO");
			else
				$urls["SSO"] = ""; */
			
			if($obj_ClientConfig->getCSSURL() !== "")
			$urls['CSS'] = htmlentities($obj_ClientConfig->getCSSURL());
			else
				$urls["CSS"] = "";
			
			if($obj_ClientConfig->getLogoURL() !== "")
			$urls['Logo'] = htmlentities($obj_ClientConfig->getLogoURL());
			else
				$urls["Logo"] = "";
			
			/* if($obj_ClientConfig->getMESBURL() !== "")
			$urls['PSP'] = htmlentities($obj_ClientConfig->getMESBURL());
			else
				$urls["PSP"] = ""; */
			
			if($obj_ClientConfig->getIconURL() !== "")
			$urls['Icon'] = htmlentities($obj_ClientConfig->getIconURL());
			else
				$urls["Icon"] = "";
			
			/* if($obj_ClientConfig->getCustomerImportURL() !== "")
			$urls['CustomerImportURL'] = htmlentities($obj_ClientConfig->getCustomerImportURL());
			else
				$urls["CustomerImportURL"] = ""; */
			
		}
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<title>mPoint Automatic Tests</title>
	<meta http-equiv="content-type" content="application/xhtml+xml; charset=ISO-8859-1"/>
</head>
<body>
<h1>UAT WEB PAYMENT</h1>
<form name="clientForm" action="http://<?= $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>?id='+document.getElementById('id').value;">
<table>
<tr>

    <input type="text" name="id" id="id" value="" />
    <input type="submit" id="clientsubmit" value="Get Client" />
</tr>
</table>
</form>
<form action="http://<?= $_SERVER['HTTP_HOST']; ?>/buy/web.php" method="post">
<table>
    <!--<tr><td><?php //if(empty($accounts) === true) { echo "No data found for selected client"; }?></td></tr>-->

<?php
			if(empty($urls) === false)
			{
				foreach ($urls as $label => $url)
				{
					if($url !== "") {
?>
	<tr><td><b><?php echo $label; ?></b></td><td><input type="text"  value="<?php echo $url; ?>" name="<?php echo $label; ?>" id="<?php echo $label; ?>" disabled="disabled"/></td>
	
	<td><a href="<?php echo $url; ?>" target="_blank">Click Here</a></td>
	</tr>
<?php 
					}
				}
				
			}
?>
<tr>
	<td>
	    <select name="accounts" onchange="showAccountDataOnChange(this);">
		<option value="-1">select Account</option>
		<?php
			if(empty($accounts) === false)
			{
				foreach ($accounts as $account)
				{
				    echo "<option value='".$account->getMarkupLanguage()."'>".$account->getId()."</option>";
				}
			}
		?>
	</select></td>
</tr>
<tr>
	<td>
	    <select name="country">
		<option value="-1">select Country</option>
		<?php
			if(empty($RSCountry) === false)
			{
				foreach ($RSCountry as $country)
				{
				    echo "<option value='".$country['ID']."'>".$country['NAME']."</option>";
				}
			}
		?>
	</select></td>
</tr>
<tr>
    <td><b>Selected client id : <?php echo $id;?></b><input type="hidden" name="clientid" id="clientid" value="<?php echo $id; ?>" /></td>
</tr>
<tr>
    <td><b>Selected account id : <span id="accountid"></span></b><input type="hidden" name="account" id="account" value="-1" /></td>
</tr>
<tr>
	<td><b>Selected markup : <span id="markupname"></span></b><input type="hidden" name="markup" id="markup" value="xhtml" /></td>
</tr>
<tr>
	<td><b>Amount* : </b><input type="tel" name="amount" value="100" /></td>
</tr>
<tr>
	
	<?php $orderid = 'UAT-'.rand(10000000,99999999); ?>
	<td><b>Order Id : </b><?php echo $orderid; ?><input type="hidden" name="orderid" value="<?php echo $orderid; ?>" /></td>
</tr>
<tr>
	<td><b>Mobile Number : </b><input type="tel" name="mobile" value="30206172" /></td>
</tr>
<tr>
	<td><b>Operator Number : </b><input type="tel" name="operator" value="10002" /></td>
</tr>
<tr>
	<td><b>Email : </b><input type="email" name="email" value="abhishek@cellpointmobile.com" /></td>
</tr>

<tr>
	<td><b>Language : </b><input type="text" name="language" value="gb" /></td>
</tr>
<tr>
	<td><b>Auth Token : </b><input type="text" name="auth-token" value="" /></td>
</tr>
<tr>
	<td><b>Customer-Ref : </b><input type="text" name="customer-ref" value="1234412" /></td>
</tr>
<tr>
	<td colspan="2"><input type="submit" value="Send" /></td>
</tr>
</table>
</form>
<script type="text/javascript">
	function showAccountDataOnChange(obj)
	{
		document.getElementById('markupname').innerHTML = obj.value; 
		document.getElementById('markup').value=obj.value; 
		document.getElementById('accountid').innerHTML = obj.options[obj.selectedIndex].text;
		document.getElementById('account').value = obj.options[obj.selectedIndex].text;
	}
</script>
</body>
</html>
