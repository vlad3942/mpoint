<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="xml" version="1.0" encoding="ISO-8859-15" indent="yes" media-type="application/xhtml+xml" doctype-public="-//WAPFORUM//DTD XHTML Mobile 1.0//EN" doctype-system="http://www.openmobilealliance.org/DTD/xhtml-mobile10.dtd" omit-xml-declaration="no" />
<xsl:include href="header.xsl" />

<xsl:template match="/root">
	<div id="progress" class="mPoint_Info">
		<xsl:value-of select="labels/progress" />
		<br /><br />
	</div>
			
	<div class="mPoint_Label"><xsl:value-of select="labels/info" /></div>
	<xsl:for-each select="cards/item">
		<xsl:choose>
			<!-- DIBS FlexWin -->
			<xsl:when test="@pspid = 1">
				<xsl:apply-templates select="." mode="dibs_flexwin" />
			</xsl:when>
			<!-- DIBS "Custom Pages" -->
			<xsl:when test="@pspid = 2">
				<xsl:apply-templates select="." mode="dibs_custom" />
			</xsl:when>
			<!-- Error -->
			<xsl:otherwise>
				
			</xsl:otherwise>
		</xsl:choose>
	</xsl:for-each>
</xsl:template>

<func:function name="func:transLanguage">
	<xsl:param name="lang" />
	
	<!-- Perform Language conversion -->
	<xsl:choose>
		<!-- British English -->
		<xsl:when test="$lang = 'uk'">
			<func:result>en</func:result>
		</xsl:when>
		<!-- American English -->
		<xsl:when test="$lang = 'us'">
			<func:result>en</func:result>
		</xsl:when>
		<!-- Danish -->
		<xsl:when test="$lang = 'dk'">
			<func:result>da</func:result>
		</xsl:when>
		<!-- Norwegian -->
		<xsl:when test="$lang = 'no'">
			<func:result>no</func:result>
		</xsl:when>
		<!-- Swedish -->
		<xsl:when test="$lang = 'se'">
			<func:result>sv</func:result>
		</xsl:when>
		<!-- German -->
		<xsl:when test="$lang = 'ge'">
			 <func:result>de</func:result>
		</xsl:when>
		<!-- Spanish -->
		<xsl:when test="$lang = 'es'">
			<func:result>es</func:result>
		</xsl:when>
		<!-- Finish -->
		<xsl:when test="$lang = 'fi'">
			<func:result>en</func:result>
		</xsl:when>
		<!-- French -->
		<xsl:when test="$lang = 'fr'">
			<func:result>fr</func:result>
		</xsl:when>
		<!-- Error -->
		<xsl:otherwise>
			
		</xsl:otherwise>
	</xsl:choose>
</func:function>

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

