<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="xml" version="1.0" encoding="ISO-8859-15" indent="yes" media-type="application/xhtml+xml" doctype-public="-//WAPFORUM//DTD XHTML Mobile 1.0//EN" doctype-system="http://www.openmobilealliance.org/DTD/xhtml-mobile10.dtd" omit-xml-declaration="no" />
<xsl:include href="mobile.xsl" />

<xsl:template match="/root">
	<div><a href="{func:constLink('overview.php')}"><xsl:value-of select="back" /></a></div>
	<div id="terms">
		<xsl:copy-of select="terms" />
	</div>
</xsl:template>
</xsl:stylesheet>