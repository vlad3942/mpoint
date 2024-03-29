<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="html" indent="no" media-type="text/html" doctype-public="HTML" omit-xml-declaration="yes" standalone="yes" />
<xsl:include href="global.xsl"/>
	
<xsl:template match="/">
	<html xml:lang="{func:transLanguage(/root/transaction/language)}">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-15" />
		<meta http-equiv="Cache-Control" content="no-cache" />
		<meta http-equiv="Content-Style-Type" content="text/css" />	
		<meta http-equiv="Content-Script-Type" content="text/javascript" />
		<meta name="format-detection" content="telephone=no" />
		<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<title><xsl:value-of select="/root/title" /></title>
		<link href="https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,400,300,600,700" rel="stylesheet" type="text/css" />
		<link href="/css/velocity/style.css" type="text/css" rel="stylesheet" />
		<link href="/css/velocity/layout.css" type="text/css" rel="stylesheet" />
		<link href="{/root/transaction/css-url}" type="text/css" rel="stylesheet" />
		<script src="https://code.jquery.com/jquery-3.1.0.min.js" integrity="sha256-cCueBR6CsyA4/9szpPfrX3s49M9vUU5BgtiJj06wt/s=" crossorigin="anonymous"></script>
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
		<script>
			(function(i, s, o, g, r, a, m)
			{
			    i['GoogleAnalyticsObject'] = r;
			    i[r] = i[r] || function()
				{
			        (i[r].q = i[r].q || []).push(arguments)
			    }, i[r].l = 1 * new Date();
			    a = s.createElement(o),
			        m = s.getElementsByTagName(o)[0];
			    a.async = 1;
			    a.src = g;
			    m.parentNode.insertBefore(a, m)
			})(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

			ga('create', 'UA-83051042-1', 'auto');
			ga('send', 'pageview');
		</script>
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
		<script type="text/javascript" src="/inc/iScroll.js"></script>
		<script type="text/javascript" src="/inc/menu.js"></script>
		<script type="text/javascript" src="/inc/mpoint.js"></script>
		<script type="text/javascript" src="/inc/card.js"></script>
	</head>
	<body>
		<div class="header">
			<!-- Display Client Logo using the provided URL -->
			<xsl:if test="string-length(/root/transaction/logo/url) &gt; 0">
				<div class="logo">
					<img src="{/root/transaction/logo/url}" width="{/root/transaction/logo/width}" height="{/root/transaction/logo/height}" alt="- {/root/client-config/name} -" />
				</div>
			</xsl:if>
			<div class="client-name"><xsl:value-of select="/root/client-config/name" /></div>
		</div>
		<xsl:apply-templates />
		<!-- Hidden Data Fields Start -->
		<div class="loader-screen">
			<div class='loader'><div></div></div>
		</div>
	</body>
	</html>
</xsl:template>

</xsl:stylesheet>