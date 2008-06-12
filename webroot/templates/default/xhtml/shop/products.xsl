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
		<table id="products">
		<!-- List Products -->
		<xsl:for-each select="products/item">
			<xsl:variable name="id" select="@id" />
		
			<tr>
				<td><img src="{logo-url}" width="40" height="40" alt="- Logo -" /></td>
				<td colspan="2">
					<xsl:value-of select="name" /><br />
					<span class="mPoint_Number"><xsl:value-of select="price" /></span><br />
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
		<input type="submit" value="{labels/next}" />
	</div>
	</form>
</xsl:template>
</xsl:stylesheet>