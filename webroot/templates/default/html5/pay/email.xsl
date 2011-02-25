<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func">
<xsl:output method="html" indent="no" media-type="text/html" doctype-public="HTML" omit-xml-declaration="yes" standalone="yes" />
<xsl:include href="../mobile.xsl" />

<xsl:template match="/root">
	<!-- Display Status Messages -->
	<xsl:apply-templates select="messages" />
	
	<div id="outer-border">
		<div class="mPoint_Help">
			<xsl:value-of select="labels/info" />
		</div>
		<form action="{func:constLink('sys/send_email.php')}" method="post">
			<div id="email">
				<div class="mPoint_Label">
					<xsl:value-of select="labels/email" />
				</div>		
				<div>
					<input type="email" name="email" value="{session/email}" maxlength="50" />
				</div>
			</div>
			<div id="submit">
				<input type="submit" value="{labels/submit}" class="mPoint_Button" />
			</div>
		</form>
	</div>
	
	<div id="link">
		<br />
		<a onclick="javascript:document.location.href='{func:constLink('accept.php') }';"><xsl:value-of select="labels/back" /></a>
	</div>
</xsl:template>

</xsl:stylesheet>