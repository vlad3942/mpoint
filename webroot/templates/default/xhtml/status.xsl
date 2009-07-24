<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes" media-type="application/xhtml+xml" doctype-public="-//WAPFORUM//DTD XHTML Mobile 1.0//EN" doctype-system="http://www.openmobilealliance.org/DTD/xhtml-mobile10.dtd" omit-xml-declaration="no" />

<xsl:template match="messages">
	<xsl:if test="count(item) &gt; 0">
		<div class="mPoint_Status">
			<xsl:choose>
			<xsl:when test="count(item) = 1">
				<xsl:value-of select="item" /> (<xsl:value-of select="item/@id" />)
			</xsl:when>
			<xsl:otherwise>
				<ul>
				<xsl:for-each select="item">
					<li><xsl:value-of select="." /> (<xsl:value-of select="@id" />)</li>
				</xsl:for-each>
				</ul>
			</xsl:otherwise>
			</xsl:choose>
		</div>
	</xsl:if>
</xsl:template>

<xsl:template match="/root">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=ISO-8859-15" />
		<meta http-equiv="Cache-Control" content="max-age=86400" />
		<meta http-equiv="Content-Style-Type" content="text/css" />	
		<title><xsl:value-of select="/root/title" /></title>
		<link href="{client-config/css-url}" type="text/css" rel="stylesheet" media="handheld" />
	</head>
	<body>
		<div id="logo">
			<img src="{client-config/logo-url}" alt="- {client-config/name} -" />
		</div>
		<!-- Display Status Messages -->
		<xsl:apply-templates select="messages" />
	</body>
	</html>
</xsl:template>

</xsl:stylesheet>