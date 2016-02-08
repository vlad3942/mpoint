<?php

	if(isset($_REQUEST['url']))
	{
		$path = "/var/log/cpm/mPoint/pspdata/";
		
		$file = $path."/".$_REQUEST['url']."_data.txt";

		if(file_exists($file))
		{
			header('Content-Description: File Transfer');
		    header('Content-Type: application/text');
		    header('Content-Disposition: attachment; filename="'.basename($file).'"');
		    header('Expires: 0');
		    header('Cache-Control: must-revalidate');
		    header('Pragma: public');
		    header('Content-Length: ' . filesize($file));
		    readfile($file);
		    exit;					
		}
		else {
			echo "File don't exists!!";
		}
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<title>URL logs</title>
	<meta http-equiv="content-type" content="application/xhtml+xml; charset=ISO-8859-1"/>
</head>
<body>
<h1>Download Url Logs</h1>
<table>
<tr>
    <td><a href="http://<?= $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>?url=customer_import">Download Customer Import URL logs</a></td>
</tr>
<tr>
    <td><a href="http://<?= $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>?url=callback">Download Callback URL logs</a></td>
</tr>
<tr>
    <td><a href="http://<?= $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>?url=notification">Download Notification URL logs</a></td>
</tr>
<tr>
    <td><a href="http://<?= $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>?url=auth">Download Auth URL logs</a></td>
</tr>
</table>
</body>
</html>
