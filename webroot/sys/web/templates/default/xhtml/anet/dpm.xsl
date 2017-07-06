<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes" media-type="text/html" doctype-public="-//WAPFORUM//DTD XHTML Mobile 1.0//EN" doctype-system="http://www.openmobilealliance.org/DTD/xhtml-mobile10.dtd" omit-xml-declaration="no" />
<xsl:include href="../mobile.xsl"/>

<xsl:template match="/root">
	<div id="progress" class="mPoint_Info">
		<xsl:value-of select="labels/progress" />
		<br /><br />
	</div>
	
	<!-- Display Status Messages -->
	<xsl:apply-templates select="messages" />
	<div id="outer-border">
		<div>	
			<form action="{authorize-net/url}" method="post" onsubmit="javascript:document.this.x_exp_date.value=this.month.value+'/'+this.year.value;">
				<div>
					<!-- Authorize.Net Required Data -->
					<input type="hidden" name="x_exp_date" value="" />
					<input type="hidden" name="x_amount" value="{transaction/amount}" />
					<input type="hidden" name="x_fp_sequence" value="{transaction/@id}" />
					<input type="hidden" name="x_fp_hash" value="{authorize-net/checksum}" />
					<input type="hidden" name="x_fp_timestamp" value="{authorize-net/time}" />
					<input type="hidden" name="x_relay_response" value="TRUE" />
					<input type="hidden" name="x_relay_url" value="{/root/system/protocol}://{/root/system/host}/callback/anet.php" />
					<input type="hidden" name="x_login" value="{authorize-net/api-login}" />
					<input type="hidden" name="x_version" value="3.1" />
					<input type="hidden" name="x_delim_char" value="," />
					<input type="hidden" name="x_delim_data" value="TRUE" />
				</div>
				<!-- Selected Card -->
				<div>
					<span class="mPoint_Label"><xsl:value-of select="labels/selected-card" />:</span>
					<table>
					<tr>
						<td><img src="{card/url}" width="{card/width}" height="{card/height}" alt="- {card/name} -" /></td>
						<td colspan="3" class="status"><xsl:value-of select="concat(' ', card/name)" /></td>						
					</tr>
					</table>
				</div>
				<!-- Price -->
				<div id="price">
					<span class="mPoint_Label"><xsl:value-of select="labels/price" />:</span>
					<xsl:value-of select="transaction/price" />
				</div>
				<!-- Credit Card Information -->
				<div><xsl:value-of select="labels/info" /></div>
				<div id="card-info">
					<div class="mPoint_Label">
						<xsl:value-of select="labels/card-number" />:<br />
						<input type="number" name="x_card_num" maxlength="19" value="" size="19" style="-wap-input-format:'*N';" pattern="[0-9]*" />
					</div>
					<div class="mPoint_Label">
						<xsl:value-of select="labels/expiry-date" /> <span class="mPoint_Info">(<xsl:value-of select="labels/expiry-month" />/<xsl:value-of select="labels/expiry-year" />)</span>:<br />
						<input type="number" name="month" maxlength="2" value="" size="2" style="-wap-input-format:'*N';" pattern="[0-9]*" />
						<xsl:value-of select="concat(' ', ' ')" />
						<input type="number" name="year" maxlength="2" value="" size="2" style="-wap-input-format:'*N';" pattern="[0-9]*" />
					</div>
					<div class="mPoint_Label">
						<xsl:value-of select="labels/cvc" />:<br />
						<xsl:choose>
						<!--  American Express -->
						<xsl:when test="card/@id = 1">
							<input type="number" name="x_card_code" maxlength="4" value="" size="4" style="-wap-input-format:'*N';" pattern="[0-9]*" />
						</xsl:when>
						<xsl:otherwise>
							<input type="number" name="x_card_code" maxlength="3" value="" size="3" style="-wap-input-format:'*N';" pattern="[0-9]*" />
						</xsl:otherwise>
						</xsl:choose>
					</div>
					<div class="mPoint_Info">
						<xsl:choose>
						<!--  American Express -->
						<xsl:when test="card/@id = 1">
							<xsl:value-of select="labels/cvc-4-help" />
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="labels/cvc-3-help" />
						</xsl:otherwise>
						</xsl:choose>
					</div>
				</div>
				<!-- Store Credit Card - DISABLED -->
				<xsl:if test="client-config/store-card &gt; 0 and 1 = 2">
					<div>
						<input type="checkbox" name="preauth" value="true" checked="true" />
						<xsl:value-of select="labels/save-card" />
					</div>
				</xsl:if>
				<!-- Complete Payment -->
				<div id="submit">
					<input type="submit" value="{labels/submit}" class="mPoint_Button" />
				</div>
			</form>
		</div>
	</div>
</xsl:template>

</xsl:stylesheet>