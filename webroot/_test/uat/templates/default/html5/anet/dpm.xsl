<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="html" indent="no" media-type="text/html" doctype-public="HTML" omit-xml-declaration="yes" standalone="yes" />
<xsl:include href="../mobile.xsl" />

<xsl:template match="/root">
	<xsl:variable name="card-number">
		<xsl:if test="transaction/@mode &gt; 0">6011000000000012</xsl:if>
	</xsl:variable>
	<xsl:variable name="expiry-month">
		<xsl:if test="transaction/@mode &gt; 0">04</xsl:if>
	</xsl:variable>
	<xsl:variable name="expiry-year">
		<xsl:if test="transaction/@mode &gt; 0">17</xsl:if>
	</xsl:variable>
	<xsl:variable name="cvc">
		<xsl:if test="transaction/@mode &gt; 0">782</xsl:if>
	</xsl:variable>
	
	<xsl:variable name="card-url" select="concat(system/protocol, '://', system/host, '/img/', card/width, 'x', card/height, '_card_', card/@id, '_', system/session/@id, '.png')" />

	<div id="wrapper">
		<div id="content">
			<div id="progress" class="mPoint_Info">
				<xsl:value-of select="labels/progress" />
				<br /><br />
			</div>
			
			<!-- Display Status Messages -->
			<xsl:apply-templates select="messages" />
			<div id="outer-border">
				<div>	
					<form id="pay-card" action="{authorize-net/url}" method="post">
						<div>
							<!-- Authorize.Net Required Data -->
							<input type="hidden" id="x_exp_date" name="x_exp_date" value="" />
							<input type="hidden" name="x_amount" value="{transaction/amount div 100}" />
							<input type="hidden" name="x_fp_sequence" value="{transaction/@id}" />
							<input type="hidden" name="x_fp_hash" value="{authorize-net/checksum}" />
							<input type="hidden" name="x_fp_timestamp" value="{authorize-net/time}" />
							<input type="hidden" name="x_relay_response" value="TRUE" />
							<input type="hidden" name="x_relay_url" value="{/root/system/protocol}://{/root/system/host}/callback/anet.php" />
							<input type="hidden" name="x_login" value="{authorize-net/api-login}" />
							<input type="hidden" name="x_version" value="3.1" />
							<input type="hidden" name="x_delim_char" value="," />
							<input type="hidden" name="x_delim_data" value="TRUE" />
							<input type="hidden" name="x_invoice_num" value="{transaction/orderid}" />
							<xsl:choose>
							<xsl:when test="transaction/auto-capture = 'true'">
								<input type="hidden" name="x_type" value="AUTH_CAPTURE" />
							</xsl:when>
							<xsl:otherwise>
								<input type="hidden" name="x_type" value="AUTH_ONLY" />
							</xsl:otherwise>
							</xsl:choose>
							<!-- Client is in Test or Certification mode -->
							<xsl:if test="transaction/@mode &gt; 0">
								<input type="hidden" name="x_test_request" value="TRUE" />
							</xsl:if>
							<!-- mPoint Data -->
							<input type="hidden" name="cardid" value="{card/@id}" />
							<input type="hidden" name="{system/session}" value="{system/session/@id}" />
							<input type="hidden" name="mpoint-id" value="{transaction/@id}" />
							<input type="hidden" name="language" value="{system/language}" />
						</div>
						
						<div id="selected-card">
							<table cellpadding="0" cellspacing="0" class="grouped">
							<tr>
								<th class="mPoint_Help" colspan="3"><xsl:value-of select="labels/selected-card" /></th>
							</tr>
							<tr class="first-row last-row">
								<td class="left-column right-column stretch" colspan="3">
									<table cellpadding="0" cellspacing="0" class="grouped">
									<tr>
										<!-- Selected Card -->
										<td><img src="{$card-url}" width="{card/width}" height="{card/height}" alt="- {card/name} -" /></td>
										<td class="mPoint_Card"><xsl:value-of select="concat(' ', card/name)" /></td>
										<!-- Price -->
										<td id="price">
											<div class="mPoint_Label"><xsl:value-of select="labels/price" /></div>
											<xsl:value-of select="transaction/price" />
										</td>
									</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td colspan="3"><br /></td>
							</tr>
							<tr>
								<td class="mPoint_Help" colspan="3"><xsl:value-of select="labels/info" /></td>
							</tr>
							</table>
						</div>
						<div id="card-info">
							<table cellpadding="0" cellspacing="0" class="grouped">
							<tr class="first-row">
								<th class="left-column"><xsl:value-of select="labels/card-number" /></th>
								<td class="right-column stretch" colspan="2">
									<input name="x_card_num" onblur="javascript:parent.postMessage('reposition', '*');" pattern="[0-9]*" value="{$card-number}" maxlength="19" class="text" style="-wap-input-format:'*N';" type="number" />
								</td>
							</tr>
							<tr class="row">
								<th class="left-column">
									<xsl:value-of select="labels/expiry-date" />:
									<div class="mPoint_Info">(<xsl:value-of select="labels/expiry-month" />/<xsl:value-of select="labels/expiry-year" />)</div>
								</th>
								<td class="right-column stretch" colspan="2">
									<input type="number" id="expiry-month" name="expiry-month" maxlength="2" value="{$expiry-month}" size="3" style="-wap-input-format:'*N';" pattern="[0-9]*" onblur="javascript:parent.postMessage('reposition', '*');" />
									<xsl:value-of select="concat(' ', '/', ' ')" />
									<input type="number" id="expiry-year" name="expiry-year" maxlength="2" value="{$expiry-year}" size="3" style="-wap-input-format:'*N';" pattern="[0-9]*" onblur="javascript:parent.postMessage('reposition', '*');" />
								</td>
							</tr>
							<tr class="row combined-row">
								<th class="left-column combined-row"><xsl:value-of select="labels/cvc" /></th>
								<td class="right-column combined-row stretch" colspan="2">
									<xsl:choose>
									<!--  American Express -->
									<xsl:when test="card/@id = 1">
										<input type="number" name="x_card_code" maxlength="4" value="{$cvc}" size="5" style="-wap-input-format:'*N';" pattern="[0-9]*" onblur="javascript:parent.postMessage('reposition', '*');" />
									</xsl:when>
									<xsl:otherwise>
										<input type="number" name="x_card_code" maxlength="3" value="{$cvc}" size="4" style="-wap-input-format:'*N';" pattern="[0-9]*" onblur="javascript:parent.postMessage('reposition', '*');" />
									</xsl:otherwise>
									</xsl:choose>
								</td>
							</tr>
							<tr class="last-row combined-row">
								<td class="left-column right-column info stretch" colspan="3">
									<xsl:choose>
									<!--  American Express -->
									<xsl:when test="card/@id = 1">
										<xsl:value-of select="labels/cvc-4-help" />
									</xsl:when>
									<xsl:otherwise>
										<xsl:value-of select="labels/cvc-3-help" />
									</xsl:otherwise>
									</xsl:choose>
								</td>
							</tr>
							<!-- Store Credit Card - DISABLED -->
							<xsl:if test="client-config/store-card &gt; 0 and 1 = 2">
								<tr>
									<td colspan="3">
										<input type="checkbox" name="preauth" value="true" checked="true" />
										<xsl:value-of select="labels/save-card" />
									</td>
								</tr>
							</xsl:if>
							</table>
						</div>
						<!-- Complete Payment -->
						<div id="submit">
							<a class="submit-button" id="pay" onclick="javascript:document.getElementById('loader').style.visibility='visible'; this.className='submit-button-clicked'; this.disabled=true; document.getElementById('x_exp_date').value=document.getElementById('expiry-month').value+'/'+document.getElementById('expiry-year').value; document.getElementById('pay-card').submit();">
								<h2><xsl:value-of select="labels/submit" /></h2>
							</a>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		parent.postMessage('mpoint-card-details,<xsl:value-of select="system/session/@id" />', '*');
	</script>
</xsl:template>

</xsl:stylesheet>