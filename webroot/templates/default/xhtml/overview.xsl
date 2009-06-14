<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="xml" version="1.0" encoding="ISO-8859-15" indent="yes" media-type="application/xhtml+xml" doctype-public="-//WAPFORUM//DTD XHTML Mobile 1.0//EN" doctype-system="http://www.openmobilealliance.org/DTD/xhtml-mobile10.dtd" omit-xml-declaration="no" />
<xsl:include href="mobile.xsl" />

<xsl:template match="/root">
	<div class="mPoint_Info">
		<br />
		<!-- Display Status Messages -->
		<xsl:apply-templates select="messages" />
		<xsl:value-of select="labels/info" />
	</div>
	<!--
	  - The colspan attribute in the table below ensures that the page is rendered correctly on the Nokia 6230.
	  - Nokia 6230 assigns the same width to all table columns but by using the colspan attribute (eventhough it really isn't needed)
	  - the phone will assign 66% of the screen width to the product name, 17% of the screen width to the product quantity and
	  - 17% of the screen width to the product price.
	  -->
	<table id="products" style="width:100%;" cellpadding="0" cellspacing="0">
	<tr>
		<td class="mPoint_Label" style="text-align:right"><xsl:value-of select="labels/quantity" /></td>
		<td colspan="4" class="mPoint_Label" style="text-align:center"><xsl:value-of select="labels/product" /></td>
		<td class="mPoint_Label" style="text-align:center"><xsl:value-of select="labels/price" /></td>
	</tr>
	<!-- List Products -->
	<xsl:for-each select="products/item">
		<xsl:variable name="css">
			<xsl:choose>
				<!-- Even row -->
				<xsl:when test="position() mod 2 = 0">
					<xsl:text>mPoint_Even</xsl:text>
				</xsl:when>
				<!-- Uneven row -->
				<xsl:otherwise>
					<xsl:text>mPoint_Uneven</xsl:text>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		
		<tr class="{$css}">
			<td> </td>
			<td colspan="5"><img src="{logo-url}" width="40" height="40" alt="- Logo -" /></td>
		</tr>
		<tr class="{$css}">
			<td class="mPoint_Number" style="vertical-align:top;"><xsl:value-of select="quantity" /></td>
			<td colspan="4" style="width:100%"><xsl:value-of select="name" /></td>
			<td class="mPoint_Number"><xsl:value-of select="price" /></td>
		</tr>
	</xsl:for-each>
	<!-- List Shipping Information -->
	<xsl:if test="string-length(shipping-info/name) &gt; 0">
		<xsl:variable name="css">
			<xsl:choose>
				<!-- Product listing ended on even numbering -->
				<xsl:when test="count(products/item) mod 2 = 0">
					<xsl:text>mPoint_Uneven</xsl:text>
				</xsl:when>
				<!-- Product listing ended on uneven numbering -->
				<xsl:otherwise>
					<xsl:text>mPoint_Even</xsl:text>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		
		<tr class="{$css}">
			<td rowspan="2"> </td>
			<td colspan="5"><img src="{shipping-info/logo-url}" width="40" height="40" alt="" /></td>
		</tr>
		<tr class="{$css}">
			<td colspan="4"><xsl:value-of select="shipping-info/name" /></td>
			<td colspan="1" class="mPoint_Number"><xsl:value-of select="shipping-info/price" /></td>
		</tr>
	</xsl:if>
	<!-- List Total -->
	<xsl:variable name="css">
		<xsl:choose>
			<!-- Product listing ended on even numbering -->
			<xsl:when test="count(products/item) mod 2 = 0">
				<xsl:text>mPoint_Even</xsl:text>
			</xsl:when>
			<!-- Product listing ended on uneven numbering -->
			<xsl:otherwise>
				<xsl:text>mPoint_Uneven</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:variable>
	<tr class="{$css}">
		<td colspan="4" class="mPoint_Label"><xsl:value-of select="labels/total" /></td>
		<td colspan="2" valign="bottom" class="mPoint_Label mPoint_Number"><xsl:value-of select="transaction/price" /></td>
	</tr>
	</table>
	<!-- List Delivery Information -->
	<xsl:if test="string-length(delivery-info/name) &gt; 0">
		<div><hr /></div>
		<div class="mPoint_Label"><xsl:value-of select="labels/delivery-info/name" /></div>
		<div><xsl:value-of select="delivery-info/name" /></div>
		<!-- List Company / CO -->
		<xsl:if test="string-length(delivery-info/company) &gt; 0">
			<div class="mPoint_Label"><xsl:value-of select="labels/delivery-info/company" /></div>
			<div><xsl:value-of select="delivery-info/company" /></div>
		</xsl:if>
		
		<div class="mPoint_Label"><xsl:value-of select="labels/delivery-info/street" /></div>
		<div><xsl:value-of select="delivery-info/street" /></div>
		
		<div class="mPoint_Label"><xsl:value-of select="labels/delivery-info/zipcode" /> &amp; <xsl:value-of select="labels/delivery-info/city" /></div>
		<div><xsl:value-of select="concat(delivery-info/zipcode, ' ', delivery-info/city)" /></div>
		
		<!-- Include Delivery Date -->
		<xsl:if test="string-length(delivery-info/delivery-date) &gt; 0">
			<div class="mPoint_Label"><xsl:value-of select="labels/delivery-info/delivery-date" /></div>
			<div><xsl:value-of select="delivery-info/delivery-date" /></div>
		</xsl:if>
	</xsl:if>
	
	<!-- Determine where to go next based on the Flow Type -->
	<xsl:choose>
		<!-- Electronic Product Flow -->
		<xsl:when test="/root/client-config/@flow-id = 1">
			<div style="padding-top:0.5em;"><hr /></div>
			<form name="terms" action="{func:constLink('sys/val_terms.php')}" method="post">
				<div>
					<input type="checkbox" name="terms" value="1" />
					<xsl:variable name="link-part" select="substring-after(labels/terms, '{LINK}')" />
					<xsl:value-of select="substring-before(labels/terms, '{LINK}')" />
					<a href="{func:constLink('terms.php')}"><xsl:value-of select="substring-before($link-part, '{/LINK}')" /></a>
					<xsl:value-of select="substring-after($link-part, '{/LINK}')" />
				</div>
				<div>
					<input type="submit" value="{labels/next}" class="mPoint_Button" />
				</div>
			</form>
		</xsl:when>
		<!-- Physical Product Flow -->
		<xsl:when test="/root/client-config/@flow-id = 2">
			<xsl:choose>
				<!-- Start of Physical Product Flow -->
				<xsl:when test="string-length(delivery-info/name) = 0 and string-length(shipping-info/name) = 0">
					<a href="{func:constLink('/shop/delivery.php')}"><xsl:value-of select="labels/next" /></a>
				</xsl:when>
				<!-- End of Physical Product Flow -->
				<xsl:otherwise>
					<div style="padding-top:0.5em;"><hr /></div>
					<form name="terms" action="{func:constLink('sys/val_terms.php')}" method="post">
						<div>
							<input type="checkbox" name="terms" value="1" />
							<xsl:variable name="link-part" select="substring-after(labels/terms, '{LINK}')" />
							<xsl:value-of select="substring-before(labels/terms, '{LINK}')" />
							<a href="{func:constLink('terms.php')}"><xsl:value-of select="substring-before($link-part, '{/LINK}')" /></a>
							<xsl:value-of select="substring-after($link-part, '{/LINK}')" />
						</div>
						<div>
							<input type="submit" value="{labels/next}" class="mPoint_Button" />
						</div>
					</form>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:when>
		<!-- Error: Unknown Flow -->
		<xsl:otherwise>
			ERROR - Unknown Flow: <xsl:value-of select="/root/client-config/@flow-id" />
		</xsl:otherwise>
	</xsl:choose>
	
</xsl:template>
</xsl:stylesheet>