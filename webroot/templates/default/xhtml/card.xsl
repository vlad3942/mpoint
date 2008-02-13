<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:include href="header.xsl"/>

<xsl:template match="/root">
	<div id="progress" class="mPoint_info">
		<xsl:value-of select="labels/progress" />
		<br /><br />
	</div>
			
	<div class="mPoint_label"><xsl:value-of select="labels/info" /></div>
	<xsl:for-each select="cards/item">
		<xsl:choose>
			<!-- DIBS -->
			<xsl:when test="@pspid = 1">
				<xsl:apply-templates select="."  mode="dibs" />
			</xsl:when>
			<!-- Error -->
			<xsl:otherwise>
				
			</xsl:otherwise>
		</xsl:choose>
	</xsl:for-each>
</xsl:template>

<xsl:template match="item" mode="dibs">
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
				<!-- Perform Language conversion -->
				<xsl:choose>
					<!-- British English -->
					<xsl:when test="/root/system/language = 'uk'">
						<input type="hidden" name="lang" value="en" />
					</xsl:when>
					<!-- American English -->
					<xsl:when test="/root/system/language = 'us'">
						<input type="hidden" name="lang" value="en" />
					</xsl:when>
					<!-- Danish -->
					<xsl:when test="/root/system/language = 'dk'">
						<input type="hidden" name="lang" value="da" />
					</xsl:when>
					<!-- Norwegian -->
					<xsl:when test="/root/system/language = 'no'">
						<input type="hidden" name="lang" value="no" />
					</xsl:when>
					<!-- Swedish -->
					<xsl:when test="/root/system/language = 'se'">
						<input type="hidden" name="lang" value="sv" />
					</xsl:when>
					<!-- German -->
					<xsl:when test="/root/system/language = 'ge'">
						<input type="hidden" name="lang" value="de" />
					</xsl:when>
					<!-- Spanish -->
					<xsl:when test="/root/system/language = 'es'">
						<input type="hidden" name="lang" value="es" />
					</xsl:when>
					<!-- Finish -->
					<xsl:when test="/root/system/language = 'fi'">
						<input type="hidden" name="lang" value="fi" />
					</xsl:when>
					<!-- French -->
					<xsl:when test="/root/system/language = 'fr'">
						<input type="hidden" name="lang" value="fr" />
					</xsl:when>
					<!-- Error -->
					<xsl:otherwise>
						
					</xsl:otherwise>
				</xsl:choose>
				
				<!-- mPoint Required Data -->
				<input type="hidden" name="width" value="{/root/transaction/logo/width}" />
				<input type="hidden" name="height" value="{/root/transaction/logo/height}" />
				<input type="hidden" name="{/root/system/session}" value="{/root/system/session/@id}" />
				<input type="hidden" name="format" value="xhtml" />
				
				<!-- Card Data -->
				<xsl:choose>
					<!-- American Express -->
					<xsl:when test="@id = 1">
						<input type="hidden" name="paytype" value="AMEX" />
					</xsl:when>
					<!-- Dankort -->
					<xsl:when test="@id = 2">
						<input type="hidden" name="paytype" value="DK" />
					</xsl:when>
					<!-- Diners Club -->
					<xsl:when test="@id = 3">
						<input type="hidden" name="paytype" value="DIN" />
					</xsl:when>
					<!-- EuroCard -->
					<!--
					<xsl:when test="@id = 4">
						<input type="hidden" name="paytype" value="AMEX" />
					</xsl:when>
					-->
					<!-- JCB -->
					<xsl:when test="@id = 5">
						<input type="hidden" name="paytype" value="JCB" />
					</xsl:when>
					<!-- Maestro -->
					<xsl:when test="@id = 6">
						<input type="hidden" name="paytype" value="MTRO" />
					</xsl:when>
					<!-- Master Card -->
					<xsl:when test="@id = 7">
						<input type="hidden" name="paytype" value="MC" />
					</xsl:when>
					<!-- VISA -->
					<xsl:when test="@id = 8">
						<input type="hidden" name="paytype" value="VISA" />
					</xsl:when>
					<!-- VISA Electron -->
					<xsl:when test="@id = 9">
						<input type="hidden" name="paytype" value="ELEC" />
					</xsl:when>
					<!-- Error -->
					<xsl:otherwise>
						
					</xsl:otherwise>
				</xsl:choose>
				
				<img src="{/root/system/protocol}://{/root/system/host}/img/card_{@id}" width="{width}" height="{height}" alt="- {name} -" border="0" />
				<input type="submit" value="{name}" class="button" />
			</div>
		</form>
	</div>
</xsl:template>

</xsl:stylesheet>