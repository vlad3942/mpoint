<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="html" indent="no" media-type="text/html" doctype-public="HTML" omit-xml-declaration="yes" standalone="yes" />
	
<xsl:template match="messages">
	<xsl:if test="count(item) &gt; 0">
		<div class="mPoint_Status">
			<xsl:choose>
			<xsl:when test="count(item) = 1">
				<xsl:value-of select="item" />
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
	</xsl:if>
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

<func:function name="func:transLanguage">
	<xsl:param name="lang" />
	
	<!-- Perform Language conversion -->
	<xsl:choose>
		<!-- British English -->
		<xsl:when test="$lang = 'gb'">
			<func:result>en</func:result>
		</xsl:when>
		<!-- American English -->
		<xsl:when test="$lang = 'us'">
			<func:result>en</func:result>
		</xsl:when>
		<!-- Danish -->
		<xsl:when test="$lang = 'da'">
			<func:result>da</func:result>
		</xsl:when>
		<!-- Norwegian -->
		<xsl:when test="$lang = 'no'">
			<func:result>no</func:result>
		</xsl:when>
		<!-- Swedish -->
		<xsl:when test="$lang = 'sv'">
			<func:result>sv</func:result>
		</xsl:when>
		<!-- German -->
		<xsl:when test="$lang = 'de'">
			 <func:result>de</func:result>
		</xsl:when>
		<!-- Spanish -->
		<xsl:when test="$lang = 'es'">
			<func:result>es</func:result>
		</xsl:when>
		<!-- Finish -->
		<xsl:when test="$lang = 'fi'">
			<func:result>fi</func:result>
		</xsl:when>
		<!-- French -->
		<xsl:when test="$lang = 'fr'">
			<func:result>fr</func:result>
		</xsl:when>
		<!-- Error -->
		<xsl:otherwise>
			
		</xsl:otherwise>
	</xsl:choose>
</func:function>

</xsl:stylesheet>