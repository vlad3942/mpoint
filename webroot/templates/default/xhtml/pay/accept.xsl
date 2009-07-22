<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="xml" version="1.0" encoding="ISO-8859-15" indent="yes" media-type="text/html" doctype-public="-//WAPFORUM//DTD XHTML Mobile 1.0//EN" doctype-system="http://www.openmobilealliance.org/DTD/xhtml-mobile10.dtd" omit-xml-declaration="no" />
<xsl:include href="../mobile.xsl"/>

<xsl:template match="/root">
	<div id="mPoint">
		<h1>
			<xsl:value-of select="labels/mpoint" /><br />
			<img src="{system/protocol}://{system/host}/img/{mpoint-logo/width}x{mpoint-logo/height}_mpoint_{/root/system/session/@id}.png" width="{mpoint-logo/width}" height="{mpoint-logo/height}" alt="- mPoint -" />
		</h1>
	</div>
	<div id="status">
		<!--
		  - The colspan attribute in the table below ensures that the page is rendered correctly on the Nokia 6230.
		  - Nokia 6230 assigns the same width to all table columns but by using the colspan attribute (eventhough it really isn't needed)
		  - the phone will assign 33% of the screen width to the "OK Image" and 66% of the screen width to the "OK Text"
		  -->
		<table>
		<tr>
			<td><img src="{system/protocol}://{system/host}/img/success.gif" width="30" height="28" alt="- OK - " /></td>
			<td colspan="2" class="mPoint_Info"><xsl:value-of select="labels/status" /></td>
		</tr>
		</table>
	</div>
	
	<table id="receipt" style="width:100%">
	<tr>
		<td class="mPoint_Label" style="white-space:nowrap;"><xsl:value-of select="labels/txnid" />:</td>
		<td class="mPoint_Number"><xsl:value-of select="transaction/@id" /></td>
		<td style="width:100%" rowspan="3"></td>
	</tr>
	<tr>
		<td class="mPoint_Label" style="white-space:nowrap;"><xsl:value-of select="labels/orderid" />:</td>
		<td class="mPoint_Number"><xsl:value-of select="transaction/orderid" /></td>
	</tr>
	<tr>
		<td class="mPoint_Label" style="white-space:nowrap;"><xsl:value-of select="labels/price" />:</td>
		<td class="mPoint_Number"><xsl:value-of select="transaction/price" /></td>
	</tr>
	</table>
	
	<!-- SMS Receipt Enabled -->
	<xsl:if test="client-config/sms-receipt = 'true'">
		<div class="mPoint_Info">
			<xsl:value-of select="labels/sms-receipt" />
		</div>
	</xsl:if>
	
	<!-- Display Status Messages -->
	<xsl:apply-templates select="messages" />
	
	<!-- E-Mail Receipt Enabled -->
	<xsl:if test="client-config/email-receipt = 'true'">
		<div>
			<a href="{func:constLink('email.php')}"><xsl:value-of select="labels/email-receipt" /></a>
		</div>
	</xsl:if>
	
	<xsl:choose>
	<!-- Current transaction is an Account Top-Up and a previous transaction is in progress -->
	<xsl:when test="original-transaction-id &gt; 0">
		<div>
			<form action="{func:constLink('/cpm/payment.php?msg=1')}" method="post">
				<p>
					<input type="hidden" name="resume" value="true" />
				</p>
				
				<p>
					<input type="submit" value="{labels/resume}" class="mPoint_Button" />
				</p>
			</form>
		</div>
	</xsl:when>
	<!-- Client has specified a return URL for successful payments -->
	<xsl:when test="string-length(transaction/accept-url) &gt; 0">
		<div>
			<form action="{func:constLink(transaction/accept-url)}" method="post">
				<p>
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
				</p>
				
				<p>
					<input type="submit" value="{labels/continue}" class="mPoint_Button" />
				</p>
			</form>
		</div>
	</xsl:when>
	</xsl:choose>
</xsl:template>

</xsl:stylesheet>