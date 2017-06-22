<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes" media-type="application/xhtml+xml" doctype-public="-//WAPFORUM//DTD XHTML Mobile 1.0//EN" doctype-system="http://www.openmobilealliance.org/DTD/xhtml-mobile10.dtd" omit-xml-declaration="no" />
<xsl:include href="global.xsl"/>

<xsl:template match="/">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{func:transLanguage(/root/transaction/language)}">
	<head>
		<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=ISO-8859-15" />
		<meta http-equiv="Cache-Control" content="max-age=86400" />
		<meta http-equiv="Content-Style-Type" content="text/css" />	
		<title><xsl:value-of select="/root/title" /></title>
		<link href="/css/mobile.css" type="text/css" rel="stylesheet" />
	</head>
	<body>
		<div id="logo">
			<img src="/img/logo.jpg" width="100%" height="20%" alt="- mPoint -" />
		</div>
		<xsl:apply-templates select="/root/content" />
	</body>
	</html>
</xsl:template>

</xsl:stylesheet>