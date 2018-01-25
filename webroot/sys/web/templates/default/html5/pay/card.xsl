<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="html" indent="no" media-type="text/html" doctype-public="HTML" omit-xml-declaration="yes" standalone="yes" />
<xsl:include href="../mobile.xsl" />

<xsl:template match="/root">
	<div id="wrapper">
		<div id="content">
			<div id="progress" class="mPoint_Info">
				<xsl:value-of select="labels/progress" />
				<br /><br />
			</div>
			
			<!-- Display Status Messages -->
			<xsl:apply-templates select="messages" />
			<div id="outer-border">
				<div class="mPoint_Help"><xsl:value-of select="labels/info" /></div>
				<div id="cards">
					<table cellpadding="0" cellspacing="0" class="grouped">
					<xsl:for-each select="cards/item">
						<xsl:choose>
							<!-- Cellpoint Mobile -->
							<xsl:when test="@pspid = 1">
								<xsl:apply-templates select="." mode="cpm" />
								<xsl:if test="position() = count(//cards/item[@pspid = 1])">
								<tr>
									<td colspan="2"><br /></td>
								</tr>
								</xsl:if>
							</xsl:when>
							<!-- DIBS -->
							<xsl:when test="@pspid = 2">
								<xsl:apply-templates select="." mode="dibs">
									<xsl:with-param name="css">
										<xsl:choose>
											<xsl:when test="position() - count(//cards/item[@pspid = 1]) = 1 and position() = count(//cards/item[@pspid = 2])">first-row last-row</xsl:when>
											<xsl:when test="position() - count(//cards/item[@pspid = 1]) = 1">first-row</xsl:when>
											<xsl:when test="position() - count(//cards/item[@pspid = 1]) = count(//cards/item[@pspid = 2])">last-row</xsl:when>
											<xsl:otherwise>row</xsl:otherwise>
										</xsl:choose>
									</xsl:with-param>
								</xsl:apply-templates>
							</xsl:when>
							<!-- WorldPay -->
							<xsl:when test="@pspid = 4">
								<xsl:apply-templates select="." mode="worldpay" />
							</xsl:when>
							<!-- PayEx -->
							<xsl:when test="@pspid = 5">
								<xsl:apply-templates select="." mode="payex" />
							</xsl:when>
							<!-- Authorize.Net -->
							<xsl:when test="@pspid = 6">
								<xsl:apply-templates select="." mode="authorize.net">
									<xsl:with-param name="css">
										<xsl:choose>
											<xsl:when test="position() - count(//cards/item[@pspid = 6]) = 1 and position() = count(//cards/item[@pspid = 6])">first-row last-row</xsl:when>
											<xsl:when test="position() - count(//cards/item[@pspid = 6]) = 1">first-row</xsl:when>
											<xsl:when test="position() - count(//cards/item[@pspid = 6]) = count(//cards/item[@pspid = 6])">last-row</xsl:when>
											<xsl:otherwise>row</xsl:otherwise>
										</xsl:choose>
									</xsl:with-param>
								</xsl:apply-templates>
							</xsl:when>
							<!-- WannaFind -->
							<xsl:when test="@pspid = 7">
								<xsl:apply-templates select="." mode="wannafind">
									<xsl:with-param name="css">
										<xsl:choose>
											<xsl:when test="position() - count(//cards/item[@pspid = 7]) = 1 and position() = count(//cards/item[@pspid = 7])">first-row last-row</xsl:when>
											<xsl:when test="position() - count(//cards/item[@pspid = 7]) = 1">first-row</xsl:when>
											<xsl:when test="position() - count(//cards/item[@pspid = 7]) = count(//cards/item[@pspid = 7])">last-row</xsl:when>
											<xsl:otherwise>row</xsl:otherwise>
										</xsl:choose>
									</xsl:with-param>
								</xsl:apply-templates>
							</xsl:when>
							<!-- Error -->
							<xsl:otherwise>
								
							</xsl:otherwise>
						</xsl:choose>
					</xsl:for-each>
					</table>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		parent.postMessage('mpoint-list-cards,<xsl:value-of select="system/session/@id" />', '*');
	</script>
</xsl:template>

