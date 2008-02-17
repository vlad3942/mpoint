<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="xml" version="1.0" encoding="ISO-8859-15" indent="yes" media-type="application/xhtml+xml" doctype-public="-//WAPFORUM//DTD XHTML Mobile 1.0//EN" doctype-system="http://www.openmobilealliance.org/DTD/xhtml-mobile10.dtd" omit-xml-declaration="no" />
<xsl:include href="header.xsl" />

<xsl:template match="/root">
	<table id="products">
	<tr>
		<td colspan="3" class="mPoint_label" style="text-align:center"><xsl:value-of select="labels/name" /></td>
		<td class="mPoint_label" style="text-align:center"><xsl:value-of select="labels/quantity" /></td>
		<td colspan="2" class="mPoint_label" style="text-align:center"><xsl:value-of select="labels/price" /></td>
	</tr>
	<!-- List Products -->
	<xsl:for-each select="products/item">
	<tr>
		<td colspan="5"><img src="{logo-url}" width="30" height="30" alt="- Logo -" /></td>
	</tr>
	<tr>
		<td colspan="3"><xsl:value-of select="name" /></td>
		<td class="mPoint_number"><xsl:value-of select="quantity" /></td>
		<td colspan="2" class="mPoint_number"><xsl:value-of select="price" /></td>
	</tr>
	</xsl:for-each>
	<!-- List Total -->
	<tr>
		<td colspan="3" class="mPoint_label"><xsl:value-of select="labels/total" /></td>
		<td colspan="3" valign="bottom" class="mPoint_label mPoint_number"><xsl:value-of select="transaction/price" /></td>
	</tr>
	</table>
	
	<div><a href="{func:constLink('card.php')}"><xsl:value-of select="labels/payment" /></a></div>
</xsl:template>
</xsl:stylesheet>