<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="xml" version="1.0" encoding="ISO-8859-15" indent="yes" media-type="application/xhtml+xml" doctype-public="-//WAPFORUM//DTD XHTML Mobile 1.0//EN" doctype-system="http://www.openmobilealliance.org/DTD/xhtml-mobile10.dtd" omit-xml-declaration="no" />
<xsl:include href="../header.xsl" />

<xsl:template match="/root">
	<div class="mPoint_Info"><xsl:value-of select="labels/info" /></div>
	
	<!--
	  - The colspan attribute in the table below ensures that the page is rendered correctly on the Nokia 6230.
	  - Nokia 6230 assigns the same width to all table columns but by using the colspan attribute (eventhough it really isn't needed)
	  - the phone will assign 66% of the screen width to the shipping company and 34% of the screen width to the shipping price.
	  -->
	<table id="products">
	<tr>
		<td colspan="2" class="mPoint_Label" style="text-align:center"><xsl:value-of select="labels/company" /></td>
		<td class="mPoint_Label" style="text-align:center"><xsl:value-of select="labels/price" /></td>
	</tr>
	<tr>
		<td colspan="3"><img src="{system/protocol}://{system/host}/img/shipping.gif" width="40" height="40" alt="- Logo -" /></td>
	</tr>
	<tr>
		<td colspan="2"><xsl:value-of select="shop-config/shipping/company" /></td>
		<td class="mPoint_Number"><xsl:value-of select="shipping-cost" /></td>
	</tr>
	</table>
	
	<div><a href="{func:constLink('sys/checkout.php')}"><xsl:value-of select="labels/next" /></a></div>
</xsl:template>
</xsl:stylesheet>