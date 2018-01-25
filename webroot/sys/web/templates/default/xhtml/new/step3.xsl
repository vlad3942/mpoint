<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes" media-type="application/xhtml+xml" doctype-public="-//WAPFORUM//DTD XHTML Mobile 1.0//EN" doctype-system="http://www.openmobilealliance.org/DTD/xhtml-mobile10.dtd" omit-xml-declaration="no" />
<xsl:include href="../admin.xsl"/>

<xsl:template match="/root/content">
	<div id="mPoint">
		<h1>
			<xsl:value-of select="labels/mpoint" /><br />
			<img src="/img/mpoint.jpg" width="30%" height="30%" alt="- mPoint -" />
		</h1>
	</div>
	
	<div id="accountinfo">
		<xsl:copy-of select="guide/mobile" />
	</div>
	<div>
		<a href="{func:constLink('/shop/topup.php') }"><xsl:value-of select="labels/top-up" /></a>
	</div>
</xsl:template>

</xsl:stylesheet>