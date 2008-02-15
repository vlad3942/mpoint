<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="xml" version="1.0" encoding="ISO-8859-15" indent="yes" media-type="application/xhtml+xml" doctype-public="-//W3C//DTD XHTML Basic 1.0//EN" doctype-system="http://www.w3.org/TR/2000/REC-xhtml-basic-20001219/xhtml-basic10.dtd" omit-xml-declaration="no" />
<xsl:include href="header.xsl"/>

<xsl:template match="/root">
	<!-- Display Status Messages -->
	<xsl:apply-templates select="messages" />
</xsl:template>

</xsl:stylesheet>