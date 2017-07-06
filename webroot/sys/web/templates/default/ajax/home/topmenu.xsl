<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:include href="../web.xsl" />

<xsl:template match="/root/top-menu">
	<ul id="menu">
		<xsl:for-each select="link">
			<li>
				<xsl:choose>
				<xsl:when test="@id = 'login'">
					<a href="#" id="topmenu-{@id}" onclick="javascript:selectMenu(this, 'current'); obj_Client.clear(); obj_Client.changePage('{url}');" class="current"><xsl:value-of select="name" /></a>
				</xsl:when>
				<xsl:when test="@id = 'logout'">
					<a href="#" id="topmenu-{@id}" onclick="javascript:selectMenu(this, 'current'); obj_Client.clear(); obj_Client.changePage('{url}');" rel="nocache"><xsl:value-of select="name" /></a>
				</xsl:when>
				<xsl:otherwise>
					<a href="#" id="topmenu-{@id}" onclick="javascript:selectMenu(this, 'current'); obj_Client.clear(); obj_Client.changePage('{url}');"><xsl:value-of select="name" /></a>
				</xsl:otherwise>
				</xsl:choose>
			</li>
		</xsl:for-each>
	</ul>
	<ul id="info">
		<li>
			<xsl:variable name="points">
			<xsl:choose>
				<xsl:when test="account/points &lt; 10000"><xsl:value-of select="account/points" /></xsl:when>
				<xsl:otherwise><xsl:value-of select="concat(substring(account/points, 1, string-length(account/points) - 3), '.', substring(account/points, string-length(account/points) - 2) )" /></xsl:otherwise>
			</xsl:choose>
			</xsl:variable>
			<xsl:value-of select="concat(info/balance, ' ', $points, ' ', account/points/@currency, ' / ', account/funds)" />
		</li>
	</ul>
</xsl:template>
</xsl:stylesheet>