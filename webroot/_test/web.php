<h1>Mobile Web Purchase Test</h1>
<form action="http://<?= $_SERVER['HTTP_HOST']; ?>/buy/web.php" method="post">
<table>
<tr>
	<td style="font-weight:bold;">Client ID</td>
	<td><input type="text" name="clientid" value="10007" /></td>
</tr>
<tr>
	<td style="font-weight:bold;">Account</td>
	<td><input type="text" name="account" value="-1" /></td>
</tr>
<tr>
	<td style="font-weight:bold;">Recipient</td>
	<td><input type="text" name="mobile" value="28882861" /></td>
</tr>
<tr>
	<td style="font-weight:bold;">Operator</td>
	<td><input type="text" name="operator" value="10002" /></td>
</tr>
<tr>
	<td style="font-weight:bold;">E-Mail</td>
	<td><input type="text" name="email" value="jona@oismail.com" /></td>
</tr>
<tr>
	<td style="font-weight:bold;">Amount</td>
	<td><input type="text" name="amount" value="100" /></td>
</tr>
<tr>
	<td style="font-weight:bold;">Language</td>
	<td><input type="text" name="language" value="da" /></td>
</tr>
<tr>
	<td style="font-weight:bold;">Logo URL</td>
	<td><input type="text" name="logo-url" value="http://demo.ois-inc.com/mpoint/_test/client_logo.jpg" /></td>
</tr>
<tr>
	<td style="font-weight:bold;">CSS URL</td>
	<td><input type="text" name="css-url" value="http://<?= $_SERVER['HTTP_HOST']; ?>/css/mobile.css" /></td>
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
<tr>
	<td style="font-weight:bold;">Order ID</td>
	<td><input type="text" name="orderid" value="123abc" /></td>
</tr>
<tr>
	<td style="font-weight:bold;">Custom Variable</td>
	<td><input type="text" name="var_test" value="Test Variable" /></td>
</tr>
<tr>
	<td colspan="2"><input type="submit" value="Send" /></td>
</tr>
</table>
</form>