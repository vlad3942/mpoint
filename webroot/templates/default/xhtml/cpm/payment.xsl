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
		<table>
		<tr>
			<td></td>
			<td><xsl:value-of select="labels/progress" /></td>
			<td id="link">
				<a href="{func:constLink('/pay/card.php') }" style="background-image:url('{system/protocol}://{system/host}/img/new.png'); background-repeat:no-repeat;">
					<xsl:value-of select="labels/add-card" />
				</a>
			</td>
		</tr>
		</table>
	</div>
	
	<!-- Display Status Messages -->
	<xsl:apply-templates select="messages" />
	
	<div id="my-account">
		<form action="{func:constLink('/cpm/sys/pay_account.php') }" method="post">
			<div>
				<input type="hidden" name="euaid" value="{account/@id}" />
				<input type="hidden" name="cardtype" value="11" />
				<xsl:choose>
					<!-- Prepaid Account available -->
					<xsl:when test="(account/balance &gt;= transaction/amount or count(stored-cards/card[client/@id = //client-config/@id]) = 0) and (transaction/@type &lt; 100 or transaction/@type &gt; 109)">
						<input type="hidden" name="prepaid" value="true" />
					</xsl:when>
					<xsl:otherwise>
						<input type="hidden" name="prepaid" value="false" />
					</xsl:otherwise>
				</xsl:choose>
			</div>
			<div id="outer-border">
				<!-- Price -->
				<div id="price">
					<span class="mPoint_Label"><xsl:value-of select="labels/price" />:</span>
					<xsl:value-of select="transaction/price" />
				</div>
				<div class="mPoint_Help"><xsl:value-of select="labels/info" /></div>
				<div id="inner-border">
					<!-- E-Money based Prepaid Account is available and Transaction is not an Account Top-Up -->
					<xsl:if test="floor(client-config/store-card div 1) mod 2 != 1 and (transaction/@type &lt; 100 or transaction/@type &gt; 109)">
						<!-- Prepaid Account -->
						<div id="prepaid">
							<div class="mPoint_Label">
								<table>
								<tr>
									<td><xsl:value-of select="labels/my-account" />:</td>
									<xsl:choose>
									<!-- End-User does not have an account -->
									<xsl:when test="string-length(account/@id) = 0">
										<td id="top-up">
											<a href="{func:constLink('/new/?msg=2') }"><xsl:value-of select="labels/create-account" /></a>
										</td>
									</xsl:when>
									<!-- Insufficient Funds -->
									<xsl:when test="account/balance &lt; transaction/amount">
										<td id="top-up">
											<a href="{func:constLink('/shop/topup.php?msg=1') }"><xsl:value-of select="labels/top-up" /></a>
										</td>
									</xsl:when>
									</xsl:choose>
								</tr>
								</table>
							</div>
							<!--
							  - The colspan attribute in the table below ensures that the page is rendered correctly on the Nokia 6230.
							  - Nokia 6230 assigns the same width to all table columns but by using the colspan attribute (eventhough it really isn't needed)
							  - the phone will assign 25% of the screen width to the card logo and 75% of the screen width to the account balance.
							  -->
							<table>
							<tr>
								<td>										
									<xsl:choose>
										<xsl:when test="count(stored-cards/card[client/@id = //client-config/@id]) = 0">
											<input type="hidden" name="cardid" value="-1" />
										</xsl:when>
										<xsl:when test="(count(session/cardid) = 0 or session/cardid = -1) and account/balance &gt;= transaction/amount">
											<input type="radio" id="cardid-prepaid" name="cardid" value="-1" checked="true" />
										</xsl:when>
										<xsl:otherwise>
											<input type="radio" id="cardid-prepaid" name="cardid" value="-1" />
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
						<xsl:if test="@single-sign-on != 'true' or string-length(transaction/auth-url) = 0">
							<div id="password">
								<div class="mPoint_Label"><xsl:value-of select="labels/password" />:</div>
								<input type="password" name="pwd" value="" /> 
							</div>
						</xsl:if>
					</div>
				</div>
			</div>
			<!-- Complete Payment -->
			<div id="submit">
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
			<!-- Only one card has been stored and prepaid account has been disabled -->
			<xsl:when test="count(//stored-cards/card[client/@id = //client-config/@id]) = 1 and floor(//client-config/store-card div 1) mod 2 = 1">
				<input type="hidden" name="cardid" value="{@id}" />
			</xsl:when>
			<!-- Only one card has been stored and there isn't enough money on the prepaid account to pay for the transaction -->
			<xsl:when test="count(//stored-cards/card[client/@id = //client-config/@id]) = 1 and //account/balance &lt; //transaction/amount">
				<input type="radio" id="cardid-{@id}" name="cardid" value="{@id}" checked="true" />
			</xsl:when>
			<!-- Card previously selected by user -->
			<xsl:when test="(count(//stored-cards/card[client/@id = //client-config/@id]) &gt; 1 or //account/balance &lt; //transaction/amount) and //session/cardid = @id">
				<input type="radio" id="cardid-{@id}" name="cardid" value="{@id}" checked="true" />
			</xsl:when>
			<!-- Card is user's preferred and no other card has been selected and prepaid account has been disabled or there isn't enough money on the prepaid account to pay for the transaction -->
			<xsl:when test="count(//stored-cards/card[client/@id = //client-config/@id]) &gt; 1 and @preferred = 'true' and count(//session/cardid) = 0 and (floor(//client-config/store-card div 1) mod 2 = 1 or //account/balance &lt; //transaction/amount)">
				<input type="radio" id="cardid-{@id}" name="cardid" value="{@id}" checked="true" />
			</xsl:when>
			<!-- Card is user's preferred and no other card has been selected and the transaction type is an Account Top-Up -->
			<xsl:when test="count(//stored-cards/card[client/@id = //client-config/@id]) &gt; 1 and @preferred = 'true' and count(//session/cardid) = 0 and //transaction/@type &gt;= 100 and //transaction/@type &lt;= 109">
				<input type="radio" id="cardid-{@id}" name="cardid" value="{@id}" checked="true" />
			</xsl:when>
			<!-- Only one card has been stored and the transaction type is an Account Top-Up -->
			<xsl:when test="count(//stored-cards/card[client/@id = //client-config/@id]) = 1 and //transaction/@type &gt;= 100 and //transaction/@type &lt;= 109">
				<input type="hidden" id="cardid-{@id}" name="cardid" value="{@id}" checked="true" />
			</xsl:when>
			<xsl:otherwise>
				<input type="radio" id="cardid-{@id}" name="cardid" value="{@id}" />
			</xsl:otherwise>
			</xsl:choose>
		</td>
		<td><img src="{/root/system/protocol}://{/root/system/host}/img/{logo-width}x{logo-height}_card_{type/@id}_{/root/system/session/@id}.png" width="{logo-width}" height="{logo-height}" alt="{type}" /></td>
		<xsl:choose>
		<!-- Card named -->
		<xsl:when test="string-length(name) &gt; 0">
			<td colspan="4"><xsl:value-of select="concat(name, ' - ', substring(mask, string-length(mask) - 4, 4), ' (', expiry, ')')" /></td>
		</xsl:when>
		<xsl:otherwise>
			<td colspan="3"><xsl:value-of select="mask" /></td>
			<td class="mPoint_Info">
			<xsl:if test="string-length(expiry) &gt; 0">
				(<xsl:value-of select="expiry" />)
			</xsl:if>
			</td>
		</xsl:otherwise>
		</xsl:choose>
	</tr>
	</table>
</xsl:template>

</xsl:stylesheet>