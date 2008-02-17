<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="xml" version="1.0" encoding="ISO-8859-15" indent="yes" media-type="application/xhtml+xml" doctype-public="-//WAPFORUM//DTD XHTML Mobile 1.0//EN" doctype-system="http://www.openmobilealliance.org/DTD/xhtml-mobile10.dtd" omit-xml-declaration="no" />
<xsl:include href="header.xsl"/>

<xsl:template match="/root">
	<div id="mPoint">
		<h1>
			<xsl:value-of select="labels/mpoint" /><br />
			<img src="{system/protocol}://{system/host}/img/mpoint_{/root/system/session/@id}.jpg" width="{mpoint-logo/width}" height="{mpoint-logo/height}" alt="- mPoint -" />
		</h1>
	</div>
	<div id="status">
		<table>
		<tr>
			<td><img src="{system/protocol}://{system/host}/img/success.gif" width="30" height="28" alt="- OK - " /></td>
			<td class="mPoint_info"><xsl:value-of select="labels/status" /></td>
		</tr>
		</table>
	</div>
	
	<table id="receipt">
	<tr>
		<td class="mPoint_label"><xsl:value-of select="labels/txn-id" />:</td>
		<td class="mPoint_number"><xsl:value-of select="transaction/@id" /></td>
	</tr>
	<tr>
		<td class="mPoint_label"><xsl:value-of select="labels/order-id" />:</td>
		<td class="mPoint_number"><xsl:value-of select="transaction/order-id" /></td>
	</tr>
	<tr>
		<td class="mPoint_label"><xsl:value-of select="labels/price" />:</td>
		<td class="mPoint_number"><xsl:value-of select="transaction/price" /></td>
	</tr>
	</table>
	
	<!-- Display Status Messages -->
	<xsl:apply-templates select="messages" />
	
	<!-- SMS Receipt Enabled -->
	<xsl:if test="client-config/sms-receipt = 'true'">
		<div class="mPoint_info">
			<xsl:value-of select="labels/sms-receipt" />
		</div>
	</xsl:if>
	<!-- Allow Access to E-Mail Receipt -->
	<xsl:if test="client-config/email-receipt = 'true'">
		<div>
			<br />
			<a href="{func:constLink('email.php')}"><xsl:value-of select="labels/email-receipt" /></a>
		</div>
	</xsl:if>
	<!-- Client has specified a return URL for successful payments -->
	<xsl:if test="string-length(transaction/accept-url) &gt; 0">
		<div>
			<form action="{func:constLink(transaction/accept-url)}" method="post">
				<p>
				<xsl:for-each select="client-vars/item">
					<input type="hidden" name="{name}" value="{value}" />
				</xsl:for-each>
				</p>
				
				<p>
					<input type="submit" value="{labels/continue}" />
				</p>
			</form>
		</div>
	</xsl:if>
</xsl:template>

</xsl:stylesheet>