<func:function name="func:transCard">
	<xsl:param name="cardid" />
	
	<xsl:choose>
		<!-- American Express -->
		<xsl:when test="$cardid = 1">
			<func:result>AMEX</func:result>
		</xsl:when>
		<!-- Dankort -->
		<xsl:when test="$cardid = 2">
			<func:result>DK</func:result>
		</xsl:when>
		<!-- Diners Club -->
		<xsl:when test="$cardid = 3">
			<func:result>DIN</func:result>
		</xsl:when>
		<!-- EuroCard -->
		<!--
		<xsl:when test="$cardid = 4">
			<func:result>MC</func:result>
		</xsl:when>
		-->
		<!-- JCB -->
		<xsl:when test="$cardid = 5">
			<func:result>JCB</func:result>
		</xsl:when>
		<!-- Maestro -->
		<xsl:when test="$cardid = 6">
			<func:result>MTRO</func:result>
		</xsl:when>
		<!-- Master Card -->
		<xsl:when test="$cardid = 7">
			<func:result>MC</func:result>
		</xsl:when>
		<!-- VISA -->
		<xsl:when test="$cardid = 8">
			<func:result>VISA</func:result>
		</xsl:when>
		<!-- VISA Electron -->
		<xsl:when test="$cardid = 9">
			<func:result>ELEC</func:result>
		</xsl:when>
		<!-- Error -->
		<xsl:otherwise>
			
		</xsl:otherwise>
	</xsl:choose>
</func:function>

<xsl:template match="item" mode="cpm">
	<xsl:variable name="css">
		<xsl:choose>
			<!-- Premium SMS -->
			<xsl:when test="@id = 10">mPoint_Card</xsl:when>
			<!-- My Account -->
			<xsl:when test="@id = 11">mPoint_Card mPoint_Account</xsl:when>
			<!-- Other -->
			<xsl:otherwise>mPoint_Card</xsl:otherwise>
		</xsl:choose>
	</xsl:variable>
	
	<!--
	  - Only display option if:
	  - Card shouldn't be "auto stored" AND
	  - "Card" is NOT "My Account" OR
	  - "Cards" stored for Merchant OR
	  - E-Money based Prepaid Account is available AND Transaction is not an Account Top-Up
	  -->
	<xsl:if test="/root/transaction/auto-store-card = 'false' and (@id != 11 or count(/root/stored-cards/card[client/@id = /root/client-config/@id]) &gt; 0 or (floor(/root/client-config/store-card div 1) mod 2 != 1 and (/root/transaction/@type &lt; 100 or /root/transaction/@type &gt; 109) ) )">
		<tr class="first-row last-row" onclick="javascript:obj_Menu.select(this, 'selected'); document.getElementById('loader').style.visibility='visible'; document.getElementById('card-{@id}').submit();">
			<td class="left-column">
				<img src="{/root/system/protocol}://{/root/system/host}/img/{logo-width}x{logo-height}_card_{@id}_{/root/system/session/@id}.png" width="{logo-width}" height="{logo-height}" alt="" />
			</td>
			<td class="stretch {$css}">
				<div class="mPoint_Card_Button"><xsl:value-of select="name" /></div>
				<form id="card-{@id}" action="{func:constLink('/cpm/payment.php') }" method="post" onsubmit="javascript:document.getElementById('loader').style.visibility='visible';">
					<input type="hidden" name="cardtype" value="{@id}" />
					<input type="hidden" name="euaid" value="{/root/cards/@accountid}" />
					<!-- Payment Page Data -->
					<input type="hidden" name="card_width" value="{logo-width}" />
					<input type="hidden" name="card_height" value="{logo-height}" />
				</form>
			</td>
			<td class="right-column">
				<h2>&gt;</h2>
			</td>
		</tr>
	</xsl:if>
</xsl:template>

