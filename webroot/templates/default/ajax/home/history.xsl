<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:include href="../web.xsl" />

<xsl:template match="/root/content">
	<div id="view-transactions">
		<h1><xsl:value-of select="headline" /></h1>
		
		<table align="center">
		<tr>
			<td id="topup-history">
			<h2><xsl:value-of select="labels/topup-history" /></h2>
			<table cellpadding="0" cellspacing="0">
				<tr class="mPoint_Even">
					<td class="label"><xsl:value-of select="labels/id" /></td>
					<td class="label"><xsl:value-of select="labels/mpointid" /></td>
					<td class="label"><xsl:value-of select="labels/amount" /></td>
					<td class="label"><xsl:value-of select="labels/timestamp" /></td>
				</tr>
				<xsl:apply-templates select="history/transaction[@type = 1000]" mode="topup" />
			</table>
			</td>
			<td id="purchase-history">
			<h2><xsl:value-of select="labels/purchase-history" /></h2>
			<table cellpadding="0" cellspacing="0">
				<tr class="mPoint_Even">
					<td class="label"><xsl:value-of select="labels/id" /></td>
					<td class="label"><xsl:value-of select="labels/mpointid" /></td>
					<td class="label"><xsl:value-of select="labels/card" /></td>
					<td class="label"><xsl:value-of select="labels/price" /></td>
					<td class="label"><xsl:value-of select="labels/client" /></td>
					<td class="label"><xsl:value-of select="labels/orderid" /></td>
					<td class="label"><xsl:value-of select="labels/timestamp" /></td>
				</tr>
				<xsl:apply-templates select="history/transaction[@type = 1001]" mode="purchase" />
			</table>
			</td>
			<td id="transfer-history">
			<h2><xsl:value-of select="labels/transfer-history" /></h2>
			<table cellpadding="0" cellspacing="0">
				<tr class="mPoint_Even">
					<td class="label"><xsl:value-of select="labels/id" /></td>
					<td class="label"><xsl:value-of select="labels/sender" /></td>
					<td class="label"><xsl:value-of select="labels/recipient" /></td>
					<td class="label"><xsl:value-of select="labels/amount" /></td>
					<td class="label"><xsl:value-of select="labels/timestamp" /></td>
				</tr>
				<xsl:apply-templates select="history/transaction[@type = 1002]" mode="transfer" />
			</table>
			</td>
		</tr>
		</table>
	</div>
</xsl:template>

<xsl:template match="transaction" mode="topup">
	<xsl:variable name="css">
		<xsl:choose>
			<!-- Even row -->
			<xsl:when test="position() mod 2 = 0">
				<xsl:text>mPoint_Even</xsl:text>
			</xsl:when>
			<!-- Uneven row -->
			<xsl:otherwise>
				<xsl:text>mPoint_Uneven</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:variable>
	
	<tr class="{$css}">
		<td class="mPoint_Number"><xsl:value-of select="@id" /></td>
		<td class="mPoint_Number"><xsl:value-of select="@mpointid" /></td>
		<td class="mPoint_Number"><xsl:value-of select="price" /></td>
		<td><xsl:value-of select="timestamp" /></td>
	</tr>
</xsl:template>

<xsl:template match="transaction" mode="purchase">
	<xsl:variable name="css">
		<xsl:choose>
			<!-- Even row -->
			<xsl:when test="position() mod 2 = 0">
				<xsl:text>mPoint_Even</xsl:text>
			</xsl:when>
			<!-- Uneven row -->
			<xsl:otherwise>
				<xsl:text>mPoint_Uneven</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:variable>
	
	<tr class="{$css}">
		<td class="mPoint_Number"><xsl:value-of select="@id" /></td>
		<td class="mPoint_Number"><xsl:value-of select="@mpointid" /></td>
		<td><img src="/img/36x23_card_{card/@id}_{/root/system/session/@id}.png" width="36" height="23" alt="- {card} -" /></td>
		<td class="mPoint_Number"><xsl:value-of select="price" /></td>
		<td><xsl:value-of select="client" /></td>
		<td><xsl:value-of select="orderid" /></td>
		<td><xsl:value-of select="timestamp" /></td>
	</tr>
</xsl:template>

<xsl:template match="transaction" mode="transfer">
	<xsl:variable name="css">
		<xsl:choose>
			<!-- Even row -->
			<xsl:when test="position() mod 2 = 0">
				<xsl:text>mPoint_Even</xsl:text>
			</xsl:when>
			<!-- Uneven row -->
			<xsl:otherwise>
				<xsl:text>mPoint_Uneven</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:variable>
	
	<tr class="{$css}">
		<td class="mPoint_Number"><xsl:value-of select="@id" /></td>
		<td>
			<xsl:value-of select="from/name" />
			<xsl:if test="string-length(from/name) &gt; 0">
				<br />
			</xsl:if>
			<xsl:choose>
			<xsl:when test="from/mobile &gt; 0">
				<xsl:value-of select="from/mobile" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="from/email" />
			</xsl:otherwise>
			</xsl:choose>
		</td>
		<td>
			<xsl:value-of select="to/name" />
			<xsl:if test="string-length(to/name) &gt; 0">
				<br />
			</xsl:if>
			<xsl:choose>
			<xsl:when test="to/mobile &gt; 0">
				<xsl:value-of select="to/mobile" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="to/email" />
			</xsl:otherwise>
			</xsl:choose>
		</td>
		<td class="mPoint_Number"><xsl:value-of select="price" /></td>
		<td><xsl:value-of select="timestamp" /></td>
	</tr>
</xsl:template>

</xsl:stylesheet>