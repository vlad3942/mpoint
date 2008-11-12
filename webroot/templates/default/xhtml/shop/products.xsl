<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="xml" version="1.0" encoding="ISO-8859-15" indent="yes" media-type="application/xhtml+xml" doctype-public="-//WAPFORUM//DTD XHTML Mobile 1.0//EN" doctype-system="http://www.openmobilealliance.org/DTD/xhtml-mobile10.dtd" omit-xml-declaration="no" />
<xsl:include href="../header.xsl" />

<xsl:template match="/root">
	<div class="mPoint_Info"><xsl:value-of select="labels/info" /></div>
	
	<!-- Display Status Messages -->
	<xsl:apply-templates select="messages" />
	
	<form action="{func:constLink('sys/purchase.php')}" method="post">
	<div>
		<xsl:for-each select="products/item">
			<xsl:variable name="id" select="@id" />
			
			<input type="hidden" name="products[{$id}]" value="{/root/session/products/item[@id = $id]}" />
		</xsl:for-each>
	</div>
	
	<div>
		<!--
		  - The colspan attribute in the table below ensures that the page is rendered correctly on the Nokia 6230.
		  - Nokia 6230 assigns the same width to all table columns but by using the colspan attribute (eventhough it really isn't needed)
		  - the phone will assign 60% of the screen width to the product name, 20% of the screen width to the product quantity and
		  - 20% of the screen width to the product price.
		  -->
		<table id="products" cellpadding="0" cellspacing="0">
		<!-- List Products -->
		<xsl:for-each select="products/item">
			<xsl:variable name="id" select="@id" />
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
				<td><img src="{logo-url}" width="40" height="40" alt="- Logo -" /></td>
				<td colspan="2">
					<div class="mPoint_Label"><xsl:value-of select="name" /></div>
					<div class="mPoint_Number"><xsl:value-of select="price" /></div>
					<a href="{func:constLink(concat('products.php?id=', $id) )}"><xsl:value-of select="//labels/add-to-basket" /></a>
					<xsl:if test="/root/session/products/item[@id = $id] &gt; 0">
						<span class="mPoint_Info"> (<xsl:value-of select="/root/session/products/item[@id = $id]" />)</span>
					</xsl:if>
				</td>
			</tr>
		</xsl:for-each>
		</table>
	</div>
	<div>
		<input type="submit" value="{labels/next}" class="mPoint_Button" />
	</div>
	</form>
</xsl:template>
</xsl:stylesheet>