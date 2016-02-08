<?php
	file_put_contents("/var/log/cpm/mPoint/pspdata/callbackdata".time().".txt", print_r($_REQUEST, true));
	header("HTTP/1.1 200 OK");
?>