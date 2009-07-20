<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="xml" version="1.0" encoding="ISO-8859-15" indent="yes" media-type="application/xhtml+xml" doctype-public="-//W3C//DTD XHTML 1.1//EN" doctype-system="http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd" omit-xml-declaration="no" />
<xsl:include href="web.xsl" />
	
<xsl:template match="/">
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
	</head>
	<body>
		<div id="main">
			<table id="page" cellpadding="0" cellspacing="0" align="center" border="0">
			<tr>
				<td bgcolor="#709EB5"><img src="/img/top.jpg" width="1280" height="111" alt=" - Top - " /></td>
			</tr>
			<tr>
				<td valign="top">
					<table cellpadding="0" cellspacing="0">
					<tr>
						<td id="top-menu" colspan="2"></td>
					</tr>
					<tr>
						<td id="left-menu" rowspan="2"></td>
						<td id="content"></td>
					</tr>
					<tr>
						<td><div id="messages" /></td>
					</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td><img src="/img/bottom.jpg" width="1280" height="93" alt=" - Bottom - "/></td>
			</tr>
			</table>
		</div>
		<div id="debug" style="color:#000" />
		<script type="text/javascript">
			// HTTP Object produced
			if ( (typeof window.XMLHttpRequest) != "undefined" || window.ActiveXObject != false)
			{
				// Instantiate AJAX Client for handling the GUI
				var obj_Client = new Client("obj_Client");
				obj_Client.changePage("<xsl:value-of select="root/url" />");
				obj_Client.keepAlive("/internal/keepalive.php", 5*60);
		
				// Instantiate global Window object
				var obj_Window = new Window("obj_Window");
			}
			// Browser not supported
			else
			{
				document.getElementById("messages").innerHTML = '<xsl:value-of select="root/unsupported" />';
			}
		</script>
	</body>
	</html>
</xsl:template>

</xsl:stylesheet>