<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes" media-type="text/html" doctype-public="-//WAPFORUM//DTD XHTML Mobile 1.0//EN" doctype-system="http://www.openmobilealliance.org/DTD/xhtml-mobile10.dtd" omit-xml-declaration="no" />
<xsl:include href="../mobile.xsl"/>

<xsl:template match="/root">
	<div id="progress" class="mPoint_Info">
		<xsl:value-of select="labels/progress" />
		<br /><br />
	</div>
	
	<!-- Display Status Messages -->
	<xsl:apply-templates select="messages" />
	
	<div>
		<xsl:value-of select="labels/info" />
	</div>
	<form action="{func:constLink('sys/send_email.php')}" method="post">
		<div id="email">
			<div class="mPoint_Label">
				<xsl:value-of select="labels/email" />
			</div>		
			<div>
				<input type="text" name="email" value="{session/email}" maxlength="50" />
			</div>
		</div>
		<div>
			<input type="submit" value="{labels/submit}" class="mPoint_Button" />
		</div>
	</form>
	
	<div>
		<br />
		<a href="{func:constLink('accept.php')}"><xsl:value-of select="labels/back" /></a>
	</div>
</xsl:template>

</xsl:stylesheet>