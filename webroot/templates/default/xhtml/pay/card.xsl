<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="xml" version="1.0" encoding="ISO-8859-15" indent="yes" media-type="text/html" doctype-public="-//WAPFORUM//DTD XHTML Mobile 1.0//EN" doctype-system="http://www.openmobilealliance.org/DTD/xhtml-mobile10.dtd" omit-xml-declaration="no" />
<xsl:include href="../mobile.xsl" />

<xsl:template match="/root">
	<div id="progress" class="mPoint_Info">
		<xsl:value-of select="labels/progress" />
		<br /><br />
	</div>
			
	<div><xsl:value-of select="labels/info" /></div>
	<div id="cards">
		<xsl:for-each select="cards/item">
			<xsl:choose>
				<!-- Cellpoint Mobile -->
				<xsl:when test="@pspid = 1">
					<xsl:apply-templates select="." mode="cpm" />
				</xsl:when>
				<!-- DIBS -->
				<xsl:when test="@pspid = 2">
					<xsl:apply-templates select="." mode="dibs" />
				</xsl:when>
				<!-- IHI -->
				<xsl:when test="@pspid = 3">
					<xsl:apply-templates select="." mode="ihi" />
				</xsl:when>
				<!-- Error -->
				<xsl:otherwise>
					
				</xsl:otherwise>
			</xsl:choose>
		</xsl:for-each>
	</div>
	
	<!-- Display Status Messages -->
	<xsl:apply-templates select="messages" />
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
			<xsl:when test="@id = 10">
				mPoint_Card
			</xsl:when>
			<!-- My Account -->
			<xsl:when test="@id = 11">
				mPoint_Card mPoint_Account
			</xsl:when>
			<!-- Other -->
			<xsl:otherwise>
				mPoint_Card
			</xsl:otherwise>
		</xsl:choose>
	</xsl:variable>

	<xsl:if test="/root/cards/@accountid &gt; 0 or @id != 11">
		<div>
			<form action="{func:constLink('/cpm/payment.php') }" method="post">
				<div class="{$css}">
					<input type="hidden" name="cardtype" value="{@id}" />
					<input type="hidden" name="euaid" value="{/root/cards/@accountid}" />
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
						<td colspan="3"><input type="submit" value="{name}" class="mPoint_Card_Button" /></td>
					</tr>
					</table>
				</div>
			</form>
		</div>
	</xsl:if>
</xsl:template>

