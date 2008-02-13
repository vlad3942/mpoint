<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:include href="header.xsl"/>

<xsl:template match="/root">
	<!-- Display Status Messages -->
	<xsl:apply-templates select="messages" />
</xsl:template>

</xsl:stylesheet>