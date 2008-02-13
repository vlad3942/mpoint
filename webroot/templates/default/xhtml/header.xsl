<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="xml" version="1.0" encoding="ISO-8859-15" indent="yes" media-type="application/xhtml+xml" doctype-public="-//W3C//DTD XHTML Basic 1.0//EN" doctype-system="http://www.w3.org/TR/2000/REC-xhtml-basic-20001219/xhtml-basic10.dtd" omit-xml-declaration="no" />
	
<xsl:template match="/">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{/root/transaction/language}">
	<head>
		<title><xsl:value-of select="/root/title" /></title>
		<meta http-equiv="content-style-type" content="text/css" />
		<link href="{/root/transaction/css-url}" type="text/css" rel="stylesheet" />
		<meta http-equiv="content-type" content="application/xhtml+xml; charset=ISO-8859-15"/>
	</head>
	<body>
		<div id="logo">
			<img src="{/root/system/protocol}://{/root/system/host}/img/client_{/root/client-config/@id}" width="{/root/transaction/logo/width}" height="{/root/transaction/logo/height}" alt="- {/root/client-config/name} -" border="0" />
		</div>
		<xsl:apply-templates />
	</body>
	</html>
</xsl:template>

<xsl:template match="messages">
	<div class="status">
		<br />
		<xsl:choose>
		<xsl:when test="count(item) = 1">
			<xsl:value-of select="." />
		</xsl:when>
		<xsl:otherwise>
			<ul>
			<xsl:for-each select="item">
				<li><xsl:value-of select="." /></li>
			</xsl:for-each>
			</ul>
		</xsl:otherwise>
		</xsl:choose>
	</div>
</xsl:template>

<func:function name="func:constLink">
	<xsl:param name="href" />
	
	<xsl:variable name="protocol" select="/root/system/protocol" />
	<xsl:variable name="host" select="/root/system/host" />
	<xsl:variable name="dir" select="/root/system/dir" />
	<xsl:variable name="file" select="/root/system/file" />
	
	<xsl:choose>
		<!-- Absolute URL -->
		<xsl:when test="substring($href, 1, 4) = 'http'">
			<xsl:variable name="link" select="$href" />
			
			<func:result>
				<xsl:value-of select="$link" />
			</func:result>
		</xsl:when>
		<!-- Absolute Path -->
		<xsl:when test="substring($href, 1, 1) = '/'">
			<xsl:variable name="link" select="concat($protocol, '://', $host, $href)" />
			
			<func:result>
				<xsl:value-of select="func:appendQueryString($link)" />
			</func:result>
		</xsl:when>
		<!-- Same page with new parameters -->
		<xsl:when test="substring($href, 1, 1) = '?'">
			<xsl:variable name="link" select="concat($protocol, '://', $host, $dir, $file, $href)" />
			
			<func:result>
				<xsl:value-of select="func:appendQueryString($link)" />
			</func:result>
		</xsl:when>
		<!-- Anchor -->
		<xsl:when test="substring($href, 1, 1) = '#'">
			<xsl:variable name="link" select="concat($protocol, '://', $host, $dir, $file, $href)" />
			
			<func:result>
				<xsl:value-of select="func:appendQueryString($link)" />
			</func:result>
		</xsl:when>
		<!-- Relative Path -->
		<xsl:otherwise>
			<xsl:variable name="link" select="concat($protocol, '://', $host, $dir, $href)" />
			
			<func:result>
				<xsl:value-of select="func:appendQueryString($link)" />
			</func:result>
		</xsl:otherwise>
	</xsl:choose>
</func:function>

<func:function name="func:appendQueryString">
	<xsl:param name="url" />
	
	<xsl:variable name="session" select="concat(/root/system/session, '=', /root/system/session/@id)" />
	
	<xsl:choose>
		<!-- URL with Query String -->
		<xsl:when test="contains($url, '?') = 'true'">
			<xsl:variable name="link" select="substring-before($url, '?')" />
			<xsl:variable name="vars" select="substring-after($url, '?')" />
			
			<func:result>
				<xsl:value-of select="concat($link, '?', $session, '&amp;', $vars)" />
			</func:result>
		</xsl:when>
		<!-- URL with Anchor -->
		<xsl:when test="contains($url, '#') = 'true'">
			<xsl:variable name="link" select="substring-before($url, '#')" />
			<xsl:variable name="anchor" select="substring-after($url, '#')" />
			
			<func:result>
				<xsl:value-of select="concat($link, '?', $session, '#', $anchor)" />
			</func:result>
		</xsl:when>
		<!-- Normal URL -->
		<xsl:otherwise>
			<func:result>
				<xsl:value-of select="concat($url, '?', $session)" />
			</func:result>
		</xsl:otherwise>
	</xsl:choose>
</func:function>

</xsl:stylesheet>
	