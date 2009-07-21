<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:include href="../web.xsl" />

<xsl:template match="/root/top-menu">
	<ul id="menu">
		<xsl:for-each select="link">
			<li>
				<xsl:choose>
				<xsl:when test="@id = 'login'">
					<a href="#" onclick="javascript:selectMenu(this, 'current'); obj_Client.changePage('{url}');" class="current"><xsl:value-of select="name" /></a>
				</xsl:when>
				<xsl:when test="@id = 'logout'">
					<a href="#" onclick="javascript:selectMenu(this, 'current'); obj_Client.changePage('{url}');" rel="nocache"><xsl:value-of select="name" /></a>
				</xsl:when>
				<xsl:otherwise>
					<a href="#" onclick="javascript:selectMenu(this, 'current'); obj_Client.changePage('{url}');"><xsl:value-of select="name" /></a>
				</xsl:otherwise>
				</xsl:choose>
			</li>
		</xsl:for-each>
	</ul>
	<ul id="info">
		<li>
			<xsl:value-of select="info/balance" />: <xsl:value-of select="account/funds" />
		</li>
	</ul>
</xsl:template>
</xsl:stylesheet>