<xsl:template match="item" mode="dibs">
	<xsl:param name="css" />
	
	<tr class="{$css}" onclick="javascript:obj_Menu.select(this, 'selected'); document.getElementById('loader').style.visibility='visible'; document.getElementById('card-{@id}').submit();">
		<td class="left-column">
			<img src="{/root/system/protocol}://{/root/system/host}/img/{logo-width}x{logo-height}_card_{@id}_{/root/system/session/@id}.png" width="{logo-width}" height="{logo-height}" alt="" />
		</td>
		<td class="stretch mPoint_Card">
			<div class="mPoint_Card_Button"><xsl:value-of select="name" /></div>

			<form id="card-{@id}" action="https://payment.architrade.com/shoppages/{account}/payment.pml" method="post" onsubmit="javascript:document.getElementById('loader').style.visibility='visible';">
				<!-- Client is in Test or Certification mode -->
				<xsl:if test="/root/transaction/@mode &gt; 0">
					<input type="hidden" name="test" value="{/root/transaction/@mode}" />
				</xsl:if>
				<!-- DIBS Required Data -->
				<input type="hidden" name="merchant" value="{account}" />
				<input type="hidden" name="callbackurl" value="{/root/system/protocol}://{/root/system/host}/callback/dibs.php" />
				<input type="hidden" name="accepturl" value="{/root/system/protocol}://{/root/system/host}/pay/accept.php" />
				<input type="hidden" name="cancelurl" value="{/root/transaction/cancel-url}" />
				<input type="hidden" name="amount" value="{/root/transaction/amount}" />
				<input type="hidden" name="currency" value="{currency}" />
				<input type="hidden" name="orderid" value="{/root/transaction/orderid}" />
				<input type="hidden" name="fullreply" value="true" />
				<!-- Sub-Account configured for DIBS -->
				<xsl:if test="subaccount &gt; 0">
					<input type="hidden" name="account" value="{subaccount}" />
				</xsl:if>
				<input type="hidden" name="lang" value="{func:transLanguage(/root/system/language)}" />
				
				<!-- mPoint Required Data -->
				<input type="hidden" name="device_name" value="{/root/uaprofile/device}" />
				<input type="hidden" name="device_width" value="{/root/uaprofile/width}" />
				<input type="hidden" name="device_height" value="{/root/uaprofile/height}" />
				<input type="hidden" name="logo_url" value="{/root/transaction/logo/url}" />
				<input type="hidden" name="logo_width" value="{/root/transaction/logo/width}" />
				<input type="hidden" name="logo_height" value="{/root/transaction/logo/height}" />
				<input type="hidden" name="{/root/system/session}" value="{/root/system/session/@id}" />
				<input type="hidden" name="format" value="xhtml" />
				<input type="hidden" name="language" value="{/root/system/language}" />
				<input type="hidden" name="cardid" value="{@id}" />
				<input type="hidden" name="mpointid" value="{/root/transaction/@id}" />
				<input type="hidden" name="markup" value="{/root/transaction/markup-language}" />
				<!-- Current transaction is an Account Top-Up and a previous transaction is in progress -->
				<xsl:if test="/root/original-transaction-id &gt; 0">
					<input type="hidden" name="org_mpointid" value="{/root/original-transaction-id}" />
				</xsl:if>
				<input type="hidden" name="euaid" value="{/root/cards/@accountid}" />
				
				<!-- Card Data -->
				<input type="hidden" name="paytype" value="{func:transCard(@id)}" />
				
				<!-- Shared Data -->
				<input type="hidden" name="clientid" value="{/root/client-config/@id}" />
				<input type="hidden" name="client" value="{/root/client-config/name}" />
				
				<!-- Payment Page Data -->
				<input type="hidden" name="card_width" value="{logo-width}" />
				<input type="hidden" name="card_height" value="{logo-height}" />
				<!-- Allow user to Store Credit Card -->
				<input type="hidden" name="store_card" value="{/root/client-config/store-card}" />
				<input type="hidden" name="auto_store_card" value="{/root/transaction/auto-store-card}" />
				
				<!-- Accept Page Data -->
				<input type="hidden" name="mpoint_width" value="{/root/accept/mpoint-logo/width}" />
				<input type="hidden" name="mpoint_height" value="{/root/accept/mpoint-logo/height}" />
				<input type="hidden" name="sms_receipt" value="{/root/client-config/sms-receipt}" />
				<input type="hidden" name="email_receipt" value="{/root/client-config/sms-receipt}" />
				<input type="hidden" name="email_url" value="{func:constLink('email.php')}" />
				<input type="hidden" name="accept_url" value="{/root/transaction/accept-url}" />
				<input type="hidden" name="mobile" value="{/root/transaction/mobile}" />
				<input type="hidden" name="operator" value="{/root/transaction/operator}" />
				<input type="hidden" name="price" value="{/root/transaction/price}" />
				<!-- Transfer Custom Variables -->
				<xsl:for-each select="/root/accept/client-vars/item">
					<input type="hidden" name="client_vars_names_{position()}" value="{name}" />
					<input type="hidden" name="client_vars_data_{position()}" value="{value}" />
				</xsl:for-each>
			</form>
		</td>
		<td class="right-column">
			<h2>&gt;</h2>
		</td>
	</tr>
</xsl:template>

