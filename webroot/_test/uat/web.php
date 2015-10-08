<h1>UAT WEB PAYMENT</h1>
<form action="http://<?= $_SERVER['HTTP_HOST']; ?>/buy/web.php" method="post">
<table>
<tr>
	<select name="clientid">
		<option value="10050">UAT TestClient</option>
		<option value="10051">UAT TestClient 2</option>
		<option value="10052">UAT TestClient 3</option>
</select>
</tr>
<tr>
	<td><input type="hidden" name="account" value="-1" /></td>
</tr>
<tr>
	<td><input type="hidden" name="mobile" value="28882861" /></td>
</tr>
<tr>
	<td><input type="hidden" name="operator" value="10002" /></td>
</tr>
<tr>
	<td><input type="hidden" name="email" value="jona@oismail.com" /></td>
</tr>
<tr>
	<td><input type="hidden" name="amount" value="100" /></td>
</tr>
<tr>
	<td><input type="hidden" name="language" value="gb" /></td>
</tr>
<tr>
	<td><input type="hidden" name="auth-token" value="123abc" /></td>
</tr>
<tr>
	<td><input type="hidden" name="customer-ref" value="1234412" /></td>
</tr>
<tr>
	<td><input type="hidden" name="orderid" value="UAT-<?= rand(10000000,99999999) ?>" /></td>
</tr>
<tr>
	<td colspan="2"><input type="submit" value="Send" /></td>
</tr>
</table>
</form>