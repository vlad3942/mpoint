<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes" media-type="text/html" doctype-public="-//WAPFORUM//DTD XHTML Mobile 1.0//EN" doctype-system="http://www.openmobilealliance.org/DTD/xhtml-mobile10.dtd" omit-xml-declaration="no" />
<xsl:include href="global.xsl"/>
	
<xsl:template match="/">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{func:transLanguage(/root/transaction/language)}">
	<head>
		<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=ISO-8859-15" />
		<meta http-equiv="Cache-Control" content="max-age=86400" />
		<meta http-equiv="Content-Style-Type" content="text/css" />	
		<title><xsl:value-of select="/root/title" /></title>
		<link href="{/root/transaction/css-url}" type="text/css" rel="stylesheet" />
		<!-- Pre-load pages -->
		<xsl:choose>
			<!-- Select Credit Card -->
			<xsl:when test="/root/system/file = 'overview.php'">
				<!-- Physical Payment Flow -->
				<xsl:if test="/root/client-config/@flow-id = 2">
					<link href="{func:constLink('/shop/delivery.php')}" rel="next" type="application/xhtml+xml" />
				</xsl:if>
				<link href="{func:constLink('card.php')}" rel="next" type="application/xhtml+xml" />
			</xsl:when>
			<!-- Purchase Products -->
			<xsl:when test="/root/system/file = 'products.php'">
				<link href="{func:constLink('delivery.php')}" rel="next" type="application/xhtml+xml" />
			</xsl:when>
			<!-- Delivery Information -->
			<xsl:when test="/root/system/file = 'delivery.php'">
				<link href="{func:constLink('shipping.php')}" rel="next" type="application/xhtml+xml" />
			</xsl:when>
			<!-- Shipping Information -->
			<xsl:when test="/root/system/file = 'shipping.php'">
				<link href="{func:constLink('/overview.php')}" rel="next" type="application/xhtml+xml" />
			</xsl:when>
			<!-- E-Mail Receipt -->
			<xsl:when test="/root/system/file = 'accept.php'">
				<link href="{func:constLink('email.php')}" rel="next" type="application/xhtml+xml" />
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
	</body>
	</html>
</xsl:template>

</xsl:stylesheet>