<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes" media-type="text/html" doctype-public="-//WAPFORUM//DTD XHTML Mobile 1.0//EN" doctype-system="http://www.openmobilealliance.org/DTD/xhtml-mobile10.dtd" omit-xml-declaration="no" />
<xsl:include href="../mobile.xsl"/>

<xsl:template match="/root">
	<h1>
		<xsl:value-of select="labels/status" />
	</h1>
	<table id="receipt" style="width:100%">
	<tr>
		<td class="mPoint_Label"><xsl:value-of select="labels/txnid" />:</td>
		<td class="mPoint_Number"><xsl:value-of select="transaction/@id" /></td>
		<td style="width:40%" rowspan="3"></td>
	</tr>
	<!-- Order Number Provided -->
	<xsl:if test="string-length(transaction/orderid) &gt; 0">
		<tr>
			<td class="mPoint_Label"><xsl:value-of select="labels/orderid" />:</td>
			<td class="mPoint_Number"><xsl:value-of select="transaction/orderid" /></td>
		</tr>
	</xsl:if>
	<tr>
		<td class="mPoint_Label"><xsl:value-of select="labels/price" />:</td>
		<td class="mPoint_Number"><xsl:value-of select="transaction/price" /></td>
	</tr>
	</table>
	<div class="mPoint_Status">
		<xsl:value-of select="labels/note" />
	</div>
	
	<div id="info">
		<!-- Client has specified a return URL for successful payments -->
		<xsl:if test="string-length(transaction/accept-url) &gt; 0">
			<div>
				<form action="{func:constLink(transaction/accept-url)}" method="post">
					<div>
						<!-- Standard mPoint Variables -->
						<input type="hidden" name="mpoint-id" value="{transaction/@id}" />
						<input type="hidden" name="orderid" value="{transaction/orderid}" />
						<input type="hidden" name="status" value="2000" />
						<input type="hidden" name="amount" value="{transaction/amount}" />
						<input type="hidden" name="currency" value="{transaction/amount/@currency}" />
						<input type="hidden" name="mobile" value="{transaction/mobile}" />
						<input type="hidden" name="operator" value="{transaction/operator}" />
						<!-- Custom Client Variables -->
						<xsl:for-each select="client-vars/item">
							<input type="hidden" name="{name}" value="{value}" />
						</xsl:for-each>
					</div>
					
					<div id="submit">
						<input type="submit" value="{labels/continue}" class="mPoint_Button" />
					</div>
				</form>
			</div>
		</xsl:if>
	</div>
</xsl:template>

</xsl:stylesheet>