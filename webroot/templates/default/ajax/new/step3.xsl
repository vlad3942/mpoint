<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:include href="../web.xsl" />

<xsl:template match="/root/content">
	<div id="create-account">
		<h1><xsl:value-of select="headline" /></h1>
		<br />
		<div class="info"><xsl:copy-of select="guide" /></div>	
	</div>
</xsl:template>

</xsl:stylesheet>