<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="html" indent="no" media-type="text/html" doctype-public="HTML" omit-xml-declaration="yes" standalone="yes" />
<xsl:include href="../mobile.xsl" />

<xsl:template match="/root">
	<xsl:variable name="card-number">
		<xsl:if test="transaction/@mode &gt; 0">4571000000000000</xsl:if>
	</xsl:variable>
	<xsl:variable name="expiry-month">
		<xsl:if test="transaction/@mode &gt; 0">12</xsl:if>
	</xsl:variable>
	<xsl:variable name="expiry-year">
		<xsl:if test="transaction/@mode &gt; 0">23</xsl:if>
	</xsl:variable>
	<xsl:variable name="cvc">
		<xsl:if test="transaction/@mode &gt; 0">123</xsl:if>
	</xsl:variable>
	
	<xsl:variable name="card-url" select="concat(system/protocol, '://', system/host, '/img/', card/width, 'x', card/height, '_card_', card/@id, '_', system/session/@id, '.png')" />

	<script type="text/javascript">
		function toggleCheckmark(obj_Elem)
		{
			if (obj_Elem.className.match("checkbox-checked") == null)
			{
				obj_Elem.className += ' checkbox-checked';
			}
			else
			{
				obj_Elem.className = obj_Elem.className.replace(" checkbox-checked", "");
				obj_Elem.className = obj_Elem.className.replace("checkbox-checked", "");
			}
			if (document.getElementById('authtype').value == 'auth')
			{
				document.getElementById('authtype').value = 'subscribe';
				<xsl:variable name="file">
					<xsl:choose>
					<xsl:when test="transaction/@eua-id &gt; 0">name.php</xsl:when>
					<xsl:otherwise>pwd.php</xsl:otherwise>
					</xsl:choose>
				</xsl:variable>
				document.getElementById('accepturl').value = '<xsl:value-of select="/root/system/protocol" />://<xsl:value-of select="/root/system/host" />/pay/<xsl:value-of select="$file" />';
			}
			else
			{
				document.getElementById('authtype').value = 'auth';
				document.getElementById('accepturl').value = '<xsl:value-of select="/root/system/protocol" />://<xsl:value-of select="/root/system/host" />//pay/accept.php';
			}
		}
	</script>
	<div id="progress" class="mPoint_Info">
		<xsl:value-of select="labels/progress" />
		<br /><br />
	</div>
	<!-- Display Status Messages -->
	<xsl:apply-templates select="messages" />
	<div id="outer-border">
		<div>	
			<form id="pay-card" action="{wannafind/url}" method="post">
				<div>
					<!-- WannaFind Required Data -->
					<input type="hidden" name="shopid" value="{wannafind/@shop-id}" />
					<input type="hidden" name="amount" value="{transaction/amount}" />
					<input type="hidden" name="paytype" value="creditcard" />
					<input type="hidden" name="currency" value="{card/currency}" />
					<input type="hidden" id="authtype" name="authtype" value="auth" />
					<input type="hidden" name="uniqorderid" value="false" />
					<input type="hidden" id="callbackurl" name="callbackurl" value="{/root/system/protocol}://{/root/system/host}/callback/wannafind.php?mpoint-id={transaction/@id}&amp;cardid={card/@id}&amp;language={system/language}" />
					<input type="hidden" id="accepturl" name="accepturl" value="{/root/system/protocol}://{/root/system/host}/pay/accept.php" />
					<input type="hidden" name="declineurl" value="{/root/system/protocol}://{/root/system/host}/wannafind/postform.php" />
					<input type="hidden" name="cardnomask" value="true" />
					<input type="hidden" name="orderid" value="{transaction/orderid}" />
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
							<input name="cardnum" pattern="[0-9]*" value="{$card-number}" maxlength="19" class="text" style="-wap-input-format:'*N';" type="number" />
						</td>
					</tr>
					<tr class="row">
						<th class="left-column">
							<xsl:value-of select="labels/expiry-date" />:
							<div class="mPoint_Info">(<xsl:value-of select="labels/expiry-month" />/<xsl:value-of select="labels/expiry-year" />)</div>
						</th>
						<td class="right-column stretch" colspan="2">
							<input type="number" id="expiry-month" name="emonth" maxlength="2" value="{$expiry-month}" size="3" style="-wap-input-format:'*N';" pattern="[0-9]*" />
							<xsl:value-of select="concat(' ', '/', ' ')" />
							<input type="number" id="expiry-year" name="eyear" maxlength="2" value="{$expiry-year}" size="3" style="-wap-input-format:'*N';" pattern="[0-9]*" />
						</td>
					</tr>
					<tr class="row combined-row">
						<th class="left-column combined-row"><xsl:value-of select="labels/cvc" /></th>
						<td class="right-column combined-row stretch" colspan="2">
							<xsl:choose>
							<!--  American Express -->
							<xsl:when test="card/@id = 1">
								<input type="number" name="cvc" maxlength="4" value="{$cvc}" size="5" style="-wap-input-format:'*N';" pattern="[0-9]*" />
							</xsl:when>
							<xsl:otherwise>
								<input type="number" name="cvc" maxlength="3" value="{$cvc}" size="4" style="-wap-input-format:'*N';" pattern="[0-9]*" />
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
					</table>
				</div>
				<!-- Store Credit Card -->
				<xsl:if test="client-config/store-card &gt; 0">
					<table cellpadding="0" cellspacing="0">
					<tr>
						<td>
							<div id="store-card" class="checkbox" onclick="javascript:toggleCheckmark(this);">
								<img src="{system/protocol}://{system/host}/img/checkmark.png" width="30" height="30" alt="" />
							</div>
						</td>
						<td class="stretch"><xsl:value-of select="labels/store-card" /></td>
					</tr>
					</table>
				</xsl:if>
				<!-- Complete Payment -->
				<div id="submit">
					<a class="submit-button" id="pay" onclick="javascript:this.className='submit-button-clicked'; this.disabled=true; document.getElementById('callbackurl').value+='&amp;authtype='+ document.getElementById('authtype').value; document.getElementById('pay-card').submit();">
						<h2><xsl:value-of select="labels/submit" /></h2>
					</a>
				</div>
			</form>
		</div>
	</div>
	<script type="text/javascript">
		<!-- Store Credit Card -->
		<xsl:if test="transaction/auto-store-card = 'true'">
			toggleCheckmark(document.getElementById('store-card') );
		</xsl:if>
	</script>
</xsl:template>

</xsl:stylesheet>