<xsl:template match="item" mode="dibs_flexwin">
	<div>
		<form action="https://payment.architrade.com/paymentweb/mobiwin.action" method="post">
			<div>
				<input type="hidden" name="test" value="yes" />
				
				<!-- DIBS Required Data -->
				<input type="hidden" name="merchant" value="{account}" />
				<input type="hidden" name="callbackurl" value="{/root/system/protocol}://{/root/system/host}/callback/dibs.php" />
				<input type="hidden" name="accepturl" value="{/root/system/protocol}://{/root/system/host}/accept.php" />
				<input type="hidden" name="cancelurl" value="{/root/transaction/cancel-url}" />
				<input type="hidden" name="amount" value="{/root/transaction/amount}" />
				<input type="hidden" name="currency" value="{currency}" />
				<input type="hidden" name="orderid" value="{/root/transaction/@id}" />
				<input type="hidden" name="uniqueoid" value="true" />
				<input type="hidden" name="capturenow" value="true" />
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
				
				<!-- Card Data -->
				<input type="hidden" name="paytype" value="{func:transCard(@id)}" />
				
				<!--
				  - The colspan attribute in the table below ensures that the page is rendered correctly on the Nokia 6230.
				  - Nokia 6230 assigns the same width to all table columns but by using the colspan attribute (eventhough it really isn't needed)
				  - the phone will assign 25% of the screen width to the card logo and 75% of the screen width to the card name.
				  -->
				<table class="mPoint_card">
				<tr>
					<td><img src="{/root/system/protocol}://{/root/system/host}/img/card_{@id}_{/root/system/session/@id}.jpg" width="{width}" height="{height}" alt="" /></td>
					<td colspan="3"><input type="submit" value="{name}" class="button" /></td>
				</tr>
				</table>
			</div>
		</form>
	</div>
</xsl:template>

<xsl:template match="item" mode="dibs_custom">
	<div>
		<form action="https://payment.architrade.com/shoppages/{account}/payment.pml" method="post">
			<div>
				<input type="hidden" name="test" value="yes" />
				
				<!-- DIBS Required Data -->
				<input type="hidden" name="merchant" value="{account}" />
				<input type="hidden" name="callbackurl" value="{/root/system/protocol}://{/root/system/host}/callback/dibs.php" />
				<input type="hidden" name="accepturl" value="{/root/system/protocol}://{/root/system/host}/accept.php" />
				<input type="hidden" name="cancelurl" value="{/root/transaction/cancel-url}" />
				<input type="hidden" name="amount" value="{/root/transaction/amount}" />
				<input type="hidden" name="currency" value="{currency}" />
				<input type="hidden" name="orderid" value="{/root/transaction/@id}" />
				<input type="hidden" name="uniqueoid" value="true" />
				<input type="hidden" name="capturenow" value="true" />
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
				
				<!-- Card Data -->
				<input type="hidden" name="paytype" value="{func:transCard(@id)}" />
				
				<!-- Shared Data -->
				<input type="hidden" name="client" value="{/root/client-config/name}" />
				
				<!-- Payment Page Data -->
				<input type="hidden" name="pay_title" value="{/root/payment/title}" />
				<input type="hidden" name="pay_card_width" value="{width}" />
				<input type="hidden" name="pay_card_height" value="{height}" />
				<input type="hidden" name="pay_progress" value="{/root/payment/progress}" />
				<input type="hidden" name="pay_sel_card" value="{/root/payment/selected}" />
				<input type="hidden" name="pay_info" value="{/root/payment/info}" />
				<input type="hidden" name="pay_card_number" value="{/root/payment/card-number}" />
				<input type="hidden" name="pay_expiry" value="{/root/payment/expiry}" />
				<input type="hidden" name="pay_em" value="{/root/payment/expiry-month}" />
				<input type="hidden" name="pay_ey" value="{/root/payment/expiry-year}" />
				<input type="hidden" name="pay_cvc" value="{/root/payment/cvc}" />
				<input type="hidden" name="pay_cvc_help" value="{/root/payment/cvc-help}" />
				<input type="hidden" name="pay_submit" value="{/root/payment/submit}" />
				
				<!-- Accept Page Data -->
				<input type="hidden" name="acc_title" value="{/root/accept/title}" />
				<input type="hidden" name="acc_mpoint" value="{/root/accept/mpoint}" />
				<input type="hidden" name="acc_mpoint_width" value="{/root/accept/mpoint-logo/width}" />
				<input type="hidden" name="acc_mpoint_height" value="{/root/accept/mpoint-logo/height}" />
				<input type="hidden" name="acc_status" value="{/root/accept/status}" />
				<input type="hidden" name="acc_txn_id_label" value="{/root/accept/txn-id}" />
				<input type="hidden" name="acc_order_no" value="{/root/transaction/order-id}" />
				<input type="hidden" name="acc_order_no_label" value="{/root/accept/order-id}" />
				<input type="hidden" name="acc_price" value="{/root/transaction/price}" />
				<input type="hidden" name="acc_price_label" value="{/root/accept/price}" />
				<input type="hidden" name="acc_sms_receipt" value="{/root/client-config/sms-receipt}" />
				<input type="hidden" name="acc_sms_receipt_text" value="{/root/accept/sms-receipt}" />
				<input type="hidden" name="acc_email_receipt" value="{/root/client-config/sms-receipt}" />
				<input type="hidden" name="acc_email_receipt_text" value="{/root/accept/email-receipt}" />
				<input type="hidden" name="acc_email_url" value="{func:constLink('/email.php')}" />
				<input type="hidden" name="acc_accept_url"  value="{/root/transaction/accept-url}" />
				<input type="hidden" name="acc_continue" value="{/root/accept/continue}" />
				<!-- Transfer Custom Variables -->
				<xsl:for-each select="/root/accept/client-vars/item">
					<input type="hidden" name="acc_client_vars_names_{position()}" value="{name}" />
					<input type="hidden" name="acc_client_vars_data_{position()}" value="{value}" />
				</xsl:for-each>
				
				<!--
				  - The colspan attribute in the table below ensures that the page is rendered correctly on the Nokia 6230.
				  - Nokia 6230 assigns the same width to all table columns but by using the colspan attribute (eventhough it really isn't needed)
				  - the phone will assign 25% of the screen width to the card logo and 75% of the screen width to the card name.
				  -->
				<table class="mPoint_card">
				<tr>
					<td><img src="{/root/system/protocol}://{/root/system/host}/img/card_{@id}_{/root/system/session/@id}.jpg" width="{width}" height="{height}" alt="" /></td>
					<td colspan="3"><input type="submit" value="{name}" class="button" /></td>
				</tr>
				</table>
			</div>
		</form>
	</div>
</xsl:template>

</xsl:stylesheet>