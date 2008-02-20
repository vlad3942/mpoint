<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="xml" version="1.0" encoding="ISO-8859-15" indent="yes" media-type="application/xhtml+xml" doctype-public="-//WAPFORUM//DTD XHTML Mobile 1.0//EN" doctype-system="http://www.openmobilealliance.org/DTD/xhtml-mobile10.dtd" omit-xml-declaration="no" />
<xsl:include href="header.xsl" />

<xsl:template match="/root">
	<div class="mPoint_Info"><xsl:value-of select="labels/info" /></div>
	<!--
	  - The colspan attribute in the table below ensures that the page is rendered correctly on the Nokia 6230.
	  - Nokia 6230 assigns the same width to all table columns but by using the colspan attribute (eventhough it really isn't needed)
	  - the phone will assign 50% of the screen width to the product name, 17% of the screen width to the product quantity and
	  - 33% of the screen width to the product price.
	  -->
	<table id="products">
	<tr>
		<td colspan="3" class="mPoint_Label" style="text-align:center"><xsl:value-of select="labels/name" /></td>
		<td class="mPoint_Label" style="text-align:center"><xsl:value-of select="labels/quantity" /></td>
		<td colspan="2" class="mPoint_Label" style="text-align:center"><xsl:value-of select="labels/price" /></td>
	</tr>
	<!-- List Products -->
	<xsl:for-each select="products/item">
	<tr>
		<td colspan="6"><img src="{logo-url}" width="30" height="30" alt="- Logo -" /></td>
	</tr>
	<tr>
		<td colspan="3"><xsl:value-of select="name" /></td>
		<td class="mPoint_Number"><xsl:value-of select="quantity" /></td>
		<td colspan="2" class="mPoint_Number"><xsl:value-of select="price" /></td>
	</tr>
	</xsl:for-each>
	<!-- List Total -->
	<tr>
		<td colspan="3" class="mPoint_Label"><xsl:value-of select="labels/total" /></td>
		<td colspan="3" valign="bottom" class="mPoint_Label mPoint_Number"><xsl:value-of select="transaction/price" /></td>
	</tr>
	</table>
	
	<div><a href="{func:constLink('card.php')}"><xsl:value-of select="labels/payment" /></a></div>
</xsl:template>
</xsl:stylesheet>