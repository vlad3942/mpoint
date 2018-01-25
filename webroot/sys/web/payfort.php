<?php
    require_once("/include.php");
   ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<title>PayFort Integration</title>
		<meta http-equiv="content-type" content="application/xhtml+xml; charset=ISO-8859-1"/>
    	<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
        <script src="js/jquery.min.js"></script>
		<script src="js/bootstrap.min.js" />
	</head>
	<body>
		<div class="container">
			<div class="row">
				<div class="col-md-4">
					<form action="http://<?= $_SERVER['HTTP_HOST']; ?>/buy/web.php" method="post" class="form-horizontal">
						<input type="hidden" name="clientid" id="clientid" value="10045" />
						<input type="hidden" name="account" id="account" value="100086" />
						<input type="hidden" name="country" id="country" value="608" />
						<input type="hidden" name="accounts" id="account" value="app" />
						<input type="hidden" name="auth-token" value="" />
						<input type="hidden" name="operator" value="60800" />
						<input type="hidden" name="language" value="us" />
						<input type="hidden" name="markup" id="markup" value="html5" />
						<input type="hidden" value="https://<?= $_SERVER['HTTP_HOST']; ?>/_test/uat/payfort.php" name="Accept" id="Accept" disabled="disabled"/>
						<input type="hidden" value="https://<?= $_SERVER['HTTP_HOST']; ?>/mpoint/dibs/cancel" name="Cancel" id="Cancel" disabled="disabled"/>
						<input type="hidden" value="http://mpoint.uat.cellpointmobile.com/_test/uat/white.css" name="CSS" id="CSS" disabled="disabled"/>
						<input type="hidden" value="http://mpoint.uat.cellpointmobile.com/_test/uat/logo1.png" name="Logo" id="Logo" disabled="disabled"/>
						<input type="hidden" value="http://mpoint.uat.cellpointmobile.com/_test/uat/logo1.png" name="Icon" id="Icon" disabled="disabled"/>
						
						<table class="table">
							<caption><b>PayFort Sandbox</b></caption>
							<tr>
								<div class="form-group">
									<td class="col-md-3">
										<b>Amount*</b>
									</td>
									<td class="col-md-3">
										<input type="tel" name="amount" value=""  class="form-control input-sm"/>
									</td>
								</div>
							</tr>
							<tr>
								<div class="form-group">
									<td class="col-md-3">
										<b>Mobile Number</b>
									</td>
									<td class="col-md-3">
										<input type="tel" name="mobile" value=""  class="form-control input-sm"/>
									</td>
								</div>
							</tr>
							<tr>
								<div class="form-group">
									<td class="col-md-3">
										<b>Email</b>
									</td>
									<td class="col-md-3">
									    <input type="email" name="email" value="payfort@cellpointmobile.com"  class="form-control input-sm"/>
									</td>
								</div>
							</tr>
							<tr>
								<?php $orderid = 'UAT-'.rand(10000000,99999999); ?>
								<div class="form-group">
									<td class="col-md-3">
										<b>Order Id</b>
									</td>
									<td class="col-md-3">
										<?php echo $orderid; ?>
									    <input type="hidden" name="orderid" value="<?php echo $orderid; ?>"  class="form-control"/>
									</td>
								</div>
							</tr>
							<tr>
							    <?php $custref = ''.rand(10000000,99999999); ?>
								<div class="form-group">
									<td class="col-md-3">
										<b>Customer-Ref</b>
									</td>
									<td class="col-md-3">
										<input type="text" name="customer-ref" value="<?php echo $custref; ?>"  class="form-control"/>
									</td>
								</div>
							</tr>
							<tr>
								<div class="form-group col-sm-offset-2 col-sm-10">
									<td></td>
									<td colspan="2">
										<input type="submit" value="Send"  class="btn btn-default"/>
									</td>
								</div>
							</tr>
						</table>
					</form>
				</div>
				<div class="col-md-8">
				
				</div>
			</div>
		</div>
	</body>
</html>
