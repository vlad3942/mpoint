<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:include href="../web.xsl" />

<xsl:template match="/root/content">
	<!-- Hidden Data Fields Start -->
	<span id="topup-data" class="hidden-data">
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
	</span>
	<span id="purchase-data" class="hidden-data">
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
	</span>
	<span id="transfer-data" class="hidden-data">
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
	</span>
	<!-- Hidden Data Fields End -->
	
	<div id="view-transactions">
		<h1><xsl:value-of select="headline" /></h1>
		<br />
		<table align="center">
		<tr>
			<td class="folder">
			<ul class="menu">
				<li>
					<a href="#" onclick="javascript:selectMenu(this, 'current'); document.getElementById('transaction-data').innerHTML = document.getElementById('topup-data').innerHTML;">
						<div>
							<span><xsl:value-of select="labels/topup-history" /></span>
							<img src="/img/folder.png" width="20" height="20" alt="" border="0" />
						</div>
					</a>
				</li>
				<li>
					<a class="current" href="#" onclick="javascript:selectMenu(this, 'current'); document.getElementById('transaction-data').innerHTML = document.getElementById('purchase-data').innerHTML;">
						<div>
							<span><xsl:value-of select="labels/purchase-history" /></span>
							<img src="/img/folder.png" width="20" height="20" alt="" border="0" />
						</div>
					</a>
				</li>
				<li>
					<a href="#" onclick="javascript:selectMenu(this, 'current'); document.getElementById('transaction-data').innerHTML = document.getElementById('transfer-data').innerHTML;">
						<div>
							<span><xsl:value-of select="labels/transfer-history" /></span>
							<img src="/img/folder.png" width="20" height="20" alt="" border="0" />
						</div>
					</a>
				</li>
			</ul>
			</td>
		</tr>
		<tr>
			<td><div id="transaction-data"><!-- Completed dynamically by JavaScript --></div></td>
		</tr>
		</table>
	</div>
	<script type="text/javascript">
		document.getElementById('transaction-data').innerHTML = document.getElementById('purchase-data').innerHTML;
	</script>
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

<xsl:variable name="quote-char"><xsl:text>'</xsl:text></xsl:variable>
<xsl:variable name="escaped-quote"><xsl:text>\'</xsl:text></xsl:variable>

</xsl:stylesheet>