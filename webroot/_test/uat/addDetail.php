<?php 
require_once("../../inc/include.php");
    header('Content-Type: text/html; charset="UTF-8"');
    $client = array();
	$account = array();
    $RSCountry = array();
$sqlcountry = "SELECT id, name, currency, symbol, maxbalance, mintransfer, minmob, maxmob, channel, priceformat, decimals,
					addr_lookup, doi, add_card_amount, max_psms_amount, min_pwd_amount, min_2fa_amount
				FROM System".sSCHEMA_POSTFIX.".Country_Tbl
				WHERE enabled = '1' ORDER BY name";
		
		$RSCountry = $_OBJ_DB->getAllNames($sqlcountry);
		//print_r($RSCountry);
		$sqlclient = "SELECT id,concat_ws(' - ',id,name) as clientnm
  FROM client.client_tbl ORDER BY name";
		
		$client = $_OBJ_DB->getAllNames($sqlclient);
		//print_r($client);
		
		/**/

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
    <body style="background-color:#fff;text-align:center">

<form class="form-horizontal" action="http://<?= $_SERVER['HTTP_HOST']; ?>/_test/uat/order.php" method="post">
<fieldset>

<!-- Form Name -->
<legend>Add Neccessary details</legend>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="clientsel">Client Id</label>
  <div class="col-md-4">
  
    <select id="clientsel" name="clientsel" class="form-control input-md">
     <option value="-1">select Client</option>
		<?php
			if(empty($client) === false)
			{
				foreach ($client as $clientid)
				{
				    echo "<option value='".$clientid['ID']."'>".$clientid['CLIENTNM']."</option>";
				}
			}
		?>
    </select>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="accountsel">Account Id</label>
  <div class="col-md-4">
    <select id="accountsel" name="accountsel" class="form-control input-md">
      <option value="-1">select account</option>
		<?php
		$sqlaccount = "SELECT id,concat_ws(' - ',id,name) as accnm 
  FROM client.account_tbl ORDER BY name";
			
		$account = $_OBJ_DB->getAllNames($sqlaccount);
			if(empty($account) === false)
			{
				foreach ($account as $accountid)
				{
				    echo "<option value='".$accountid['ID']."'>".$accountid['ACCNM']."</option>";
				}
			}
		?>
    </select>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="countrysel">Country</label>
  <div class="col-md-4">
    <select id="countrysel" name="countrysel" class="form-control input-md">
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
    </select>
  </div>
</div>
<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="email">Email</label>  
  <div class="col-md-4">
  <input id="email" name="email" type="text" placeholder="email" value="manish@cellpointmobile.com" class="form-control input-md" >
    
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="add1">Mobile</label>  
  <div class="col-md-4">
  <input id="mobile" name="mobile" type="text" placeholder="" value="7385793006" class="form-control input-md" >
    
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="add1">Language</label>  
  <div class="col-md-4">
  <input id="lan" name="lan" type="text" placeholder="" value="ga" class="form-control input-md" >
    
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="add1">Auth Token</label>  
  <div class="col-md-4">
  <input id="AT" name="AT" type="text" placeholder="" class="form-control input-md" >
    
  </div>
</div>

<div class="form-group">
  <label class="col-md-4 control-label" for="add1">Flight Number</label>  
  <div class="col-md-4">
  <input id="FN" name="FN" type="text" placeholder="" class="form-control input-md" value="UAT-00000" >
    
  </div>
</div>

<div class="form-group">
  <label class="col-md-4 control-label" for="add1">From</label>  
  <div class="col-md-4">
  <input id="from" name="from" type="text" placeholder="" class="form-control input-md" >
    
  </div>
</div>


<div class="form-group">
  <label class="col-md-4 control-label" for="add1">To</label>  
  <div class="col-md-4">
  <input id="to" name="to" type="text" placeholder="" class="form-control input-md" >
    
  </div>
</div>

<div class="form-group">
  <label class="col-md-4 control-label" for="add1">Ticket</label>  
  <div class="col-md-4">
  <input id="ticket" name="ticket" type="text" placeholder="" class="form-control input-md" value="0" >
    
  </div>
</div>


<div class="form-group">
  <label class="col-md-4 control-label" for="add1">Excess Baggage</label>  
  <div class="col-md-4">
  <input id="excb" name="excb" type="text" placeholder="" class="form-control input-md" value="0"  >
    
  </div>
</div>


<div class="form-group">
  <label class="col-md-4 control-label" for="add1">Taxes</label>  
  <div class="col-md-4">
  <input id="taxes" name="taxes" type="text" placeholder="" class="form-control input-md" value="0" >
    
  </div>
</div>


<div class="form-group">
  <label class="col-md-4 control-label" for="add1">Fuel Surcharge</label>  
  <div class="col-md-4">
  <input id="fuel" name="fuel" type="text" placeholder="" class="form-control input-md" value="0"  >
    
  </div>
</div>

<div class="form-group">
  <label class="col-md-4 control-label" for="add1">Premium Seat</label>  
  <div class="col-md-4">
  <input id="premium" name="premium" type="text" placeholder="" class="form-control input-md" value="0"  >
    
  </div>
</div>

<div class="form-group">
  <label class="col-md-4 control-label" for="submit"></label>
  <div class="col-md-4">
    <input class="btn pull-right" type="submit" value="Process Details" />
  </div>
</div>

<input name="total" id="total" type="hidden" />


</fieldset>
</form>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
      <!-- Include all compiled plugins (below), or include individual files as needed -->
      <script src="js/bootstrap.min.js"></script>
      <!-- <script src="js/custom.js"></script> -->
      <script src="js/validate.js"></script>

 <script type="text/javascript">
	  
	  $('document').ready(function(){
		  
		  function add()
		  {
			  
			var ticket = $("#ticket").val();	
			var excb  = $("#excb").val();
			var tax = $("#taxes").val();
			var fuel = $("#fuel").val();
			var pre = $("#premium").val();
			
			var tott = parseInt(ticket) + parseInt(excb) + parseInt(tax) + parseInt(fuel) + parseInt(pre);
			  $("#total").val(tott);
			 var tttt = $("#total").val();
			
		  }
		  $('#ticket').change(function(){
			  add();
			  
		  
		  })
		  $('#excb').change(function(){
			  add();
		  
		  })
		  $('#taxes').change(function(){
			  add();
		  
		  })
		  $('#fuel').change(function(){
			  add();
		  
		  })
		  $('#premium').change(function(){
			  add();
		  
		  })
		 	
	  })
	  
	  </script>
</body>
</html>