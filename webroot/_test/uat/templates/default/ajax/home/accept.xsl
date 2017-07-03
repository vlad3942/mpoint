<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:include href="../web.xsl" />

<xsl:template match="/root">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="uk">
	<head>
		<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=ISO-8859-15" />
		<meta http-equiv="Cache-Control" content="max-age=86400" />
		<meta http-equiv="Content-Style-Type" content="text/css" />
		<meta http-equiv="Content-Script-Type" content="text/javascript" />
		<title><xsl:value-of select="/root/title" /></title>
		<link href="{/root/transaction/css-url}" type="text/css" rel="stylesheet" />
	</head>
	<body onload="javascript:window.opener.obj_Client.changePage('/home/update.php');">
		<div id="logo">
			<img src="/img/{/root/transaction/logo/width}x{/root/transaction/logo/height}_client_{/root/system/session/@id}.png" width="{/root/transaction/logo/width}" height="{/root/transaction/logo/height}" alt="- {/root/client-config/name} -" />
		</div>
		<br />
		<div id="top-up">
			<div class="mPoint_Info">
				<xsl:value-of select="labels/info" />
			</div>
			<div id="close">
				<button type="button" onclick="javascript:window.close();"><xsl:value-of select="labels/close" /></button>
			</div>
		</div>
	</body>
	</html>
</xsl:template>
</xsl:stylesheet>