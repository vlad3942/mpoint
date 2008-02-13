<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:include href="header.xsl"/>

<xsl:template match="/root">
	<div id="mPoint">
		<h1>
			<xsl:value-of select="labels/mpoint" /><br />
			<img src="{system/protocol}://{system/host}/img/mpoint" width="{mpoint-logo/width}" height="{mpoint-logo/height}" alt="- mPoint -" />
		</h1>
	</div>
	<div id="status">
		<h2>
			<img src="{system/protocol}://{system/host}/img/success.gif" width="30" height="28" alt="- OK - " />
			<xsl:value-of select="labels/status" />
		</h2>
	</div>
	
	<table id="receipt">
	<tr>
		<td class="mPoint_label"><xsl:value-of select="labels/txn-id" />:</td>
		<td><xsl:value-of select="transaction/@id" /></td>
	</tr>
	<tr>
		<td class="mPoint_label"><xsl:value-of select="labels/order-id" />:</td>
		<td><xsl:value-of select="transaction/order-id" /></td>
	</tr>
	<tr>
		<td class="mPoint_label"><xsl:value-of select="labels/price" />:</td>
		<td><xsl:value-of select="transaction/price" /></td>
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