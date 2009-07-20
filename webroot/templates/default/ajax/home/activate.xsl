<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:include href="../web.xsl" />

<xsl:template match="/root/content">
	<div id="activate">
		<h1><xsl:value-of select="headline" /></h1>
		<div id="progress">
			<xsl:value-of select="labels/progress" />
			<br /><br />
			<div class="info"><xsl:value-of select="guide" /></div>
		</div>
		
		<br />
		
		<!-- Display Status Messages -->
		<xsl:apply-templates select="messages" />
	</div>
</xsl:template>
</xsl:stylesheet>