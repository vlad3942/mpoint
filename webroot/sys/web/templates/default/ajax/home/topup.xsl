<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:include href="../web.xsl" />

<xsl:template match="/root/content">
	<span id="data-source" class="hidden-data">
		<!-- Hidden Data Fields Start -->
		<span id="emoney-topup">
			<xsl:apply-templates select="deposits/option[amount + //account/balance &lt;= //country-config/max-balance]">
				<xsl:with-param name="type-id">100</xsl:with-param>
			</xsl:apply-templates>
		</span>
		<span id="points-topup">
			<xsl:apply-templates select="deposits/option[amount + //account/balance &lt;= //country-config/max-balance]">
				<xsl:with-param name="type-id">102</xsl:with-param>
			</xsl:apply-templates>
		</span>
		<!-- Hidden Data Fields End -->
	</span>
	
	<div id="top-up">
		<h1><xsl:value-of select="headline" /></h1>
		<br />
		<h2>
			<label><xsl:value-of select="labels/balance" /></label>
			<xsl:variable name="points">
			<xsl:choose>
				<xsl:when test="account/points &lt; 10000"><xsl:value-of select="account/points" /></xsl:when>
				<xsl:otherwise><xsl:value-of select="concat(substring(account/points, 1, string-length(account/points) - 3), '.', substring(account/points, string-length(account/points) - 2) )" /></xsl:otherwise>
			</xsl:choose>
			</xsl:variable>
			<xsl:value-of select="concat($points, ' ', account/points/@currency, ' / ', account/funds)" />
		</h2>
		<table align="center">
		<tr>
			<td class="folder">
			<ul class="menu">
				<li>
					<a class="current" href="#" onclick="javascript:selectMenu(this, 'current'); changeFolder(document.getElementById('data-source'), document.getElementById('deposit-options'), document.getElementById('points-topup') );">
						<div>
							<span><xsl:value-of select="labels/points-topup" /></span>
							<img src="/img/folder.png" width="20" height="20" alt="" border="0" />
						</div>
					</a>
				</li>
				<li>
					<a href="#" onclick="javascript:selectMenu(this, 'current'); changeFolder(document.getElementById('data-source'), document.getElementById('deposit-options'), document.getElementById('emoney-topup') );">
						<div>
							<span><xsl:value-of select="labels/emoney-topup" /></span>
							<img src="/img/folder.png" width="20" height="20" alt="" border="0" />
						</div>
					</a>
				</li>
			</ul>
			</td>
		</tr>
		<tr>
			<td id="deposit-options"><!-- Completed dynamically by JavaScript --></td>
		</tr>
		</table>
		
		<script type="text/javascript">
			changeFolder(document.getElementById('data-source'), document.getElementById('deposit-options'), document.getElementById('points-topup') );
		</script>
	</div>
</xsl:template>

<xsl:template match="/root/content/deposits/option">
	<xsl:param name="type-id" />
	
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
	<xsl:variable name="points" select="amount * 10" />
		
	<form id="purchase-top-up" name="purchase-top-up" action="/buy/topup.php" method="post" target="Top-Up" onsubmit="javascript:window.open('', 'Top-Up', 'width=429,height=600,status=yes,resizable=no,scrollbars=no');">
		<div>
			<input type="hidden" name="typeid" value="{$type-id}" />
			<!-- mPoint client parameters -->
			<input type="hidden" name="clientid" value="{//client-config/@id}" />
			<xsl:if test="//account-config/@id &gt; 0">
				<input type="hidden" name="account" value="{//account-config/@id}" />
			</xsl:if>
			<!-- mPoint transaction parameters -->
			<input type="hidden" name="amount" value="{amount}" />
			<input type="hidden" name="points" value="{$points}" />
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
				<xsl:choose>
				<!-- e-Money Top-Up -->
				<xsl:when test="$type-id = 100"></xsl:when>
				<!-- Points Top-Up -->
				<xsl:when test="$type-id = 102">
					<xsl:variable name="pts">
						<xsl:choose>
							<xsl:when test="$points &lt; 1000"><xsl:value-of select="$points" /></xsl:when>
							<xsl:otherwise><xsl:value-of select="concat(substring($points, 1, string-length($points) - 3), '.', substring($points, string-length($points) - 2) )" /></xsl:otherwise>
						</xsl:choose>
					</xsl:variable>
					<td><input type="submit" value="{concat($pts, ' ', //account/points/@currency)}" class="{concat('mPoint_', $css, '_Card_Button') }" style="width:120px;" /></td>
					<td>-</td>
				</xsl:when>
				</xsl:choose>
				<td><input type="submit" value="{price}" class="{concat('mPoint_', $css, '_Card_Button') }" /></td>
			</tr>
			</table>
		</div>
	</form>
</xsl:template>
</xsl:stylesheet>