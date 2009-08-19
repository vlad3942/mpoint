<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes" media-type="text/html" doctype-public="-//WAPFORUM//DTD XHTML Mobile 1.0//EN" doctype-system="http://www.openmobilealliance.org/DTD/xhtml-mobile10.dtd" omit-xml-declaration="no" />
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

	<div id="progress" class="mPoint_Info">
		<xsl:value-of select="labels/progress" />
		<br /><br />
	</div>
	
	<!-- Display Status Messages -->
	<xsl:apply-templates select="messages" />
	
	<div id="my-account">
		<form action="{func:constLink('/cpm/sys/pay_account.php') }" method="post">
			<div>
				<input type="hidden" name="euaid" value="{stored-cards/@accountid}" />
				<input type="hidden" name="cardtype" value="11" />
				<xsl:choose>
					<!-- Prepaid Account available -->
					<xsl:when test="account/balance &gt;= transaction/amount and (transaction/@type &lt; 100 or transaction/@type &gt; 109)">
						<input type="hidden" name="prepaid" value="true" />
					</xsl:when>
					<xsl:otherwise>
						<input type="hidden" name="prepaid" value="false" />
					</xsl:otherwise>
				</xsl:choose>
			</div>
			<!-- Price -->
			<div id="price">
				<span class="mPoint_Label"><xsl:value-of select="labels/price" />:</span>
				<xsl:value-of select="transaction/price" />
			</div>
			<div><xsl:value-of select="labels/info" /></div>
			
			<!-- Not Account Top-Up -->
			<xsl:if test="transaction/@type &lt; 100 or transaction/@type &gt; 109">
				<!-- Prepaid Account -->
				<div id="prepaid">
					<div class="mPoint_Label {$css}"><xsl:value-of select="labels/my-account" />:</div>
					<!--
					  - The colspan attribute in the table below ensures that the page is rendered correctly on the Nokia 6230.
					  - Nokia 6230 assigns the same width to all table columns but by using the colspan attribute (eventhough it really isn't needed)
					  - the phone will assign 25% of the screen width to the card logo and 75% of the screen width to the account balance.
					  -->
					<table>
					<tr>
						<td>
							<xsl:choose>
							<!-- End-User does not have an account -->
							<xsl:when test="string-length(account/@id) = 0">
								<a id="top-up" href="{func:constLink('/new/?msg=2') }"><xsl:value-of select="labels/create-account" /></a>
							</xsl:when>
							<!-- Insufficient Funds -->
							<xsl:when test="account/balance &lt; transaction/amount">
								<a id="top-up" href="{func:constLink('/shop/topup.php?msg=1') }"><xsl:value-of select="labels/top-up" /></a>
							</xsl:when>
							<!-- Sufficient Funds -->
							<xsl:otherwise>
								<xsl:choose>
								<xsl:when test="count(stored-cards/card) = 0">
									<input type="hidden" name="cardid" value="-1" />
								</xsl:when>
								<xsl:when test="count(session/cardid) = 0 or session/cardid = -1">
									<input type="radio" name="cardid" value="-1" checked="true" />
								</xsl:when>
								<xsl:otherwise>
									<input type="radio" name="cardid" value="-1" />
								</xsl:otherwise>
								</xsl:choose>
							</xsl:otherwise>
							</xsl:choose>
						</td>
	
						<td><img src="{/root/system/protocol}://{/root/system/host}/img/{account/logo-width}x{account/logo-height}_card_11_{/root/system/session/@id}.png" width="{account/logo-width}" height="{account/logo-height}" alt="" /></td>
						<td colspan="3"><xsl:value-of select="labels/balance" />: <xsl:value-of select="account/funds" /></td>
					</tr>
					</table>
				</div>
			</xsl:if>
			<!-- Stored Credit Cards -->
			<div id="cardinfo">
				<xsl:choose>
				<xsl:when test="count(stored-cards/card[client/@id = //client-config/@id]) = 1">
					<div class="mPoint_Label"><xsl:value-of select="labels/stored-card" />:</div>
					<xsl:apply-templates select="stored-cards/card[client/@id = //client-config/@id]" />
				</xsl:when>
				<xsl:when test="count(stored-cards/card[client/@id = //client-config/@id]) &gt; 1">
					<div class="mPoint_Label"><xsl:value-of select="labels/multiple-stored-cards" />:</div>
					<xsl:apply-templates select="stored-cards/card[client/@id = //client-config/@id]" />
				</xsl:when>
				</xsl:choose>
				<div id="password">
					<div class="mPoint_Label"><xsl:value-of select="labels/password" />:</div>
					<input type="password" name="pwd" value="" /> 
				</div>
			</div>
			<!-- Complete Payment -->
			<div>
				<input type="submit" value="{labels/submit}" class="mPoint_Button" />
			</div>
		</form>
	</div>
</xsl:template>

<xsl:template match="card">
	<!--
	  - The colspan attribute in the table below ensures that the page is rendered correctly on the Nokia 6230.
	  - Nokia 6230 assigns the same width to all table columns but by using the colspan attribute (eventhough it really isn't needed)
	  - the phone will assign 20% of the screen width to the card logo and 60% of the screen width to the masked card number
	  - and 20% of the screen width to the card expiry date.
	  -->
	<table>
	<tr>
		<td>
			<xsl:choose>
			<!-- Card previously selected by user -->
			<xsl:when test="(count(//stored-cards/card[client/@id = //client-config/@id]) &gt; 1 or //account/balance &gt;= //transaction/amount) and //session/cardid = @id and (//transaction/@type &lt; 100 or //transaction/@type &gt; 109)">
				<input type="radio" name="cardid" value="{@id}" checked="true" />
			</xsl:when>
			<!-- Card is user's preferred and no other card has been selected nor is there enough money on the prepaid account to pay for the transaction -->
			<xsl:when test="count(//stored-cards/card[client/@id = //client-config/@id]) &gt; 1 and @preferred = 'true' and count(//session/cardid) = 0 and //account/balance &lt; //transaction/amount">
				<input type="radio" name="cardid" value="{@id}" checked="true" />
			</xsl:when>
			<!-- Card is user's preferred and no other card has been selected and the transaction type is an Account Top-Up -->
			<xsl:when test="count(//stored-cards/card[client/@id = //client-config/@id]) &gt; 1 and @preferred = 'true' and count(//session/cardid) = 0 and //transaction/@type &gt;= 100 and //transaction/@type &lt;= 109">
				<input type="radio" name="cardid" value="{@id}" checked="true" />
			</xsl:when>
			<!-- Only one card has been stored and there isn't enough money on the prepaid account to pay for the transaction -->
			<xsl:when test="count(//stored-cards/card[client/@id = //client-config/@id]) = 1 and //account/balance &lt; //transaction/amount">
				<input type="hidden" name="cardid" value="{@id}" />
			</xsl:when>
			<!-- Only one card has been stored and the transaction type is an Account Top-Up -->
			<xsl:when test="count(//stored-cards/card[client/@id = //client-config/@id]) = 1 and //transaction/@type &gt;= 100 and //transaction/@type &lt;= 109">
				<input type="hidden" name="cardid" value="{@id}" />
			</xsl:when>
			<xsl:otherwise>
				<input type="radio" name="cardid" value="{@id}" />
			</xsl:otherwise>
			</xsl:choose>
		
		</td>
		<td><img src="{/root/system/protocol}://{/root/system/host}/img/{logo-width}x{logo-height}_card_{type/@id}_{/root/system/session/@id}.png" width="{logo-width}" height="{logo-height}" alt="{type}" /></td>
		<td colspan="3"><xsl:value-of select="mask" /></td>
		<td class="mPoint_Info">(<xsl:value-of select="expiry" />)</td>
	</tr>
	</table>
</xsl:template>

</xsl:stylesheet>