<?php
header("Content-Type: text/plain");
$xml = '<?xml version="1.0" encoding="ISO-8859-15"?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.openmobilealliance.org/DTD/xhtml-mobile10.dtd">
<html lang="en">
<head>
<title>Redirecting to payment server</title>
<meta http-equiv="refresh" content="0; URL=https://secure-test.wp3.rbsworldpay.com/wcc/card?Lang=en&DispatcherID=mm2imscs4p&PaymentID=bW0yaW1zY3M0cC1kcHByLTEyODc1MDU0MTgxNDI%3D&op-PMInitial=" />
</head>
<form action="https://secure-test.wp3.rbsworldpay.com/wcc/card" method="post">
<div id="redirect">
	<div class="mPoint_Info">
		Your browser is being redirected to our payment server.<br />If this does not happen shortly, please press the continue button.
	</div>
</div>
<div id="link">
	<a href="https://secure-test.wp3.rbsworldpay.com/wcc/card?Lang=en&DispatcherID=mm2imscs4p&PaymentID=bW0yaW1zY3M0cC1kcHByLTEyODc1MDU0MTgxNDI%3D&op-PMInitial=&date=19%2FOct%2F2010+16%3A23%3A38">
		<img src="/images/buttons/proceed.gif" alt="Continue" border="0" />
	</a>
</div>
</form>
</html>';

$aMatches = array();
echo preg_match('/<meta http-equiv="refresh" content="(.*)" \/>/', $xml, $aMatches);
list(, $url) = explode("; ", $aMatches[1]);
echo $url;
?>