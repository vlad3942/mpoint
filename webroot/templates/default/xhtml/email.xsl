<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="xml" version="1.0" encoding="ISO-8859-15" indent="yes" media-type="application/xhtml+xml" doctype-public="-//WAPFORUM//DTD XHTML Mobile 1.0//EN" doctype-system="http://www.openmobilealliance.org/DTD/xhtml-mobile10.dtd" omit-xml-declaration="no" />
<xsl:include href="header.xsl"/>

<xsl:template match="/root">
	<div>
		<h1>
			<xsl:value-of select="labels/mpoint" /><br />
			<img src="{system/protocol}://{system/host}/img/mpoint" width="{mpoint-logo/width}" height="{mpoint-logo/height}" alt="- mPoint -" />
		</h1>
	</div>
	<div class="mPoint_info">
		<xsl:value-of select="labels/info" />
	</div>
	
	<div class="mPoint_label">
		<xsl:value-of select="labels/email" />
	</div>
	
	<form action="{system/protocol}://{system/host}/sys/send_mail.php" method="post">
		<div>
			<input type="text" name="email" value="{session/email}" maxlength="50" /><br />
			<input type="submit" value="{labels/submit}" />
		</div> 
	</form>
	
	<!-- Display Status Messages -->
	<xsl:apply-templates select="messages" />
	
	<div>
		<br />
		<a href="{func:constLink('accept.php')}"><xsl:value-of select="labels/back" /></a>
	</div>
</xsl:template>

</xsl:stylesheet>