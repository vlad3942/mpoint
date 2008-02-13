<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:include href="header.xsl"/>

<xsl:template match="/root">
	<div>
		<h1>
			<xsl:value-of select="labels/mpoint" /><br />
			<img src="{system/protocol}://{system/host}/img/mpoint" width="{mpoint-logo/width}" height="{mpoint-logo/height}" alt="- mPoint -" border="0" />
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

	<div>
		<a href="{func:constLink('accept.php')}"><xsl:value-of select="labels/back" /></a>
	</div>
</xsl:template>

</xsl:stylesheet>