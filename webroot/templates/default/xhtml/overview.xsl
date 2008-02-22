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
		<td colspan="3" class="mPoint_Label" style="text-align:center"><xsl:value-of select="labels/product" /></td>
		<td class="mPoint_Label" style="text-align:center"><xsl:value-of select="labels/quantity" /></td>
		<td colspan="2" class="mPoint_Label" style="text-align:center"><xsl:value-of select="labels/price" /></td>
	</tr>
	<!-- List Products -->
	<xsl:for-each select="products/item">
	<tr>
		<td colspan="6"><img src="{logo-url}" width="40" height="40" alt="- Logo -" /></td>
	</tr>
	<tr>
		<td colspan="3"><xsl:value-of select="name" /></td>
		<td class="mPoint_Number"><xsl:value-of select="quantity" /></td>
		<td colspan="2" class="mPoint_Number"><xsl:value-of select="price" /></td>
	</tr>
	</xsl:for-each>
	<!-- List Shipping Information -->
	<xsl:if test="count(shipping-info) &gt; 0">
		<tr>
			<td colspan="6"><img src="{system/protocol}://{system/host}/img/shipping.gif" width="40" height="40" alt="- Logo -" /></td>
		</tr>
		<tr>
			<td colspan="4"><xsl:value-of select="shipping-info/name" /></td>
			<td colspan="2" class="mPoint_Number"><xsl:value-of select="shipping-info/price" /></td>
		</tr>
	</xsl:if>
	<!-- List Total -->
	<tr>
		<td colspan="3" class="mPoint_Label"><xsl:value-of select="labels/total" /></td>
		<td colspan="3" valign="bottom" class="mPoint_Label mPoint_Number"><xsl:value-of select="transaction/price" /></td>
	</tr>
	</table>
	<!-- List Delivery Information -->
	<xsl:if test="count(delivery-info) &gt; 0">
		<div class="mPoint_Label"><xsl:value-of select="labels/delivery-info/name" /></div>
		<div><xsl:value-of select="delivery-info/name" /></div>
		
		<div class="mPoint_Label"><xsl:value-of select="labels/delivery-info/company" /></div>
		<div><xsl:value-of select="delivery-info/company" /></div>
		<div class="mPoint_Label"><xsl:value-of select="labels/delivery-info/street" /></div>
		<div><xsl:value-of select="delivery-info/street" /></div>
		
		<div class="mPoint_Label"><xsl:value-of select="labels/delivery-info/zipcode" /> &amp; <xsl:value-of select="labels/delivery-info/city" /></div>
		<div><xsl:value-of select="delivery-info/zipcode" /> <xsl:value-of select="delivery-info/city" /></div>
		
		<!-- Include Delivery Date -->
		<xsl:if test="string-length(delivery-info/delivery-date) &gt; 0">
			<div class="mPoint_Label"><xsl:value-of select="labels/delivery-info/delivery-date" /></div>
			<div><xsl:value-of select="delivery-info/delivery-date" /></div>
		</xsl:if>
	</xsl:if>
	
	<div>
		<xsl:variable name="link-part" select="substring-after(labels/terms, '{LINK}')" />
		<xsl:value-of select="substring-before(labels/terms, '{LINK}')" />
		<a href="{func:constLink('terms.php')}"><xsl:value-of select="substring-before($link-part, '{/LINK}')" /></a>
		<xsl:value-of select="substring-after($link-part, '{/LINK}')" />
	</div>
	
	<div><a href="{func:constLink('/pay/card.php')}"><xsl:value-of select="labels/payment" /></a></div>
</xsl:template>
</xsl:stylesheet>