<xsl:template match="item" mode="dibs_flexwin">
	<div>
		<form action="https://payment.architrade.com/paymentweb/mobiwin.action" method="post">
			<div class="mPoint_Card">
				<input type="hidden" name="test" value="yes" />
				
				<!-- DIBS Required Data -->
				<input type="hidden" name="merchant" value="{account}" />
				<input type="hidden" name="callbackurl" value="{/root/system/protocol}://{/root/system/host}/callback/dibs.php" />
				<input type="hidden" name="accepturl" value="{/root/system/protocol}://{/root/system/host}/pay/accept.php" />
				<input type="hidden" name="cancelurl" value="{/root/transaction/cancel-url}" />
				<input type="hidden" name="amount" value="{/root/transaction/amount}" />
				<input type="hidden" name="currency" value="{currency}" />
				<input type="hidden" name="orderid" value="{/root/transaction/orderid}" />
				<!-- Use Auto Capture -->
				<xsl:if test="/root/transaction/auto-capture = true">
					<input type="hidden" name="capturenow" value="true" />
				</xsl:if>
				<!-- Sub-Account configured for DIBS -->
				<xsl:if test="subaccount &gt; 0">
					<input type="hidden" name="account" value="{subaccount}" />
				</xsl:if>
				<input type="hidden" name="lang" value="{func:transLanguage(/root/system/language)}" />
				
				<!-- mPoint Required Data -->
				<input type="hidden" name="width" value="{/root/transaction/logo/width}" />
				<input type="hidden" name="height" value="{/root/transaction/logo/height}" />
				<input type="hidden" name="{/root/system/session}" value="{/root/system/session/@id}" />
				<input type="hidden" name="format" value="xhtml" />
				<input type="hidden" name="language" value="{/root/system/language}" />
				<input type="hidden" name="cardid" value="{@id}" />
				<input type="hidden" name="mpointid" value="{/root/transaction/@id}" />
				
				<!-- Card Data -->
				<input type="hidden" name="paytype" value="{func:transCard(@id)}" />
				
				<!--
				  - The colspan attribute in the table below ensures that the page is rendered correctly on the Nokia 6230.
				  - Nokia 6230 assigns the same width to all table columns but by using the colspan attribute (eventhough it really isn't needed)
				  - the phone will assign 25% of the screen width to the card logo and 75% of the screen width to the card name.
				  -->
				<table>
				<tr>
					<td><img src="{/root/system/protocol}://{/root/system/host}/img/{logo-width}x{logo-height}_card_{@id}_{/root/system/session/@id}.png" width="{logo-width}" height="{logo-height}" alt="" /></td>
					<td colspan="3"><input type="submit" value="{name}" class="mPoint_Card_Button" /></td>
				</tr>
				</table>
			</div>
		</form>
	</div>
</xsl:template>

<xsl:template match="item" mode="dibs">
	<div>
		<form action="https://payment.architrade.com/shoppages/{account}/payment.pml" method="post">
			<div class="mPoint_Card">
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
				<!-- Use Auto Capture -->
				<xsl:if test="/root/transaction/auto-capture = 'true' and /root/client-config/store-card = 0">
					<input type="hidden" name="capturenow" value="true" />
				</xsl:if>
				<!-- Sub-Account configured for DIBS -->
				<xsl:if test="subaccount &gt; 0">
					<input type="hidden" name="account" value="{subaccount}" />
				</xsl:if>
				<input type="hidden" name="lang" value="{func:transLanguage(/root/system/language)}" />
				
				<!-- mPoint Required Data -->
				<input type="hidden" name="width" value="{/root/transaction/logo/width}" />
				<input type="hidden" name="height" value="{/root/transaction/logo/height}" />
				<input type="hidden" name="{/root/system/session}" value="{/root/system/session/@id}" />
				<input type="hidden" name="format" value="xhtml" />
				<input type="hidden" name="language" value="{/root/system/language}" />
				<input type="hidden" name="cardid" value="{@id}" />
				<input type="hidden" name="mpointid" value="{/root/transaction/@id}" />
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
				
				<!--
				  - The colspan attribute in the table below ensures that the page is rendered correctly on the Nokia 6230.
				  - Nokia 6230 assigns the same width to all table columns but by using the colspan attribute (eventhough it really isn't needed)
				  - the phone will assign 25% of the screen width to the card logo and 75% of the screen width to the card name.
				  -->
				<table>
				<tr>
					<td><img src="{/root/system/protocol}://{/root/system/host}/img/{logo-width}x{logo-height}_card_{@id}_{/root/system/session/@id}.png" width="{logo-width}" height="{logo-height}" alt="" /></td>
					<td colspan="3"><input type="submit" value="{name}" class="mPoint_Card_Button" /></td>
				</tr>
				</table>
			</div>
		</form>
	</div>
</xsl:template>

<xsl:template match="item" mode="ihi">
	<div>
		<form action="https://usrtestmobile.ihi.com/payment/payment.aspx" method="post">
			<div class="mPoint_Card">
				<!-- IHI Required Data -->
				<input type="hidden" name="callbackurl" value="{/root/system/protocol}://{/root/system/host}/callback/ihi.php" />
				<input type="hidden" name="accepturl" value="{/root/system/protocol}://{/root/system/host}/pay/accept.php" />
				<input type="hidden" name="cancelurl" value="{/root/transaction/cancel-url}" />
				<input type="hidden" name="amount" value="{/root/transaction/amount}" />
				<input type="hidden" name="currency" value="{currency}" />
				<!-- Use Auto Capture -->
				<xsl:if test="/root/transaction/auto-capture = 'true'">
					<input type="hidden" name="capturenow" value="true" />
				</xsl:if>
				<!-- Sub-Account configured for IHI -->
				<xsl:if test="subaccount &gt; 0">
					<input type="hidden" name="account" value="{subaccount}" />
				</xsl:if>
				<input type="hidden" name="lang" value="{func:transLanguage(/root/system/language)}" />
				
				<!-- mPoint Required Data -->
				<input type="hidden" name="width" value="{/root/transaction/logo/width}" />
				<input type="hidden" name="height" value="{/root/transaction/logo/height}" />
				<input type="hidden" name="{/root/system/session}" value="{/root/system/session/@id}" />
				<input type="hidden" name="format" value="xhtml" />
				<input type="hidden" name="language" value="{/root/system/language}" />
				<input type="hidden" name="cardid" value="{@id}" />
				<input type="hidden" name="mpointid" value="{/root/transaction/@id}" />
				
				<!-- Card Data -->
				<input type="hidden" name="paytype" value="{func:transCard(@id)}" />
				
				<!-- Shared Data -->
				<input type="hidden" name="clientid" value="{/root/client-config/@id}" />
				<input type="hidden" name="client" value="{/root/client-config/name}" />
				
				<!-- Payment Page Data -->
				<input type="hidden" name="card_width" value="{logo-width}" />
				<input type="hidden" name="card_height" value="{logo-height}" />
				
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
					<input type="hidden" name="{name}" value="{value}" />
				</xsl:for-each>
				
				<!--
				  - The colspan attribute in the table below ensures that the page is rendered correctly on the Nokia 6230.
				  - Nokia 6230 assigns the same width to all table columns but by using the colspan attribute (eventhough it really isn't needed)
				  - the phone will assign 25% of the screen width to the card logo and 75% of the screen width to the card name.
				  -->
				<table>
				<tr>
					<td><img src="{/root/system/protocol}://{/root/system/host}/img/{logo-width}x{logo-height}_card_{@id}_{/root/system/session/@id}.png" width="{logo-width}" height="{logo-height}" alt="" /></td>
					<td colspan="3"><input type="submit" value="{name}" class="mPoint_Card_Button" /></td>
				</tr>
				</table>
			</div>
		</form>
	</div>
</xsl:template>

</xsl:stylesheet>