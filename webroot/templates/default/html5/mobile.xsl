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
		<link href="/css/mobile.css" type="text/css" rel="stylesheet" />
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
		<style>
			<xsl:choose>
			<xsl:when test="root/system/platform = 'iPhone'">
				body
				{
					height: 330px;
				}
				#messages
				{
					bottom: 115px;
				}
			</xsl:when>
			<xsl:when test="root/system/platform = 'iPad'">
				body
				{
					height: 630px;
				}
				#messages
				{
					bottom: 115px;
				}
			</xsl:when>
			<xsl:when test="root/system/platform = 'Galaxy Tab'">
				body
				{
					height: 400px;
				}
				#content #console #data
				{
					height: 150px;
				}
				#view-details #account-info
				{
					width: 70%;
				}
				#view-details #commands
				{
					width: 30%;
				}
			</xsl:when>
			<xsl:when test="root/system/platform = 'Android' or root/system/platform = 'Skyfire'">
				body
				{
					<xsl:choose>
					<!-- UA Profile not found for Device -->
					<xsl:when test="number(root/uaprofile/height) &gt; 0 and root/uaprofile/height - 400 &gt; 0">
						height: <xsl:value-of select="root/uaprofile/height - 400" />px;
					</xsl:when>
					<xsl:otherwise>
						height: 400px;
					</xsl:otherwise>
					</xsl:choose>
				}
			</xsl:when>
			<xsl:when test="root/system/platform = 'Firefox'">
				body
				{
					<xsl:choose>
					<!-- UA Profile not found for Device -->
					<xsl:when test="number(root/uaprofile/height) &gt; 0">
						height: <xsl:value-of select="root/uaprofile/height - 400" />px;
					</xsl:when>
					<xsl:otherwise>
						height: 400px;
					</xsl:otherwise>
					</xsl:choose>
				}
                #content
				{
					<xsl:choose>
					<!-- UA Profile not found for Device -->
					<xsl:when test="number(root/uaprofile/height) &gt; 0">
						height: <xsl:value-of select="root/uaprofile/height - 450" />px;
					</xsl:when>
					<xsl:otherwise>
						height: 350px;
					</xsl:otherwise>
					</xsl:choose>
					overflow: auto;
				}
			</xsl:when>
			<xsl:otherwise>
				body
				{
					<xsl:choose>
					<!-- UA Profile not found for Device -->
					<xsl:when test="number(root/uaprofile/height) &gt; 0">
						height: <xsl:value-of select="root/uaprofile/height - 84" />px;
					</xsl:when>
					<xsl:otherwise>
						height: 420px;
					</xsl:otherwise>
					</xsl:choose>
				}
			</xsl:otherwise>
			</xsl:choose>
		</style>
		<script type="text/javascript" src="/inc/iScroll.js"></script>
		<script type="text/javascript" src="/inc/menu.js"></script>
		<script type="text/javascript" src="/inc/mpoint.js"></script>
		<script type="text/javascript">
			var myScroll;
			function loaded()
			{
				document.addEventListener('touchmove', function(e) { e.preventDefault(); }, false);
				if (myScroll == null)
				{
					myScroll = new iScroll('wrapper', { // checkDOMChanges:false,
//														snap:false,
//														momentum:true,
//														hScrollbar:false,
//														vScrollbar:true,
														useTransform:false,
														onBeforeScrollStart: function (e)
														{
															var target = e.target;
															while (target.nodeType != 1)
															{
																target = target.parentNode;
															}
															try
															{
																switch (target.tagName.toLowerCase() )
																{
																case "select":
																case "input":
																case "textarea":
//																	e.stopPropagation();
																	break;
																default:
																	e.preventDefault();
																	break;
																}
															}
															catch (ignore) { /* Ignore */ }
														}
			                                           });
				}
			}
//			document.addEventListener('DOMContentLoaded', loaded, false);
		</script>
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
			 obj_Menu = new Menu();
			 setTimeout(function () { myScroll.refresh(); }, 0);
		</script>
	</body>
	</html>
</xsl:template>

</xsl:stylesheet>