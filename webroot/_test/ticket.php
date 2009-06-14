<h1>Mobile Web Purchase Test</h1>
<form action="https://Mpoint:telefon23@payment.architrade.com/cgi-adm/delticket.cgi" method="post">
<table>
<tr>
	<td style="font-weight:bold;">Merchant</td>
	<td><input type="text" name="merchant" value="4216310" /></td>
</tr>
<tr>
	<td style="font-weight:bold;">Ticket</td>
	<td><input type="text" name="ticket" value="211306399" /></td>
</tr>
<tr>
	<td colspan="2"><input type="submit" value="Send" /></td>
</tr>
</table>
</form>
<?php
var_dump($_POST);
?>