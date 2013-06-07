<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="html" indent="no" media-type="text/html" doctype-public="HTML" omit-xml-declaration="yes" standalone="yes" />
<xsl:include href="../global.xsl"/>

<xsl:template match="/root">
	<html xml:lang="{func:transLanguage(transaction/language)}">
		<head>
		</head>
		<body>
			<script type="text/javascript">
				<xsl:variable name="code">
					<xsl:choose>
						<!-- Payment Captured -->
						<xsl:when test="transaction/auto-capture = 'true'">2001</xsl:when>
						<!-- Payment Authorized -->
						<xsl:otherwise>2000</xsl:otherwise>
					</xsl:choose>
				</xsl:variable>
				parent.postMessage('<xsl:value-of select="$code" />', '*');
			</script>
		</body>
	</html>
</xsl:template>

</xsl:stylesheet>