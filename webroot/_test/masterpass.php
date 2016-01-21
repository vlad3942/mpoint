<?php
define("sAPI_CLASS_PATH", "/apps/php/php5api/classes/");

require_once(sAPI_CLASS_PATH ."template.php");
require_once(sAPI_CLASS_PATH ."http_client.php");
// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."simpledom.php");

if(isset($_POST['submit'])) 
{	
	/**
	 * Connection info for sending error reports to a remote host
	*/
	$aHTTP_CONN_INFO["mesb"]["protocol"] = "http";
	//$aHTTP_CONN_INFO["mesb"]["host"] = "10.150.242.42";
	$aHTTP_CONN_INFO["mesb"]["host"] = $_SERVER['HTTP_HOST'];
	$aHTTP_CONN_INFO["mesb"]["port"] = 80; // mPoint
	//$aHTTP_CONN_INFO["mesb"]["port"] = 9000; // MESB
	$aHTTP_CONN_INFO["mesb"]["timeout"] = 120;
	$aHTTP_CONN_INFO["mesb"]["path"] = "/mApp/api/pay.php";
	$aHTTP_CONN_INFO["mesb"]["method"] = "POST";
	$aHTTP_CONN_INFO["mesb"]["contenttype"] = "text/xml";
	$aHTTP_CONN_INFO["mesb"]["username"] = $_POST['client-username'];
	$aHTTP_CONN_INFO["mesb"]["password"] = $_POST['client-password'];
	
	$obj_ConnInfo = HTTPConnInfo::produceConnInfo($aHTTP_CONN_INFO["mesb"]);
	
	$h = "{METHOD} {PATH} HTTP/1.0" .HTTPClient::CRLF;
	$h .= "host: {HOST}" .HTTPClient::CRLF;
	$h .= "referer: {REFERER}" .HTTPClient::CRLF;
	$h .= "content-length: {CONTENTLENGTH}" .HTTPClient::CRLF;
	$h .= "content-type: {CONTENTTYPE}" .HTTPClient::CRLF;
	$h .= "user-agent: mPoint" .HTTPClient::CRLF;
	$h .= "Authorization: Basic ". base64_encode($aHTTP_CONN_INFO["mesb"]["username"] .":". $aHTTP_CONN_INFO["mesb"]["password"]) .HTTPClient::CRLF;
	
	$b = '<?xml version="1.0" encoding="UTF-8"?>
	<root>
		<pay client-id="'.$_POST['client-id'].'" account="'.$_POST['account-id'].'">
			<transaction id="'.$_POST['transaction-id'].'" store-card="false">
				<card type-id="23">
					<amount country-id="'.$_POST['country-id'].'">'.$_POST['amount'].'</amount>
				</card>
			</transaction>
			<client-info language="us" version="1.28" platform="iOS/8.1">
	            <mobile operator-id="10000" country-id="602">288828610</mobile>
	            <email>jona@oismail.com</email>
	            <device-id>23lkhfgjh24qsdfkjh</device-id>
	        </client-info>
		</pay>
	</root>';
	
	try
	{
		$obj_Client = new HTTPClient(new Template(), $obj_ConnInfo);
		$obj_Client->connect();
		$code = $obj_Client->send($h, $b);
		$obj_Client->disconnect();
		if ($code == 200 && strlen($obj_Client->getReplyBody() ) > 0)
		{
			$obj_XML = simplexml_load_string($obj_Client->getReplyBody() );
		}
		else
		{
			header("Content-Type: text/plain");
			var_dump($obj_Client);
			die();
		}
		
		$sHead = trim($obj_XML->{'psp-info'}->head);	
		?>
		<html>
		<head>
		<!-- Master Pass JavaScript function -->
		<?= $sHead; ?>
		</head>
		<body>
		<!-- Master Pass button img tag -->
		<?= $obj_XML->{'psp-info'}->body; ?>	
		</body>
		</html>
		<script type = "text/javascript"><!--
			function mpSuccessCallback(data) { 
				console.log(data); 
				var url = 'masterpass_authorize.php';
				var username = '<?php echo $_POST['client-username']; ?>';
				var password = '<?php echo $_POST['client-password']; ?>';
				var form = $('<form action="' + url + '" method="post">' +
				  '<input type="hidden" name="client-id" value="' + <?php echo $_POST['client-id']; ?> + '" />' +
				  '<input type="hidden" name="client-username" value="' + username + '" />' +
				  '<input type="hidden" name="client-password" value="' + password + '" />' +
				  '<input type="hidden" name="account-id" value="' + <?php echo $_POST['account-id']; ?> + '" />' +
				  '<input type="hidden" name="transaction-id" value="' + <?php echo $_POST['transaction-id']; ?> + '" />' +
				  '<input type="hidden" name="country-id" value="' + <?php echo $_POST['country-id']; ?> + '" />' +
				  '<input type="hidden" name="amount" value="' + <?php echo $_POST['amount']; ?> + '" />' +
				  '<input type="hidden" name="token" value="' + data.oauth_token + '" />' +
				  '<input type="hidden" name="verifier" value="' + data.oauth_verifier + '" />' +
				  '<input type="hidden" name="checkouturl" value="' + data.checkout_resource_url + '" />' +				  
				  '</form>');
				$('body').append(form);
				form.submit();
			}
			function mpFailureCallback(data) { console.log(data); }
			function mpCancelCallback(data) { console.log(data); }
		</script>
	<?php
	}
	catch (Exception $e)
	{
		header("Content-Type: text/plain");
		var_dump($e);
		var_dump($obj_Client);
		die();
	}
}
else {
?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
   <label>Client ID&nbsp;&nbsp;&nbsp;</label><input type="text" name="client-id">
   <input type="text" name="client-username" placeholder = "username">
   <input type="text" name="client-password" placeholder = "password">   
   <br>
   <label>Account ID&nbsp;&nbsp;&nbsp;</label><input type="text" name="account-id"><br>
   <label>Transaction ID&nbsp;&nbsp;&nbsp;</label><input type="text" name="transaction-id"><br>   
   <label>Amount&nbsp;&nbsp;&nbsp;</label><input type="text" name="amount"><br>
   <label>Country&nbsp;&nbsp;&nbsp;</label>
   <select name = "country-id">
	  <option value="100">Denmark</option>
	  <option value="103">UK</option>
	  <option value="200">USA</option>
	  <option value="602">Dubai</option>
</select><br>
   <input type="submit" name="submit" value="Submit Form"><br>
</form>
<?php } ?>