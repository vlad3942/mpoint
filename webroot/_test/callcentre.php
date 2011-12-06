<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="uk">
	<head>
		<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=ISO-8859-15" />
		<meta http-equiv="Cache-Control" content="max-age=86400" />
		<meta http-equiv="Content-Style-Type" content="text/css" />
		<meta http-equiv="Content-Script-Type" content="text/javascript" />
		<title><xsl:value-of select="/root/title" /></title>
		<link href="/css/global.css" type="text/css" rel="stylesheet" />
		
		<script type="text/javascript" src="/inc/global.js"></script>
		<script type="text/javascript" src="/inc/client.js"></script>
		<script type="text/javascript" src="/inc/window.js"></script>
		<script type="text/javascript" src="/inc/mpoint.js"></script>
	</head>
	<body>
		<div id="main">
			<table id="page" cellpadding="0" cellspacing="0" align="center">
			<tr>
				<td id="top"><img src="/img/top.jpg" width="800" height="111" alt=" - Top - " /></td>
			</tr>
			<tr>
				<td valign="top">
					<table cellpadding="0" cellspacing="0">
					<tr>
						<td id="top-menu" colspan="3"></td>
					</tr>
					<tr>
						<td id="left-menu" rowspan="2"></td>
						<td colspan="2">
<h1>Call Centre Purchase Test</h1>
<form action="/buy/callcentre.php" method="post">
<table>
<!--
<tr>
	<td style="font-weight:bold;">Client ID</td>
	<td><input type="text" name="clientid" value="10000" /></td>
</tr>
<tr>
	<td style="font-weight:bold;">Account</td>
	<td><input type="text" name="account" value="-1" /></td>
</tr>
-->
<tr>
	<td style="font-weight:bold;">Mobile</td>
	<td><input type="text" name="mobile" value="3053315242" /></td>
</tr>
<!--
<tr>
	<td style="font-weight:bold;">Operator</td>
	<td><input type="text" name="operator" value="20000" /></td>
</tr>
-->
<tr>
	<td style="font-weight:bold;">Product Name</td>
	<td><input type="text" name="prod-names[test]" value="Test Product" /></td>
</tr>
<tr>
	<td style="font-weight:bold;">Product Quantity</td>
	<td><input type="text" name="prod-quantities[test]" value="3" /></td>
</tr>
<tr>
	<td style="font-weight:bold;">Product Price</td>
	<td><input type="text" name="prod-prices[test]" value="100" /></td>
</tr>
<tr>
	<td style="font-weight:bold;">Product Logo</td>
	<td><input type="text" name="prod-logos[test]" value="http://demo.ois-inc.com/mpoint/_test/product_logo.jpg" /></td>
</tr>
<tr>
	<td style="font-weight:bold;">Language</td>
	<td><input type="text" name="language" value="gb" /></td>
</tr>
<!--
<tr>
	<td style="font-weight:bold;">Logo URL</td>
	<td><input type="text" name="logo-url" value="http://demo.ois-inc.com/mpoint/_test/client_logo.jpg" /></td>
</tr>
<tr>
	<td style="font-weight:bold;">CSS URL</td>
	<td><input type="text" name="css-url" value="http://demo.ois-inc.com/mpoint/_test/styles.css" /></td>
</tr>
<tr>
	<td style="font-weight:bold;">Accept URL</td>
	<td><input type="text" name="accept-url" value="http://demo.ois-inc.com/mpoint/_test/accept.php" /></td>
</tr>
<tr>
	<td style="font-weight:bold;">Cancel URL</td>
	<td><input type="text" name="cancel-url" value="http://demo.ois-inc.com/mpoint/_test/cancel.php" /></td>
</tr>
<tr>
	<td style="font-weight:bold;">Callback URL</td>
	<td><input type="text" name="callback-url" value="http://demo.ois-inc.com/mpoint/_test/callback.php" /></td>
</tr>
-->
<tr>
	<td style="font-weight:bold;">Order ID</td>
	<td><input type="text" name="orderid" value="123abc" /></td>
</tr>
<tr>
	<td style="font-weight:bold;">Booking Ref.</td>
	<td><input type="text" name="var_bookingref" value="" /></td>
</tr>
<tr>
	<td style="font-weight:bold;">Passenger Name</td>
	<td><input type="text" name="var_name" value="" /></td>
</tr>
<tr>
	<td style="font-weight:bold;">Passenger Name</td>
	<td><select style="width:140px; height:25px"></select></td>
</tr>
<tr>
	<td colspan="2"><input type="submit" value="Send" /></td>
</tr>
</table>
</form>
</td>
					</tr>
					<tr>
						<td width="100%"><div id="messages" /></td>
						<td id="provider"><img src="/img/provider.jpg" width="145" height="21" alt=" - Provider - "/></td>
					</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td id="bottom"><img src="/img/bottom.jpg" width="800" height="58" alt=" - Bottom - "/></td>
			</tr>
			</table>
