<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:include href="header.xsl"/>

<xsl:template match="/root">
	<table border="0" cellpadding="0" cellspacing="0" id="products">
	<tr>
		<td class="mPoint_label"><xsl:value-of select="labels/name" /></td>
		<td class="mPoint_label"><xsl:value-of select="labels/quantity" /></td>
		<td class="mPoint_label"><xsl:value-of select="labels/price" /></td>
	</tr>
	<xsl:for-each select="products/item">
		<tr>
			<td>
				<img src="{logo-url}" width="30" height="30" alt="- Logo -" border="0" /><br />
				<xsl:value-of select="name" />
			</td>
			<td valign="bottom" class="number"><xsl:value-of select="quantity" /></td>
			<td valign="bottom" class="number"><xsl:value-of select="price" /></td>
		</tr>
	</xsl:for-each>
	<tr>
		<td colspan="2" class="label"><xsl:value-of select="labels/total" /></td>
		<td valign="bottom" class="number"><xsl:value-of select="transaction/price" /></td>
	</tr>
	</table>
	
	<div><a href="{func:constLink('card.php')}"><xsl:value-of select="labels/payment" /></a></div>
</xsl:template>
</xsl:stylesheet>