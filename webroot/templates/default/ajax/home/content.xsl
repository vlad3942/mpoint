<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:include href="../web.xsl" />

<xsl:template match="/root/content">
	<div id="home">
		<h1><xsl:value-of select="headline" /></h1>
		<div id="overview">
			<xsl:value-of select="overview" />
		</div>
		<br />
		<div id="help">
			<h2><xsl:value-of select="labels/sms-traffic" /></h2>
			<xsl:copy-of select="help/sms-traffic" />
			
			<h2><xsl:value-of select="labels/page-views" /></h2>
			<xsl:copy-of select="help/page-views" />
		</div>
	</div>
</xsl:template>
</xsl:stylesheet>