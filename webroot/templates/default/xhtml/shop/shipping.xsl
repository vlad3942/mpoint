<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="xml" version="1.0" encoding="ISO-8859-15" indent="yes" media-type="application/xhtml+xml" doctype-public="-//WAPFORUM//DTD XHTML Mobile 1.0//EN" doctype-system="http://www.openmobilealliance.org/DTD/xhtml-mobile10.dtd" omit-xml-declaration="no" />
<xsl:include href="../header.xsl" />

<xsl:template match="/root">
	<div class="mPoint_Info"><xsl:value-of select="labels/info" /></div>
	
	<div id="shipping">
		<!-- List Shipping Companies -->
		<xsl:for-each select="shipping/company">
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
			
			<div>
				<!--
				  - The colspan attribute in the table below ensures that the page is rendered correctly on the Nokia 6230.
				  - Nokia 6230 assigns the same width to all table columns but by using the colspan attribute (eventhough it really isn't needed)
				  - the phone will assign 25% of the screen width to the card logo and 75% of the screen width to the card name.
				  -->
				<table cellpadding="0" cellspacing="0" style="width:100%;">
				<tr class="{$css}">
					<td style="width:40px; vertical-align:top;"><img src="{logo-url}" width="40" height="40" alt="" style="border-style:none; text-decoration:none;" /></td>
					<td colspan="3" style="text-align:left;">
						<a href="{func:constLink(concat('sys/checkout.php?id=', @id, '&amp;cost=', cost) )}">
							<div class="mPoint_Label"><xsl:value-of select="name" /></div>
							<div><xsl:value-of select="price" /></div>
						</a>
					</td>
				</tr>
				</table>
			</div>
		</xsl:for-each>
	</div>
</xsl:template>
</xsl:stylesheet>