<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:include href="../web.xsl" />

<xsl:template match="/root/content">
	<div id="top-up">
		<h1><xsl:value-of select="headline" /></h1>
		<br />
		<h2>
			<label><xsl:value-of select="labels/balance" /></label>
			<xsl:value-of select="account/funds" />
		</h2>
		<table align="center">
		<tr>
			<td id="deposit-options">
				<xsl:apply-templates select="deposits/option[amount + //account/balance &lt;= //country-config/max-balance]" />
			</td>
		</tr>
		</table>
	</div>
</xsl:template>

<xsl:template match="/root/content/deposits/option">
	<!-- List Deposit Options -->
	<xsl:variable name="css">
		<xsl:choose>
			<!-- Even row -->
			<xsl:when test="position() mod 2 = 0">
				<xsl:text>Even</xsl:text>
			</xsl:when>
			<!-- Uneven row -->
			<xsl:otherwise>
				<xsl:text>Uneven</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:variable>
		
	<form id="purchase-top-up" name="purchase-top-up" action="/buy/topup.php" method="post" target="Top-Up" onsubmit="javascript:window.open('', 'Top-Up', 'width=450,height=600,status=yes,resizable=no,scrollbars=no');">
		<div>
			<!-- mPoint client parameters -->
			<input type="hidden" name="clientid" value="{//client-config/@id}" />
			<xsl:if test="//account-config/@id &gt; 0">
				<input type="hidden" name="account" value="{//account-config/@id}" />
			</xsl:if>
			<!-- mPoint transaction parameters -->
			<input type="hidden" name="amount" value="{amount}" />
			<!-- mPoint end-user parameters -->
			<input type="hidden" name="mobile" value="{//account/mobile}" />
			<xsl:if test="//account/operator &gt; 0">
				<input type="hidden" name="operator" value="{//account/operator}" />
			</xsl:if>
			<xsl:if test="string-length(//account/email) &gt; 0">
				<input type="hidden" name="email" value="{//account/email}" />	
			</xsl:if>
			<!-- mPoint customization parameters -->
			<input type="hidden" name="logo-url" value="{//client-config/logo-url}" />
			<input type="hidden" name="css-url" value="{//client-config/css-url}" />
			<!-- <input type="hidden" name="language" value="{/root/system/language}" />  -->
			<input type="hidden" name="mode" value="{//client-config/@mode}" />
		</div>
		<div class="{concat('mPoint_', $css) }">
			<table align="center">
			<tr>
				<td><img src="/img/topup.png" width="25" height="25" alt="" /></td>
				<td><input type="submit" value="{price}" class="{concat('mPoint_', $css, '_Card_Button') }" /></td>
			</tr>
			</table>
		</div>
	</form>
</xsl:template>
</xsl:stylesheet>