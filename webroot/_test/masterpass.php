<?php
define("sPHP5_API_CLASS_PATH", "/apps/php/php5api/classes/");

require_once(__DIR__.'/../inc/include.php');
require_once(sPHP5_API_CLASS_PATH ."template.php");
require_once(sPHP5_API_CLASS_PATH ."http_client.php");
// Require API for Simple DOM manipulation
require_once(sPHP5_API_CLASS_PATH ."simpledom.php");

header('Content-Type: text/html; charset="UTF-8"');

if(isset($_POST['submit']) === true ) 
{	
	/**
	 * Connection info for sending error reports to a remote host
	*/	
	$aHTTP_CONN_INFO["mesb"]["path"] = "/mpoint/pay";
	$aHTTP_CONN_INFO["mesb"]["username"] = trim($_POST['client-username']);
	$aHTTP_CONN_INFO["mesb"]["password"] = trim($_POST['client-password']);
	
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
		<pay client-id="'.intval($_POST['client-id']).'" account="'.intval($_POST['account-id']).'">
			<transaction id="'.intval($_POST['transaction-id']).'" store-card="false">
				<card type-id="23">
					<amount country-id="'.intval($_POST['country-id']).'">'.$_POST['amount'].'</amount>
				</card>
			</transaction>
			<client-info language="us" version="1.28" platform="iOS/8.1">
	            <mobile operator-id="10000" country-id="'.intval($_POST['country-id']).'">'.intval($_POST['mobile']).'</mobile>
	            <email>'.trim($_POST['email']).'</email>
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
				var euapassword = '<?php echo $_POST['password']; ?>';
				var euaemail = '<?php echo $_POST['email']; ?>';
				var form = $('<form action="' + url + '" method="post">' +
				  '<input type="hidden" name="client-id" value="' + <?php echo $_POST['client-id']; ?> + '" />' +
				  '<input type="hidden" name="client-username" value="' + username + '" />' +
				  '<input type="hidden" name="client-password" value="' + password + '" />' +
				  '<input type="hidden" name="account-id" value="' + <?php echo $_POST['account-id']; ?> + '" />' +
				  '<input type="hidden" name="transaction-id" value="' + <?php echo $_POST['transaction-id']; ?> + '" />' +
				  '<input type="hidden" name="country-id" value="' + <?php echo $_POST['country-id']; ?> + '" />' +
				  '<input type="hidden" name="amount" value="' + <?php echo $_POST['amount']; ?> + '" />' +
				  '<input type="hidden" name="email" value="' + euaemail + '" />' +
				  '<input type="hidden" name="mobile" value="' + <?php echo $_POST['mobile']; ?> + '" />' +
				  '<input type="hidden" name="password" value="' + euapassword + '" />' +		  
				  '<input type="hidden" name="token" value="' + data.oauth_token + '" />' +
				  '<input type="hidden" name="verifier" value="' + data.oauth_verifier + '" />' +
				  '<input type="hidden" name="checkouturl" value="' + data.checkout_resource_url + '" />' +				  
				  '</form>');
				$('body').append(form);
				form.submit();
			}
			function mpFailureCallback(data) { console.log("in FAILURE"); console.log(data); }
			function mpCancelCallback(data) { console.log("in CANCEL"); console.log(data); }
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
   <label>EndUser Email&nbsp;&nbsp;&nbsp;</label><input type="text" name="email"><br>
   <label>EndUser Mobile&nbsp;&nbsp;&nbsp;</label><input type="text" name="mobile"><br>
   <label>EndUser Password&nbsp;&nbsp;&nbsp;</label><input type="text" name="password"><br>
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