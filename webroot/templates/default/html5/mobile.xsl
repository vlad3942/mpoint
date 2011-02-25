<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="html" indent="no" media-type="text/html" doctype-public="HTML" omit-xml-declaration="yes" standalone="yes" />
<xsl:include href="global.xsl"/>
	
<xsl:template match="/">
	<html  xml:lang="{func:transLanguage(/root/transaction/language)}">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-15" />
		<meta http-equiv="Cache-Control" content="no-cache" />
		<meta http-equiv="Content-Style-Type" content="text/css" />	
		<meta http-equiv="Content-Script-Type" content="text/javascript" />
		<meta name="format-detection" content="telephone=no" />
		<meta name="viewport" content="target-densitydpi=device-dpi, width=device-width, height=device-height, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<title><xsl:value-of select="/root/title" /></title>
		<title><xsl:value-of select="/root/title" /></title>
		<link href="{/root/transaction/css-url}" type="text/css" rel="stylesheet" />
		<!-- Pre-load pages -->
		<xsl:choose>
			<!-- Order Overview -->
			<xsl:when test="/root/system/file = 'overview.php'">
				<!-- Physical Payment Flow -->
				<xsl:if test="/root/client-config/@flow-id = 2">
					<link href="{func:constLink('/shop/delivery.php')}" rel="next" type="text/html" />
				</xsl:if>
				<link href="{func:constLink('card.php')}" rel="next" type="text/html" />
			</xsl:when>
			<!-- Purchase Products -->
			<xsl:when test="/root/system/file = 'products.php'">
				<link href="{func:constLink('delivery.php')}" rel="next" type="text/html" />
			</xsl:when>
			<!-- Delivery Information -->
			<xsl:when test="/root/system/file = 'delivery.php'">
				<link href="{func:constLink('shipping.php')}" rel="next" type="text/html" />
			</xsl:when>
			<!-- Shipping Information -->
			<xsl:when test="/root/system/file = 'shipping.php'">
				<link href="{func:constLink('/overview.php')}" rel="next" type="text/html" />
			</xsl:when>
			<!-- Payment Completed -->
			<xsl:when test="/root/system/file = 'accept.php'">
				<link href="{func:constLink('email.php')}" rel="next" type="text/html" />
			</xsl:when>
		</xsl:choose>
		<script type="text/javascript" src="/inc/iscroll.js"></script>
		<xsl:choose>
			<!-- Select Card -->
			<xsl:when test="/root/system/file = 'card.php'">
				<xsl:choose>
				<!-- Too much data to fit on screen -->
				<xsl:when test="count(/root/cards/item) &gt; 5 and /root/transaction/auto-store-card = 'false' and (@id != 11 or count(/root/stored-cards/card[client/@id = /root/client-config/@id]) &gt; 0 or (floor(/root/client-config/store-card div 1) mod 2 != 1 and (/root/transaction/@type &lt; 100 or /root/transaction/@type &gt; 109) ) )">
					<style>
						#cards
						{
						<xsl:choose>
						<xsl:when test="count(/root/messages/item) &gt; 0">
							<xsl:choose>
							<xsl:when test="/root/system/platform = 'iPhone'">
								/*height: <xsl:value-of select="/root/uaprofile/height - 120" />px;*/
								height: 210px;
							</xsl:when>
							<xsl:when test="/root/system/platform = 'Droid X'">
								/*height: <xsl:value-of select="/root/uaprofile/height - 565" />px;*/
								height: 359px;
							</xsl:when>
							<xsl:when test="/root/system/platform = 'Android'">
								/*height: <xsl:value-of select="/root/uaprofile/height - 565" />px;*/
								height: 305px;
							</xsl:when>
							<xsl:otherwise>
								height: <xsl:value-of select="/root/uaprofile/height - 120" />px;
							</xsl:otherwise>
							</xsl:choose>
						</xsl:when>
						<xsl:otherwise>
							<xsl:choose>
							<xsl:when test="/root/system/platform = 'iPhone'">
								/*height: <xsl:value-of select="/root/uaprofile/height - 120" />px;*/
								height: 240px;
							</xsl:when>
							<xsl:when test="/root/system/platform = 'Droid X'">
								/*height: <xsl:value-of select="/root/uaprofile/height - 565" />px;*/
								height: 389px;
							</xsl:when>
							<xsl:when test="/root/system/platform = 'Android'">
								/*height: <xsl:value-of select="/root/uaprofile/height - 565" />px;*/
								height: 335px;
							</xsl:when>
							<xsl:otherwise>
								height: <xsl:value-of select="/root/uaprofile/height - 120" />px;
							</xsl:otherwise>
							</xsl:choose>
						</xsl:otherwise>
						</xsl:choose>	
						}
						#content
						{
							height: <xsl:value-of select="count(/root/cards/item) * /root/cards/item/logo-height + 100" />px;
						}
					</style>
				</xsl:when>
				<!-- Too much data to fit on screen -->
				<xsl:when test="count(/root/cards/item) &gt; 6">
					<style>
						#cards
						{
						<xsl:choose>
						<xsl:when test="count(/root/messages/item) &gt; 0">
							<xsl:choose>
							<xsl:when test="/root/system/platform = 'iPhone'">
								/*height: <xsl:value-of select="/root/uaprofile/height - 120" />px;*/
								height: 210px;
							</xsl:when>
							<xsl:when test="/root/system/platform = 'Droid X'">
								/*height: <xsl:value-of select="/root/uaprofile/height - 565" />px;*/
								height: 359px;
							</xsl:when>
							<xsl:when test="/root/system/platform = 'Android'">
								/*height: <xsl:value-of select="/root/uaprofile/height - 565" />px;*/
								height: 305px;
							</xsl:when>
							<xsl:otherwise>
								height: <xsl:value-of select="/root/uaprofile/height - 120" />px;
							</xsl:otherwise>
							</xsl:choose>
						</xsl:when>
						<xsl:otherwise>
							<xsl:choose>
							<xsl:when test="/root/system/platform = 'iPhone'">
								/*height: <xsl:value-of select="/root/uaprofile/height - 120" />px;*/
								height: 240px;
							</xsl:when>
							<xsl:when test="/root/system/platform = 'Droid X'">
								/*height: <xsl:value-of select="/root/uaprofile/height - 565" />px;*/
								height: 389px;
							</xsl:when>
							<xsl:when test="/root/system/platform = 'Android'">
								/*height: <xsl:value-of select="/root/uaprofile/height - 565" />px;*/
								height: 335px;
							</xsl:when>
							<xsl:otherwise>
								height: <xsl:value-of select="/root/uaprofile/height - 120" />px;
							</xsl:otherwise>
							</xsl:choose>
						</xsl:otherwise>
						</xsl:choose>	
						}
						#content
						{
							height: <xsl:value-of select="count(/root/cards/item) * /root/cards/item/logo-height + 70" />px;
						}
					</style>
				</xsl:when>
				</xsl:choose>
				<script type="text/javascript">
					var myScroll;
					function loaded()
					{
						document.addEventListener('touchmove', function(e){ e.preventDefault(); }, false);
						myScroll = new iScroll('content', { checkDOMChanges:false, snap:false, momentum:true, hScrollbar:false, vScrollbar:true });
					}
								
					// Load iScroll when DOM content is ready.
					document.addEventListener('DOMContentLoaded', loaded, false);
				</script>
			</xsl:when>
			<!-- My Account -->
			<xsl:when test="/root/system/file = 'payment.php'">
				<xsl:choose>
				<!-- Too much data to fit on screen -->
				<xsl:when test="count(/root/stored-cards/card[client/@id = //client-config/@id]) &gt; 0 and floor(/root/client-config/store-card div 1) mod 2 != 1 and (/root/transaction/@type &lt; 100 or /root/transaction/@type &gt; 109)">
					<style>
						#my-account
						{
						<xsl:choose>
						<xsl:when test="count(/root/messages/item) &gt; 0">
							<xsl:choose>
							<xsl:when test="/root/system/platform = 'iPhone'">
								/*height: <xsl:value-of select="/root/uaprofile/height - 120" />px;*/
								height: 230px;
							</xsl:when>
							<xsl:when test="/root/system/platform = 'Droid X'">
								/*height: <xsl:value-of select="/root/uaprofile/height - 565" />px;*/
								height: 379px;
							</xsl:when>
							<xsl:when test="/root/system/platform = 'Android'">
								/*height: <xsl:value-of select="/root/uaprofile/height - 565" />px;*/
								height: 325px;
							</xsl:when>
							<xsl:otherwise>
								height: <xsl:value-of select="/root/uaprofile/height - 120" />px;
							</xsl:otherwise>
							</xsl:choose>
						</xsl:when>
						<xsl:otherwise>
							<xsl:choose>
							<xsl:when test="/root/system/platform = 'iPhone'">
								/*height: <xsl:value-of select="/root/uaprofile/height - 120" />px;*/
								height: 260px;
							</xsl:when>
							<xsl:when test="/root/system/platform = 'Droid X'">
								/*height: <xsl:value-of select="/root/uaprofile/height - 565" />px;*/
								height: 409px;
							</xsl:when>
							<xsl:when test="/root/system/platform = 'Android'">
								/*height: <xsl:value-of select="/root/uaprofile/height - 565" />px;*/
								height: 355px;
							</xsl:when>
							<xsl:otherwise>
								height: <xsl:value-of select="/root/uaprofile/height - 120" />px;
							</xsl:otherwise>
							</xsl:choose>
						</xsl:otherwise>
						</xsl:choose>
						}
						#content
						{
							height: <xsl:value-of select="count(/root/stored-cards/card[client/@id = //client-config/@id]) * /root/stored-cards/card[client/@id = //client-config/@id]/logo-height + 350" />px;
						}
					</style>
				</xsl:when>
				<!-- Too much data to fit on screen -->
				<xsl:when test="count(/root/stored-cards/card[client/@id = //client-config/@id]) &gt; 3">
					<style>
						#my-account
						{
						<xsl:choose>
						<xsl:when test="count(/root/messages/item) &gt; 0">
							<xsl:choose>
							<xsl:when test="/root/system/platform = 'iPhone'">
								/*height: <xsl:value-of select="/root/uaprofile/height - 120" />px;*/
								height: 230px;
							</xsl:when>
							<xsl:when test="/root/system/platform = 'Droid X'">
								/*height: <xsl:value-of select="/root/uaprofile/height - 565" />px;*/
								height: 379px;
							</xsl:when>
							<xsl:when test="/root/system/platform = 'Android'">
								/*height: <xsl:value-of select="/root/uaprofile/height - 565" />px;*/
								height: 325px;
							</xsl:when>
							<xsl:otherwise>
								height: <xsl:value-of select="/root/uaprofile/height - 120" />px;
							</xsl:otherwise>
							</xsl:choose>
						</xsl:when>
						<xsl:otherwise>
							<xsl:choose>
							<xsl:when test="/root/system/platform = 'iPhone'">
								/*height: <xsl:value-of select="/root/uaprofile/height - 120" />px;*/
								height: 260px;
							</xsl:when>
							<xsl:when test="/root/system/platform = 'Droid X'">
								/*height: <xsl:value-of select="/root/uaprofile/height - 565" />px;*/
								height: 409px;
							</xsl:when>
							<xsl:when test="/root/system/platform = 'Android'">
								/*height: <xsl:value-of select="/root/uaprofile/height - 565" />px;*/
								height: 355px;
							</xsl:when>
							<xsl:otherwise>
								height: <xsl:value-of select="/root/uaprofile/height - 120" />px;
							</xsl:otherwise>
							</xsl:choose>
						</xsl:otherwise>
						</xsl:choose>
/*							height: <xsl:value-of select="count(/root/stored-cards/card[client/@id = //client-config/@id]) * /root/stored-cards/card[client/@id = //client-config/@id]/logo-height + 130" />px; */
						}
						#content
						{
							height: <xsl:value-of select="count(/root/stored-cards/card[client/@id = //client-config/@id]) * /root/stored-cards/card[client/@id = //client-config/@id]/logo-height + 250" />px;
						}
					</style>
				</xsl:when>
				<xsl:when test="count(/root/messages/item) &gt; 0">
					<style>
						#my-account
						{
						<xsl:choose>
						<xsl:when test="/root/system/platform = 'iPhone'">
							/*height: <xsl:value-of select="/root/uaprofile/height - 120" />px;*/
							height: 210px;
						</xsl:when>
						<xsl:when test="/root/system/platform = 'Droid X'">
							/*height: <xsl:value-of select="/root/uaprofile/height - 565" />px;*/
							height: 359px;
						</xsl:when>
						<xsl:when test="/root/system/platform = 'Android'">
							/*height: <xsl:value-of select="/root/uaprofile/height - 565" />px;*/
							height: 305px;
						</xsl:when>
						<xsl:otherwise>
							height: <xsl:value-of select="/root/uaprofile/height - 150" />px;
						</xsl:otherwise>
						</xsl:choose>
						}
						#content
						{
							height: <xsl:value-of select="count(/root/stored-cards/card[client/@id = //client-config/@id]) * /root/stored-cards/card[client/@id = //client-config/@id]/logo-height + 250" />px;
						}
					</style>
				</xsl:when>
				</xsl:choose>
				<script type="text/javascript">
					var myScroll;
					function loaded()
					{
						document.addEventListener('touchmove', function(e){ e.preventDefault(); }, false);
						myScroll = new iScroll('content', { checkDOMChanges:false, snap:false, momentum:true, hScrollbar:false, vScrollbar:true });
					}
								
					// Load iScroll when DOM content is ready.
					document.addEventListener('DOMContentLoaded', loaded, false);
				</script>
			</xsl:when>
		</xsl:choose>
	</head>
	<body>
		<!-- Display Client Logo using the provided URL -->
		<xsl:if test="string-length(/root/transaction/logo/url) &gt; 0">
			<div id="logo">
				<img src="{/root/system/protocol}://{/root/system/host}/img/{/root/transaction/logo/width}x{/root/transaction/logo/height}_client_{/root/system/session/@id}.png" width="{/root/transaction/logo/width}" height="{/root/transaction/logo/height}" alt="- {/root/client-config/name} -" />
			</div>
		</xsl:if>
		<xsl:apply-templates />
		<!-- Hidden Data Fields Start -->
		<span id="loader">
			<img src="data:image/gif;base64,{/root/system/spinner}" width="28" height="28" alt="- {/root/system/loading} -" border="0" />
			<br />
			<span><xsl:value-of select="/root/system/loading" /></span>
		</span>
		<!-- Hidden Data Fields End -->
		<script type="text/javascript">
			 document.getElementById('loader').style.visibility = 'hidden';
		</script>
	</body>
	</html>
</xsl:template>

</xsl:stylesheet>