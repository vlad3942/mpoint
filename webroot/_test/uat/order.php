<?php
    require_once("../../inc/include.php");
    
    header('Content-Type: text/html; charset="UTF-8"');
    
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
   // $details=Array ("accounts" => 'html5', "country" => 100, "clientid" => 10007, "account" => 100007, "markup" => 'html5', "amount" => 100 ,"orderid" => 'UAT-77813110' ,"mobile" => 30206172 ,"operator" => 10002 ,"email" => 'abhishek@cellpointmobile.com' ,"language" => 'gb', "auth-token" => '',"customer-ref" => 1234412);
	if(isset($_REQUEST['account']) && $_REQUEST['account'] > 0 && isset($_REQUEST['client']) && $_REQUEST['client'] > 0)
	{
		
		function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
		
	$accountidd=$_REQUEST['account'];
	  $clientidd=$_REQUEST['client'];	
	  $countryidd=$_REQUEST['country'];	
	  $orderidd=generateRandomString();
	  $operatoridd=$countryidd*100;
	  $customeridd=generateRandomString();
	  $mobileno="7385793006";
		// $sql = "SELECT markup as abc FROM client.account_tbl where id='".$accountidd."'";
		//		echo $sql ."\n";
		// $getmarkup = $_OBJ_DB->getAllNames($sql);
		// echo "<pre/>";
		// print_r($getmarkup);
		// print($getmarkup['ABC']);
		// $markup=$getmarkup['ABC'];
		
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
  <title>Cart Screen</title>

  <!-- Bootstrap -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">


  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet"/>
</head>
    <body>

      <section>
	  <form action="http://<?= $_SERVER['HTTP_HOST']; ?>/buy/web.php" method="post">
	  <table>
    <!--<tr><td></td></tr>-->


	
	
	
	
<tr>
	
</tr>
<tr>
	<td><?php echo "<input name=\"country\" id=\"country\" value='".$countryidd."' type=\"hidden\" />"; ?>
	    </td>
</tr>
<tr>
    <td><?php echo "<input name=\"clientid\" id=\"clientid\" value='".$clientidd."' type=\"hidden\" /></td>"; ?>
</tr>
<tr>
    <td><?php echo "<input name=\"account\" id=\"account\" value='".$accountidd."' type=\"hidden\" /></td>"; ?></td>
</tr>
<tr>
	
</tr>
<tr>
	
		<td><?php echo "<input name=\"orderid\" id=\"orderid\" value='".$orderidd."' type=\"hidden\" /></td>"; ?></td>
</tr>
<tr>
	<td><?php echo "<input name=\"mobile\" id=\"mobile\" value='".$mobileno."' type=\"hidden\" /></td>"; ?></td>
</tr>
<tr>
	<td><?php echo "<input name=\"operator\" id=\"operator\" value='".$operatoridd."' type=\"hidden\" /></td>"; ?></td>
</tr>
<tr>
	<td><input name="email" value="abhishek@cellpointmobile.com" type="hidden" /></td>
</tr>

<tr>
	<td><input name="language" value="gb" type="hidden" /></td>
</tr>
<tr>
	<td><input name="auth-token" value="" type="hidden" /></td>
</tr>
<tr>
	<td><?php echo "<input name=\"customer-ref\" id=\"customer-ref\" value='".$customeridd."' type=\"hidden\" /></td>"; ?></td>
</tr>
<tr>
	
</tr>
</table>
        <div class="container main">
          <div class="row">
            <div class="col-md-3">
              <a href="" class="logo"><img src="img/logo.jpg" alt="CellPoint Mobile" /></a>
            </div>
            <div class="col-md-9 text-right">
              <h2 class="sub-header">Order Summary <small>(step 1/3)</small></h2>
            </div>
          </div> 
          <hr>
          <div class="row panel-main">
            <div class="col-md-7">
              <div class="row">
                <div class="col-md-12 flight">
                  <div class="row flight-main">
                    <img src="img/airplane-white.png" class="depart-img" alt="Departure flight">
                    <div class="col-md-1">
                     
                      <h4>LON-141292</h4>
                    </div>
					<div class="col-md-4 text-right">
					
                      <h1>LON</h1>
                      <p>London</p>
                      <hr>
                      <h3>03:00</h3>
                      <p>Mon, 19 December 2016</p>
                    </div>
                    <div class="col-md-2 text-center">
                      &nbsp;
                    </div>
                    <div class="col-md-4 text-left">
                      <h1>DXB</h1>
                      <p>Dubai</p>
                      <hr>
                      <h3>04:40</h3>
                      <p>Mon, 19 December 2016</p>
                    </div>
                  </div>
                  <div class="row flight-footer">
                    <div class="col-md-4 text-center">
                      <p><small>Passengers</small></p>
                      <p class="red">1 Adult</p>
                    </div>
                    <div class="col-md-4 text-center">
                      <p><small>Cabin</small></p>
                      <p class="red">Economy</p>
                    </div>
                    <div class="col-md-4 text-center">
                      <p><small>Seat</small></p>
                      <p class="red">15A</p>
                    </div>
                  </div>
                  <div class="row flight-header">
                    <div class="col-md-6">
                      <p>Total</p>
                    </div>
                    <div class="col-md-6 text-right">
                      <h2 class="blue">32,000 USD</h2>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-md-offset-1">
              <div class="billing-summary">
                <h4 class="sub-header">Billing Summary</h4>
                <hr>
                <table class="table table-bordered">
                  <tbody> 
                    <tr> 
                      <th scope="row">Ticket</th>
                      <td>29,040</td>
                    </tr>
                    <tr> 
                      <th scope="row">Excess Baggage</th>
                      <td>2,000</td>
                    </tr>
                    <tr> 
                      <th scope="row">Taxes</th>
                      <td>4,500</td> 
                    </tr> 
                    <tr> 
                      <th scope="row">Fuel surcharge</th> 
                      <td>3,460</td> 
                    </tr>
                    <tr> 
                      <th scope="row">Premium Seat</th> 
                      <td>NA</td> 
                    </tr>
                    <tr class="active"> 
                      <th scope="row">Total (in USD)</th> 
                      <td> 38,000 <input type="hidden" name="amount" value="38,000" /></td> 
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div> 
          <div class="row panel-footer">
            <div class="col-sm-6">
              <a class="btn cancel" href="#" role="button">Cancel</a>
            </div>
            <div class="col-sm-6">
			<input class="btn pull-right" type="submit" value="Proceed to pay" />
         
            </div>
          </div>
        </div>
		</form>
      </section>

      <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
      <!-- Include all compiled plugins (below), or include individual files as needed -->
      <script src="js/bootstrap.min.js"></script>
      <!-- <script src="js/custom.js"></script> -->
      <script src="js/validate.js"></script>
    </body>
    </html>

