<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="html" indent="no" media-type="text/html" doctype-public="HTML" omit-xml-declaration="yes" standalone="yes" />
<xsl:include href="../mobile.xsl" />

<xsl:template match="/root">
	<xsl:variable name="css">
		<xsl:choose>
			<!-- Insufficient Funds -->
			<xsl:when test="account/balance &lt; transaction/amount">
				<xsl:text>passive</xsl:text>
			</xsl:when>
			<!-- Sufficient Funds -->
			<xsl:otherwise>
				<xsl:text></xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:variable>
	
	<xsl:variable name="selected-card-id">
		<xsl:choose>
		<!-- Card previously selected by user -->
		<xsl:when test="session/cardid &gt; 0">
			<xsl:value-of select="session/cardid" />
		</xsl:when>
		<!-- Prepaid account available and there's enough money to pay for the transaction and the transaction type is NOT an Account Top-Up -->
		<xsl:when test="floor(client-config/store-card div 1) mod 2 != 1 and account/balance &gt; transaction/amount and transaction/@type &gt;= 100 and transaction/@type &lt;= 109">
			<xsl:value-of select="session/cardid" />B
		</xsl:when>
		<!-- Only one card has been stored -->
		<xsl:when test="count(stored-cards/card[client/@id = //client-config/@id]) = 1">
			<xsl:value-of select="stored-cards/card[client/@id = //client-config/@id]/@id" />
		</xsl:when>
		<!-- Card is user's preferred -->
		<xsl:when test="count(stored-cards/card[@preferred = 'true' and client/@id = //client-config/@id]) = 1">
			<xsl:value-of select="stored-cards/card[@preferred = 'true' and client/@id = //client-config/@id]/@id" />
		</xsl:when>
		<!-- First card -->
		<xsl:otherwise>
			<xsl:value-of select="stored-cards/card[client/@id = //client-config/@id]/@id" />
		</xsl:otherwise>
		</xsl:choose>
	</xsl:variable>
	
	<div id="wrapper">
		<div id="content">
			<div id="progress" class="mPoint_Info">
				<xsl:value-of select="labels/progress" />
				<div id="link">
					<a class="button" href="{func:constLink('/pay/card.php') };" onclick="javascript:this.className+=' clicked'; document.getElementById('loader').style.visibility='visible';" style="background-image:url('{system/protocol}://{system/host}/img/new.png'); background-repeat:no-repeat;">
						<xsl:value-of select="labels/add-card" />
					</a>
				</div>
				<br />
				<br />
			</div>
			
			<!-- Display Status Messages -->
			<xsl:apply-templates select="messages" />
		
			<div id="outer-border">
				<div class="mPoint_Help"><xsl:value-of select="labels/info" /></div>
				
				<div id="my-account">
					<form id="pay-account" action="{func:constLink('/cpm/sys/pay_account.php') }" method="post" onsubmit="javascript:document.getElementById('loader').style.visibility='visible'; document.getElementById('loader').style.visibility='visible';">
						<input type="hidden" name="euaid" value="{account/@id}" />
						<input type="hidden" name="cardtype" value="11" />
						<input type="hidden" name="prepaid" value="false" />
						<input type="hidden" id="cardid" name="cardid" value="{$selected-card-id}" />
						
						<table cellpadding="0" cellspacing="0" class="grouped">
						<tr class="first-row">
							<xsl:apply-templates select="stored-cards/card[@id = $selected-card-id]" mode="display" />
							<td id="price" class="right-column">
								<div class="mPoint_Label"><xsl:value-of select="labels/price" />:</div>
								<xsl:value-of select="transaction/price" />
							</td>
						</tr>
						<tr id="password" class="last-row">
							<td colspan="3" class="left-column right-column stretch mPoint_Label">
								<span class="mPoint_Label"><xsl:value-of select="labels/password" />:</span>
								<input type="password" name="pwd" value="" onblur="javascript:parent.postMessage('reposition', '*');" />
							</td> 
						</tr>
						<xsl:if test="count(stored-cards/card[@id != $selected-card-id]) &gt; 0">
							<tr>
								<td colspan="3"><br /></td>
							</tr>
							<xsl:apply-templates select="stored-cards/card[@id != $selected-card-id]" mode="select" />
						</xsl:if>
						<tr>
							<td colspan="3"><br /></td>
						</tr>
						</table>
						<!-- Complete Payment -->
						<div id="submit">
							<a id="pay" class="submit-button" onclick="javascript:this.className+=' clicked'; this.disabled=true; document.getElementById('loader').style.visibility='visible'; document.getElementById('pay-account').submit();">
								<h2><xsl:value-of select="labels/submit" /></h2>
							</a>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</xsl:template>

<xsl:template match="card" mode="display">
	<td class="left-column"><img id="selected-card-image" src="{/root/system/protocol}://{/root/system/host}/img/{logo-width}x{logo-height}_card_{type/@id}_{/root/system/session/@id}.png" onclick="javascript:document.getElementById('cardid-{@id}').checked=true;" width="{logo-width}" height="{logo-height}" alt="{type}" /></td>
	<td class="stretch" id="selected-card-name">
		<xsl:choose>
		<!-- Card named -->
		<xsl:when test="string-length(name) &gt; 0">
			<xsl:value-of select="name" />
		</xsl:when>
		<xsl:otherwise>
			<xsl:value-of select="mask" />
			<span class="mPoint_Info">(<xsl:value-of select="expiry" />)</span>
		</xsl:otherwise>
		</xsl:choose>
	</td>
</xsl:template>

<xsl:template match="card" mode="select">
	<xsl:variable name="css">
		<xsl:choose>
			<xsl:when test="position() = 1 and position() = count(//stored-cards/card) - 1">first-row last-row</xsl:when>
			<xsl:when test="position() = 1">first-row</xsl:when>
			<xsl:when test="position() = count(//stored-cards/card) - 1">last-row</xsl:when>
			<xsl:otherwise>row</xsl:otherwise>
		</xsl:choose>
	</xsl:variable>
	
	<tr class="{$css}" onclick="javascript:selectCard(this, {@id} );">
		<td class="left-column"><img id="card-{@id}-image" src="{/root/system/protocol}://{/root/system/host}/img/{logo-width}x{logo-height}_card_{type/@id}_{/root/system/session/@id}.png" width="{logo-width}" height="{logo-height}" alt="{type}" /></td>
		<td colspan="2" class="right-column stretch">
			<table cellpadding="0" cellspacing="0" class="stretch">
			<tr>
				<td class="stretch" id="card-{@id}-name">
					<xsl:choose>
					<!-- Card named -->
					<xsl:when test="string-length(name) &gt; 0">
						<xsl:value-of select="name" />
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="mask" />
						<div class="mPoint_Info">(<xsl:value-of select="expiry" />)</div>
					</xsl:otherwise>
					</xsl:choose>
				</td>
				<td>
					<h2>&gt;</h2>
				</td>
			</tr>
			</table>
		</td>
	</tr>
</xsl:template>

</xsl:stylesheet>