<xsl:template match="item" mode="worldpay">
	<div>
			<form action="{func:constLink('/worldpay/sys/rxml.php') }" method="post" onsubmit="javascript:document.getElementById('loader').style.visibility='visible';">
				<a onclick="javascript:this.parentNode.submit();">
					<div class="mPoint_Card">
						<!-- WorldPay data -->
						<input type="hidden" name="cardid" value="{@id}" />
						<input type="hidden" name="merchantcode" value="{account}" />
						<input type="hidden" name="currency" value="{currency}" />
						<!-- Payment Page Data -->
						<input type="hidden" name="card_width" value="{logo-width}" />
						<input type="hidden" name="card_height" value="{logo-height}" />
						
						<!--
						  - The colspan attribute in the table below ensures that the page is rendered correctly on the Nokia 6230.
						  - Nokia 6230 assigns the same width to all table columns but by using the colspan attribute (eventhough it really isn't needed)
						  - the phone will assign 25% of the screen width to the card logo and 75% of the screen width to the card name.
						  -->
						<table>
						<tr>
							<td><img src="{/root/system/protocol}://{/root/system/host}/img/{logo-width}x{logo-height}_card_{@id}_{/root/system/session/@id}.png" width="{logo-width}" height="{logo-height}" alt="" /></td>
							<td colspan="3" class="mPoint_Card_Button"><xsl:value-of select="name" /></td>
						</tr>
						</table>
					</div>
				</a>
			</form>
		</div>
</xsl:template>

<xsl:template match="item" mode="payex">
	<div>
		<form action="{func:constLink('/payex/sys/redirect.php') }" method="post" onsubmit="javascript:document.getElementById('loader').style.visibility='visible';">
			<a onclick="javascript:this.parentNode.submit();">
				<div>
					<!-- WorldPay data -->
					<input type="hidden" name="cardid" value="{@id}" />
					<input type="hidden" name="accountNumber" value="{account}" />
					<input type="hidden" name="currency" value="{currency}" />
					<!-- Payment Page Data -->
					<input type="hidden" name="card_width" value="{logo-width}" />
					<input type="hidden" name="card_height" value="{logo-height}" />
					
					<!--
					  - The colspan attribute in the table below ensures that the page is rendered correctly on the Nokia 6230.
					  - Nokia 6230 assigns the same width to all table columns but by using the colspan attribute (eventhough it really isn't needed)
					  - the phone will assign 25% of the screen width to the card logo and 75% of the screen width to the card name.
					  -->
					<table>
					<tr>
						<td><img src="{/root/system/protocol}://{/root/system/host}/img/{logo-width}x{logo-height}_card_{@id}_{/root/system/session/@id}.png" width="{logo-width}" height="{logo-height}" alt="" /></td>
						<td colspan="3" class="mPoint_Card_Button"><xsl:value-of select="name" /></td>
					</tr>
					</table>
				</div>
			</a>
		</form>
	</div>
</xsl:template>

<xsl:template match="item" mode="authorize.net">
	<xsl:param name="css" />
	<xsl:variable name="url" select="concat(/root/system/protocol, '://', /root/system/host, '/img/', logo-width, 'x', logo-height, '_card_', @id, '_', /root/system/session/@id, '.png')" />
	
	<tr class="{$css}" onclick="javascript:obj_Menu.select(this, 'selected'); document.getElementById('loader').style.visibility='visible'; document.getElementById('card-{@id}').submit();">
		<td class="left-column">
			<img src="{$url}" width="{logo-width}" height="{logo-height}" alt="" />
		</td>
		<td class="stretch mPoint_Card">
			<div class="mPoint_Card_Button"><xsl:value-of select="name" /></div>

			<form id="card-{@id}" action="{func:constLink('/anet/dpm.php') }" method="post" onsubmit="javascript:document.getElementById('loader').style.visibility='visible';">
				<!-- Payment Page Data -->
				<input type="hidden" name="cardid" value="{@id}" />
			</form>
		</td>
		<td class="right-column">
			<h2>&gt;</h2>
		</td>
	</tr>
</xsl:template>

<xsl:template match="item" mode="wannafind">
	<xsl:param name="css" />
	<xsl:variable name="url" select="concat(/root/system/protocol, '://', /root/system/host, '/img/', logo-width, 'x', logo-height, '_card_', @id, '_', /root/system/session/@id, '.png')" />
	
	<tr class="{$css}" onclick="javascript:obj_Menu.select(this, 'selected'); document.getElementById('loader').style.visibility='visible'; document.getElementById('card-{@id}').submit();">
		<td class="left-column">
			<img src="{$url}" width="{logo-width}" height="{logo-height}" alt="" />
		</td>
		<td class="stretch mPoint_Card">
			<div class="mPoint_Card_Button"><xsl:value-of select="name" /></div>

			<form id="card-{@id}" action="{func:constLink('/wannafind/postform.php') }" method="post" onsubmit="javascript:document.getElementById('loader').style.visibility='visible';">
				<!-- Payment Page Data -->
				<input type="hidden" name="cardid" value="{@id}" />
			</form>
		</td>
		<td class="right-column">
			<h2>&gt;</h2>
		</td>
	</tr>
</xsl:template>

</xsl:stylesheet>