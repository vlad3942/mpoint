<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="html" indent="no" media-type="text/html" doctype-public="HTML" omit-xml-declaration="yes" standalone="yes" />
<xsl:include href="../mobile.xsl" />

<xsl:template match="/root">
	<div id="wrapper">
		<div id="content">
			<div id="accept">
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
				
				<table id="receipt" cellpadding="0" cellspacing="0" class="grouped">
				<tr class="first-row">
					<th class="left-column mPoint_Label"><xsl:value-of select="labels/txnid" />:</th>
					<td class="mPoint_Number"><xsl:value-of select="transaction/@id" /></td>
					<td class="right-column stretch"></td>
				</tr>
				<!-- Order Number Provided -->
				<xsl:if test="string-length(transaction/orderid) &gt; 0">
					<tr class="row">
						<th class="left-column mPoint_Label"><xsl:value-of select="labels/orderid" />:</th>
						<td class="mPoint_Number"><xsl:value-of select="transaction/orderid" /></td>
						<td class="right-column stretch"></td>
					</tr>
				</xsl:if>
				<tr class="last-row">
					<th class="left-column mPoint_Label"><xsl:value-of select="labels/price" />:</th>
					<td class="mPoint_Number"><xsl:value-of select="transaction/price" /></td>
					<td class="right-column stretch"></td>
				</tr>
				</table>
				
				<div id="info">
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
							<a href="{func:constLink('email.php') };"><xsl:value-of select="labels/email-receipt" /></a>
						</div>
					</xsl:if>
					
					<xsl:choose>
					<!-- Current transaction is an Account Top-Up and a previous transaction is in progress -->
					<xsl:when test="original-transaction-id &gt; 0">
						<div>
							<form id="resume" action="{func:constLink('/cpm/payment.php?msg=1')}" method="post">
								<p>
									<input type="hidden" name="resume" value="true" />
								</p>
								
								<p>
									<a class="submit-button" onclick="javascript:document.getElementById('loader').style.visibility='visible'; this.className+=' clicked'; this.disabled=true; document.getElementById('resume').submit();">
										<h2><xsl:value-of select="labels/resume" /></h2>
									</a>
								</p>
							</form>
						</div>
					</xsl:when>
					<!-- Client has specified a return URL for successful payments -->
					<xsl:when test="string-length(transaction/accept-url) &gt; 0">
						<div>
							<form id="continue" action="{func:constLink(transaction/accept-url)}" method="post">
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
									<a class="submit-button" onclick="javascript:document.getElementById('loader').style.visibility='visible'; this.className='submit-button-clicked'; this.disabled=true; document.getElementById('continue').submit();">
										<h2><xsl:value-of select="labels/continue" /></h2>
									</a>
								</div>
							</form>
						</div>
					</xsl:when>
					</xsl:choose>
				</div>
			</div>
			<script type="text/javascript">
				parent.postMessage('mpoint-payment-completed,<xsl:value-of select="transaction/@id" />', '*');
			</script>
		</div>
	</div>
</xsl:template>

</xsl:stylesheet>